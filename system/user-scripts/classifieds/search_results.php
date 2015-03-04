<?php

class SJB_Classifieds_SearchResults extends SJB_Function
{
	public function isAccessible()
	{
		$listingTypeID = SJB_Array::get($this->params, 'listing_type_id');
		if ($listingTypeID) {
			$permissionLabel = 'view_' . strtolower($listingTypeID) . '_search_results';
			$this->setPermissionLabel($permissionLabel);
		}
		return parent::isAccessible() && SJB_System::isUserAccessThisPage();
	}

	public function execute()
	{
		$this->redirectToListingByKeywords();
		// SEO friendly URL for company profile
		$m = array();
		$isCompanyProfilePage = false;
		if (preg_match('#/company/([0-9]+)/.*#', SJB_Navigator::getURI(), $m)) {
			$isCompanyProfilePage = true;
			$params = SJB_FixedUrlParamProvider::getParams($_REQUEST);
			if (!empty($params)) {
				$aliasUsername = SJB_UserManager::getUserNameByUserSID($m[1]);
				if (!empty($aliasUsername)) {
					$_REQUEST['username']['equal'] = $aliasUsername;
					$_REQUEST['anonymous']['equal'] = 0;
				}
			}
		}
		if (!empty($_REQUEST['username']['equal']) && is_int($_REQUEST['username']['equal'])) {
			$aliasUsername = SJB_UserManager::getUserNameByUserSID(intval($_REQUEST['username']['equal']));
			if (!empty($aliasUsername))
				$_REQUEST['username']['equal'] = $aliasUsername;
		}

		$listingTypeId = SJB_Request::getVar('listing_type_id', 0);
		if (!$listingTypeId) {
			$listingTypeId = isset($_REQUEST['listing_type']['equal']) ? $_REQUEST['listing_type']['equal'] : SJB_Session::getValue('listing_type_id');
		}
		if ($listingTypeId) {
			$_REQUEST['listing_type']['equal'] = $listingTypeId;
		}
		$action = SJB_Request::getVar('action', 'search');

		//XSS defense
		$searchId = SJB_Request::getVar('searchId', false);
		if ($searchId && !is_numeric($searchId)) {
			$_REQUEST['searchId'] = false;
		}
		$request = $_REQUEST;
		if (SJB_System::getSettingByName('turn_on_refine_search_' . $listingTypeId)) {
			switch ($action) {
				case 'refine':
					$searchID = SJB_Request::getVar('searchId', false);
					unset($request['searchId']);
					$criteria_saver = new SJB_ListingCriteriaSaver($searchID);
					$request = SJB_RefineSearch::mergeCriteria($criteria_saver->getCriteria(), $request);
					break;
				case 'undo':
					$param = SJB_Request::getVar('param', false);
					$field_type = SJB_Request::getVar('type', false);
					$value = SJB_Request::getVar('value', false);
					if ($param && $field_type && $value) {
						$searchID = SJB_Request::getVar('searchId', false);
						unset($request['searchId']);
						$criteria_saver = new SJB_ListingCriteriaSaver($searchID);
						$criteria = $criteria_saver->criteria;
						if (isset($criteria[$param][$field_type])) {
							switch ($field_type) {
								case 'geo':
									if ($criteria[$param][$field_type]['location'] == $value)
										unset($criteria[$param]);
									break;
								case 'monetary':
									if ($criteria[$param][$field_type]['not_less'] == $value)
										$criteria[$param][$field_type]['not_less'] = "";
									if ($criteria[$param][$field_type]['not_more'] == $value)
										$criteria[$param][$field_type]['not_more'] = "";
									break;
								case 'tree':
									// search params incoming as string, where params separated by ','
									// we need to undo one of them
									$params = explode(',', $criteria[$param][$field_type]);
									$params = array_flip($params);
									unset($params[$value]);
									$params = array_flip($params);
									$criteria[$param][$field_type] = implode(',', $params);
									break;
								default:
									if (is_array($criteria[$param][$field_type])) {
										foreach ($criteria[$param][$field_type] as $key => $val) {
											if ($val == $value)
												unset($criteria[$param][$field_type][$key]);
										}
									}
									else {
										unset($criteria[$param]);
									}
									break;
							}
						}
						$criteria['default_sorting_field'] = $request['default_sorting_field'];
						$criteria['default_sorting_order'] = $request['default_sorting_order'];
						$criteria['default_listings_per_page'] = $request['default_listings_per_page'];
						$criteria['results_template'] = $request['results_template'];

						$request = array_merge($criteria, $request);
					}
					break;
			}
		}

		$searchResultsTP = new SJB_SearchResultsTP($request, $listingTypeId, false, true);
		$searchResultsTP->usePriority(true);
		$template = SJB_Request::getVar("results_template", "search_results.tpl");
		$allowViewContactInfo = false;
		if (!empty($_REQUEST['username']['equal'])) {
			$pageID = 'contact_info';
			$username = $_REQUEST['username']['equal'];
			if (SJB_UserManager::isUserLoggedIn()) {
				$current_user = SJB_UserManager::getCurrentUser();
				if (SJB_ContractManager::isPageViewed($current_user->getSID(), $pageID, $username) || ($this->acl->isAllowed('view_' . $listingTypeId . '_contact_info') && in_array($this->acl->getPermissionParams('view_' . $listingTypeId . '_contact_info'), array('', '0'))))
					$allowViewContactInfo = true;
				elseif ($this->acl->isAllowed('view_' . $listingTypeId . '_contact_info')) {
					$viewContactInfo['count_views'] = 0;
					$contractIDs = $current_user->getContractID();
					$numberOfContactViewed = SJB_ContractManager::getNumbeOfPagesViewed($current_user->getSID(), $contractIDs, $pageID);
					foreach ($contractIDs as $contractID) {
						if ($this->acl->getPermissionParams('view_' . $listingTypeId . '_contact_info', $contractID, 'contract')) {
							$params = $this->acl->getPermissionParams('view_' . $listingTypeId . '_contact_info', $contractID, 'contract');
							if (isset($viewContactInfo['count_views'])) {
								$viewContactInfo['count_views'] += $params;
								$viewContactInfo['contract_id'] = $contractID;
							}
						}
					}
					if ($viewContactInfo && $viewContactInfo['count_views'] > $numberOfContactViewed) {
						$allowViewContactInfo = true;
						SJB_ContractManager::addViewPage($current_user->getSID(), $pageID, $username, $viewContactInfo['contract_id']);
					}
				}
			}
			elseif ($this->acl->isAllowed('view_' . $listingTypeId . '_contact_info')) {
				$allowViewContactInfo = true;
			}
		}

		$tp = $searchResultsTP->getChargedTemplateProcessor();
		SJB_Statistics::addSearchStatistics($searchResultsTP->getListingSidCollectionForCurrentPage(), $listingTypeId);
		$userForm = null;
		if ($isCompanyProfilePage) {
			$user = SJB_UserManager::getObjectBySID(intval($m[1]));
			$userForm = new SJB_Form($user);
			$userForm->registerTags($tp);
		}

		$errors = array();
		if (!empty($searchResultsTP->pluginErrors)) {
			foreach ($searchResultsTP->pluginErrors as $err)
				$errors[] = $err;
		}

		$tp->assign('errors', $errors);

		$tp->assign('is_company_profile_page', $isCompanyProfilePage);
		$tp->assign("listing_type_id", $listingTypeId);
		$tp->assign('allowViewContactInfo', $allowViewContactInfo);
		if ($userForm) {
			$tp->assign('form_fields', $userForm->getFormFieldsInfo());
		}
		$tp->display($template);
	}

	private function redirectToListingByKeywords()
	{
		$arrayKeywords = SJB_Request::getVar('keywords');
		if (empty ($arrayKeywords))
			return;
		$keywords = array_pop($arrayKeywords);
		if (empty ($keywords))
			return;
		$id_listing = intval($keywords);
		if ($id_listing == 0)
			return;
		$listing_info = SJB_ListingDBManager::getListingInfoBySID($id_listing);
		if (empty ($listing_info))
			return;

		$type = SJB_ListingTypeDBManager::getListingTypeInfoBySID($listing_info['listing_type_sid']);
		$listing_type = $type['id'];
		$arrayType = SJB_Request::getVar('listing_type');

		if (empty ($arrayType))
			return;
		$expected_type = array_pop($arrayType);

		if ($expected_type != $listing_type)
			return;

		$id = $listing_info['sid'];
		$name_listing = SJB_HelperFunctions::slugify($listing_info['Title']);
		$type = strtolower($listing_type);
		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/display-{$type}/{$id}/{$name_listing}.html");
	}
}
