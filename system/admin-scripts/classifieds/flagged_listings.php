<?php

class SJB_Admin_Classifieds_FlaggedListings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_flagged_listings');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$listingTypeID = SJB_Request::getVar('listing_type_id');
		$listingTypeSID = SJB_Request::getVar('listing_type');

		if ($listingTypeID !== null) {
			$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
		}

		// SET PAGINATION AND SORTING VALUES
		$restore = SJB_Request::getVar('restore', false);
		$paginator = new SJB_FlaggedListingsPagination();

		// FILTERS
		$filters = array();
		$filters['title'] = SJB_Request::getVar('filter_title');
		$filters['username'] = SJB_Request::getVar('filter_user');
		$filters['flag'] = SJB_Request::getVar('filter_flag');

		// check session for pagination settings
		$sessionFlaggedSettings = !is_null(SJB_Session::getValue('flagged_settings')) ? SJB_Session::getValue('flagged_settings') : false;
		if ($sessionFlaggedSettings !== false) {
			if (!$restore) {
				SJB_Session::setValue('flagged_settings', array('filters' => $filters));
			} else {
				if (!$listingTypeSID && !empty($sessionFlaggedSettings['listing_type_sid'])) {
					$listingTypeSID = $sessionFlaggedSettings['listing_type_sid'];
				}
				$filters = $sessionFlaggedSettings['filters'];
			}
		} else {
			SJB_Session::setValue('flagged_settings', array('filters' => $filters));
		}

		// DEFAULT SORTING

		// resolve flag to it text value for search
		$filterFlag = $filters['flag'];
		if (!empty($filterFlag) && is_numeric($filterFlag)) {
			$result = SJB_DB::query('SELECT * FROM `flag_listing_settings` WHERE `sid` = ?n LIMIT 1', $filterFlag);
			if (!empty($result)) {
				$filters['flag_reason'] = $result[0]['value'];
			}
		}

		//////////////////////  ACTIONS
		$action = SJB_Request::getVar('action_name');
		$flagSIDs = SJB_Request::getVar('flagged');

		if (!empty($flagSIDs)) {
			switch ($action) {
				case 'remove':
					foreach ($flagSIDs as $sid => $val)
						SJB_ListingManager::removeFlagBySID($sid);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flagged-listings/?page=1');
					break;
				case 'deactivate':
					foreach ($flagSIDs as $sid => $val)
						SJB_ListingManager::deactivateListingByFlagSID($sid);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flagged-listings/?page=1');
					break;
				case 'delete':
					foreach ($flagSIDs as $sid => $val)
						SJB_ListingManager::deleteListingByFlagSID($sid);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flagged-listings/?page=1');
					break;
			}
		}

		//////////////////////// OUTPUT
		$allListingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();

		$allFlags = SJB_ListingManager::getAllFlags();

		$countFlaggedListings = SJB_ListingManager::getFlagsNumberByListingTypeSID($listingTypeSID, $filters);
		$paginator->setItemsCount($countFlaggedListings);
		$flaggedListings = SJB_ListingManager::getFlaggedListings($listingTypeSID, $paginator->currentPage, $paginator->itemsPerPage, $paginator->sortingField, $paginator->sortingOrder, $filters);
		if (empty($flaggedListings) && $paginator->currentPage != 1) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flagged-listings/?page=1');
		}

		foreach ($flaggedListings as $key => $val) {
			$listingInfo = SJB_ListingManager::getListingInfoBySID($val['listing_sid']);
			$listingUser = SJB_UserManager::getUserInfoBySID($listingInfo['user_sid']);
			$flaggedUser = SJB_UserManager::getUserInfoBySID($val['user_sid']);
			$flaggedListings[$key]['listing_info'] = $listingInfo;
			$flaggedListings[$key]['user_info'] = $listingUser;
			$flaggedListings[$key]['flagged_user'] = $flaggedUser;
		}

		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign('listing_types', $allListingTypes);
		$tp->assign('listings', $flaggedListings);
		$tp->assign('listing_type_sid', $listingTypeSID);
		$tp->assign('all_flags', $allFlags);
		$tp->assign('filters', $filters);

		$tp->display('flagged_listings.tpl');
	}
}
