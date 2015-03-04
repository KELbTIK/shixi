<?php

class SJB_Admin_Miscellaneous_GeographicData extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_listing_types_and_specific_listing_fields', 'edit_zipcode_database'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$location = new SJB_Location($_REQUEST);

		$action_name = SJB_Request::getVar('action_name', false);
		$action = SJB_Request::getVar('action', false);
		$action = $action_name ? $action_name : $action;
		$paginator = new SJB_GeographicDataPagination();
		$search = '';
		$params  = array();
		$locationAdded = false;

		switch ($action) {
			case 'add':
				if ($location->isDataValid($errors)) {
					if (SJB_LocationManager::saveLocation($location)) {
						$location = new SJB_Location();
						$locationAdded = true;
					}
					else
						$errors['Name'] = 'NOT_UNIQUE_VALUE';
				}
				break;

			case 'delete':
				$location_sid = SJB_Request::getVar('location_sid', false);
				if (!$location_sid) {
					$locations_sids = SJB_Request::getVar('locations', false);
					if ($locations_sids)
						foreach ($locations_sids as $l_sid => $value)
							SJB_LocationManager::deleteLocationBySID($l_sid);
				}
				else
					SJB_LocationManager::deleteLocationBySID($location_sid);

				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/geographic-data/');
				break;

			case 'clear_data':
				SJB_LocationManager::deleteAllLocations();
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/geographic-data/');
				break;

			case 'search':
				$searchParams = SJB_Request::getVar('search', false);
				$search .= 'WHERE 1 ';
				if (!empty($searchParams['name'])) {
					$search .= "AND l.`name` LIKE ?s";
					$params[] = "%{$searchParams['name']}%";
				}
				if (!empty($searchParams['longitude'])) {
					$search .= " AND l.`longitude` LIKE ?s";
					$params[] = "%{$searchParams['longitude']}%";
				}
				if (!empty($searchParams['latitude'])) {
					$search .= " AND l.`latitude` LIKE ?s";
					$params[] = "%{$searchParams['latitude']}%";
				}
				if (!empty($searchParams['city'])) {
					$search .= " AND l.`city` LIKE ?s";
					$params[] = "%{$searchParams['city']}%";
				}
				if (!empty($searchParams['state'])) {
					$search .= " AND l.`state` LIKE ?s";
					$params[] = "%{$searchParams['state']}%";
				}
				if (!empty($searchParams['state_code'])) {
					$search .= " AND l.`state_code` LIKE ?s";
					$params[] = "%{$searchParams['state_code']}%";
				}
				if (!empty($searchParams['country_sid'])) {
					$search .= " AND l.`country_sid` = ?n";
					$params[] = "{$searchParams['country_sid']}";
				}
				$tp->assign('search', $searchParams);
				$searchQuery['action'] = 'search';
				$searchQuery['search'] = $searchParams;
				$paginator->setUniqueUrlParam(trim(http_build_query($searchQuery)));
				break;
		}

		$countries = SJB_CountriesManager::getAllCountriesCodesAndNames();
		$location_info = $location->getInfo();
		$paginator->setItemsCount(SJB_LocationManager::getLocationNumber($search, $params));
		$location_collection = SJB_LocationManager::getLocationsInfoWithLimit(($paginator->currentPage - 1) * $paginator->itemsPerPage, $paginator->itemsPerPage, $search, $paginator->sortingField, $paginator->sortingOrder, $params);
		unset($_REQUEST['zip_codes_per_page']);

		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign('locationAdded', $locationAdded);
		$tp->assign('countries', $countries);
		$tp->assign('query', http_build_query($_REQUEST));
		$tp->assign('errors', $errors);
		$tp->assign('location_info', $location_info);
		$tp->assign('location_collection', $location_collection);
		$tp->display('geographic_data.tpl');
	}
}