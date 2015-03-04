<?php

/**
 * This script used for ajax file uploads from multifields forms of SJB.
 *
 * Work flow;
 *
 * 1. looks for $_FILES and gets file names from it
 * 2. checks this names with fields incoming from form
 * 3. if names of $_FILES is presents - handle file and add it to temporary storage
 * 4. when form will be really submitted (not only ajax upload), needs to check temporary storage and gets data from it
 */

class SJB_Miscellaneous_AjaxFileUploadHandler extends SJB_Function
{
	private $fileUniqueId  = '';
	private $errors        = array();
	private $property      = null;
	private $propertyValue = null;
	
	public function execute()
	{
		$ajaxAction = SJB_Request::getVar('ajax_action', '', 'GET');
		$formToken  = SJB_Request::getVar('form_token', '');


		// save token date in session. In some code we needs to get list of it, and clean old tokens data from
		// session.
		self::setTokenDateToSession($formToken);


		switch ($ajaxAction) {
			// UPLOAD USER PROFILE VIDEO
			case 'upload_profile_video':
			case 'upload_profile_logo':

				$uploadedFieldId = SJB_Request::getVar('uploaded_field_name', '', 'GET');
				// get field by user group return not all fields of profile.
				// but now we use getAllFieldsInfo() to check fields
				$userProfileFields = SJB_UserProfileFieldManager::getAllFieldsInfo();

				$fieldSid = null;
				foreach ($userProfileFields as $field) {
					if ($field['id'] != $uploadedFieldId) {
						continue;
					}
					$fieldSid = $field['sid'];
				}

				if ($fieldSid == null) {
					echo "Wrong profile field specified";
					exit;
				}

				$fieldInfo  = SJB_UserProfileFieldManager::getFieldInfoBySID($fieldSid);
				$tp         = SJB_System::getTemplateProcessor();
				$validation = $this->validationManager($fieldInfo, $tp, $uploadedFieldId);

				if ($validation === true) {
					// video file already uploaded after isValid checks
					// but for 'Logo' - we need some actions to make save picture
					if ($fieldInfo['type'] == 'logo') {
						$upload_manager = new SJB_UploadPictureManager();
						$upload_manager->setUploadedFileID($this->fileUniqueId);
						$upload_manager->setHeight($fieldInfo['height']);
						$upload_manager->setWidth($fieldInfo['width']);
						$upload_manager->uploadPicture($fieldInfo['id'], $fieldInfo);
						// and set value of file id to property
						$this->property->setValue($this->fileUniqueId);
						$this->propertyValue = $this->property->getValue();
					}

					// set uploaded video to temporary value
					if ($fieldInfo['type'] == 'video' && isset($this->propertyValue['file_id'])) {
						$uploadedID = $this->propertyValue['file_id'];
						// rename it to unique value
						SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $this->fileUniqueId, $uploadedID);

						// fill session data for tmp storage
						$fieldValue = array(
							'file_id'         => $this->fileUniqueId,
							'file_url'        => $this->propertyValue['file_url'],
							'file_name'       => $this->propertyValue['file_name'],
							'saved_file_name' => $this->propertyValue['saved_file_name'],
						);
						$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
						$tmpUploadsStorage = SJB_Array::setPathValue($tmpUploadsStorage, "{$formToken}/{$uploadedFieldId}", $fieldValue);
						SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);

					} elseif ($fieldInfo['type'] == 'logo') {
						// for Logo - we already have file_url data and file_thumb data, without file_id
						// just add this to session storage

						// fill session data for tmp storage
						$fieldValue = array(
							'file_id'         => $this->fileUniqueId,
							'file_url'        => $this->propertyValue['file_url'],
							'file_name'       => $this->propertyValue['file_name'],
							'thumb_file_url'  => $this->propertyValue['thumb_file_url'],
							'thumb_file_name' => $this->propertyValue['thumb_file_name'],
						);
						$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
						$tmpUploadsStorage = SJB_Array::setPathValue($tmpUploadsStorage, "{$formToken}/{$uploadedFieldId}", $fieldValue);
						SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);
					}

					$tp->assign(array(
						'id' 	=> $uploadedFieldId,
						'value'	=> $fieldValue,
					));
				}

				$template = '';
				switch ($fieldInfo['type']) {
					case 'video':
						$template = '../field_types/input/video_profile.tpl';
						break;
					case 'logo':
						$template = '../field_types/input/logo.tpl';
						break;
					default:
						break;
				}

				$tp->assign('form_token', $formToken);
				$tp->assign('errors', $this->errors);
				$tp->display($template);
				break;

			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			case 'delete_profile_video':
			case 'delete_profile_logo':

				$userSid  = SJB_Request::getVar('user_sid', null);
				if (empty($userSid)) {
					$userInfo = SJB_UserManager::getCurrentUserInfo();
				} else {
					$userInfo = SJB_UserManager::getUserInfoBySID($userSid);
				}


				$fieldId  = SJB_Request::getVar('field_id', null);

				// check session value
				$sessionFileStorage = SJB_Session::getValue('tmp_uploads_storage');
				$sessionFileId      = SJB_Array::getPath($sessionFileStorage, "{$formToken}/{$fieldId}/file_id");

				if (is_null($fieldId)) {
					$this->errors['PARAMETERS_MISSED'] = 1;
				} elseif ( (!empty($userInfo) && !isset($userInfo[$fieldId])) && empty($sessionFileId) ) {
					echo  json_encode( array('result' => 'success') );
					exit;
				} else {

					if (!empty($userInfo)) {
						$uploaded_file_id = $userInfo[$fieldId];
						SJB_UploadFileManager::deleteUploadedFileByID($uploaded_file_id);
					}


					if (!empty($sessionFileId)) {
						$formFileId    = SJB_Request::getVar('file_id');
						if ($sessionFileId == $formFileId) {
							SJB_UploadFileManager::deleteUploadedFileByID($formFileId);

							$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}/{$fieldId}");
							SJB_Session::setValue('tmp_uploads_storage', $sessionFileStorage);
						}
					}
				}

				if (empty($this->errors)) {
					echo  json_encode( array('result' => 'success') ) ;
				} else {
					echo  json_encode( array('result' => 'error', 'errors' => $this->errors) ) ;
				}

				exit;
				break;

			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			// UPLOAD LISTIG FILES
			case 'upload_classifieds_video':
			case 'upload_file':

				$uploadedFieldId = SJB_Request::getVar('uploaded_field_name', '', 'GET');

				// OK. For listings form we have 'listing_id' and optional field (for new listings with temporary id) - listing_type_id
				$listingId     = SJB_Request::getVar('listing_id');
				$listingTypeId = SJB_Request::getVar('listing_type_id');

				if (empty($listingTypeId)) {
					$listingInfo   = SJB_ListingManager::getListingInfoBySID($listingId);
					$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
				}
				$listingTypeSid = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeId);

				$commonListingFields = SJB_ListingFieldManager::getCommonListingFieldsInfo();
				$listingFieldsByType = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listingTypeSid);
				$listingFields       = array_merge($commonListingFields, $listingFieldsByType);


				$fieldSid = null;
				foreach ($listingFields as $field) {
					if ($field['id'] != $uploadedFieldId) {
						continue;
					}
					$fieldSid = $field['sid'];
				}

				$fieldInfo  = SJB_ListingFieldManager::getFieldInfoBySID($fieldSid);
				$tp         = SJB_System::getTemplateProcessor();
				$validation = $this->validationManager($fieldInfo, $tp, $uploadedFieldId);

				if (!$validation) {
					$tp->assign(array(
						// and fix to listing_id param
						'listing_id' => $listingId,
						'listing' => array(
							'id' => $listingId,
						),
					));
				} else {
					// video file already uploaded after isValid checks
					// but for 'Logo' - we need some actions to make save picture
					if ($this->property->getType() == 'file') {
						if ($_FILES[$uploadedFieldId]['error']) {
							$this->errors[SJB_UploadFileManager::getErrorId($_FILES[$uploadedFieldId]['error'])] = 1;
						}

						$upload_manager = new SJB_UploadFileManager();
						$upload_manager->setUploadedFileID($this->fileUniqueId);
						$upload_manager->setFileGroup('files');
						$upload_manager->uploadFile($fieldInfo['id']);
						// and set value of file id to property
						$this->property->setValue($this->fileUniqueId);
					}

					$this->propertyValue = $this->property->getValue();

					// set uploaded video to temporary value
					if (isset($this->propertyValue['file_id'])) {
						$uploadedID = $this->propertyValue['file_id'];
						// rename it to unique value
						SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $this->fileUniqueId, $uploadedID);


						// SET VALUE TO TEMPORARY SESSION STORAGE
						$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
						$fileValue = array(
							'file_id'    => $this->fileUniqueId,
							'saved_name' => $this->propertyValue['saved_file_name'],
						);
						$tmpUploadsStorage = SJB_Array::setPathValue($tmpUploadsStorage, "{$formToken}/{$uploadedFieldId}", $fileValue);
						SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);

						// update listing property
						$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
						$listing = isset($listingInfo['listing_type_sid']) ? new SJB_Listing($listingInfo, $listingInfo['listing_type_sid']) : new SJB_Listing($listingInfo);
						$listingProperties = $listing->getProperties();
						$propertyInfo = array(
							'id'        => $uploadedFieldId,
							'type'      => 'string',
							'value'     => $this->fileUniqueId,
							'is_system' => true,
						);
						foreach ($listingProperties as $property) {
							if ($property->getID() == $uploadedFieldId) {
								$listing->addProperty($propertyInfo);
							}
						}
						$listing->setSID($listingId);
						SJB_ListingManager::saveListing($listing);

						$tp->assign(array(
							'id' 	=> $uploadedFieldId,
							'value'	=> array(
								'file_url'        => $this->propertyValue['file_url'],
								'file_name'       => $this->propertyValue['file_name'],
								'saved_file_name' => $this->propertyValue['saved_file_name'],
								'file_id'         => $this->fileUniqueId,
							),
							// and fix to listing_id param
							'listing_id' => $listingId,
							'listing' => array(
								'id' => $listingId,
							),
						));
					}
				}

				switch ($this->property->getType()) {
					case 'video':
						$template = '../field_types/input/video.tpl';
						break;
					case 'file':
						$template = '../field_types/input/file.tpl';
						break;
					default:
						$template = '../field_types/input/video.tpl';
						break;
				}

				$tp->assign('errors', $this->errors);
				$tp->assign('form_token', $formToken);
				$tp->display($template);
				self::cleanOldTokensFromSession();
				break;


			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			case 'delete_classifieds_video':
			case 'delete_file':

				$listingId  = SJB_Request::getVar('listing_id', null);
				$fieldId    = SJB_Request::getVar('field_id', null);
				$formFileId = SJB_Request::getVar('file_id');
				$this->errors     = array();

				// check session value
				$sessionFileStorage = SJB_Session::getValue('tmp_uploads_storage');
				$sessionFileId = SJB_Array::getPath($sessionFileStorage, "{$formToken}/{$fieldId}/file_id");

				// if empty listing id - check end empty temporary storage
				if (strlen($listingId) == strlen( time() )) {
					if ($sessionFileId == $formFileId) {
						SJB_UploadFileManager::deleteUploadedFileByID($formFileId);
						// remove field from temporary storage
						if (!is_null($sessionFileStorage)) {
							$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}/{$fieldId}");
							SJB_Session::setValue('tmp_uploads_storage', $sessionFileStorage);
						}
					}
				} else {
					// we change existing listing
					$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);

					if ( (is_null($listingInfo) || !isset($listingInfo[$fieldId])) && empty($sessionFileId)) {
						$this->errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
					}
					else {
						if (!$this->isOwner($listingId)) {
							$this->errors['NOT_OWNER'] = 1;
						}
						else {
							$uploadedFileId = $listingInfo[$fieldId];
							if (!empty($uploadedFileId)) {
								SJB_UploadFileManager::deleteUploadedFileByID($uploadedFileId);
							}
							SJB_UploadFileManager::deleteUploadedFileByID($formFileId);

							$listingInfo[$fieldId] = '';
							$listing = isset($listingInfo['listing_type_sid'])? new SJB_Listing($listingInfo, $listingInfo['listing_type_sid']): new SJB_Listing($listingInfo);
							// remove all non-changed properties and save only changed property in listing
							$props = $listing->getProperties();
							foreach ($props as $prop) {
								if ($prop->getID() !== $fieldId) {
									$listing->deleteProperty($prop->getID());
								}
							}
							$listing->setSID($listingId);
							SJB_ListingManager::saveListing($listing);


							// remove field from temporary storage
							$sessionFileStorage = SJB_Session::getValue('tmp_uploads_storage');
							if (!is_null($sessionFileStorage)) {
								$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}/{$fieldId}");
								SJB_Session::setValue('tmp_uploads_storage', $sessionFileStorage);
							}
						}
					}
				}

				if (empty($this->errors)) {
					echo  json_encode( array('result' => 'success') ) ;
				} else {
					echo  json_encode( array('result' => 'error', 'errors' => $this->errors) ) ;
				}
				exit;
				break;


			////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			case 'get_classifieds_video_data':
			case 'get_file_field_data':

				$fieldId   = isset($_REQUEST['field_id']) ? $_REQUEST['field_id'] : null;
				$listingId = SJB_Request::getVar('listing_id');

				$filesFromTmpStorage = SJB_Session::getValue('tmp_uploads_storage');
				$fileUniqueId        = SJB_Array::getPath($filesFromTmpStorage, "{$formToken}/{$fieldId}/file_id");

				// if no temporary files uploaded, return empty string
				if (empty($fileUniqueId)) {
					return '';
				}

				$tp = SJB_System::getTemplateProcessor();
				$upload_manager = new SJB_UploadFileManager();

				$fileInfo = array(
					'id' 	=> $fieldId,
					'value'	=> array(
						'file_url'        => $upload_manager->getUploadedFileLink($fileUniqueId),
						'file_name'       => $upload_manager->getUploadedFileName($fileUniqueId),
						'saved_file_name' => $upload_manager->getUploadedSavedFileName($fileUniqueId),
						'file_id'         => $fileUniqueId,
					),
					// and fix to listing_id param
					'listing_id' => $listingId,
					'listing' => array(
						'id' => $listingId,
					),
				);

				$tp->assign($fileInfo);


				$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($fieldId);
				$fieldType = $fieldInfo['type'];

				$template = '';
				switch ($fieldType) {
					case 'video':
						$template = '../field_types/input/video.tpl';
						break;
					case 'file':
						$template = '../field_types/input/file.tpl';
						break;
					case 'logo':
						$template = '../field_types/input/logo_listing.tpl';
						break;
					default:
						break;
				}

				$uploadedFilesize = $upload_manager->getUploadedFileSize($fileUniqueId);
				$filesizeInfo = SJB_HelperFunctions::getFileSizeAndSizeToken($uploadedFilesize);
				$tp->assign(array(
						'filesize'   => $filesizeInfo['filesize'],
						'size_token' => $filesizeInfo['size_token']
					)
				);

				$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				$tp->assign('form_token', $formToken);
				$tp->display($template);
				break;


			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			case 'upload_file_complex':
			case 'upload_classifieds_video_complex':
				$uploadedFieldId = SJB_Request::getVar('uploaded_field_name', '', 'GET');

				list($parentField, $subFieldId, $complexStep) = explode(':', $uploadedFieldId);

				// OK. For listings form we have 'listing_id' and optional field (for new listings with temporary id) - listing_type_id
				$listingId     = SJB_Request::getVar('listing_id');
				$listingTypeId = SJB_Request::getVar('listing_type_id');

				if (empty($listingTypeId)) {
					$listingInfo   = SJB_ListingManager::getListingInfoBySID($listingId);
					$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
				}
				$listingTypeSid = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeId);

				$commonListingFields = SJB_ListingFieldManager::getCommonListingFieldsInfo();
				$listingFieldsByType = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listingTypeSid);
				$listingFields       = array_merge($commonListingFields, $listingFieldsByType);

				// check parent field
				$fieldSid = null;
				foreach ($listingFields as $field) {
					if ($field['id'] != $parentField) {
						continue;
					}
					$fieldSid = $field['sid'];
				}


				$complexFieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($fieldSid);
				$subFields = SJB_Array::get($complexFieldInfo, 'fields');
				if (empty($subFields)) {
					echo 'wrong field ID';
					exit;
				}

				// check field
				$fieldInfo = '';
				foreach ($subFields as $subField) {
					if ($subField['id'] != $subFieldId) {
						continue;
					}
					$fieldInfo = $subField;
				}

				$complexParameters = array(
					'parentField' => $parentField,
					'subFieldId'  => $subFieldId,
					'complexStep' => $complexStep
				);
				$tp         = SJB_System::getTemplateProcessor();
				$validation = $this->validationManager($fieldInfo, $tp, $uploadedFieldId, $complexParameters);

				$upload_manager = new SJB_UploadFileManager();
				$upload_manager->setUploadedFileID($this->fileUniqueId);
				$upload_manager->setFileGroup('files');
				$upload_manager->uploadFile($fieldInfo['id'], $parentField);
				$this->property->setValue($this->fileUniqueId);
				$this->propertyValue = $this->property->getPropertyVariablesToAssign();

				// set uploaded video to temporary value
				if ((isset($this->propertyValue['value']['file_id']) || isset($this->propertyValue['value'][$complexStep]['file_id'])) && $validation) {

					// fix for FILE type in complex field
					if (isset($this->propertyValue['value'][$complexStep]['file_id'])) {
						$this->propertyValue['value'] = $this->propertyValue['value'][$complexStep];
					}

					$filesInfo  = array($complexStep => $this->propertyValue['value']);
					$uploadedID = $this->propertyValue['value']['file_id'];

					// rename it to unique value
					SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $this->fileUniqueId, $uploadedID);

					// SET VALUE TO TEMPORARY SESSION STORAGE
					$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
					$fileValue = array(
						'file_id'    => $this->fileUniqueId,
						'saved_name' => $this->propertyValue['value']['saved_file_name'],
					);
					$tmpUploadsStorage = SJB_Array::setPathValue($tmpUploadsStorage, "{$formToken}/{$uploadedFieldId}", $fileValue);
					SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);

					$tp->assign(array(
						'id'           => $subFieldId,
						'value'        => $this->propertyValue['value']['file_name'],
						'filesInfo'    => $filesInfo,

						'complexField' => $parentField,
						'complexStep'  => $complexStep,
						// and fix to listing_id param
						'listing_id' => $listingId,
						'listing' => array(
							'id' => $listingId,
						),
					));
				}
				else {
					$tp->assign(array(
						'id'           => $subFieldId,
						'complexField' => $parentField,
						'complexStep'  => $complexStep,
						// and fix to listing_id param
						'listing_id' => $listingId,
						'listing' => array(
							'id' => $listingId,
						),
					));
				}


				switch ($this->property->getType()) {
					case 'video':
						$template = '../field_types/input/video.tpl';
						break;
					case 'file':
					case 'complexfile':
						$template = '../field_types/input/file.tpl';
						break;

					default:
						$template = '../field_types/input/video.tpl';
						break;
				}

				$tp->assign('form_token', $formToken);
				$tp->assign('errors', $this->errors);
				$tp->display($template);
				break;


			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			case 'delete_file_complex':

				$listingId  = SJB_Request::getVar('listing_id', null);
				$fieldId    = SJB_Request::getVar('field_id', null);
				$formFileId = SJB_Request::getVar('file_id');
				$this->errors     = array();

				// check session value
				$sessionFileStorage = SJB_Session::getValue('tmp_uploads_storage');
				$sessionFileId      = SJB_Array::getPath($sessionFileStorage, "{$formToken}/{$fieldId}/file_id");

				// if empty listing id - check and empty temporary storage
				if (strlen($listingId) == strlen( time() )) {
					if ($sessionFileId == $formFileId) {
						SJB_UploadFileManager::deleteUploadedFileByID($formFileId);
						// remove field from temporary storage
						$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}/{$fieldId}");
						SJB_Session::setValue('tmp_uploads_storage', $sessionFileStorage);
					}
				} else {
					// we change existing listing
					$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);

					list($complexField, $subField, $complexStep) = explode(':', $fieldId);
					$fieldValue = SJB_Array::getPath($listingInfo, "{$complexField}/{$subField}/{$complexStep}");

					// if field value not present in listing and not present in temporary storage - throw error
					if ( (is_null($listingInfo) || $fieldValue === null) && empty($sessionFileId)) {
						$this->errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
					}
					else {
						if (!$this->isOwner($listingId)) {
							$this->errors['NOT_OWNER'] = 1;
						}
						else {
							$uploadedFileId = $fieldValue;

							if (!empty($uploadedFileId)) {
								SJB_UploadFileManager::deleteUploadedFileByID($uploadedFileId);
							}
							SJB_UploadFileManager::deleteUploadedFileByID($formFileId);

							$listingInfo = SJB_Array::setPathValue($listingInfo, "{$complexField}/{$subField}/{$complexStep}", '');

							$listing = new SJB_Listing($listingInfo, $listingInfo['listing_type_sid']);
							// remove all non-changed properties and save only changed property in listing
							$props = $listing->getProperties();
							foreach ($props as $prop) {
								if ($prop->getID() !== $fieldId) {
									$listing->deleteProperty($prop->getID());
								}
							}
							$listing->setSID($listingId);
							SJB_ListingManager::saveListing($listing);


							// remove field from temporary storage
							$sessionFileStorage = SJB_Session::getValue('tmp_uploads_storage');
							if (!empty($sessionFileStorage)) {
								$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}/{$fieldId}");
								SJB_Session::setValue('tmp_uploads_storage', $sessionFileStorage);
							}
						}
					}
				}

				if (empty($this->errors)) {
					echo json_encode( array('result' => 'success') );
				} else {
					echo json_encode( array('result' => 'error', 'errors' => $this->errors) );
				}
				exit;
				break;


			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			case 'get_complexfile_field_data':

				$listingId      = SJB_Request::getVar('listing_id', null);
				$fieldId        = SJB_Request::getVar('field_id', null);
				$listingTypeId  = SJB_Request::getVar('listing_type_id');
				$listingTypeSid = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeId);

				$uploadFileManager = new SJB_UploadFileManager();

				// replace square brackets in complex field name
				$fieldId = str_replace("][", ":", $fieldId);
				$fieldId = str_replace("[", ":", $fieldId);
				$fieldId = str_replace("]", "", $fieldId);


				list($parentField, $subFieldId, $complexStep) = explode(':', $fieldId);

				$filesFromTmpStorage = SJB_Session::getValue('tmp_uploads_storage');
				//$fileUniqueId = SJB_Array::getPath($filesFromTmpStorage, "listings/{$listingId}/{$fieldId}/file_id");
				$fileUniqueId = SJB_Array::getPath($filesFromTmpStorage, "{$formToken}/{$fieldId}/file_id");

				// if no temporary files uploaded, return empty string
				if (empty($fileUniqueId)) {
					return '';
				}

				// get list of fields for all listing types
				$listingTypesInfo = SJB_ListingTypeManager::getAllListingTypesInfo();
				$allFields = array();
				foreach ($listingTypesInfo as $listingTypeInfo) {
					$typeFields = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listingTypeInfo['sid']);
					$allFields  = array_merge($allFields, $typeFields);
				}

				// NEED TO GET COMPLEX SUBFIELD PROPERTY
				$commonListingFields = SJB_ListingFieldManager::getCommonListingFieldsInfo();
				$listingFieldsByType = $allFields;
				$listingFields       = array_merge($commonListingFields, $listingFieldsByType);

				// check parent field
				$fieldSid = null;
				foreach ($listingFields as $field) {
					if ($field['id'] != $parentField) {
						continue;
					}
					$fieldSid = $field['sid'];
				}
				// parent complex field
				$complexFieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($fieldSid);
				$subFields = SJB_Array::get($complexFieldInfo, 'fields');



				if (empty($subFields)) {
					echo 'wrong field ID';
					exit;
				}

				// check field for subfield
				$complexSubFieldInfo = '';
				foreach ($subFields as $subField) {
					if ($subField['id'] != $subFieldId) {
						continue;
					}
					$complexSubFieldInfo = $subField;
				}

				if (empty($complexSubFieldInfo)) {
					echo 'Wrong field info';
					exit;
				}

				// OK. COMPLEX SUBFIELD WE HAVE
				$complexSubFieldProperty = new SJB_ObjectProperty($complexSubFieldInfo);
				// complex file fields contents array of values, not just string filename
				$complexSubFieldProperty->setValue( array($complexStep => $fileUniqueId) );

				$valueToAssign = $complexSubFieldProperty->getPropertyVariablesToAssign();
				$additionalInfo = array(
					// and fix to listing_id param
					'listing_id' => $listingId,
					'listing' => array(
						'id' => $listingId,
					),
					'complexField' => $parentField,
					'complexStep'  => $complexStep,
				);


				$tp = SJB_System::getTemplateProcessor();

				$tp->assign($valueToAssign);
				$tp->assign($additionalInfo);


				$template = '';
				switch ($complexSubFieldProperty->getType()) {
					case 'complexfile':
						$template = '../field_types/input/file.tpl';
						break;
					default:
						break;
				}

				$uploadedFilesize = $uploadFileManager->getUploadedFileSize($fileUniqueId);
				$filesizeInfo = SJB_HelperFunctions::getFileSizeAndSizeToken($uploadedFilesize);
				$tp->assign(array(
						'filesize'   => $filesizeInfo['filesize'],
						'size_token' => $filesizeInfo['size_token']
					)
				);

				$tp->assign('form_token', $formToken);
				$tp->display($template);
				break;

			case 'upload_listing_logo':

				$uploadedFieldId = SJB_Request::getVar('uploaded_field_name', '', 'GET');
				$listingSid      = SJB_Request::getVar('listing_id', null);

				$fieldInfo  = SJB_ListingFieldDBManager::getListingFieldInfoByID($uploadedFieldId);
				$tp         = SJB_System::getTemplateProcessor();
				$validation = $this->validationManager($fieldInfo, $tp, $uploadedFieldId);

				if ($validation === true) {
					$upload_manager = new SJB_UploadPictureManager();
					$upload_manager->setUploadedFileID($this->fileUniqueId);
					$upload_manager->setHeight($fieldInfo['height']);
					$upload_manager->setWidth($fieldInfo['width']);
					$upload_manager->uploadPicture($fieldInfo['id'], $fieldInfo);
					// and set value of file id to property
					$this->property->setValue($this->fileUniqueId);
					$this->propertyValue = $this->property->getValue();

					// for Logo - we already have file_url data and file_thumb data, without file_id
					// just add this to session storage
					// fill session data for tmp storage
					$fieldValue = array(
						'file_id'         => $this->fileUniqueId,
						'file_url'        => $this->propertyValue['file_url'],
						'file_name'       => $this->propertyValue['file_name'],
						'thumb_file_url'  => $this->propertyValue['thumb_file_url'],
						'thumb_file_name' => $this->propertyValue['thumb_file_name'],
					);
					$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
					$tmpUploadsStorage = SJB_Array::setPathValue($tmpUploadsStorage, "{$formToken}/{$uploadedFieldId}", $fieldValue);
					SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);

					$tp->assign(array(
						'id' 	=> $uploadedFieldId,
						'value'	=> $fieldValue,
					));

				}
				$template = '../field_types/input/logo_listing.tpl';

				$tp->assign('form_token', $formToken);
				$tp->assign('errors', $this->errors);
				$tp->assign('listing_id', $listingSid);
				$tp->display($template);
				break;

			default:
				echo "Action not defined!";
				break;
		}

		exit;

	}



	public static function setTokenDateToSession($token)
	{
		$currentTime = time();
		if (!empty($token)) {
			$tokensStorage = SJB_Session::getValue('tokens');
			if (!is_array($tokensStorage)) {
				$tokensStorage = array();
			}
			$tokensStorage = SJB_Array::setPathValue($tokensStorage, "{$token}", $currentTime);
			SJB_Session::setValue('tokens', $tokensStorage);
		}
	}


	public static function cleanOldTokensFromSession()
	{
		$origTokensStorage = SJB_Session::getValue('tokens');
		if (!is_array($origTokensStorage)) {
			return;
		}
		$currentTime = time();
		$expireTime  = 1440; // 24 minutes

		$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
		$tokensStorage     = $origTokensStorage;

		// foreach token check time and remove it from session and remove it data from temporary uploads storage
		foreach ($tokensStorage as $token => $time) {
			$tokenTime = $currentTime - $time;
			if ($tokenTime > $expireTime) {
				// remove token data from session
				$tmpUploadsStorage = SJB_Array::unsetValueByPath($tmpUploadsStorage, "{$token}");
				// remove token from tokens list
				$origTokensStorage = SJB_Array::unsetValueByPath($origTokensStorage, $token);
			}
		}
		unset($tokensStorage);

		SJB_Session::setValue('tokens', $origTokensStorage);
		SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);
	}
	
	private function validationManager($fieldInfo, $tp, $uploadedFieldId, $complex = null)
	{
		// will use tmp_uploads_storage in $_SESSION to storage file info
		$uniqueStorageId    = SJB_Session::getSessionId();
		$this->fileUniqueId = $uniqueStorageId . "_" . $uploadedFieldId . "_tmp";
		
		// delete uniquie value
		SJB_UploadFileManager::deleteUploadedFileByID($this->fileUniqueId);

		$this->property = new SJB_ObjectProperty($fieldInfo);
		$this->property->setValue('');
		
		if ($complex) {
			$this->property->setComplexParent($complex['parentField']);
			$this->property->setComplexEnum($complex['complexStep']);
			$fileNamePath = "{$complex['parentField']}/name/{$complex['subFieldId']}/{$complex['complexStep']}";
			$fileSizePath = "{$complex['parentField']}/size/{$complex['subFieldId']}/{$complex['complexStep']}";
		} else {
			$fileNamePath = $uploadedFieldId . '/name';
			$fileSizePath = $uploadedFieldId . '/size';
		}

		$fileName = SJB_Array::getPath($_FILES, $fileNamePath);
		if (!$fileName) {
			$validation = 'UPLOAD_ERR_INI_SIZE';
		} else {
			$uploadedFilesize = SJB_Array::getPath($_FILES, $fileSizePath);
			$filesizeInfo = SJB_HelperFunctions::getFileSizeAndSizeToken($uploadedFilesize);
			$tp->assign(array(
					'filesize'   => $filesizeInfo['filesize'],
					'size_token' => $filesizeInfo['size_token']
				)
			);
			
			$validation = $this->property->isValid();
		}
		
		$this->propertyValue = $this->property->getValue();
		
		if ($validation !== true) {
			$this->errors[$validation] = 1;

			if (!$complex) {
				$tp->assign(array(
					'id' 	=> $uploadedFieldId,
					'value'	=> array(
						'file_url'        => SJB_Array::get($this->propertyValue, 'file_url'),
						'file_name'       => SJB_Array::get($this->propertyValue, 'file_name'),
						'saved_file_name' => SJB_Array::get($this->propertyValue, 'saved_file_name'),
						'file_id'         => $this->fileUniqueId,
					),
				));
			}
		}

		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		
		return $validation === true;
	}

	private function isOwner($listingSid)
	{
		$ownerSid       = SJB_ListingManager::getUserSIDByListingSID($listingSid);
		$currentUserSid = SJB_UserManager::getCurrentUserSID();
		
		if ($ownerSid != $currentUserSid
				&& !SJB_Admin::admin_authed()
				&& !SJB_SubAdmin::admin_authed()) {
			return false;
		}
		
		return true;
	}
}
