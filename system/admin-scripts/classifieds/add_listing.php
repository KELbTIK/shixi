<?php

class SJB_Admin_Classifieds_AddListing extends SJB_Function
{
	public function isAccessible()
	{
		$listingTypeId = SJB_Request::getVar('listing_type_id', null);
		$listingType = !in_array($listingTypeId, array('resume', 'job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel('manage_' . strtolower($listingType));
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listingTypeID = SJB_Request::getVar('listing_type_id', null);
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
		$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listingTypeSID);
		$productSID = SJB_Request::getVar('product_sid', false);
		$editUser = SJB_Request::getVar('edit_user', false);
		$action = SJB_Request::getVar('action', false);

		$username = SJB_Request::getVar('username', false);
		$errors = array();

		if ($username && $userSID = SJB_UserManager::getUserSIDbyUsername($username)) {
			$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
			$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']);
			if (!$productSID) {
				$products = SJB_ProductsManager::getProductsInfoByUserGroupSID($userGroupInfo['sid']); 
				foreach ($products as $key =>$product) {
					if (empty($product['listing_type_sid']) || $product['listing_type_sid'] != $listingTypeSID)
						unset($products[$key]);
				}
				if ($action == 'productVerify')
					$errors['PRODUCT_NOT_SELECTED'] = 1;

				$tp->assign('errors', $errors);
				$tp->assign('username', $username);
				$tp->assign('products', $products);
				$tp->assign('edit_user', $editUser);
				$tp->assign('userSID', $userSID);
				$tp->assign('userGroupInfo', $userGroupInfo);
				$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
				$tp->display('select_product.tpl');
			}
			else {
				$form_submitted = SJB_Request::getVar('action', '') == 'add';
				$tmp_listing_id_from_request = SJB_Request::getVar('listing_id', false, 'default', 'int');
				if (!empty($tmp_listing_id_from_request))
					$tmp_listing_sid = $tmp_listing_id_from_request;
				elseif (!$tmp_listing_id_from_request)
					$tmp_listing_sid = time();
					
				$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
				$extraInfo = is_null($productInfo['serialized_extra_info']) ? null : unserialize($productInfo['serialized_extra_info']);
				if (!empty($extraInfo)) {
					$extraInfo['product_sid'] = $productSID;
				}
				$_REQUEST['featured'] = !empty($_REQUEST['featured'])?$_REQUEST['featured']:$productInfo['featured'];
				$_REQUEST['priority'] = !empty($_REQUEST['priority'])?$_REQUEST['priority']:$productInfo['priority'];
					
				$listing = new SJB_Listing($_REQUEST, $listingTypeSID);
				$properties = $listing->getPropertyList();
				foreach ($properties as $property) {
					$propertyInfo = $listing->getPropertyInfo($property);
					$propertyInfo['user_sid'] = $userSID;
					if ($propertyInfo['type'] == 'location') {
						$child = $listing->getChild($property);
						$childProperties = $child->getPropertyList();
						foreach ($childProperties as $childProperty) {
							$childPropertyInfo = $child->getPropertyInfo($childProperty);
							$childPropertyInfo['user_sid'] = $userSID;
							$child->setPropertyInfo($childProperty, $childPropertyInfo);
						}
					}
					$listing->setPropertyInfo($property, $propertyInfo);
				}
				$listing->deleteProperty('status');
				$listing->deleteProperty('reject_reason');
				$access_type = $listing->getProperty('access_type');
				if ($form_submitted) {
					if (!empty($access_type)) {
						$listing->addProperty(
							array('id' => 'access_list',
								'type' => 'multilist',
								'value' => SJB_Request::getVar("list_emp_ids"),
								'is_system' => true));
					}
				}
				
				$screening_questionnaires = SJB_ScreeningQuestionnaires::getList($userSID);
				if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires') && $screening_questionnaires) {
					$issetQuestionnairyField = $listing->getProperty('screening_questionnaire');
					if ($issetQuestionnairyField) {
						$value = SJB_Request::getVar("screening_questionnaire");
						$listing_info = $_REQUEST;
						$value = $value ? $value : isset($listing_info['screening_questionnaire']) ? $listing_info['screening_questionnaire'] : '';
						$listing->addProperty(
							array('id' => 'screening_questionnaire',
								'type' => 'list',
								'caption' => 'Screening Questionnaire',
								'value' => $value,
								'list_values' => SJB_ScreeningQuestionnaires::getListSIDsAndCaptions($userSID),
								'is_system' => true));
					}
				}
				else 
					$listing->deleteProperty('screening_questionnaire');
				
				if ($listing->getProperty('captcha')) {
					$listing->deleteProperty('captcha');
				}
				
				$add_listing_form = new SJB_Form($listing);
				$add_listing_form->registerTags($tp);

				$field_errors = array();
				if ($form_submitted && $add_listing_form->isDataValid($field_errors)) {
						$listing->addProperty(
							array('id' => 'complete',
								'type' => 'integer',
								'value' => 1,
								'is_system' => true));
						$listing->setUserSID($userSID);
						$listing->setProductInfo($extraInfo);
						if (empty($access_type->value))
							$listing->setPropertyValue('access_type', 'everyone');

						SJB_ListingManager::saveListing($listing);
						SJB_Statistics::addStatistics('addListing', $listing->getListingTypeSID(), $listing->getSID(), false, $_REQUEST['featured'], $_REQUEST['priority'], $userSID);
						
						if (isset($_SESSION['tmp_file_storage'])) {
							foreach ($_SESSION['tmp_file_storage'] as $v) {
								SJB_DB::query("UPDATE `listings_pictures` SET `listing_sid` = ?n WHERE `picture_saved_name` = ?s", $listing->getSID(), $v['picture_saved_name']);
								SJB_DB::query("UPDATE `listings_pictures` SET `listing_sid` = ?n WHERE `thumb_saved_name` = ?s", $listing->getSID(), $v['thumb_saved_name']);
							}
							SJB_Session::unsetValue('tmp_file_storage');
						}
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
	
							SJB_ListingManager::saveListing($listing);
						}

						SJB_ListingManager::activateListingBySID($listing->getSID());
						SJB_ProductsManager::incrementPostingsNumber($productSID);
					
						$listingSid = $listing->getSID();
						SJB_Event::dispatch('listingSaved', $listingSid);
						if ($editUser)
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-user/?user_sid=".$userSID);
						else {
							if ($listingTypeID == 'resume' || $listingTypeID == 'job') {
								$link = "manage-" . strtolower($listingTypeID) . "s";
							} else {
								$link = "manage-" . strtolower($listingTypeID) . "-listings";
							}
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/$link/?action=search&listing_type_sid=" . $listingTypeSID);
						}
				}
				else {
					$listing->deleteProperty('access_list');
					$listing->deleteProperty('contract_id');
					$add_listing_form = new SJB_Form($listing);
					if ($form_submitted)
						$add_listing_form->isDataValid($field_errors);
					$add_listing_form->registerTags($tp);
					
					$form_fields = $add_listing_form->getFormFieldsInfo();
					$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listingTypeSID);
					$formFieldsSorted = array();
					$formFieldsSorted['featured'] = $form_fields['featured'];
					$formFieldsSorted['priority'] = $form_fields['priority'];
					foreach ($pages as $page) {
						$listing_fields = SJB_PostingPagesManager::getAllFieldsByPageSIDForForm($page['sid']);
						foreach (array_keys($listing_fields) as $field) {
							if ($listing->propertyIsSet($field))
								$formFieldsSorted[$field] =  $form_fields[$field];
						}
					}
					$form_fields = $formFieldsSorted;
					//SJB_HelperFunctions::d($form_fields);
					$employers_list = SJB_Request::getVar('list_emp_ids', false);
					$employers = array();
					if (is_array($employers_list)) {
						foreach ($employers_list as $emp) {
							$currEmp = SJB_UserManager::getUserInfoBySID($emp);
							$employers[] = array('user_id' => $emp, 'value' => $currEmp['CompanyName']);
						}
						sort($employers);
					}

					$numberOfPictures = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
					$tp->assign("pic_limit", $numberOfPictures);
					$tp->assign("listing_id", $tmp_listing_sid);
					$tp->assign("listing_access_list", $employers);
					$tp->assign("errors", $field_errors);
					$tp->assign("form_fields", $form_fields);

					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign(
						"METADATA",
						array(
							"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields),
						)
					);
				}

				$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				$tp->assign('edit_user', $editUser);
				$tp->assign('productInfo', $productInfo);
				$tp->assign('username', $username);
				$tp->assign('product_sid', $productSID);
				$tp->assign('userSID', $userSID);
				$tp->assign('userGroupInfo', $userGroupInfo);
				$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
				$tp->display('input_form.tpl');
			}
		}
		else {
			if ($username && !$userSID)
				$errors['USER_NOT_FOUND'] = 1;
			elseif ($action == 'userVerify')
				$errors['USER_NOT_SELECTED'] = 1;
			$tp->assign('errors', $errors);
			$tp->assign('username', $username);
			$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
			$tp->display('select_user.tpl');
		}
	}
}
