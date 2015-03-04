<?php

class SJB_Classifieds_Browsecompany extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('open_search_by_company_form');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('display_template');
		$page = 1;
		$searchId = strip_tags(SJB_Request::getVar('searchId', time()));
		if (!empty($_REQUEST["page"]))
			$page = intval($_REQUEST["page"]);
		$items_per_page = SJB_Request::getVar('companies_per_page', false);
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($_REQUEST["listing_type_id"]);
		$alphabets = SJB_AlphabetManager::getAlphabetsForDisplay();
		$abArr = array();
		foreach ($alphabets as $alphabet)
			$abArr[] = explode(' ', $alphabet['value']);

		$action = SJB_Request::getVar('action', 'search_form');
		if (SJB_Request::getVar('first_char')) {
			$action = 'search';
			$_REQUEST['CompanyName']['first_char_like'] = SJB_Request::getVar('first_char');
		}
		elseif (!isset($_REQUEST['CompanyName']) || $_REQUEST['CompanyName']['like'] == '') {
			$_REQUEST['CompanyName']['not_empty'] = true;
		}
		$userGroupSid = SJB_UserGroupManager::getUserGroupSIDByID('Employer');
		$userGroupFields = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($userGroupSid);
		foreach ($userGroupFields as $key => $userGroupField) {
			if ($userGroupField['type'] == 'location') {
				$userGroupFields[$key]['fields'] = array();
				$userGroupFields[$key]['fields'][$userGroupField['sid']] = $userGroupField;
			}
		}
		$user = new SJB_User(array(), $userGroupSid);
		$_REQUEST['active']['equal'] = 1;
		$search_form_builder = new SJB_SearchFormBuilder($user);
		$criteria_saver = new SJB_UserCriteriaSaver($searchId);
		$criteria_saver->setSessionForOrderInfo($_REQUEST);
		if (isset($_REQUEST['searchId'])) {
			$action = 'search';
			$criteria =  $criteria_saver->getCriteria();
			if (!empty($_REQUEST['sorting_field'])) {
				unset($criteria['sorting_field']);
			} else {
				$orderInfo = $criteria_saver->getOrderInfo();
				if (!empty($orderInfo['sorting_order'])) {
					$criteria['sorting_order'] = $orderInfo['sorting_order'];
				}
			}
			$_REQUEST = array_merge($_REQUEST, $criteria);
			if (!$items_per_page)
				$items_per_page = $criteria_saver->listings_per_page;
		}

		$items_per_page = $items_per_page ? $items_per_page : 10;
		$criteria = $search_form_builder->extractCriteriaFromRequestData(array_merge($_REQUEST, array('username' => array('not_equal' => 'jobg8'))), $user);
		if ($items_per_page) {
			$criteria_saver->setSessionForListingsPerPage($items_per_page);
		}
		$search_form_builder->setCriteria($criteria);
		$search_form_builder->registerTags($tp);

		$form_fields = $search_form_builder->getFormFieldsInfo();
		$tp->assign('form_fields', $form_fields);
		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$tp->assign('METADATA',
			array(
				'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
			)
		);

		$tp->assign('userGroupFields', $userGroupFields);
		$tp->assign('action', $action);
		$tp->assign('alphabets', $abArr);

		if ($action == 'search') {
			$sorting_field = SJB_Request::getVar('sorting_field', false);
			$sorting_order = SJB_Request::getVar('sorting_order', false);
			if (isset($_REQUEST['searchId']) && !$sorting_field) {
				$order_info = $criteria_saver->order_info;
				if ($order_info) {
					$sorting_field = $order_info['sorting_field'];
					$sorting_order = $order_info['sorting_order'];
				}
			}
			if (!$sorting_field) {
				$sorting_field = 'CompanyName';
				$sorting_order = 'ASC';
			}

			$inner_join = array();
			if ($sorting_field == 'number_of_jobs') {
				if (SJB_ListingTypeManager::getWaitApproveSettingByListingType($listing_type_sid) == 1) {
					$count = "sum( if( `listings`.`status` = 'approved', `listings`.`active`, 0 ) )";
				} else {
					$count = "sum(`listings`.`active`)";
				}
				$inner_join = array(
					'listings' => array(
					'sort_field' 	=> $count,
					'noPresix'		=> true,
					'join_field'	=> 'user_sid',
					'join_field2' 	=> 'sid',
					'join' 			=> 'LEFT JOIN',
					'groupBy' 		=> '`users`.`sid`'
				));
			} elseif ($sorting_field == 'Location_State') {
				$inner_join = array(
					'states' => array(
					'sort_field' 	=> 'state_name',
					'noPresix'		=> true,
					'join_field'	=> 'sid',
					'join_field2' 	=> 'Location_State',
					'join' 			=> 'LEFT JOIN'
				));
			}

			$searcher = new SJB_UserSearcher(false, $sorting_field, $sorting_order, $inner_join, array('limit' => ($page - 1) * $items_per_page, 'num_rows' => $items_per_page));
			$found_users = array();
			$found_users_sids = array();
			$found_users_by_criteria = $searcher->getObjectsByCriteria($criteria, null, array(), true);
			$foundObjectSIDs = $searcher->getFoundObjectSIDs();

			// display search form
			$tp->display($template);

			$criteria_saver->setSession($_REQUEST, $foundObjectSIDs);
			if (count($foundObjectSIDs) > 0) {
				$listingType = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sid);
				$countListings = SJB_ListingDBManager::getActiveAndApproveJobsNumberForUsers($foundObjectSIDs, $listingType);
			}
			foreach ($found_users_by_criteria as $id => $user) {
				$listingsNumber = isset($countListings[$id]) ? $countListings[$id] : 0;
				$user->addProperty(array('id' => 'countListings', 'type' => 'string', 'value' => $listingsNumber));
				if ($user->getProperty('CompanyName')) {
					$found_users_sids[$user->getSID()] = $user->getSID();
					$found_users[$id] = $user;
				}
			}

			$usersCount = $searcher->getAffectedRows();

			$form_collection = new SJB_FormCollection($found_users);
			$form_collection->registerTags($tp);
			$pages = array();

			for ($i = $page - 3; $i < $page + 3; $i++) {
				if ($i > 0)
					$pages[] = $i;
				if ($i * $items_per_page > $usersCount)
					break;
			}

			$totalPages = ceil($usersCount / $items_per_page);
			if (empty($totalPages))
				$totalPages = 1;

			if (array_search(1, $pages) === false)
				array_unshift($pages, 1);
			if (array_search($totalPages, $pages) === false)
				array_push($pages, $totalPages);

			$tp->assign("sorting_order", $sorting_order);
			$tp->assign("sorting_field", $sorting_field);
			$tp->assign("found_users_sids", $found_users_sids);
			$tp->assign("companies_per_page", $items_per_page);
			$tp->assign("searchId", $searchId);
			$tp->assign("usersCount", $usersCount);
			$tp->assign("current_page", $page);
			$tp->assign("pages_number", $totalPages);
			$tp->display('search_result_company.tpl');
		}
		else {
			// display search form
			$tp->display($template);
		}
	}
}
