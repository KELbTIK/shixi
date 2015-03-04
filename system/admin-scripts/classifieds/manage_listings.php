<?php

class SJB_Admin_Classifieds_ManageListings extends SJB_Function
{
	public function isAccessible()
	{
		$permLabel = preg_replace(array("/\//","/-/"), array("","_"), SJB_Navigator::getURI());
		$this->setPermissionLabel($permLabel);
		return parent::isAccessible();
	}

	public function execute()
	{
		$listingTypeSid = SJB_Request::getVar('listing_type_sid', false);
		$template_processor = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['restore']) && isset($listingSearcher['criteria_values']['listing_type_sid'])) {
			$listingSearcher = SJB_Session::getValue('ListingSearcher', null);
			$listingTypeSid = $listingSearcher['criteria_values']['listing_type_sid']['equal'];
		}
		$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listingTypeSid);
		$template_processor->assign('showApprovalStatusField', $listingTypeInfo['waitApprove']);
		$template_processor->assign('listingsType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));

		$show_search_form = true;
		if (empty($_REQUEST['action']) && empty($_REQUEST['restore']))
			$show_search_form = false;

		$template_processor->assign('show_search_form', $show_search_form);

		/**************** S E A R C H   F O R M ****************/
		$listing = SJB_ObjectMother::createListing(array(), $listingTypeSid);
		$id_alias_info = $listing->addIDProperty();
		$username_alias_info = $listing->addUsernameProperty();
		$productAliasInfo = $listing->addProductProperty($listingTypeSid);
		$listing->addCompanyNameProperty();
		$listing->addActivationDateProperty();
		$listing->addExpirationDateProperty();
		$listing->addActiveProperty();
		$listing->addKeywordsProperty();
		$listing->addDataSourceProperty();
		$listing->addPriorityProperty();

		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($username_alias_info);
		$aliases->addAlias($id_alias_info);
		$aliases->addAlias($productAliasInfo);

		$search_form_builder = new SJB_SearchFormBuilder($listing);
		$criteria_saver = SJB_ObjectMother::createListingCriteriaSaver();

		$keywords = NULL;
		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $criteria_saver->getCriteria());
			$criteria = $criteria_saver->getCriteria();
			$listingSid =  SJB_Array::getPath($criteria, 'sid') ? $criteria['sid']['equal'] : '';
			$keywords = SJB_Array::getPath($criteria, 'keywords') ? $criteria['keywords']['like'] : $listingSid;
		}

		if ($listingTypeSid) {
			$_REQUEST['listing_type_sid'] = array('equal' => $listingTypeSid);
		}

		$template_processor->assign('companyName', isset($_REQUEST['company_name']['like']) ? $_REQUEST['company_name']['like'] : '');
		$template_processor->assign('idKeyword', isset($_REQUEST['idKeyword']) ? $_REQUEST['idKeyword'] : $keywords);
		$this->prepareRequestedCriteria();
		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST, $listing);
		$search_form_builder->setCriteria($criteria);
		$search_form_builder->registerTags($template_processor);
		$template_processor->display("manage_listings.tpl");

		/************* S E A R C H   F O R M   R E S U L T S *************/

		$paginator = new SJB_ListingPagination($listingTypeInfo);
		$searcher = SJB_ObjectMother::createListingSearcher();
		$inner_join = array();
		if (SJB_Request::getVar('action', '') == 'search' || isset($_REQUEST['restore'])) {
			if (!isset($_REQUEST['restore']))
				$criteria_saver->resetSearchResultsDisplay();

			if (isset($_REQUEST['company_name']['like']) && $_REQUEST['company_name']['like'] != '') {
				$inner_join = array('users' => array('join_field' => 'sid', 'join_field2' => 'user_sid', 'main_table' => 'listings', 'join' => 'INNER JOIN'));
			}
			$foundListingsSIDs = $searcher->getObjectsSIDsByCriteria($criteria, $aliases, array(), $inner_join); //get found listing sids
			if (empty($foundListingsSIDs) && $paginator->currentPage != 1) {
				if ($listingTypeInfo['id'] == 'Job' || $listingTypeInfo['id'] == 'Resume') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-' . strtolower($listingTypeInfo['id']) . 's/?page=1&restore=1');
				} else {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-' . strtolower($listingTypeInfo['id']) . '-listings/?page=1&restore=1');
				}
			}

			$criteria_saver->setSessionForListingsPerPage($paginator->itemsPerPage);
			$criteria_saver->setSessionForCurrentPage($paginator->currentPage);
			$criteria_saver->setSessionForCriteria($_REQUEST);

			$orderInfo = array(
				'sorting_field' => $paginator->sortingField,
				'sorting_order' => $paginator->sortingOrder
			);

			$criteria_saver->setSessionForOrderInfo($orderInfo);
		}
		else {
			$criteria_saver->resetSearchResultsDisplay();
			return;
		}
		$criteria_saver->setSessionForObjectSIDs($foundListingsSIDs);

		$listing_search_structure = $criteria_saver->createTemplateStructureForSearch();

		/**************** S O R T I N G *****************/
		$empty_listing = SJB_ObjectMother::createListing(array(), $listingTypeSid);
		$empty_listing->addPicturesProperty();
		$empty_listing->addIDProperty();
		$empty_listing->addListingTypeIDProperty();
		$empty_listing->addActivationDateProperty();
		$empty_listing->addExpirationDateProperty();
		$empty_listing->addUsernameProperty();
		$empty_listing->addPicturesProperty();
		$empty_listing->addNumberOfViewsProperty();
		$empty_listing->addActiveProperty();
		$empty_listing->addKeywordsProperty();
		$empty_listing->addDataSourceProperty();

		$listing->addRejectReasonProperty();

		if ($empty_listing->propertyIsSet($listing_search_structure['sorting_field']) && !empty($foundListingsSIDs)) {
			$sorting_field = $listing_search_structure['sorting_field'];
			$sorting_order = $listing_search_structure['sorting_order'];

			switch ($sorting_field) {

				case 'username':
					$ids = join(", ", $foundListingsSIDs);
					$sql = "	SELECT		listings.*
						FROM 		listings
						LEFT JOIN	users on listings.user_sid = users.sid
						WHERE 		listings.sid IN ({$ids})
						ORDER BY users.username {$sorting_order}";

					$listings_info = SJB_DB::query($sql);
					break;

				case 'listing_type':
					$ids = join(", ", $foundListingsSIDs);
					$sql = "	SELECT		listings.*
						FROM 		listings
						LEFT JOIN	listing_types on listings.listing_type_sid = listing_types.sid
						WHERE 		listings.sid IN ({$ids})
						ORDER BY listing_types.id {$sorting_order}";

					$listings_info = SJB_DB::query($sql);
					break;

				case 'id':
					$ids = join(", ", $foundListingsSIDs);
					$sql = "	SELECT		listings.*
						FROM 		listings
						WHERE		listings.sid IN ({$ids})
						ORDER BY sid $sorting_order";

					$listings_info = SJB_DB::query($sql);
					break;
				default:
					$property = $empty_listing->getProperty($sorting_field);
					$listing_request_creator = new SJB_ListingRequestCreator($foundListingsSIDs, array('property' => $property, 'sorting_order' => $sorting_order));
					$listings_info = SJB_DB::query($listing_request_creator->getRequest());
					break;
			}

			$listings_sids = array();
			foreach ($listings_info as $listing_info) {
				$listings_sids[$listing_info['sid']] = $listing_info['sid'];
			}
			$sortedFoundListingsSIDs = array_keys($listings_sids);
			$criteria_saver->setSessionForObjectSIDs($sortedFoundListingsSIDs);
		}
		else {
			$sortedFoundListingsSIDs = $foundListingsSIDs;
			$criteria_saver->setSessionForObjectSIDs($foundListingsSIDs);
		}

		/**************** P A G I N G *****************/
		$sortedFoundListingsSIDsByPages = array_chunk($sortedFoundListingsSIDs, $paginator->itemsPerPage, true);
		$paginator->setItemsCount(count($sortedFoundListingsSIDs));

		/************* S T R U C T U R E **************/
		$listings_structure = array();
		if (isset($sortedFoundListingsSIDsByPages[$paginator->currentPage - 1])) {
			foreach ($sortedFoundListingsSIDsByPages[$paginator->currentPage - 1] as $sid) {
				$listing = SJB_ListingManager::getObjectBySID($sid);
				$listing->addPicturesProperty();
				$listings_structure[$listing->getID()] = SJB_ListingManager::createTemplateStructureForListing($listing);
			}
		}

		/*************** D I S P L A Y ****************/
		$template_processor->assign("search_criteria", $criteria_saver->createTemplateStructureForCriteria());
		$template_processor->assign('paginationInfo', $paginator->getPaginationInfo());
		$template_processor->assign('listings', $listings_structure);
		$template_processor->display('display_results.tpl');
	}

	private function prepareRequestedCriteria()
	{
		if ($idKeyword = SJB_Request::getVar('idKeyword', false)) {
			if (strpos($idKeyword, ',') !== false) {
				$idKeywordTrimmed = array();
				foreach (explode(',', $idKeyword) as $idK) {
					$idKeywordTrimmed[] = SJB_HelperFunctions::trimValue($idK);
				}
				foreach ($idKeywordTrimmed as $val) {
					if (intval($val)) {
						$_REQUEST['sid']['in'][] = (int)$val;
					} else {
						unset($_REQUEST['sid']['in']);
						$_REQUEST['keywords']['like'][] = $val;
					}
				}
			} else {
				if (intval($idKeyword)) {
					$_REQUEST['sid']['equal'] = (int)$idKeyword;
				} else {
					$_REQUEST['keywords']['like'] = SJB_HelperFunctions::trimValue($idKeyword);
				}
			}
		}
		if ($companyUserName = SJB_Request::getVar('company_name', false)) {
			if (!empty($companyUserName['like'])) {
				$listingSids = SJB_UserManager::getUserSIDsLikeCompanyName($companyUserName['like']);
				if (empty($listingSids)) {
					unset($_REQUEST['company_name']);
				}
				$usernameLikeSids				= SJB_UserManager::getUserSIDsLikeUsername(SJB_DB::quote($companyUserName['like']));
				$firstLastNameLikeSids			= SJB_UserManager::getUserSIDsLikeFirstNameOrLastName(SJB_DB::quote($companyUserName['like']));
				$_REQUEST['user_sid']['in']		= array_merge(
					!empty($usernameLikeSids) ? $usernameLikeSids : array(''),
					!empty($firstLastNameLikeSids) ? $firstLastNameLikeSids : array('')
				);
			}
		}
	}
}