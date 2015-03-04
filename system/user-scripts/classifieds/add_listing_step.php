<?php

class SJB_Classifieds_AddListingStep extends SJB_Classifieds_AddListing
{
	const PREVIEW_LISTING_SID = 'preview_listing_sid_for_add';

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('input_template', 'input_form.tpl');
		$error = null;

		$listingTypeID = SJB_Request::getVar('listing_type_id', false);
		$passed_parameters_via_uri = SJB_Request::getVar('passed_parameters_via_uri', false);
		$pageID = false;
		if ($passed_parameters_via_uri) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listingTypeID = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : $listingTypeID;
			$pageID = isset($passed_parameters_via_uri[1]) ? $passed_parameters_via_uri[1] : false;
			$listing_id = isset($passed_parameters_via_uri[2]) ? $passed_parameters_via_uri[2] : false;
		}

		if (SJB_UserManager::isUserLoggedIn()) {
			$post_max_size_orig = ini_get('post_max_size');
			$server_content_length = isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : null;
			$fromPreview = SJB_Request::getVar('from-preview', false);

			// get post_max_size in bytes
			$val = trim($post_max_size_orig);
			$tmp = substr($val, strlen($val) - 1);
			$tmp = strtolower($tmp);
			/* if ini value is K - then multiply to 1024
				 * if ini value is M - then multiply twice: in case 'm', and case 'k'
				 * if ini value is G - then multiply tree times: in 'g', 'm', 'k'
				 * out value - in bytes!
				 */
			switch ($tmp) {
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			$post_max_size = $val;

			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				$file = SJB_UploadFileManager::openFile($filename, $listing_id);
				$errors['NO_SUCH_FILE'] = true;
			}

			if (empty($_POST) && ($server_content_length > $post_max_size)) {
				$errors['MAX_FILE_SIZE_EXCEEDED'] = 1;
				$tp->assign('post_max_size', $post_max_size_orig);
			}

			$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
			$currentUser = SJB_UserManager::getCurrentUser();
			$contractID = $listingInfo['contract_id'];
			if ($contractID == 0) {
				$extraInfo = unserialize($listingInfo['product_info']);
				$productSID = $extraInfo['product_sid'];
			} else {
				$contract = new SJB_Contract(array('contract_id' => $contractID));
				$extraInfo = $contract->extra_info;
			}
			if ($listingInfo['user_sid'] != SJB_UserManager::getCurrentUserSID()) {
				$errors['NOT_OWNER_OF_LISTING'] = $listing_id;
			}
			else {
				$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
				$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing_type_sid);

				if (!$pageID)
					$pageID = $pages[0]['page_id'];
				$pageSID = SJB_PostingPagesManager::getPostingPageSIDByID($pageID, $listing_type_sid);
				$isPageLast = SJB_PostingPagesManager::isLastPageByID($pageSID, $listing_type_sid);

				// preview listing
				$isPreviewListingRequested = SJB_Request::getVar('preview_listing', false, 'POST');

				$form_submitted = isset($_REQUEST['action_add']) || isset($_REQUEST['action_add_pictures']) || $isPreviewListingRequested;

				// fill listing from an array of social data if allowed
				$aAutoFillData = array('formSubmitted' => &$form_submitted, 'listingTypeID' => &$listingTypeID);
				SJB_Event::dispatch('SocialSynchronization', $aAutoFillData);

				$listingInfo = array_merge($listingInfo, $_REQUEST);
				$listing = new SJB_Listing($listingInfo, $listing_type_sid, $pageSID);
				if ($fromPreview) {
					if ($form_submitted) {
						$properties = $listing->getProperties();
						foreach ($properties as $fieldID => $property) {
							switch ($property->getType()) {
								case 'date':
									if (!empty($listing_info[$fieldID])) {
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
						$listing = new SJB_Listing($listingInfo, $listing_type_sid, $pageSID);
					}
				}
				$previousComplexFields = $this->processComplexFields($listing, $listingInfo);
				$listing->deleteProperty('featured');
				$listing->deleteProperty('priority');
				$listing->deleteProperty('status');
				$listing->deleteProperty('reject_reason');
				$listing->deleteProperty('ListingLogo');
				$listing->setSID($listing_id);

				$access_type = $listing->getProperty('access_type');
				if ($form_submitted && !empty($access_type)) {
					$listing->addProperty(
						array('id' => 'access_list',
							'type' => 'multilist',
							'value' => SJB_Request::getVar('list_emp_ids'),
							'is_system' => true));
				}

				$screening_questionnaires = SJB_ScreeningQuestionnaires::getList($currentUser->getSID());
				if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires') && $screening_questionnaires) {
					$issetQuestionnairyField = $listing->getProperty('screening_questionnaire');
					if ($issetQuestionnairyField) {
						$value = SJB_Request::getVar('screening_questionnaire');
						$value = $value ? $value : isset($listingInfo['screening_questionnaire']) ? $listingInfo['screening_questionnaire'] : '';
						$listing->addProperty(
							array('id' => 'screening_questionnaire',
								'type' => 'list',
								'caption' => 'Screening Questionnaire',
								'value' => $value,
								'list_values' => SJB_ScreeningQuestionnaires::getListSIDsAndCaptions($currentUser->getSID()),
								'is_system' => true));
					}
				}
				else {
					$listing->deleteProperty('screening_questionnaire');
				}

				/* social plugin
						 * "synchronization"
						 * if user is not registered using linkedin , delete linkedin sync property
						 * also deletes it if sync is turned off in admin part
						 */
				if ($pages[0]['page_id'] == $pageID) {
					$aAutoFillData = array('oListing' => &$listing, 'userSID' => $currentUser->getSID(), 'listingTypeID' => $listingTypeID, 'listing_info' => $listingInfo);
					SJB_Event::dispatch('SocialSynchronizationFields', $aAutoFillData);
				}

				$add_listing_form = new SJB_Form($listing);
				$add_listing_form->registerTags($tp);

				$field_errors = array();

				if ($form_submitted && (SJB_Session::getValue(self::PREVIEW_LISTING_SID) == $listing_id || $add_listing_form->isDataValid($field_errors))) {
					/* delete temp preview listing sid */
					SJB_Session::unsetValue(self::PREVIEW_LISTING_SID);

					if ($isPageLast) {
						$listing->addProperty(
							array('id' => 'complete',
								'type' => 'integer',
								'value' => 1,
								'is_system' => true));
					}
					$listing->setUserSID($currentUser->getSID());

					if (empty($access_type->value))
						$listing->setPropertyValue('access_type', 'everyone');

					if (isset($_SESSION['tmp_file_storage'])) {
						foreach ($_SESSION['tmp_file_storage'] as $k => $v) {
							SJB_DB::query('UPDATE `listings_pictures` SET `listing_sid` = ?n WHERE `picture_saved_name` = ?s', $listing->getSID(), $v['picture_saved_name']);
							SJB_DB::query('UPDATE `listings_pictures` SET `listing_sid` = ?n WHERE `thumb_saved_name` = ?s', $listing->getSID(), $v['thumb_saved_name']);
						}
						SJB_Session::unsetValue('tmp_file_storage');
					}



					// >>> SJB-1197
					// check temporary uploaded storage for listing uploads and assign it to saved listing
					$formToken           = SJB_Request::getVar('form_token');
					$sessionFilesStorage = SJB_Session::getValue('tmp_uploads_storage');
					$uploadedFields      = SJB_Array::getPath($sessionFilesStorage, $formToken);

					if (!empty($uploadedFields)) {
						foreach ($uploadedFields as $fieldId => $fieldValue) {
							// get field of listing
							$isComplex = false;
							if (strpos($fieldId, ':') !== false) {
								$isComplex = true;
							}

							$tmpUploadedFileId = $fieldValue['file_id'];
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

							// unset value from session temporary storage
							$sessionFilesStorage = SJB_Array::unsetValueByPath($sessionFilesStorage, "{$formToken}/{$fieldId}");
						}

						//and remove token key from temporary storage
						$sessionFilesStorage = SJB_Array::unsetValueByPath($sessionFilesStorage, "{$formToken}");
						SJB_Session::setValue('tmp_uploads_storage', $sessionFilesStorage);


					}
					// <<< SJB-1197

					SJB_ListingManager::saveListing($listing);
					foreach ($previousComplexFields as $propertyId) {
						$listing->deleteProperty($propertyId);
					}

					if ($isPageLast && !$isPreviewListingRequested) {
						$listingSID = $listing->getSID();
						$listing = SJB_ListingManager::getObjectBySID($listingSID);
						$listing->setSID($listingSID);

						$keywords = $listing->getKeywords();
						SJB_ListingManager::updateKeywords($keywords, $listing->getSID());

						// Start Event
						$listingSid = $listing->getSID();
						SJB_Event::dispatch('listingSaved', $listingSid);

						// is listing featured by default
						if ($extraInfo['featured'])
							SJB_ListingManager::makeFeaturedBySID($listing->getSID());
						if ($extraInfo['priority'])
							SJB_ListingManager::makePriorityBySID($listing->getSID());

						if ($contractID) {
							if (SJB_ListingManager::activateListingBySID($listing->getSID())) {
								SJB_Notifications::sendUserListingActivatedLetter($listing, $listing->getUserSID());
							}
							// notify administrator
							SJB_AdminNotifications::sendAdminListingAddedLetter($listing);

							if (isset($_REQUEST['action_add_pictures'])) {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-pictures/?listing_id=" . $listing->getSID());
							} else {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-' . strtolower($listingTypeID) . '/?listing_id='. $listing->getSID());
							}
						} else {
							SJB_ListingManager::unmakeCheckoutedBySID($listing->getSID());
							$this->proceedToCheckout($currentUser->getSID(), $productSID);
						}
					}
					elseif ($isPageLast && $isPreviewListingRequested) { // for listing preview
						SJB_Session::setValue(self::PREVIEW_LISTING_SID, $listing->getSID());
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/' . strtolower($listingTypeID) . '-preview/' . $listing->getSID() . '/');
					}
					else { // listing steps (pages)
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/add-listing/{$listingTypeID}/" . SJB_PostingPagesManager::getNextPage($pageSID) . '/' . $listing->getSID());
					}
				} else {
					foreach ($previousComplexFields as $propertyId) {
						$listing->deleteProperty($propertyId);
					}
					$listing->deleteProperty('access_list');
					$listing->deleteProperty('contract_id');
					$add_listing_form = new SJB_Form($listing);
					if (SJB_Request::get('action_add') == 'Next') {
						$add_listing_form->setUseDefaultValues();
					}
					if ($form_submitted)
						$add_listing_form->isDataValid($field_errors);
					$add_listing_form->registerTags($tp);
					$form_fields = $add_listing_form->getFormFieldsInfo();
					$employers_list = SJB_Request::getVar('list_emp_ids', false);
					$employers = array();
					if (is_array($employers_list)) {
						foreach ($employers_list as $emp) {
							$currEmp = SJB_UserManager::getUserInfoBySID($emp);
							$employers[] = array('user_id' => $emp, 'value' => $currEmp['CompanyName']);
						}
						sort($employers);
					}
					else {
						$access_type = $listing->getPropertyValue('access_type');
						$employers = SJB_ListingManager::getListingAccessList($listing_id, $access_type);
					}

					$numberOfPictures = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
					$tp->assign('pic_limit', $numberOfPictures);
					$tp->assign('listing_sid', $listing_id);
					$tp->assign('listing_id', $listing_id);
					$tp->assign('listingSID', $listing->getSID());
					$tp->assign('listing_access_list', $employers);
					$tp->assign('listingTypeID', $listingTypeID);
					$tp->assign('contract_id', $contractID);
					$tp->assign('field_errors', $field_errors);
					$tp->assign('form_fields', $form_fields);
					$tp->assign("extraInfo", $extraInfo);
					$tp->assign('pages', $pages);
					$tp->assign('pageSID', $pageSID);
					$tp->assign('currentPage', SJB_PostingPagesManager::getPageInfoBySID($pageSID));
					$tp->assign('isPageLast', $isPageLast);
					$tp->assign('nextPage', SJB_PostingPagesManager::getNextPage($pageSID));
					$tp->assign('prevPage', SJB_PostingPagesManager::getPrevPage($pageSID));

					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign(
						'METADATA', array(
							'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields))
					);

					// social plugin  only for Resume listing types
					$aAutoFillData = array('tp' => &$tp, 'listingTypeID' => $listingTypeID, 'userSID' => $currentUser->getSID());
					SJB_Event::dispatch('SocialSynchronizationForm', $aAutoFillData);
					SJB_Session::unsetValue(self::PREVIEW_LISTING_SID);

					$tp->display($template);
				}
			}
		}
		else {
			$tp->assign('listingTypeID', $listingTypeID);
			$tp->assign('error', 'NOT_LOGGED_IN');
			$tp->display('add_listing_error.tpl');
		}
	}

	private function processComplexFields(SJB_Listing $listing, $listingInfo)
	{
		if (!empty($listingInfo['complex'])) {
			$i18n = SJB_I18N::getInstance();
			$listingComplex = unserialize($listingInfo['complex']);
			$complexFieldsIds = array();
			foreach ($listingComplex as $complexId => $complexValues) {
				if (!$listing->getProperty($complexId)) {
					$complexFieldsIds[] = $complexId;
					$complexInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($complexId);
					$complexInfo['value'] = $complexValues;
					foreach ($complexValues as $fieldId => $fieldValue) {
						$fieldSid = SJB_ListingFieldDBManager::getComplexFieldSIDbyID($fieldId);
						$fieldInfo = SJB_ListingFieldDBManager::getListingComplexFieldInfoBySID($fieldSid);
						$complexInfo['fields'][] = $fieldInfo;
						
						foreach ($fieldValue as $key => $value) {
							if ($value != null) {
								switch ($fieldInfo['type']) {
									case 'int':
									case 'integer':
										$complexInfo['value'][$fieldId][$key] = $i18n->getInt($value);
										break;
									case 'float':
										$complexInfo['value'][$fieldId][$key] = $i18n->getFloat($value);
										break;
									case 'date':
										$complexInfo['value'][$fieldId][$key] = $i18n->getDate($value);
										break;
								}
							}
						}
					}
					
					$listing->addProperty($complexInfo);
				}
			}
			return $complexFieldsIds;
		}
		return array();
	}
}
