<?php

class SJB_Admin_Classifieds_ImportUsers extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('import_users');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$template_processor = SJB_System::getTemplateProcessor();
		$errors = array();

		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$file_info = isset($_FILES['import_file']) ? $_FILES['import_file'] : null;
		if (!empty($file_info)) {
			$extension = $_REQUEST['file_type'];
			if (!SJB_ImportFile::isValidFileExtensionByFormat($extension, $file_info)) {
				$errors['DO_NOT_MATCH_SELECTED_FILE_FORMAT'] = true;
			}
		}

		if (empty($file_info) || $file_info['error'] || !empty($errors)) {

			if (isset($file_info['error']) && $file_info['error'] > 0) {
				$errors[SJB_UploadFileManager::getErrorId($file_info['error'])] = 1;
			}

			$user_groups = SJB_UserGroupManager::getAllUserGroupsInfo();
			$template_processor->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
			$template_processor->assign('user_groups', $user_groups);
			$template_processor->assign('errors', $errors);
			$template_processor->assign('charSets', SJB_HelperFunctions::getCharSets());
			$template_processor->display('import_users.tpl');
		} else {
			$csv_delimiter = SJB_Request::getVar('csv_delimiter', null);
			$user_group_id = SJB_Request::getVar('user_group_id', null);
            $user_group_sid= SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);

			if ($extension == 'xls')
				$import_file = new SJB_ImportFileXLS($file_info);
			elseif ($extension == 'csv')
				$import_file = new SJB_ImportFileCSV($file_info, $csv_delimiter);

			$import_file->parse($encodingFromCharset);
			$user            = $this->CreateUser(array(), $user_group_id);
			$imported_data   = $import_file->getData();
			$count           = 0;
			$import_file_url = false;
			$usersID         = array();
			
			foreach ($imported_data as $key => $importedColumn) {
				if ($key == 1) {
					$imported_user_processor = new SJB_ImportedUserProcessor($importedColumn, $user);
					continue;
				}
				if (!$importedColumn)
					continue;
				
				$userInfo = $imported_user_processor->getData($importedColumn);
				$extUserID = isset($userInfo['extUserID']) ? $userInfo['extUserID'] : '';
				$user     = $this->CreateUser(array(), $user_group_id);
				$user->addExtUserIDProperty();
				$doc      = new DOMDocument();
				foreach ($user->getProperties() as $property) {
					if ($property->id == 'active') {
						$property->type->property_info['value'] = $property->value;
					}

					/*Этот код пригодится при реализации импорта файлов из архива. Авто распаковка файлов.
					 if (in_array($property->getType(), array('file', 'logo', 'picture', 'video')) && $property->value !== '') {
						$field_info = SJB_UserProfileFieldManager::getFieldInfoBySID($property->sid);
						if (SJB_UploadFileManager::fileImport($userInfo, $field_info))
							$import_file_url = true;
					}*/

					elseif ($property->getType() == 'location') {
						$locationFields = array($property->id.'.Country', $property->id.'.State', $property->id.'.City', $property->id.'.ZipCode', $property->id.'.Address');
						$locationFieldAdded = array();
						foreach ($locationFields as $locationField) {
							if (array_key_exists($locationField, $userInfo)) {
								switch ($locationField) {
									case $property->id.'.Country':
										$value = SJB_CountriesManager::getCountrySIDByCountryName($userInfo[$locationField]);
										if (!$value) {
											$value = SJB_CountriesManager::getCountrySIDByCountryCode($userInfo[$locationField]);
										}
										break;
									case $property->id.'.State':
										$value = SJB_StatesManager::getStateSIDByStateName($userInfo[$locationField]);
										if (!$value) {
											$value = SJB_StatesManager::getStateSIDByStateCode($userInfo[$locationField]);
										}
										break;
									default:
										$value = $userInfo[$locationField];
										break;
								}
								unset($userInfo[$locationField]);
								$userInfo[$property->id][str_replace($property->id.'.', '', $locationField)] = $value;
								$locationFieldAdded[] = str_replace($property->id.'.', '', $locationField);
							}
						}
						if ($property->id == 'Location') {
							$locationFields = array('Country', 'State', 'City', 'ZipCode', 'Address');
							foreach ($locationFields as $locationField) {
								if (array_key_exists($locationField, $userInfo) && !in_array($locationField, $locationFieldAdded) && !$user->getProperty($locationField)) {
									switch ($locationField) {
										case 'Country':
											$value = SJB_CountriesManager::getCountrySIDByCountryName($userInfo[$locationField]);
											if (!$value) {
												$value = SJB_CountriesManager::getCountrySIDByCountryCode($userInfo[$locationField]);
											}
											break;
										case 'State':
											$value = SJB_StatesManager::getStateSIDByStateName($userInfo[$locationField]);
											if (!$value) {
												$value = SJB_StatesManager::getStateSIDByStateCode($userInfo[$locationField]);
											}
											break;
										default:
											$value = $userInfo[$locationField];
											break;
									}
									$userInfo[$property->id][$locationField] = $value;
									unset($userInfo[$locationField]);
								}
							}
						}
					}
				}

				$user = $this->CreateUser($userInfo, $user_group_id);
				$user->addExtUserIDProperty($extUserID);
				
				$username = SJB_Array::get($userInfo, 'username');
				if (empty($username)) {
					$errors[] = 'Empty username is not allowed, record ignored.';
				} elseif (!is_null(SJB_UserManager::getUserSIDbyUsername($username))) {
					$errors[] = '\'' . $userInfo['username'] . '\' - this user name already exists, record ignored.';
				} else {
					$originalMd5Password = $user->getPropertyValue('password');
					SJB_UserManager::saveUser($user);

					$this->extraProperties($user, $userInfo, $usersID);

					if (!empty($originalMd5Password)) {
						SJB_UserManager::saveUserPassword($user->getSID(), $originalMd5Password);
					}
                    $isApproveByAdmin = SJB_UserGroupManager::isApproveByAdmin($user_group_sid);
                    if($isApproveByAdmin)
                        SJB_UserManager::setApprovalStatusByUserName($user->getUserName(), 'Pending');
					$count++;
				}
			}

			if ($import_file_url) {
				SJB_Filesystem::delete(SJB_System::getSystemSettings("IMPORT_FILES_DIRECTORY"));
			}

			$template_processor->assign('imported_users_count', $count);
			$template_processor->assign('errors', $errors);
			$template_processor->display('import_users_result.tpl');
		}
	}

	private function CreateUser($user_info, $user_group_id)
	{
		$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);
		return new SJB_User($user_info, $user_group_sid);
	}

	private function extraProperties($user, $userInfo, &$usersID)
	{
		$savedProperties = array(
			'user_group' => 1,
			'pictures' 	 => 1,
		);
		foreach ($user->getProperties() as $property) {
			if (!in_array($property->id, array('file', 'Logo', 'video'))) {
				$savedProperties[$property->id] = 1;
			}
		}

		$queryFields = '';
		foreach (array_diff_key($userInfo, $savedProperties) as $key => $value) {
			if($key == 'id') {
				$usersID[$value] = $user->getSID();
				continue;
			}

			if($key == 'product') {
				$products = $value? explode(',', $value): array();
				$i        = sizeof($products);
				while(--$i != -1) {
					$productProperties = @unserialize($products[$i]);
					if (!$productProperties) {
						continue;
					}

					$productSid = SJB_ProductsManager::getProductSidByName($productProperties['name']);
					if (!$productSid) {
						continue;
					}

					$contract = new SJB_Contract(array('product_sid' => $productSid, 'numberOfListings' => $productProperties['number_of_listings']));
					$contract->setPrice($productProperties['price']);
					$contract->setCreationDate($productProperties['creation_date']);
					$contract->setExpiredDate($productProperties['expired_date']);
					$contract->setStatus($productProperties['status']);
					$contract->setUserSID($user->getSID());
					$contract->saveInDB();
					SJB_ContractSQL::updatePostingsNumber($contract->id, $productProperties['number_of_postings']);
					SJB_ProductsManager::incrementPostingsNumber($productSid, $productProperties['number_of_postings']);
				}

				continue;
			}

			if (in_array($key, array('file', 'Logo', 'video')) && !empty($value)) {
				$property = $user->getProperty($key);
				if (!$property) {
					continue;
				}

				$fileProperties = @unserialize($value);
				if (!$fileProperties) {
					continue;
				}

				$fieldInfo = SJB_UserProfileFieldManager::getFieldInfoBySID($property->sid);

				switch ($key) {
					case 'file':
						$value = 'Resume_' . $user->getSID();
						$this->databaseFileRegister('file', $value, $fileProperties['name'] . '.' . $fileProperties['extension'], $fileProperties['mimeType']);
						break;
					case 'Logo':
						$value = 'Logo_' . $user->getSID();
						$this->databaseFileRegister('pictures', $value, $fileProperties['name'] . '.' . $fileProperties['extension'], $fileProperties['mimeType']);
						$this->databaseFileRegister('pictures', 'Logo_' . $user->getSID() . '_thumb', $fileProperties['name'] . '_thumb.' . $fileProperties['extension'], $fileProperties['mimeType']);
						break;
					case 'video':
						$value = 'video_' . $user->getSID();
						$this->databaseFileRegister('video', 'video_' . $user->getSID(), $fileProperties['name'] . '.' . $fileProperties['extension'], $fileProperties['mimeType']);
						break;
				}
			}

			if ($key == 'registration_date') {
				$isValid = SJB_UserRegistrationDateValidator::isValid($userInfo['registration_date']);
				if ($isValid !== true) {
					if (!isset($errors['registrationDate'])) {
						$errors['registrationDate'][] = $isValid;
					}

					if (isset($userInfo['username'])) {
						$errors['registrationDate'][] = $userInfo['username'] . ', ';
					}

					continue;
				}
			}

			if (!empty($value)) {
				$queryFields .= $queryFields? ", `" . SJB_DB::quote($key) . "` = '" . SJB_DB::quote($value) . "'": "`" . SJB_DB::quote($key) . "` = '" . SJB_DB::quote($value) . "'";
			}
		}

		if (!empty($queryFields)) {
			SJB_DB::queryExec("UPDATE ?w SET " . $queryFields . " WHERE `sid` = ?n", 'users', $user->getSID());
		}
	}

	private function databaseFileRegister($fileGroup, $fileId, $fileSaveName, $mimeType)
	{
		$uploadManager = new SJB_UploadFileManager();
		$uploadManager->setFileGroup($fileGroup);
		$uploadManager->registNewFile($fileId, $fileSaveName);

		SJB_DB::query("UPDATE `uploaded_files` SET `mime_type` = ?s WHERE `id` = ?s", SJB_DB::quote($mimeType), $fileId);
	}
}