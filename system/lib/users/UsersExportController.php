<?php

class SJB_UsersExportController
{
	const USER_OPTIONS_INDEX = '__options__';

	public static function createUser($userGroupSID)
	{
		$userGroupSID = SJB_UserGroupManager::getUserGroupSIDByID($userGroupSID);
		$user = new SJB_User(array(), $userGroupSID);
		$user->addUserGroupProperty();
		$user->addRegistrationDateProperty();
		$user->addProductProperty(null, $userGroupSID);
		return $user;
	}

	public static function getSearchPropertyAliases()
	{
		$property_aliases = new SJB_PropertyAliases();

		$property_aliases->addAlias(array(
				'id' => 'user_group',
				'real_id' => 'user_group_sid',
				'transform_function' => 'SJB_UserGroupManager::getUserGroupSIDByID'
			)
		);

		return $property_aliases;
	}

	public static function getUserSIDByUsername($raw_value)
	{
		$sid = SJB_UserManager::getUserSIDByUsername($raw_value);
		if (empty($sid) && !empty($raw_value))
			$sid = -1;
		return $sid;
	}

	public static function getExportPropertyAliases()
	{
		$property_aliases = new SJB_PropertyAliases();

		$property_aliases->addAlias(array(
				'id' => 'user_group',
				'real_id' => 'user_group_sid',
				'transform_function' => 'SJB_UserGroupManager::getUserGroupNameBySID'
			)
		);

		$property_aliases->addAlias(array(
				'id' => 'product',
				'real_id' => 'sid',
				'transform_function' => 'SJB_ContractManager::getAllContractsInfoByUserSID'
			)
		);
		return $property_aliases;
	}

	public static function getExportData(array $usersSids, array $exportProperties, SJB_PropertyAliases $aliases)
	{
		$exportData = new SJB_ExportIterator;
		$exportData->setArray($usersSids);
		$exportData->setAdditionalParameters(array('exportProperties' => $exportProperties, 'aliases' => $aliases));
		$exportData->setCallbackFunction('SJB_UsersExportController::generateExportData');
		return $exportData;
	}
	
	public static function generateExportData($parameters)
	{
		$exportProperties = $aliases = $sid = null;
		
		extract($parameters);
		$exportData     = array();
		$userInfo       = SJB_UserManager::getUserInfoBySID($sid);
		$userInfo['id'] = $userInfo['sid'];
		$userInfo       = $aliases->changePropertiesInfo($userInfo);
		
		if (!empty($userInfo['product'])) {
			$contracts = $userInfo['product'];
			$userInfo['product'] = array();
			foreach ($contracts as $contract) {
				$productInfo = SJB_ProductsManager::getProductInfoBySID($contract['product_sid']);
				if ($productInfo) {
					$extraInfo = !empty($contract['serialized_extra_info']) ? unserialize($contract['serialized_extra_info']) : null;
					$userInfo['product'][] = serialize(
						array(
							'name'               => $productInfo['name'],
							'creation_date'      => $contract['creation_date'],
							'expired_date'       => $contract['expired_date'],
							'price'              => $contract['price'],
							'number_of_postings' => $contract['number_of_postings'],
							'number_of_listings' => $extraInfo ? $extraInfo['number_of_listings'] : 0,
							'status'             => $contract['status'],
						)
					);
				}
			}
			$userInfo['product'] = implode(',', $userInfo['product']);
		} else {
			$userInfo['product'] = '';
		}
		
		// this data is necessary for additional properties : like tree
		$exportData[$sid][self::USER_OPTIONS_INDEX]['user_group_id'] = SJB_Array::get($userInfo, 'user_group');
		foreach ($exportProperties as $propertyId => $value) {
			$exportData[$sid][$propertyId] = isset($userInfo[$propertyId]) ? $userInfo[$propertyId] : null;
		}
		
		self::changeTreeProperties($exportData);
		self::changeListProperties($exportData);
		self::cleanOptions($exportData);
		self::changeMonetaryProperties($exportProperties, $exportData);
		self::changeFileProperties($exportProperties, $exportData, 'file');
		self::changeFileProperties($exportProperties, $exportData, 'video');
		self::changeFileProperties($exportProperties, $exportData, 'Logo');
		self::changeLocationProperties($exportProperties, $exportData);
		
		return $exportData[$sid];
	}


	/**
	 * @static
	 * @param $export_properties
	 * @param $export_data
	 * @param $export_file_name
	 */
	public static function makeExportFile($exportData, $exportFileName)
	{
		SJB_HelperFunctions::makeXLSExportFile($exportData, $exportFileName, 'Users');
	}

	private static function changeTreeProperties(&$export_data)
	{
		$tree_fields_info = SJB_UserProfileFieldManager::getFieldsInfoByType('tree');

		foreach ($export_data as $user_sid => $property) {
			$userGroupSID = (int)SJB_UserGroupManager::getUserGroupSIDByName(SJB_Array::get($property[self::USER_OPTIONS_INDEX], 'user_group_id'));
			foreach ($tree_fields_info as $field_info) {
				$fieldID = SJB_Array::get($field_info, 'id');
				$fieldUserGroupSID = (int)SJB_Array::get($field_info, 'user_group_sid');

				if ($fieldUserGroupSID === $userGroupSID && !empty($property[$fieldID])) {
					$treeValue = SJB_Array::get($property, $fieldID);

					if (!empty($treeValue)) {
						$tree_values = explode(',', $treeValue);
						$tree_display_value = array();
						foreach ($tree_values as $value) {
							$display_value = SJB_UserProfileFieldManager::getTreeDisplayValueBySID($value);
							if (!empty($display_value)) {
								$tree_display_value = array_unique(array_merge($tree_display_value, $display_value));
								$export_data[$user_sid][$fieldID] = implode(',', $tree_display_value);
							}
						}
					}
				}
			}
		}
	}

	private static function changeListProperties(&$export_data)
	{
		$listFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('list');
		$multilistFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('multilist');
		$fieldsInfo = array_merge($listFieldsInfo, $multilistFieldsInfo);
		foreach ($export_data as $user_sid => $property) {
			$userGroupSID = (int)SJB_UserGroupManager::getUserGroupSIDByName(SJB_Array::get($property[self::USER_OPTIONS_INDEX], 'user_group_id'));
			foreach ($fieldsInfo as $field_info) {
				$fieldID = SJB_Array::get($field_info, 'id');
				$fieldUserGroupSID = (int)SJB_Array::get($field_info, 'user_group_sid');
				if ($fieldUserGroupSID === $userGroupSID && !empty($property[$fieldID])) {
					$fieldInfo = SJB_UserProfileFieldManager::getFieldInfoBySID($field_info['sid']);
					switch ($fieldInfo['type']) {
						case 'list':
							foreach ($fieldInfo['list_values'] as $listValues) {
								if ($listValues['id'] == $property[$field_info['id']]) {
									$export_data[$user_sid][$field_info['id']] = $listValues['caption'];
									break;
								}
							}
							break;
						case 'multilist':
							$multilistValues = explode(',', $property[$field_info['id']]);
							$multilistDisplayValues = array();
							foreach ($fieldInfo['list_values'] as $listValues) {
								if (in_array($listValues['id'], $multilistValues))
									$multilistDisplayValues[] = $listValues['caption'];
							}
							$export_data[$user_sid][$field_info['id']] = implode(',', $multilistDisplayValues);
							break;
					}
				}
			}
		}
	}

	private static function changeMonetaryProperties(&$export_properties, &$export_data)
	{
		$fieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('monetary');

		foreach ($fieldsInfo as $fieldInfo) {
			if (isset($export_properties[$fieldInfo['id']])) {
				foreach ($export_data as $user_sid => $property)
					$export_data[$user_sid][$fieldInfo['id']] = isset($property[$fieldInfo['id']]['value']) ? $property[$fieldInfo['id']]['value'] : '';
			}
		}
	}

	private static function changeFileProperties(&$exportProperties, &$exportData, $fileType)
	{
		$filePropertiesInfo = SJB_UserProfileFieldManager::getFieldsInfoByType($fileType);

		foreach ($filePropertiesInfo as $propertyInfo) {
			if (isset($exportProperties[$propertyInfo['id']])) {

				foreach ($exportData as $userSid => $property) {
					$fileId   = $exportData[$userSid][$propertyInfo['id']];
					$fileData = &$exportData[$userSid][$propertyInfo['id']];
					$fileData = null;

					$fileInfo = empty($fileId)? null: $fileInfo = SJB_UploadFileManager::getUploadedFileInfo($fileId);

					if (!empty($fileInfo)) {
						$fileSavedName        = $fileInfo['saved_file_name'];
						$fileGroup            = $fileInfo['file_group'];
						$uploadFilesDirectory = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY") . '/' . $fileGroup . '/';
						$exportFilesDirectory =  SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY") . '/' . $fileGroup . '/';

						$fileNameParsed = explode('.', $fileSavedName);
						$fileExtension  = array_pop($fileNameParsed);
						$fileName       = implode('.', $fileNameParsed);

						@copy($uploadFilesDirectory . $fileSavedName, $exportFilesDirectory . $fileSavedName);

						if ($propertyInfo['id'] == 'Logo') {
							@copy($uploadFilesDirectory . $fileName . '_thumb.' . $fileExtension, $exportFilesDirectory . $fileName . '_thumb.' . $fileExtension);
						} else if ($propertyInfo['id'] == 'video') {
							@copy($uploadFilesDirectory . $fileName . '.png', $exportFilesDirectory . $fileName . '.png');
						}

						$fileData = serialize(
							array(
								'name'      => $fileName,
								'extension' => $fileExtension,
								'mimeType'  => $fileInfo['mime_type'],
							)
						);
					}
				}
			}
		}
	}
	
	private static function changeLocationProperties(&$exportProperties, &$exportData)
	{
		$locationFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('location');
		foreach ($locationFieldsInfo as $fieldInfo) {
			if (isset($exportProperties[$fieldInfo['id']])) {
				unset($exportProperties[$fieldInfo['id']]);
				$exportLocationProperties[$fieldInfo['id'].'.Country'] = $fieldInfo['id'].'.Country';
				$exportLocationProperties[$fieldInfo['id'].'.State'] = $fieldInfo['id'].'.State';
				$exportLocationProperties[$fieldInfo['id'].'.City'] = $fieldInfo['id'].'.City';
				$exportLocationProperties[$fieldInfo['id'].'.ZipCode'] = $fieldInfo['id'].'.ZipCode';
				$exportLocationProperties[$fieldInfo['id'].'.Address'] = $fieldInfo['id'].'.Address';
				ksort($exportLocationProperties);
				$exportProperties = array_merge($exportProperties, $exportLocationProperties);
				foreach ($exportData as $userSID => $property) {
					if (isset($property[$fieldInfo['id']]) && is_array($property[$fieldInfo['id']])) {
						$propertyLocation = array();
						foreach ($property[$fieldInfo['id']] as $locationField => $fieldValue) {
							if ($locationField == 'Country' && !empty($locationField)) {
								$countryInfo = SJB_CountriesManager::getCountryInfoBySID($fieldValue);
								$fieldValue = !empty($countryInfo['country_name']) ? $countryInfo['country_name'] : '';
							} elseif ($locationField == 'State') {
								$stateInfo = SJB_StatesManager::getStateInfoBySID($fieldValue);
								$fieldValue = !empty($stateInfo['state_name']) ? $stateInfo['state_name'] : '';
							}
							$propertyLocation[$fieldInfo['id'].'.'.$locationField] = $fieldValue;
						}
						unset($property[$fieldInfo['id']]);
						ksort($propertyLocation);
						$exportData[$userSID] = array_merge($property, $propertyLocation);
					}
				}
			}
		}
	}

	public static function _getUserSiteURL()
	{
		if ($user_config_file_path = SJB_System::getSystemSettings('USER_CONFIG_FILE'))
			return SJB_System::getSettingsFromFile($user_config_file_path, 'SITE_URL');
		return SJB_System::getSystemSettings('SITE_URL');
	}

	public static function _getPictureExportURL($picture_info)
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		return "{$export_files_dir}/pictures/{$picture_info['listing_sid']}_{$picture_info['order']}.jpeg";
	}

	public static function _getUploadedPictureURL($picture_info)
	{
		$uploaded_files_dir = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");
		return "{$uploaded_files_dir}/pictures/{$picture_info['picture_saved_name']}";
	}

	public static function createExportDirectories()
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");

		if (!is_dir($export_files_dir))
			mkdir($export_files_dir, 0777);
		if (!is_dir($export_files_dir . '/pictures'))
			mkdir($export_files_dir . '/pictures', 0777);
		if (!is_dir($export_files_dir . '/files'))
			mkdir($export_files_dir . '/files', 0777);
		if (!is_dir($export_files_dir . '/video'))
			mkdir($export_files_dir . '/video', 0777);

		return true;
	}

	public static function createExportDirectoriesForExample()
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		if (empty($export_files_dir))
			return;
		if (!is_dir($export_files_dir))
			mkdir($export_files_dir, 0777);
	}

	public static function archiveAndSendExportFile()
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");

		if (empty($export_files_dir))
			return;

		$archive_file_path = SJB_Path::combine($export_files_dir, "users.tar.gz");

		$old_path = getcwd();
		chdir($export_files_dir);

		$tar = new Archive_Tar('users.tar.gz', 'gz');
		$tar->create("files video pictures users.xls");

		chdir($old_path);
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=users.tar.gz");
		header("Content-Length: " . filesize($archive_file_path));
		readfile($archive_file_path);
		SJB_Filesystem::delete($export_files_dir);
	}

	private static function cleanOptions(&$export_data)
	{
		foreach ($export_data as &$properties)
			unset($properties[self::USER_OPTIONS_INDEX]);
	}
}
