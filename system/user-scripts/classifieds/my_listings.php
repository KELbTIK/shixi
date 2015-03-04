<?php

class SJB_Classifieds_MyListings extends SJB_Function
{
	protected $listingTypeID = null;
	protected $listingTypeSID = null;
	protected $requestCriteria = array();

	public function isAccessible()
	{
		if ($this->getAclRoleID())
			$this->setPermissionLabel(array('subuser_add_listings', 'subuser_manage_listings'));
		return parent::isAccessible();
	}

	public function execute()
	{
		if (!function_exists('_filter_data')) {
			function _filter_data(&$array, $key, $pattern)
			{
				if (isset($array[$key])) {
					if (!preg_match($pattern, $array[$key]))
						unset($array[$key]);
				}
			}
		}

		_filter_data($_REQUEST, 'sorting_field', "/^[_\w\d]+$/");
		_filter_data($_REQUEST, 'sorting_order', "/(^DESC$)|(^ASC$)/i");
		_filter_data($_REQUEST, 'default_sorting_field', "/^[_\w\d]+$/");
		_filter_data($_REQUEST, 'default_sorting_order', "/(^DESC$)|(^ASC$)/i");

		$tp = SJB_System::getTemplateProcessor();
		if (!SJB_UserManager::isUserLoggedIn()) {
			$errors['NOT_LOGGED_IN'] = true;
			$tp->assign("ERRORS", $errors);
			$tp->display("error.tpl");
			return;
		}

		$this->defineRequestedListingTypeID();

		if (!$this->listingTypeID) {
			$tp->assign('listingTypes', SJB_ListingTypeManager::getAllListingTypesInfo());
			$tp->display('my_available_listing_types.tpl');
			return;
		}

		$this->listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($this->listingTypeID);

		if (!$this->listingTypeSID) {
			SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/my-listings/');
			return;
		}

		$currentUser = SJB_UserManager::getCurrentUser();
		$userSID = $currentUser->getSID();

		$this->requestCriteria = array(
			'user_sid' 			=> array('equal' => $userSID),
			'listing_type_sid' 	=> array('equal' => $this->listingTypeSID)
		);

		$acl = SJB_Acl::getInstance();

		if ($currentUser->isSubuser()) {
			$subUserInfo = $currentUser->getSubuserInfo();
			if (!$acl->isAllowed('subuser_manage_listings', $subUserInfo['sid'])) {
				$this->requestCriteria['subuser_sid'] = array('equal' => $subUserInfo['sid']);
			}
		}
		
		SJB_ListingManager::deletePreviewListingsByUserSID($userSID);

		$searcher = new SJB_ListingSearcher();

		// to save criteria in the session different from search_results
		$criteriaSaver = new SJB_ListingCriteriaSaver('MyListings');

		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $criteriaSaver->getCriteria());
		}

		if (isset($_REQUEST['listings'])) {
			$listingsSIDs = $_REQUEST['listings'];
			if (isset($_REQUEST['action_deactivate'])) {
				$this->executeAction($listingsSIDs, 'deactivate');
			}
			elseif (isset($_REQUEST['action_activate'])) {
				$redirectToShoppingCard = false;
				$activatedListings = array();
				foreach ($listingsSIDs as $listingSID => $value) {
					$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);
					$productInfo = !empty($listingInfo['product_info']) ? unserialize($listingInfo['product_info']) : array();
					if ($listingInfo['active']) {
						continue;
					}
					else if (SJB_ListingManager::getIfListingHasExpiredBySID($listingSID)
							&& isset($productInfo['renewal_price'])
							&& $productInfo['renewal_price'] > 0) {
						$redirectToShoppingCard = true;

						$listingTypeId  = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
						$newProductName = "Reactivation of \"{$listingInfo['Title']}\" {$listingTypeId}";
						$newProductInfo = SJB_ShoppingCart::createInfoForCustomProduct($userSID, $productInfo['product_sid'], $listingSID, $productInfo['renewal_price'], $newProductName, 'activateListing');

						SJB_ShoppingCart::createCustomProduct($newProductInfo, $userSID);
					}
					else if ($listingInfo['checkouted'] == 0) {
						$redirectToShoppingCard = true;
					}
					else if (SJB_ListingManager::activateListingBySID($listingSID, false)) {
						$listing = SJB_ListingManager::getObjectBySID($listingSID);
						SJB_Notifications::sendUserListingActivatedLetter($listing, $listing->getUserSID());
						$activatedListings[] = $listingSID;
					}
				}
				SJB_BrowseDBManager::addListings($activatedListings);
				if ($redirectToShoppingCard) {
					$shoppingUrl = SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/';
					SJB_HelperFunctions::redirect($shoppingUrl);
				}
			} else {
				if (isset($_REQUEST['action_delete'])) {
					$this->executeAction($listingsSIDs, 'delete');
					$allowedPostBeforeCheckout = SJB_Settings::getSettingByName('allow_to_post_before_checkout');
					foreach ($listingsSIDs as $listingSID => $value) {
						if ($allowedPostBeforeCheckout == true) {
							$this->deleteCheckoutedListingFromShopCart($listingSID, $userSID);
						}
					}
				}
				elseif (isset($_REQUEST['action_sendToApprove'])) {
					$processListingsIds = array();
					foreach ($listingsSIDs as $listingSID => $value) {
						$processListingsIds[] = $listingSID;
					}
					SJB_ListingManager::setListingApprovalStatus($processListingsIds, 'pending');
				}
			}
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/my-listings/{$this->listingTypeID}/");
		}

		$listing = new SJB_Listing(array(), $this->listingTypeSID);
		$idAliasInfo = $listing->addIDProperty();
		$listing->addActivationDateProperty();
		$listing->addKeywordsProperty();
		$listing->addPicturesProperty();
		$listingTypeIdAliasInfo = $listing->addListingTypeIDProperty();

		$sortingFields = array();
		$innerJoin = array();
		$sortingField = SJB_Request::getVar("sorting_field", null);
		$sortingOrder = SJB_Request::getVar("sorting_order", null);
		if (isset($sortingField, $sortingOrder)) {
			$orderInfo = array(
				'sorting_field' => $sortingField,
				'sorting_order' => $sortingOrder
			);
		} else {
			$orderInfo = $criteriaSaver->getOrderInfo();
		}

		if ($orderInfo['sorting_field'] == 'applications') {
			$innerJoin['applications'] = array(
				'count'       => 'count(`applications`.id) as appCount',
				'join'        => 'LEFT JOIN',
				'join_field'  => 'listing_id',
				'join_field2' => 'sid',
				'main_table'  => 'listings',
			);
			$sortingFields['appCount'] = $orderInfo['sorting_order'];
			$searcher->setGroupByField(array('listings' => 'sid'));
		}
		else if ($orderInfo['sorting_field'] == 'id') {
			$sortingFields['sid'] = $orderInfo['sorting_order'];
		}
		else if ($orderInfo['sorting_field'] == 'subuser_sid') {
			$innerJoin['users'] = array(
				'join'        => 'LEFT JOIN',
				'join_field'  => 'sid',
				'join_field2' => 'subuser_sid',
				'main_table'  => 'listings'
			);
			$sortingFields['username'] = $orderInfo['sorting_order'];
		} else {
			$property = $listing->getProperty($sortingField);
			if (!empty($property) && $property->isSystem()) {
				$sortingFields[$orderInfo['sorting_field']] = $orderInfo['sorting_order'];
			} else {
				$sortingFields['activation_date'] = 'DESC';
			}
		}

		$this->requestCriteria['sorting_field'] = $orderInfo['sorting_field'];
		$this->requestCriteria['sorting_order'] = $orderInfo['sorting_order'];

		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData(array_merge($_REQUEST, $this->requestCriteria), $listing);
		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($idAliasInfo);
		$aliases->addAlias($listingTypeIdAliasInfo);
		$foundListingsSIDs = $searcher->getObjectsSIDsByCriteria($criteria, $aliases, $sortingFields, $innerJoin);

		$searchFormBuilder = new SJB_SearchFormBuilder($listing);
		$searchFormBuilder->registerTags($tp);
		$searchFormBuilder->setCriteria($criteria);

		// получим информацию о имеющихся листингах
		$listingsInfo = array();
		$currentUserInfo = SJB_UserManager::getCurrentUserInfo();
		$contractInfo['extra_info']['listing_amount'] = 0;

		if ($acl->isAllowed('post_' . $this->listingTypeID)) {
			$permissionParam = $acl->getPermissionParams('post_' . $this->listingTypeID);
			if (empty($permissionParam)) {
				$contractInfo['extra_info']['listing_amount'] = 'unlimited';
			} else {
				$contractInfo['extra_info']['listing_amount'] = $permissionParam;
			}
		}
		$currentUser = SJB_UserManager::getCurrentUser();
		$contractsSIDs = $currentUser->getContractID();
		$listingsInfo['listingsNum'] = SJB_ContractManager::getListingsNumberByContractSIDsListingType($contractsSIDs, $this->listingTypeID);
		$listingsInfo['listingsMax'] = $contractInfo['extra_info']['listing_amount'];
		if ($listingsInfo['listingsMax'] === 'unlimited') {
			$listingsInfo['listingsLeft'] = 'unlimited';
		} else {
			$listingsInfo['listingsLeft'] = $listingsInfo['listingsMax'] - $listingsInfo['listingsNum'];
			$listingsInfo['listingsLeft'] = $listingsInfo['listingsLeft'] < 0 ? 0 : $listingsInfo['listingsLeft'];
		}

		$tp->assign('listingTypeID', $this->listingTypeID);
		$tp->assign('listingTypeName', SJB_ListingTypeManager::getListingTypeNameBySID($this->listingTypeSID));
		$tp->assign('listingsInfo', $listingsInfo);
		$tp->display('my_listings_form.tpl');

		$page = SJB_Request::getVar('page', 1);
		$listingsPerPage = $criteriaSaver->getListingsPerPage(); //save 'listings per page' in the session
		if (empty($listingsPerPage)) {
			$listingsPerPage = 10;
		}
		$listingsPerPage = SJB_Request::getVar('listings_per_page', $listingsPerPage);
		$criteriaSaver->setSessionForListingsPerPage($listingsPerPage);
		$criteriaSaver->setSessionForCurrentPage($page);
		$criteriaSaver->setSessionForCriteria($_REQUEST);
		$criteriaSaver->setSessionForOrderInfo($orderInfo);
		$criteriaSaver->setSessionForObjectSIDs($foundListingsSIDs);

		// get Applications
		$appsGroups = SJB_Applications::getAppGroupsByEmployer($currentUserInfo['sid']);
		$apps = array();
		foreach ($appsGroups as $group) {
			$apps[$group['listing_id']] = $group['count'];
		}

		$searchCriteriaStructure = $criteriaSaver->createTemplateStructureForCriteria();
		$listingSearchStructure = $criteriaSaver->createTemplateStructureForSearch();

		/**************** P A G I N G *****************/
		if ($listingSearchStructure['current_page'] > $listingSearchStructure['pages_number']) {
			$listingSearchStructure['current_page'] = $listingSearchStructure['pages_number'];
		}
		if ($listingSearchStructure['current_page'] < 1) {
			$listingSearchStructure['current_page'] = 1;
		}

		$sortedFoundListingsSIDsByPages = array_chunk($foundListingsSIDs, $listingSearchStructure['listings_per_page'], true);

		/************* S T R U C T U R E **************/
		$listingsStructure = array();
		$listingStructureMetaData = array();

		if (isset($sortedFoundListingsSIDsByPages[$listingSearchStructure['current_page'] - 1])) {
			foreach ($sortedFoundListingsSIDsByPages[$listingSearchStructure['current_page'] - 1] as $sid) {
				$listing = SJB_ListingManager::getObjectBySID($sid);
				$listing->addPicturesProperty();
				$listingStructure = SJB_ListingManager::createTemplateStructureForListing($listing);
				$listingsStructure[$listing->getID()] = $listingStructure;

				if (isset($listingStructure['METADATA'])) {
					$listingStructureMetaData = array_merge($listingStructureMetaData, $listingStructure['METADATA']);
				}
			}
		}

		/*************** D I S P L A Y ****************/
		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$metadata = array();
		$metadata['listing'] = $metaDataProvider->getMetaData($listingStructureMetaData);

		$waitApprove = SJB_ListingTypeManager::getWaitApproveSettingByListingType($this->listingTypeSID);

		$tp->assign('show_rates', SJB_Settings::getSettingByName('show_rates'));
		$tp->assign('show_comments', SJB_Settings::getSettingByName('show_comments'));
		$tp->assign('METADATA', $metadata);
		$tp->assign('sorting_field', $listingSearchStructure['sorting_field']);
		$tp->assign('sorting_order', $listingSearchStructure['sorting_order']);
		$tp->assign('property', $this->getSortableProperties());
		$tp->assign('listing_search', $listingSearchStructure);
		$tp->assign('search_criteria', $searchCriteriaStructure);
		$tp->assign('listings', $listingsStructure);
		$tp->assign('waitApprove', $waitApprove);
		$tp->assign('apps', $apps);

		$hasSubusersWithListings = false;
		$subusers = SJB_UserManager::getSubusers($currentUserInfo['sid']);
		foreach ($subusers as $subuser) {
			if ($acl->isAllowed('subuser_add_listings', $subuser['sid']) || $acl->isAllowed('subuser_manage_listings', $subuser['sid'])) {
				$hasSubusersWithListings = true;
				break;
			}
		}
		$tp->assign('hasSubusersWithListings', $hasSubusersWithListings);

		$tp->display('my_listings.tpl');
	}

	/**
	 * @param $listingSID
	 * @param $userSID
	 */
	public function deleteCheckoutedListingFromShopCart($listingSID, $userSID)
	{
		$listingInfoCheckout = SJB_DB::query("SELECT `sid`,`product_info` FROM `listings` WHERE `sid` = ?n AND `checkouted` = 0 AND `complete` = 1", $listingSID);
		$listingProductInfo = unserialize($listingInfoCheckout[0]['product_info']);
		$shopCartProducts = SJB_ShoppingCart::getProductsInfoFromCartByProductSID($listingProductInfo['product_sid'], $userSID);
		if ($listingProductInfo['pricing_type'] == 'fixed') {
			$countCheckoutedListings = SJB_ListingDBManager::getNumberOfCheckoutedListingsByProductSID($listingProductInfo['product_sid'], $userSID);
			if (((count($shopCartProducts) * $listingProductInfo['number_of_listings']) - ($countCheckoutedListings - 1)) >= $listingProductInfo['number_of_listings']) {
				SJB_ShoppingCart::deleteItemFromCartBySID($shopCartProducts[0]['sid'], $userSID);
			}
		}
		if (isset($listingProductInfo['pricing_type']) && $listingProductInfo['pricing_type'] == 'volume_based') {
			$shopCartSIDMinNumberOfListings = '';
			$minNumberOfListings = '';
			foreach ($shopCartProducts as $shopCartProduct) {
				$shopCartProductInfo = unserialize($shopCartProduct['product_info']);
				if (empty($minNumberOfListings) || $minNumberOfListings > $shopCartProductInfo['number_of_listings']) {
					$minNumberOfListings = $shopCartProductInfo['number_of_listings'];
					$shopCartSIDMinNumberOfListings = $shopCartProduct['sid'];
				}
			}
			if ($minNumberOfListings == 1) {
				SJB_ShoppingCart::deleteItemFromCartBySID($shopCartSIDMinNumberOfListings, $userSID);
			} else {
				$shopCartProductInfo['number_of_listings'] = $minNumberOfListings - 1;
				SJB_ShoppingCart::updateItemBySID($shopCartSIDMinNumberOfListings, $shopCartProductInfo);
			}
		}
	}

	/**
	 * @param array  $listingsIds Used listing sids
	 * @param string $action      Actions performed with the listings(delete, deactivate, activate)
	 */
	private function executeAction(array $listingsIds, $action)
	{
		if (empty($listingsIds)) {
			return;
		}
		
		$processListingsIds = array();
		foreach ($listingsIds as $key => $value) {
			$processListingsIds[] = $key;
		}
		
		switch ($action) {
			case 'delete':
				SJB_ListingManager::deleteListingBySID($processListingsIds);
				return;
			case 'deactivate':
				SJB_ListingManager::deactivateListingBySID($processListingsIds);
				return;
		}
	}

	protected function defineRequestedListingTypeID()
	{
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$params = SJB_FixedUrlParamProvider::getParams($_REQUEST);
			if ($params) {
				$this->listingTypeID = array_pop($params);
			}
		} else {
			$this->listingTypeID = isset($_REQUEST['listing_type_id']) ? $_REQUEST['listing_type_id'] : null;
		}
	}

	/**
	 * Returns sortable properties by listing
	 * @return array
	 */
	private function getSortableProperties()
	{
		$emptyListing = new SJB_Listing(array(), $this->listingTypeSID);
		$emptyListing->addPicturesProperty();
		$emptyListing->addIDProperty();
		$emptyListing->addListingTypeIDProperty();
		$emptyListing->addActivationDateProperty();
		$emptyListing->addNumberOfViewsProperty();
		$emptyListing->addApplicationsProperty();
		$emptyListing->addSubuserProperty();
		$emptyListing->addActiveProperty();
		$emptyListing->addExpirationDateProperty(null);

		$sortableProperties = array();
		$propertyList = $emptyListing->getPropertyList();

		foreach ($propertyList as $property) {
			$sortableProperties[$property]['is_sortable'] = true;
		}
		return $sortableProperties;
	}
}