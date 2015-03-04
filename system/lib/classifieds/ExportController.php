<?php

class SJB_ExportController
{
	public static function createListing($listing_type_id)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$listing = new SJB_Listing(array(), $listing_type_sid);
		$listing->addUsernameProperty();
		$listing->addListingTypeIDProperty();
		$listing->addActivationDateProperty();
		$listing->addExpirationDateProperty();
		return $listing;
	}

	public static function getSearchPropertyAliases()
	{
		$property_aliases = new SJB_PropertyAliases();
		$property_aliases->addAlias(array
		(
			'id' 				 => 'listing_type',
			'real_id' 			 => 'listing_type_sid',
			'transform_function' => 'SJB_ListingTypeManager::getListingTypeSIDByID',
		));
		$property_aliases->addAlias(array
		(
			'id' 				 => 'username',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_ExportController::getUserSIDByUsername',
		));
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
		$property_aliases->addAlias(array
		(
			'id' 				 => 'listing_type',
			'real_id' 			 => 'listing_type_sid',
			'transform_function' => 'SJB_ListingTypeManager::getListingTypeIDBySID',
		));
		$property_aliases->addAlias(array
		(
			'id' 				 => 'username',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_UserManager::getUserNameByUserSID',
		));
		$property_aliases->addAlias(array
		(
			'id' 				 => 'extUserID',
			'real_id' 			 => 'user_sid',
			'transform_function' => 'SJB_UserManager::getExtUserIDByUserSID',
		));
		return $property_aliases;
	}

	public static function getExportData(array $listingsSid, array $exportProperties, SJB_PropertyAliases $aliases)
	{
		$exportData = new SJB_ExportIterator();
		$exportData->setArray($listingsSid);
		$exportData->setAdditionalParameters(array('exportProperties' => $exportProperties, 'aliases' => $aliases));
		$exportData->setCallbackFunction('SJB_ExportController::generateExportData');
		return $exportData;
	}
	
	public static function generateExportData($parameters)
	{
		$exportProperties = $aliases = $sid = null;
		
		extract($parameters);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($sid);
		$listingInfo = $aliases->changePropertiesInfo($listingInfo);
		$exportData  = array();
		$i18n        = SJB_I18N::getInstance();
		
		foreach ($exportProperties as $propertyId => $value) {
			if ('ApplicationSettings' == $propertyId) {
				$exportData[$sid][$propertyId] = isset($listingInfo[$propertyId]['value']) ? $listingInfo[$propertyId]['value'] : null;
			} else {
				$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($propertyId);
				if (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'complex' && isset($listingInfo[$propertyId])) {
					$complexFields = $listingInfo[$propertyId];
					if (is_string($listingInfo[$propertyId]))
						$complexFields = unserialize($complexFields);
					if (is_array($complexFields)) {
						$fieldsInfo = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($fieldInfo['sid']);
						foreach ($fieldsInfo as $key => $info) {
							$fieldsInfo[$info['id']] = $info;
							unset($fieldsInfo[$key]);
						}
						$domDocument = new DOMDocument();
						$rootElement = $domDocument->createElement($propertyId . 's');
						$domDocument->appendChild($rootElement);
						$propertyElements = array();
						$createPropertyElements = true;
						foreach ($complexFields as $fieldName => $fieldValue) {
							$fieldInfo = isset($fieldsInfo[$fieldName]) ? $fieldsInfo[$fieldName] : array();
							foreach ($fieldValue as $key => $value) {
								if (isset($fieldInfo['type']) && $fieldInfo['type'] == 'complexfile' && $value != '') {
									$fileName = SJB_UploadFileManager::getUploadedSavedFileName($value);
									$value = $fileName ? 'files/' . $fileName : '';
								}
								elseif (isset($fieldInfo['type']) && $fieldInfo['type'] == 'date' && $value != '') {
									$value = $i18n->getDate($value);
								}
								if ($createPropertyElements) {
									$propertyElement = $domDocument->createElement($propertyId);
									$rootElement->appendChild($propertyElement);
									$propertyElements[$key] = $propertyElement;
								}
								$fieldElement = $domDocument->createElement($fieldName);
								$propertyElements[$key]->appendChild($fieldElement);
								$valElement = $domDocument->createTextNode(XML_Util::replaceEntities($value));
								$fieldElement->appendChild($valElement);
							}
							$createPropertyElements = false;
						}
						$exportData[$sid][$propertyId] = $domDocument->saveXML();
					} else {
						$exportData[$sid][$propertyId] = null;
					}
				} else {
					$exportData[$sid][$propertyId] = isset($listingInfo[$propertyId]) ? $listingInfo[$propertyId] : null;
				}
			}
		}
		
		self::changeTreeProperties($exportProperties, $exportData);
		self::changeMonetaryProperties($exportProperties, $exportData);
		self::changeListProperties($exportProperties, $exportData);
		self::changePicturesProperties($exportProperties, $exportData);
		self::changeFileProperties($exportProperties, $exportData, 'file');
		self::changeFileProperties($exportProperties, $exportData, 'video');
		self::changeComplexFileProperties($exportProperties, $exportData, 'complexfile');
		self::changeLocationProperties($exportProperties, $exportData);
		
		return $exportData[$sid];
	}

	/**
	 * @static
	 * @param $exportData
	 * @param $exportFileName
	 */
	public static function makeExportFile($exportData, $exportFileName)
	{
		SJB_HelperFunctions::makeXLSExportFile($exportData, $exportFileName, 'Listings');
	}

	private static function changeTreeProperties(&$exportProperties, &$exportData)
	{
		$tree_fields_info = SJB_ListingFieldManager::getFieldsInfoByType('tree');
		
		foreach ($tree_fields_info as $field_info) {
			$field_info = SJB_ListingFieldManager::getFieldInfoBySID($field_info['sid']);
			$fieldID = $field_info['id'];
			if (isset($exportProperties[$fieldID])) {
				foreach ($exportData as $listing_sid => $property) {
					$fieldTreeExportHelper = new SJB_FieldTreeExportHelper($fieldID, $exportData[$listing_sid][$fieldID]);
					$exportData[$listing_sid][$fieldID] = $fieldTreeExportHelper->getDataToExport();
				}
			}
		}
	}
	
	private static function changeListProperties(&$exportProperties, &$exportData)
	{
		$listFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('list');
		$multilistFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('multilist');
		$fieldsInfo = array_merge($listFieldsInfo, $multilistFieldsInfo);
		foreach ($fieldsInfo as $field_info) {
			$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($field_info['sid']);
			if (isset($exportProperties[$field_info['id']])) {
				foreach ($exportData as $listing_sid => $property) {
					switch ($fieldInfo['type']) {
						case 'list':
							foreach ($fieldInfo['list_values'] as $listValues) {
								if ($listValues['id'] == $property[$field_info['id']]) {
									$exportData[$listing_sid][$field_info['id']] = $listValues['caption'];
									break;
								}
							}
							break;
						case 'multilist':
							$multilistValues = explode(',', $exportData[$listing_sid][$field_info['id']]);
							$multilistDisplayValues = array();
							foreach ($fieldInfo['list_values'] as $listValues) {
								if (in_array($listValues['id'], $multilistValues)) 
									$multilistDisplayValues[] = $listValues['caption'];
							}
							$exportData[$listing_sid][$field_info['id']] = implode(',', $multilistDisplayValues);
							break;
					}
				}
			}
		}
	}

	private static function changeMonetaryProperties(&$exportProperties, &$exportData)
	{
		$fieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('monetary');
		foreach ($fieldsInfo as $fieldInfo) {
			if (isset($exportProperties[$fieldInfo['id']])) {
				foreach ($exportData as $listing_sid => $property)
					$exportData[$listing_sid][$fieldInfo['id']] = isset($property[$fieldInfo['id']]['value']) ? $property[$fieldInfo['id']]['value'] : '';
			}
		}
	}

	private static function changePicturesProperties(&$exportProperties, &$exportData)
	{
		if (isset($exportProperties['pictures'])) {
			// listings walkthrough
			foreach ($exportData as $listing_sid => $property) {
				$pictures = &$exportData[$listing_sid]['pictures'];
				$pictures = null;

				$gallery = new SJB_ListingGallery();
				$gallery->setListingSID($listing_sid);

				$pictures_info = $gallery->getPicturesInfo();

				foreach ($pictures_info as $picture_info) {
					$picture_export_url = SJB_ExportController::_getPictureExportURL($picture_info);

					$uploaded_picture_url = SJB_ExportController::_getUploadedPictureURL($picture_info);
					if (!@copy($uploaded_picture_url, $picture_export_url)) {
						continue;
					}

					$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
					$picture_export_url = str_replace($export_files_dir, '', $picture_export_url);
					$pictures .= ltrim($picture_export_url, '/') . ";";
				}
			}
		}
	}

	private static function changeFileProperties(&$exportProperties, &$exportData, $file_type)
	{
		$file_properties_info = SJB_ListingFieldManager::getFieldsInfoByType($file_type);

		foreach ($file_properties_info as $property_info) {
			if (isset($exportProperties[$property_info['id']])) {
				// listings walkthrough
				foreach ($exportData as $listing_sid => $property) {
					$file_value = $exportData[$listing_sid][$property_info['id']];

					$file = &$exportData[$listing_sid][$property_info['id']];
					$file = null;

					if (!empty($file_value)) {
						$file_name 		  = SJB_UploadFileManager::getUploadedSavedFileName($file_value);
						$file_group 	  = SJB_UploadFileManager::getUploadedFileGroup($file_value);
						$file_path 		  = SJB_ExportController::_getUploadedFileURL($file_name, $file_group);
						$file_export_path = SJB_ExportController::_getFileExportURL($file_name, $file_group, $listing_sid);

						@copy($file_path, $file_export_path);
						$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
						$file_export_path = str_replace($export_files_dir, '', $file_export_path);
						$file = ltrim($file_export_path, '/');
					}
				}
			}
		}
	}

	private static function changeComplexFileProperties(&$exportProperties, &$exportData, $file_type)
	{
		$file_properties_info = SJB_ListingComplexFieldManager::getFieldsInfoByType($file_type);

		foreach ($file_properties_info as $property_info) {
			$parent_property_info = SJB_ListingFieldManager::getFieldInfoBySID($property_info['field_sid']);
			if (isset($exportProperties[$parent_property_info['id']])) {
				// listings walkthrough
				foreach ($exportData as $listing_sid => $property) {
					$listing_info = SJB_ListingManager::getListingInfoBySID($listing_sid);
					if (!isset($listing_info[$parent_property_info['id']][$property_info['id']]) || !is_array($listing_info[$parent_property_info['id']])) {
						continue;
					}
					$file_values = $listing_info[$parent_property_info['id']][$property_info['id']];

					if (!empty($file_values)) {
						foreach ($file_values as $file_value) {
							if ($file_value) {
								$file_name 		  = SJB_UploadFileManager::getUploadedSavedFileName($file_value);
								$file_group 	  = SJB_UploadFileManager::getUploadedFileGroup($file_value);
								$file_path 		  = SJB_ExportController::_getUploadedFileURL($file_name, $file_group);
								$file_export_path = SJB_ExportController::_getFileExportURL($file_name, $file_group, $listing_sid, $file_name);

								@copy($file_path, $file_export_path);
							}
						}
					}
				}
			}
		}
	}
	
	private static function changeLocationProperties(&$exportProperties, &$exportData)
	{
		$locationFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('location');
		foreach ($locationFieldsInfo as $fieldInfo) {
			if (isset($exportProperties[$fieldInfo['id']])) {
				unset($exportProperties[$fieldInfo['id']]);
				$exportLocationProperties[$fieldInfo['id'].'.Country'] = $fieldInfo['id'].'.Country';
				$exportLocationProperties[$fieldInfo['id'].'.State'] = $fieldInfo['id'].'.State';
				$exportLocationProperties[$fieldInfo['id'].'.City'] = $fieldInfo['id'].'.City';
				$exportLocationProperties[$fieldInfo['id'].'.ZipCode'] = $fieldInfo['id'].'.ZipCode';
				ksort($exportLocationProperties);
				$exportProperties = array_merge($exportProperties, $exportLocationProperties);
				foreach ($exportData as $listingSID => $property) {
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
						$exportData[$listingSID] = array_merge($property, $propertyLocation);;
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

	public static function _getFileExportURL($file_name, $file_group, $listing_sid, $file_export_name = false)
	{
		$export_files_dir = SJB_System::getSystemSettings("EXPORT_FILES_DIRECTORY");
		$file_name_parsed = explode(".", $file_name);
		$file_extension = end($file_name_parsed);
		$file_export_name = $file_export_name ? $file_export_name : $listing_sid . "." . $file_extension;
		return "{$export_files_dir}/{$file_group}/{$file_export_name}";
	}

	public static function _getUploadedFileURL($file_name, $file_group)
	{
		$uploaded_files_dir = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");
		return "{$uploaded_files_dir}/{$file_group}/{$file_name}";
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

		$archive_file_path = SJB_Path::combine($export_files_dir, "export.tar.gz");
		$old_path = getcwd();
		chdir($export_files_dir);
		$tar = new Archive_Tar('export.tar.gz', 'gz');
		$tar->create("files video pictures export.xls");

		chdir($old_path);
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		header("Content-type: application/octet-stream");
		header("Content-disposition: attachment; filename=export.tar.gz");
		header("Content-Length: " . filesize($archive_file_path));
		readfile($archive_file_path);
		SJB_Filesystem::delete($export_files_dir);
	}

}
