<?php

class SJB_Classifieds_CopyListing extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('subuser_add_listings');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$current_user = SJB_UserManager::getCurrentUser();
		$currentUserInfo = SJB_UserManager::getCurrentUserInfo();
		$tp->assign('current_user', $currentUserInfo);

		$errors = array();
		$error = '';
		$listing_id = SJB_Request::getVar('listing_id', null, 'default', 'int');

		if (SJB_UserGroupManager::getUserGroupIDBySID($current_user->user_group_sid) == 'Employer')
			$template = SJB_Request::getVar('input_template', 'copy_listing.tpl');
		else
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-listings/Job/');

		//getting $tmp_listing_id from request
		$tmp_listing_id_from_request = SJB_Request::getVar('tmp_listing_id', false, 'default', 'int');

		$listing_info = SJB_ListingManager::getListingInfoBySID($listing_id);
		$listing_type_id = SJB_ListingTypeManager::getListingTypeIDBySID($listing_info['listing_type_sid']);
		if ($productsInfo = $this->canCurrentUserAddListing($error, $listing_type_id)) {

			$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing_info['listing_type_sid']);

			if (!empty($tmp_listing_id_from_request))
				$tmp_listing_sid = $tmp_listing_id_from_request;
			elseif (!$tmp_listing_id_from_request)
				$tmp_listing_sid = time();

			$gallery = new SJB_ListingGallery();
			$gallery->setListingSID($listing_info['sid']);
			$pictures_info = $gallery->getPicturesInfo();
			$gallery->setListingSID($tmp_listing_sid);
			$pictures_info_new = $gallery->getPicturesInfo();
			//reuploading pictures
			if (!$pictures_info_new) {
				foreach ($pictures_info as $v) {
					if (!$gallery->uploadImage($v['picture_url'], $v['caption']))
						$field_errors['Picture'] = $gallery->getError();
				}
			}

			$contractID = SJB_Request::getVar('contract_id', false, 'default', 'int');

			if ($contractID) {
				$contract = new SJB_Contract(array('contract_id' => $contractID));
			} elseif (count($productsInfo) == 1) {
				$productInfo = array_pop($productsInfo);
				$contractID = $productInfo['contract_id'];
				$contract = new SJB_Contract(array('contract_id' => $contractID));
			}
			else {
				$tp->assign('listing_id', $listing_id);
				$tp->assign("products_info", $productsInfo);
				$tp->assign("listing_type_id", $listing_type_id);
				$tp->display("listing_product_choice.tpl");
			}

			if ($contractID) {
				$tp->assign('tmp_listing_id', $tmp_listing_sid);
				$extraInfo = $contract->extra_info;
				$numberOfPictures = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
				$tp->assign("pic_limit", $numberOfPictures);
				$tp->assign('contractID', $contractID);
				if ($listing_info['user_sid'] != SJB_UserManager::getCurrentUserSID())
					$errors['NOT_OWNER_OF_LISTING'] = $listing_id;
				elseif (!is_null($listing_info)) {
					$listing_info = array_merge($listing_info, $_REQUEST);
					$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
					$listing->deleteProperty('featured');
					$listing->deleteProperty('priority');
					$listing->deleteProperty('status');
					$listing->deleteProperty('reject_reason');
					$listing->setSID($listing_id);
					$screening_questionnaires = SJB_ScreeningQuestionnaires::getList($current_user->getSID());
					if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires') && $screening_questionnaires) {
						$issetQuestionnairyField = $listing->getProperty('screening_questionnaire');
						if ($issetQuestionnairyField) {
							$value = SJB_Request::getVar('screening_questionnaire');
							$value = $value ? $value : isset($listing_info['screening_questionnaire']) ? $listing_info['screening_questionnaire'] : '';
							$listing->addProperty(
								array('id' => 'screening_questionnaire',
									'type' => 'list',
									'caption' => 'Screening Questionnaire',
									'value' => $value,
									'list_values' => SJB_ScreeningQuestionnaires::getListSIDsAndCaptions($current_user->getSID()),
									'is_system' => true));
						}
					}
					else {
						$listing->deleteProperty('screening_questionnaire');
					}
					$listing_edit_form = new SJB_Form($listing);
					$listing_edit_form->registerTags($tp);
					$extraInfo = $listing_info['product_info'];
					if ($extraInfo) {
						$extraInfo = unserialize($extraInfo);
						$numberOfPictures = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
						$tp->assign("pic_limit", $numberOfPictures);
					}
					$form_is_submitted = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save_info' || isset($_REQUEST['action']) && $_REQUEST['action'] == 'add');
					$listing->addProperty(
						array('id' => 'contract_id',
							'type' => 'id',
							'value' => $contractID,
							'is_system' => true));

					$delete = SJB_Request::getVar('action', '') == 'delete';
					$field_errors = null;
					if ($delete && isset($_REQUEST['field_id'])) {
						$field_id = $_REQUEST['field_id'];
						$listing->details->properties[$field_id]->type->property_info['value'] = null;
					}
					elseif ($form_is_submitted && $listing_edit_form->isDataValid($field_errors)) {
						$listing->addProperty(
							array('id' => 'complete',
								'type' => 'integer',
								'value' => 1,
								'is_system' => true));
						$listing->setUserSID($current_user->getSID());
						$extraInfo = $contract->extra_info;
						$listing->setProductInfo($extraInfo);
						$listing->sid = null;
						if (!empty($listing_info['subuser_sid'])) {
							$listing->addSubuserProperty($listing_info['subuser_sid']);
						}
						$listingSidsForCopy = array(
							'filesFrom'    => $listing_id,
							'picturesFrom' => $tmp_listing_sid
						);
						SJB_ListingManager::saveListing($listing, $listingSidsForCopy);

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
								// clean temporary field storage
								$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}/{$fieldId}");
							}

							//and remove token key from temporary storage
							$sessionFileStorage = SJB_Array::unsetValueByPath($sessionFileStorage, "{$formToken}");

							// clear temporary data in session storage
							SJB_Session::setValue('tmp_uploads_storage', $sessionFileStorage);

							$listingSidsForCopy = array(
								'filesFrom'    => $listing_id,
								'picturesFrom' => $listing_id
							);
							SJB_ListingManager::saveListing($listing, $listingSidsForCopy);
						}
						// <<< SJB-1197

						SJB_Statistics::addStatistics('addListing', $listing->getListingTypeSID(), $listing->getSID(), false, $extraInfo['featured'], $extraInfo['priority']);
						$contract->incrementPostingsNumber();
						SJB_ProductsManager::incrementPostingsNumber($contract->product_sid);

						// is listing featured by default
						if ($extraInfo['featured'])
							SJB_ListingManager::makeFeaturedBySID($listing->getSID());
						if ($extraInfo['priority'])
							SJB_ListingManager::makePriorityBySID($listing->getSID());

						SJB_ListingManager::activateListingBySID($listing->getSID());

						SJB_AdminNotifications::sendAdminListingAddedLetter($listing);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-' . strtolower($listing_type_id) . '/?listing_id=' . $listing->getSID());
					}
					elseif ($form_is_submitted) {
						$field_id = 'video';
						if (!isset($_REQUEST['video_hidden']) && $listing->getPropertyValue($field_id)) {
							$listing->details->properties[$field_id]->type->property_info['value'] = null;
						}
					}
					$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
					$form_fields = $listing_edit_form->getFormFieldsInfo();
					$listing_fields_by_page = array();
					$countPages = count($pages);
					$i = 1;
					foreach ($pages as $page) {
						$listing_fields_by_page[$page['page_name']] = SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']);
						if ($i == $countPages && isset($form_fields['screening_questionnaire']))
							$listing_fields_by_page[$page['page_name']]['screening_questionnaire'] = $form_fields['screening_questionnaire'];
						foreach (array_keys($listing_fields_by_page[$page['page_name']]) as $field) {
							if (!$listing->propertyIsSet($field))
								unset($listing_fields_by_page[$page['page_name']][$field]);
						}
						$i++;
					}
					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign
					(
						'METADATA',
						array
						(
							'listing' => $metaDataProvider->getMetaData($listing_structure['METADATA']),
							'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
						)
					);
					
					$contract_id = $listing_info['contract_id'];
					$contract = new SJB_Contract(array('contract_id' => $contract_id));
					$tp->assign('contract_id', $contract_id);
					$tp->assign('contract', $contract->extra_info);
					$tp->assign('countPages', count($listing_fields_by_page));
					$tp->assign('copy_listing', 1);
					$tp->assign('tmp_listing_id', $tmp_listing_sid);
					$tp->assign('listing_id', $listing_id);
					$tp->assign('contractID', $contractID);
					$tp->assign('listing', $listing_structure);
					$tp->assign('pages', $listing_fields_by_page);
					$tp->assign('field_errors', $field_errors);
				}
				$tp->assign('errors', $errors);
				$tp->display($template);
			}
		}
		else {
			$listing_type_id = isset($listing_info['listing_type_sid']) ? $listing_info['listing_type_sid'] : false;
			if ($error == 'NO_CONTRACT') {
				if ($_GET) {
					$getParam = '?';
					foreach ($_GET as $key => $val)
						$getParam .= $key . '=' . $val . '&';
					$getParam = substr($getParam, 0, -1);
				}
				$page = base64_encode(SJB_System::getURI() . $getParam);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-products/?page=' . $page);
			}
			$tp->assign('clone_job', 1);
			$tp->assign('listing_type_id', $listing_type_id);
			$tp->assign('error', $error);
			$tp->display('add_listing_error.tpl');
		}

	}

	private function canCurrentUserAddListing(& $error, $listing_type_id)
	{
		$acl = SJB_Acl::getInstance();
		if (SJB_UserManager::isUserLoggedIn()) {
			$current_user = SJB_UserManager::getCurrentUser();
			if ($current_user->hasContract()) {
				$contracts_id = $current_user->getContractID();
				$contractsSIDs = $contracts_id ? implode(',', $contracts_id) : 0;
				$resultContractInfo = SJB_DB::query("SELECT `id`, `product_sid`, `expired_date`, `number_of_postings` FROM `contracts` WHERE `id` in ({$contractsSIDs}) ORDER BY `expired_date` DESC");
				$planAccess = count($resultContractInfo) > 0 ? true : false;
				if ($planAccess && $acl->isAllowed('post_' . $listing_type_id)) {
					$productsInfo = array();
					$is_contract = false;
					foreach ($resultContractInfo as $contractInfo) {
						if ($acl->isAllowed('post_' . $listing_type_id, $contractInfo['id'], 'contract')) {
							$permissionParam = $acl->getPermissionParams('post_' . $listing_type_id, $contractInfo['id'], 'contract');
							if (empty($permissionParam) || $acl->getPermissionParams('post_' . $listing_type_id, $contractInfo['id'], 'contract') > $contractInfo['number_of_postings']) {
								$product = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
								$productsInfo[$contractInfo['id']]['product_name'] = $product['name'];
								$productsInfo[$contractInfo['id']]['expired_date'] = $contractInfo['expired_date'];
								$productsInfo[$contractInfo['id']]['contract_id'] = $contractInfo['id'];
							}
						}
						$is_contract = true;
					}

					if ($is_contract && count($productsInfo) > 0)
						return $productsInfo;
					else
						$error = 'LISTINGS_NUMBER_LIMIT_EXCEEDED';
				}
				else
					$error = 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING';
			}
			else
				$error = 'NO_CONTRACT';
		}
		else
			$error = 'NOT_LOGGED_IN';
		return false;
	}
}
