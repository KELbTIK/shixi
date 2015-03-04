<?php

class SJB_Classifieds_AddListing extends SJB_Function
{
	protected $listingTypeID;
	protected $formSubmittedFromPreview;
	protected $tp;
	protected $buttonPressedPostToProceed;
	
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('subuser_add_listings');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$this->tp = SJB_System::getTemplateProcessor();
		$error = null;

		$post_max_size_orig = ini_get("post_max_size");
		$session_maxlifetime = ini_get("session.gc_maxlifetime");
		$server_content_length = isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : null;
		$this->listingTypeID = SJB_Request::getVar('listing_type_id', false);

		/**
		 * >>>>> for listing preview @author still
		 */
		$this->formSubmittedFromPreview = SJB_Request::getVar('action_add', false, 'POST') && SJB_Request::getVar('from-preview', false, 'POST');
		$editTempListing = SJB_Request::getVar('edit_temp_listing', false, 'POST');

		if ($this->formSubmittedFromPreview || $editTempListing) {
			$listingSID = SJB_Session::getValue('preview_listing_sid_for_add');
			$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);

			if (empty($this->listingTypeID) && !empty($listingInfo)) {
				// if on preview page "POST" button was pressed

				if ($this->formSubmittedFromPreview) {
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
				if ($editTempListing || $this->formSubmittedFromPreview) {
					$current_user = SJB_UserManager::getCurrentUser();

					$this->listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
					// check wether user is owner of the temp listing
					if ($listingInfo['user_sid'] != $current_user->getID()) {
						$error['NOT_OWNER_OF_LISTING'] = $listingSID;
					}
					// set listing info and listing type id
					$_REQUEST = array_merge($_REQUEST, $listingInfo);
					$_REQUEST['listing_type_id'] = $this->listingTypeID;
				}
			}
			if (empty($listingInfo)) {
				$listingSID = null;
				SJB_Session::unsetValue('preview_listing_sid_for_add');
			}
		} else {
			$listingSID = null;
			SJB_Session::unsetValue('preview_listing_sid_for_add');
		}
		/*
		 * <<<<< for listing preview
		 */

		// get post_max_size in bytes
		$val = trim($post_max_size_orig);
		$tmp = substr($val, strlen($val) - 1);
		$tmp = strtolower($tmp);
		/*
		 * if ini value is K - then multiply to 1024
		 * if ini value is M - then multiply twice: in case 'm', and case 'k'
		 * if ini value is G - then multiply tree times: in 'g', 'm', 'k'
		 *
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
			$listing_id = SJB_Request::getVar('listing_id', '', 'default', 'int');
			$file = SJB_UploadFileManager::openFile($filename, $listing_id);
			$errors['NO_SUCH_FILE'] = true;
		}

		if (empty($_POST) && ($server_content_length > $post_max_size)) {
			$errors['MAX_FILE_SIZE_EXCEEDED'] = 1;
			$listing_id = SJB_Request::getVar('listing_id', null, 'GET', 'int');
			$this->tp->assign('post_max_size', $post_max_size_orig);
		}

		$tmpListingIDFromRequest = SJB_Request::getVar('listing_id', false, 'default', 'int');
		if (!empty($tmpListingIDFromRequest)) {
			$tmpListingSID = $tmpListingIDFromRequest;
		} elseif (!$tmpListingIDFromRequest) {
			$tmpListingSID = time();
		}

		$this->buttonPressedPostToProceed = SJB_Request::getVar('proceed_to_posting');
		if (SJB_UserManager::isUserLoggedIn()) {
			SJB_Session::unsetValue('proceed_to_posting');
			SJB_Session::unsetValue('productSID');
			SJB_Session::unsetValue('listing_type_id');
			if (!is_null($this->buttonPressedPostToProceed)) {
				$productSID = SJB_Request::getVar('productSID', false, 'default', 'int');
				$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
				$userInfo = SJB_UserManager::getCurrentUserInfo();
				if ($userInfo['user_group_sid'] == $productInfo['user_group_sid']) {
					$this->tp->assign('productSID', $productSID);
					$this->tp->assign('proceed_to_posting', $productSID);
					$this->tp->assign("listing_id", $tmpListingSID);
					$this->addListing($listingSID, 0, $productSID);
				} else {
					$this->displayErrorTpl('DO_NOT_MATCH_POST_THIS_TYPE_LISTING');
				}
			} else {
				if ($productsInfo = SJB_ListingManager::canCurrentUserAddListing($error, $this->listingTypeID)) {
					if ($contractID = SJB_Request::getVar('contract_id', false, 'POST')) {
						$this->tp->assign("listing_id", $tmpListingSID);
						$this->addListing($listingSID, $contractID, false);
					} elseif (count($productsInfo) == 1) {
						$productInfo = array_pop($productsInfo);
						$contractID = $productInfo['contract_id'];
						$this->tp->assign("listing_id", $tmpListingSID);
						$this->addListing($listingSID, $contractID, false);
					} else {
						$this->tp->assign('listing_id', $tmpListingSID);
						$this->tp->assign('products_info', $productsInfo);
						$this->tp->assign('listingTypeID', $this->listingTypeID);
						$this->tp->display('listing_product_choice.tpl');
					}
				} else {
					if ($error == 'NO_CONTRACT') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/products/?postingProductsOnly=1&page=');
					}
					$this->displayErrorTpl($error);
				}
			}
		} else {
			if ($this->buttonPressedPostToProceed != false) {
				SJB_Session::setValue('proceed_to_posting', true);
				SJB_Session::setValue('productSID', SJB_Request::getVar('productSID', '', 'default', 'int'));
				SJB_Session::setValue('listing_type_id', $this->listingTypeID);
			}
			$this->displayErrorTpl('NOT_LOGGED_IN');
		}
	}


	/**
	 * @param $error
	 */
	public function displayErrorTpl($error)
	{
		$listingTypeName = SJB_ListingTypeManager::getListingTypeNameBySID(SJB_ListingTypeManager::getListingTypeSIDByID($this->listingTypeID));
		if (!in_array($this->listingTypeID, array('Job', 'Resume'))) {
			$listingTypeName .= $listingTypeName . ' Listing';
		}
		$this->tp->assign('listingTypeName', $listingTypeName);
		$this->tp->assign('error', $error);
		$this->tp->display('add_listing_error.tpl');
	}

	/**
	 * @param $listingSID
	 * @param $contractID
	 * @param $productSID
	 */
	public function addListing($listingSID, $contractID = false, $productSID = false)
	{
		if ($productSID != false) {
			$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
			$extraInfo['product_sid'] = (string)$extraInfo['product_sid'];
		} else {
			$contract = new SJB_Contract(array('contract_id' => $contractID));
			$extraInfo = $contract->extra_info;
		}
		$numberOfPictures = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
		$this->tp->assign("pic_limit", $numberOfPictures);
		$listingTypesInfo = SJB_ListingTypeManager::getAllListingTypesInfo();
		if (!$this->listingTypeID && count($listingTypesInfo) == 1) {
			$listingTypeInfo = array_pop($listingTypesInfo);
			$this->listingTypeID = $listingTypeInfo['id'];
		}
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($this->listingTypeID);
		$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listingTypeSID);
		$pageSID = $this->getPageSID($pages, $listingTypeSID);
		$isPageLast = SJB_PostingPagesManager::isLastPageByID($pageSID, $listingTypeSID);
		$isPreviewListingRequested = SJB_Request::getVar('preview_listing', false, 'POST');
		if (($contractID || !empty($this->buttonPressedPostToProceed)) && $this->listingTypeID) {
			$formSubmitted = isset($_REQUEST['action_add']) || isset($_REQUEST['action_add_pictures']) || $isPreviewListingRequested;
			/*
			 * social plugin
			 * complete listing of data from an array of social data
			 * if is allowed
			 */
			$aAutoFillData = array('formSubmitted' => &$formSubmitted, 'listingTypeID' => &$this->listingTypeID);
			SJB_Event::dispatch('SocialSynchronization', $aAutoFillData);
			/*
			 * end of "social plugin"
			 */
			$listing = new SJB_Listing($_REQUEST, $listingTypeSID, $pageSID);
			$listing->deleteProperty('featured');
			$listing->deleteProperty('priority');
			$listing->deleteProperty('status');
			$listing->deleteProperty('reject_reason');
			$listing->deleteProperty('ListingLogo');
			$access_type = $listing->getProperty('access_type');
			if ($formSubmitted) {
				if (!empty($access_type)) {
					$listing->addProperty(
						array('id' => 'access_list',
							'type' => 'multilist',
							'value' => SJB_Request::getVar("list_emp_ids"),
							'is_system' => true));
				}
				$listing->addProperty(
					array('id' => 'contract_id',
						'type' => 'id',
						'value' => $contractID,
						'is_system' => true));
			}
			$currentUser = SJB_UserManager::getCurrentUser();
			$screeningQuestionnaires = SJB_ScreeningQuestionnaires::getList($currentUser->getSID());
			if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires') && $screeningQuestionnaires) {
				$issetQuestionnairyField = $listing->getProperty('screening_questionnaire');
				if ($issetQuestionnairyField) {
					$value = SJB_Request::getVar("screening_questionnaire");
					$listingInfo = $_REQUEST;
					$value = $value ? $value : isset($listingInfo['screening_questionnaire']) ? $listingInfo['screening_questionnaire'] : '';
					$listing->addProperty(
						array('id' => 'screening_questionnaire',
							'type' => 'list',
							'caption' => 'Screening Questionnaire',
							'value' => $value,
							'list_values' => SJB_ScreeningQuestionnaires::getListSIDsAndCaptions($currentUser->getSID()),
							'is_system' => true));
				}
			} else {
				$listing->deleteProperty('screening_questionnaire');
			}
			/*
			 * social plugin
			 * "synchronization"
			 * if user is not registered using linkedin , delete linkedin sync property
			 * also if sync is turned off in admin part
			 */
			$aAutoFillData = array('oListing' => &$listing, 'userSID' => $currentUser->getSID(), 'listingTypeID' => $this->listingTypeID, 'listing_info' => $_REQUEST);
			SJB_Event::dispatch('SocialSynchronizationFields', $aAutoFillData);
			/*
			 * end of social plugin "sync"
			 */

			$listingFormAdd = new SJB_Form($listing);
			$listingFormAdd->registerTags($this->tp);

			$fieldErrors = array();

			if ($formSubmitted && ($this->formSubmittedFromPreview || $listingFormAdd->isDataValid($fieldErrors))) {
				if ($isPageLast) {
					$listing->addProperty(
						array('id' => 'complete',
							'type' => 'integer',
							'value' => 1,
							'is_system' => true));
				}
				$listing->setUserSID($currentUser->getSID());
				$listing->setProductInfo($extraInfo);

				if (empty($access_type->value)) {
					$listing->setPropertyValue('access_type', 'everyone');
				}

				if ($currentUser->isSubuser()) {
					$subuserInfo = $currentUser->getSubuserInfo();
					$listing->addSubuserProperty($subuserInfo['sid']);
				}
				/**
				 * >>>>> listing preview @author still
				 */
				if (!empty($listingSID)) {
					$listing->setSID($listingSID);
				}
				/*
				 * <<<<< listing preview
				 */
				SJB_ListingManager::saveListing($listing);

				if (!empty($this->buttonPressedPostToProceed)) {
					SJB_ListingManager::unmakeCheckoutedBySID($listing->getSID());
				}

				SJB_Statistics::addStatistics('addListing', $listing->getListingTypeSID(), $listing->getSID(), false, $extraInfo['featured'], $extraInfo['priority']);
				if ($contractID) {
					$contract = new SJB_Contract(array('contract_id' => $contractID));
					$contract->incrementPostingsNumber();
					SJB_ProductsManager::incrementPostingsNumber($contract->product_sid);
				}

				if (SJB_Session::getValue('tmp_file_storage')) {
					foreach ($_SESSION['tmp_file_storage'] as $v) {
						SJB_DB::query("UPDATE `listings_pictures` SET `listing_sid` = ?n WHERE `picture_saved_name` = ?s", $listing->getSID(), $v['picture_saved_name']);
						SJB_DB::query("UPDATE `listings_pictures` SET `listing_sid` = ?n WHERE `thumb_saved_name` = ?s", $listing->getSID(), $v['thumb_saved_name']);
					}
					SJB_Session::unsetValue('tmp_file_storage');
				}

				// >>> SJB-1197
				// check temporary uploaded storage for listing uploads and assign it to saved listing
				$formToken = SJB_Request::getVar('form_token');
				$sessionFilesStorage = SJB_Session::getValue('tmp_uploads_storage');
				$uploadedFields = SJB_Array::getPath($sessionFilesStorage, $formToken);

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
							$parentProp = $listing->getProperty($parentField);
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
					$keywords = $listing->getKeywords();
					SJB_ListingManager::updateKeywords($keywords, $listing->getSID());
				}
				// <<< SJB-1197

				if ($isPageLast && !$isPreviewListingRequested) {

					/* delete temp preview listing sid */
					SJB_Session::unsetValue('preview_listing_sid_for_add');

					// Start Event
					$listingSid = $listing->getSID();
					SJB_Event::dispatch('listingSaved', $listingSid);

					if ($extraInfo['featured']) {
						SJB_ListingManager::makeFeaturedBySID($listing->getSID());
					}
					if ($extraInfo['priority']) {
						SJB_ListingManager::makePriorityBySID($listing->getSID());
					}

					if (!empty($this->buttonPressedPostToProceed)) {
						$this->proceedToCheckout($currentUser->getSID(), $productSID);
					} else {
						if (SJB_ListingManager::activateListingBySID($listing->getSID())) {
							SJB_Notifications::sendUserListingActivatedLetter($listing, $listing->getUserSID());
						}

						// notify administrator
						SJB_AdminNotifications::sendAdminListingAddedLetter($listing);

						if (isset($_REQUEST['action_add_pictures']))
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-pictures/?listing_id=" . $listing->getSID());
						else
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-' . strtolower($this->listingTypeID) .'/?listing_id=' . $listing->getSID());
					}
				} elseif ($isPageLast && $isPreviewListingRequested) { // for listing preview
					SJB_Session::setValue('preview_listing_sid_for_add', $listing->getSID());
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/' . strtolower($this->listingTypeID) . '-preview/' . $listing->getSID() . '/');
				} else { // listing steps (pages)
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/add-listing/{$this->listingTypeID}/" . SJB_PostingPagesManager::getNextPage($pageSID) . "/" . $listing->getSID());
				}
			} else {
				$listing->deleteProperty('access_list');
				$listing->deleteProperty('contract_id');
				$listingFormAdd = new SJB_Form($listing);
				if ($formSubmitted) {
					$listingFormAdd->isDataValid($fieldErrors);
				}
				$listingFormAdd->registerTags($this->tp);
				$template = isset($_REQUEST['input_template']) ? $_REQUEST['input_template'] : "input_form.tpl";
				$formFields = $listingFormAdd->getFormFieldsInfo();
				$employersList = SJB_Request::getVar('list_emp_ids', false);
				$employers = array();
				if (is_array($employersList)) {
					foreach ($employersList as $emp) {
						$currEmp = SJB_UserManager::getUserInfoBySID($emp);
						$employers[] = array('user_id' => $emp, 'value' => $currEmp['CompanyName']);
					}
					sort($employers);
				}

				$this->tp->assign('form_token', SJB_Request::getVar('form_token'));

				$this->tp->assign("account_activated", SJB_Request::getVar('account_activated', ''));
				$this->tp->assign("contract_id", $contractID);
				$this->tp->assign("listing_access_list", $employers);
				$this->tp->assign("listingTypeID", $this->listingTypeID);
				$this->tp->assign('listingTypeStructure', SJB_ListingTypeManager::createTemplateStructure(SJB_ListingTypeManager::getListingTypeInfoBySID($listing->listing_type_sid)));
				$this->tp->assign("field_errors", $fieldErrors);
				$this->tp->assign("form_fields", $formFields);
				$this->tp->assign("pages", $pages);
				$this->tp->assign("pageSID", $pageSID);
				$this->tp->assign("extraInfo", $extraInfo);
				$this->tp->assign("currentPage", SJB_PostingPagesManager::getPageInfoBySID($pageSID));
				$this->tp->assign("isPageLast", $isPageLast);
				$this->tp->assign("nextPage", SJB_PostingPagesManager::getNextPage($pageSID));
				$this->tp->assign("prevPage", SJB_PostingPagesManager::getPrevPage($pageSID));

				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$this->tp->assign(
					"METADATA",
					array(
						"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
					)
				);

				/*
				 * social plugin
				 * only for Resume listing types
				 */
				$aAutoFillData = array('tp' => &$this->tp, 'listingTypeID' => &$this->listingTypeID, 'userSID' => $currentUser->getSID());
				SJB_Event::dispatch('SocialSynchronizationForm', $aAutoFillData);
				/*
				 * social plugin
				 */

				$this->tp->display($template);
			}
		}
	}

	/**
	 * @param $pages
	 * @param $listingTypeSID
	 * @return bool|int|mixed
	 */
	public function getPageSID($pages, $listingTypeSID)
	{
		$passedParametersViaUri = SJB_Request::getVar('passed_parameters_via_uri', false);
		$pageID = false;
		if ($passedParametersViaUri) {
			$passedParametersViaUri = SJB_UrlParamProvider::getParams();
			$this->listingTypeID = isset($passedParametersViaUri[0]) ? $passedParametersViaUri[0] : $this->listingTypeID;
			$pageID = isset($passedParametersViaUri[1]) ? $passedParametersViaUri[1] : false;
		}
		if (!$pageID) {
			$pageID = $pages[0]['page_id'];
		}
		$pageSID = SJB_PostingPagesManager::getPostingPageSIDByID($pageID, $listingTypeSID);
		return $pageSID;
	}

	/**
	 * @param int $currentUserID
	 * @param int $productSID
	 * @return bool|int|mixed
	 */
	public function proceedToCheckout($currentUserID, $productSID)
	{
		$errors = array();
		$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
		if (SJB_UserManager::isUserLoggedIn()) {
			$numberOfListings = SJB_ListingDBManager::getNumberOfCheckoutedListingsByProductSID($productSID, $currentUserID);
			$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
			$shoppingCartProducts = SJB_ShoppingCart::getProductsInfoFromCartByProductSID($productSID,$currentUserID);
			if (!empty($shoppingCartProducts)) {
				if ($productInfo['product_type'] == 'mixed_product' || isset($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
					if ($numberOfListings / (count($shoppingCartProducts) * $productInfo['number_of_listings']) > 1) {
						SJB_ShoppingCart::addToShoppingCart($productInfo, $currentUserID);
					}
				}
				if (isset($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'volume_based') {
					if ($numberOfListings / (count($shoppingCartProducts) * end($productInfo['volume_based_pricing']['listings_range_to'])) > 1) {
						$productInfo['number_of_listings'] = 1;
						$productObj = new SJB_Product($productInfo, $productInfo['product_type']);
						$productObj->setNumberOfListings($productInfo['number_of_listings']);
						$productInfo['price'] = $productObj->getPrice();
						SJB_ShoppingCart::addToShoppingCart($productInfo, $currentUserID);
					} else {
						foreach ($shoppingCartProducts as $shoppingCartProduct) {
							$unserializedProductInfoFromShopCart = unserialize($shoppingCartProduct['product_info']);
							if ($unserializedProductInfoFromShopCart['number_of_listings'] < end($unserializedProductInfoFromShopCart['volume_based_pricing']['listings_range_to'])) {
								$unserializedProductInfoFromShopCart['number_of_listings'] += 1;
								SJB_ShoppingCart::updateItemBySID($shoppingCartProduct['sid'], $unserializedProductInfoFromShopCart);
								break;
							}
						}
					}
				}
			} else {
				if (!empty($extraInfo['pricing_type']) && $extraInfo['pricing_type'] == 'volume_based') {
					$productInfo['number_of_listings'] = 1;
					$productObj = new SJB_Product($productInfo, $productInfo['product_type']);
					$productObj->setNumberOfListings($productInfo['number_of_listings']);
					$productInfo['price'] = $productObj->getPrice();
				}
				SJB_ShoppingCart::addToShoppingCart($productInfo, $currentUserID);
			}
		} else {
			$products = SJB_Session::getValue('products');
			if (isset($products)) {
				foreach ($products as $addedProduct) {
					$addedProductInfo = unserialize($addedProduct['product_info']);
					if ($addedProductInfo['user_group_sid'] != $productInfo['user_group_sid']) {
						$errors[] = 'You are trying to add products of different User Groups in your Shopping Cart. You сan add only products belonging to one User Group. If you want to add this product in the Shopping Cart please go back to the Shopping Cart and remove products of other User Groups.';
						break;
					}
				}
			}
			if (!$errors) {
				$id = time();
				$products[$id]['product_info'] = serialize($productInfo);
				$products[$id]['sid'] = $id;
				$products[$id]['user_sid'] = 0;
				SJB_Session::setValue('products', $products);
			}
		}
		if (!$errors) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemsettings('SITE_URL') . '/shopping-cart/');
		}
	}
}