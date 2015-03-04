<?php

class SJB_Classifieds_EditListing extends SJB_Function
{
	public function execute()
	{
		$formToken = SJB_Request::getVar('form_token');

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('form_token', $formToken);

		$post_max_size_orig = ini_get('post_max_size');
		$server_content_length = isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : null;

		// get post_max_size in bytes
		$val = trim($post_max_size_orig);
		$tmp = substr($val, strlen($val) - 1);
		$tmp = strtolower($tmp);
		switch ($tmp) {
			case 'g':
				$val *= 1024;
				break;
			case 'm':
				$val *= 1024;
				break;
			case 'k':
				$val *= 1024;
				break;
		}
		$post_max_size = $val;

		$errors = array();
		if (SJB_Request::getVar('from-preview', false, 'POST') && !SJB_Request::getVar('action_add', false, 'POST')) {
			$listingId = SJB_Request::getVar('listing_id', null, 'GET', 'int');
			$previewListingId = SJB_Session::getValue('preview_listing_sid');
			if ($previewListingId && SJB_ListingManager::isListingExists($previewListingId)) {
				$listingId = $previewListingId;
			}
		} else {
			$listingId = SJB_Request::getVar('listing_id', null, 'default', 'int');
		}
		$template = SJB_Request::getVar('edit_template', 'edit_listing.tpl');
		$filename = SJB_Request::getVar('filename', false);
		if ($filename) {
			SJB_UploadFileManager::openFile($filename, $listingId);
			// if file not found - set error here
			$errors['NO_SUCH_FILE'] = true;
		}

		if (empty($_POST) && ($server_content_length > $post_max_size)) {
			$errors['MAX_FILE_SIZE_EXCEEDED'] = 1;
			$listingId = SJB_Request::getVar('listing_id', null, 'GET', 'int');
			$tp->assign('post_max_size', $post_max_size_orig);
		}

		$current_user = SJB_UserManager::getCurrentUser();
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		// for listing preview
		$formSubmittedFromPreview = false;

		if (empty($listingInfo)) {
			$listingId = SJB_Session::getValue('preview_listing_sid');
			$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);

			if (!empty($listingInfo)) {
				// if on preview page 'POST' button was pressed
				$formSubmittedFromPreview = SJB_Request::getVar('action_add', false, 'POST') && SJB_Request::getVar('from-preview', false, 'POST');
				if ($formSubmittedFromPreview) { 
					$listing = new SJB_Listing($listingInfo, $listingInfo['listing_type_sid']);
					$properties = $listing->getProperties();
					foreach ($properties as $fieldID => $property) {
						switch ($property->getType()) {
							case 'date':
								if (!empty($listingInfo[$fieldID])) {
									$listingInfo[$fieldID] = SJB_I18N::getInstance()->getDate($listingInfo[$fieldID] );
								}
								break;
							case 'complex':
								$complex = $property->type->complex;
								$complexProperties = $complex->getProperties();
								foreach ($complexProperties as $complexfieldID => $complexProperty) {
									if ($complexProperty->getType() == 'date') {
										$values = $complexProperty->getValue();
										foreach ($values as $index => $value) {
											if (!empty($listingInfo[$fieldID][$complexfieldID][$index])) {
												$listingInfo[$fieldID][$complexfieldID][$index] = SJB_I18N::getInstance()->getDate($listingInfo[$fieldID][$complexfieldID][$index]);
											}
										}	
									}
								}
								break;
						}
					}
				}
			}
			else {
				$listingId = null;
				SJB_Session::unsetValue('preview_listing_sid');
			}
		}
		// if preview button was pressed
		$isPreviewListingRequested = SJB_Request::getVar('preview_listing', false, 'POST');

		if (SJB_UserManager::isUserLoggedIn()) {
			if ($listingInfo['user_sid'] != $current_user->getID()) {
				$errors['NOT_OWNER_OF_LISTING'] = $listingId;
			} elseif (!is_null($listingInfo)) {
				$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listingInfo['listing_type_sid']);
				$form_is_submitted = (SJB_Request::getVar('action', '') == 'save_info' || SJB_Request::getVar('action', '') == 'add') || $isPreviewListingRequested || $formSubmittedFromPreview;

				if (!$form_is_submitted && !SJB_Request::getVar('from-preview', false, 'POST')) {
					SJB_Session::unsetValue('previewListingId');
					SJB_Session::unsetValue('preview_listing_sid_or');
				}
				
				// fill listing from an array of social data if allowed
				$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($listingInfo['listing_type_sid']);
				$listingTypeID = $listing_type_info['id'];
				$aAutoFillData = array('formSubmitted' => $form_is_submitted, 'listingTypeID' => $listingTypeID);
				SJB_Event::dispatch('SocialSynchronization', $aAutoFillData);

				$listingInfo = array_merge($listingInfo, $_REQUEST);
				$listing = new SJB_Listing($listingInfo, $listingInfo['listing_type_sid']);
				$listing->deleteProperty('ListingLogo');
				$listing->deleteProperty('featured');
				$listing->deleteProperty('priority');
				$listing->deleteProperty('reject_reason');
				$listing->deleteProperty('status');

				$list_emp_ids = SJB_Request::getVar('list_emp_ids');
				$listing->setSID($listingId);

				$screening_questionnaires = SJB_ScreeningQuestionnaires::getList($current_user->getSID());
				if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires') && $screening_questionnaires) {
					$value = SJB_Request::getVar('screening_questionnaire');
					$value = $value ? $value : isset($listingInfo['screening_questionnaire']) ? $listingInfo['screening_questionnaire'] : '';
					$listing->addProperty(
						array('id' => 'screening_questionnaire',
							'type' => 'list',
							'caption' => 'Screening Questionnaire',
							'value' => $value,
							'list_values' => SJB_ScreeningQuestionnaires::getListSIDsAndCaptions($current_user->getSID()),
							'is_system' => true));
				}
				else {
					$listing->deleteProperty('screening_questionnaire');
				}
				//--->CLT-2637
				$properties = $listing->getProperties();
				$listing_fields_by_page = array();
				foreach ($pages as $page) {
					$listing_fields_by_page = array_merge(SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']), $listing_fields_by_page);
				}
				foreach ($properties as $property) {
					if (!in_array($property->getID(), array_keys($listing_fields_by_page))){
						$listing->deleteProperty($property->getID());
					}
				}
				//--->CLT-2637

				// if user is not registered using linkedin , delete linkedin sync property, also if sync is turned off in admin part
				$aAutoFillData = array('oListing' => &$listing, 'userSID' => $current_user->getSID(), 'listingTypeID' => $listingTypeID, 'listing_info' => $listingInfo);
				SJB_Event::dispatch('SocialSynchronizationFields', $aAutoFillData);

				$listing_edit_form = new SJB_Form($listing);
				$listing_edit_form->registerTags($tp);
				$extraInfo = $listingInfo['product_info'];
				if ($extraInfo) {
					$extraInfo = unserialize($extraInfo);
					$numberOfPictures = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
					$listingSidForPictures = SJB_Session::getValue('preview_listing_sid_or') ? SJB_Session::getValue('preview_listing_sid_or') : $listingId;
					$tp->assign('pic_limit', $numberOfPictures);
					$tp->assign('listingSidForPictures', $listingSidForPictures);
				}

				if ($form_is_submitted) {
					$listing->addProperty(
						array('id' => 'access_list',
							'type' => 'multilist',
							'value' => SJB_Request::getVar('list_emp_ids'),
							'is_system' => true,
						)
					);
				}
				$field_errors = array();

				if ($form_is_submitted && ($formSubmittedFromPreview || $listing_edit_form->isDataValid($field_errors))) {

					$or_listing_id = SJB_Session::getValue('preview_listing_sid_or');
					/* preview listing */

					if ($isPreviewListingRequested && SJB_Session::getValue('preview_listing_sid') != $listing->getSID()) {
						SJB_Session::setValue('preview_listing_sid_or', $listing->getSID());
						$listing->setSID(null);
					}
					elseif (!$isPreviewListingRequested && SJB_Session::getValue('preview_listing_sid') == $listing->getSID() && $or_listing_id && $or_listing_id != $listingId) {
						$listing->setSID($or_listing_id);
					}

					if ($isPreviewListingRequested) {
						$listing->addProperty(
							array('id' => 'preview',
								'type' => 'integer',
								'value' => 1,
								'is_system' => true));
					} else {
						$listing->addProperty(
							array('id' => 'complete',
								'type' => 'integer',
								'value' => 1,
								'is_system' => true));
					}

					if ($isPreviewListingRequested) {
						$listing->product_info = $extraInfo;
						if (SJB_Session::getValue('previewListingId')) {
							$listing->setSID(SJB_Session::getValue('previewListingId'));
						}
					} else {
						SJB_BrowseDBManager::deleteListings($listing->getID());
					}
					
					$listingSidsForCopy = array(
						'filesFrom'    => $listingId,
						'picturesFrom' => $isPreviewListingRequested && (!$or_listing_id || $or_listing_id === $listingId) ? $listingId : null
					);
					SJB_ListingManager::saveListing($listing, $listingSidsForCopy);
					
					if (!$isPreviewListingRequested && SJB_Session::getValue('preview_listing_sid') == $listingId && $or_listing_id && $or_listing_id != $listingId) {
						SJB_Session::unsetValue('preview_listing_sid');
						SJB_ListingManager::deleteListingBySID($listingId);
					}
					
					$listingInfo = SJB_ListingManager::getListingInfoBySID($listing->getSID());
					if ($listingInfo['active']) {
						SJB_ListingManager::activateListingKeywordsBySID($listing->getSID());
						SJB_BrowseDBManager::addListings($listing->getID());
					}

					// >>> SJB-1197
					// SET VALUES FROM TEMPORARY SESSION STORAGE
					$formToken          = SJB_Request::getVar('form_token');
					$sessionFileStorage = SJB_Session::getValue('tmp_uploads_storage');
					$tempFieldsData     = SJB_Array::getPath($sessionFileStorage, $formToken);

					if (is_array($tempFieldsData)) {
						foreach ($tempFieldsData as $fieldId => $fieldData) {

							$isComplex = false;
							if (strpos($fieldId, ':') !== false) {
								$isComplex = true;
							}

							$tmpUploadedFileId = $fieldData['file_id'];
							// rename it to real listing field value
							$newFileId = $fieldId . "_" . $listing->getSID();
							SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` =?s", $newFileId, $tmpUploadedFileId);

							if ($isComplex) {
								list($parentField, $subField, $complexStep) = explode(':', $fieldId);

								$parentProp  = $listing->getProperty($parentField);
								$parentValue = $parentProp->getValue();

								// look for complex property with current $fieldID and set it to new value of property
								if (!empty($parentValue)) {
									foreach ($parentValue as $id => $value) {
										if ($id == $subField) {
											$parentValue[$id][$complexStep] = $newFileId;
										}
									}
									$listing->setPropertyValue($parentField, $parentValue);
								}
							} else {
								$listing->setPropertyValue($fieldId, $newFileId);
							}
						}

						SJB_ListingManager::saveListing($listing);

						// recreate form object for saved listing
						// it fix display of complex file fields
						$listing = SJB_ListingManager::getObjectBySID($listing->getSID());
						$listing->deleteProperty('featured');
						$listing->deleteProperty('priority');
						$listing->deleteProperty('reject_reason');
						$listing->deleteProperty('status');

						$listing_edit_form = new SJB_Form($listing);
						$listing_edit_form->registerTags($tp);
					}
					// <<< SJB-1197

					if ($isPreviewListingRequested) {
						SJB_Session::setValue('previewListingId', $listing->getSID());
					}
			
					/* preview listing */
					if ($isPreviewListingRequested) {
						$listing->setUserSID($current_user->getSID());
						SJB_Session::setValue('preview_listing_sid', $listing->getSID());
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/' . strtolower($listingTypeID) . '-preview/' . $listing->getSID() . '/');
					} else { /* normal */
						$listingSid = $listing->getSID();
						SJB_Event::dispatch('listingEdited', $listingSid);
						$tp->assign('display_preview', 1);
						SJB_Session::unsetValue('preview_listing_sid');
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-' . strtolower($listingTypeID) . '-details/' . $listing->getSID() . '/');
					}
				}
				$listing->deleteProperty('access_list');
				$tp->assign('form_is_submitted', $form_is_submitted);

				$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);

				$form_fields = $listing_edit_form->getFormFieldsInfo();
				$listing_fields_by_page = array();
				foreach ($pages as $page) {
					$listing_fields_by_page[$page['page_name']] = SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']);
					foreach (array_keys($listing_fields_by_page[$page['page_name']]) as $field) {
						if (!$listing->propertyIsSet($field))
							unset($listing_fields_by_page[$page['page_name']][$field]);
					}
				}

				// delete sync fields from posting pages that are not in array $form_fields
				$aAutoFillData = array('listing_fields_by_page' => &$listing_fields_by_page, 'pages' => &$pages, 'form_fields' => $form_fields);
				SJB_Event::dispatch('SocialSynchronizationFieldsOnPostingPages', $aAutoFillData);

				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$tp->assign(
					'METADATA', array(
						'listing' => $metaDataProvider->getMetaData($listing_structure['METADATA']),
						'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
					)
				);
				if (!isset($listing_structure['access_type']))
					$listing_structure['access_type'] = 'everyone';

				$listing_access_list = SJB_ListingManager::getListingAccessList($listingId, $listing->getPropertyValue('access_type'));
				$tp->assign('contract_id', $listingInfo['contract_id']);
				$tp->assign('extraInfo', $extraInfo);
				$tp->assign('listing', $listing_structure);
				$tp->assign('pages', $listing_fields_by_page);
				$tp->assign('countPages', count($listing_fields_by_page));
				$tp->assign('field_errors', $field_errors);
				$tp->assign('listing_access_list', $listing_access_list);
				$tp->assign('listingTypeID', $listingTypeID);
				$tp->assign('expired', SJB_ListingManager::getIfListingHasExpiredBySID($listing->getSID()));

				// only for Resume listing types
				$aAutoFillData = array('tp' => &$tp, 'listingTypeID' => $listingTypeID, 'userSID' => $current_user->getSID());
				SJB_Event::dispatch('SocialSynchronizationForm', $aAutoFillData);
			}
		} else {
			$errors['NOT_LOGGED_IN'] = 1;
		}

		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}
