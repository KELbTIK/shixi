<?php

class SJB_Admin_Statistics_ListingsStatistics extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('listings_reports');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'search');
		$template = SJB_Request::getVar('template', 'listings_statistics.tpl');
		$errors = array();

		$userGroups = SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions();
		foreach ($userGroups as $key => $userGroup) {
			unset($userGroups[$key]);
			$userGroups[$userGroup['id']] = $userGroup;
		}

		switch ($action) {
			case 'export':
				$period = SJB_Request::getVar('period', array());
				$filter = SJB_Request::getVar('filter', false);
				$listingTypeSID = SJB_Request::getVar('listingTypeSID', false);
				$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSID);
				$sorting_field = SJB_Request::getVar('sorting_field', 'total');
				$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');
				$statistics = array();
				if ($filter) {
					$statistics = SJB_Statistics::getListingsStatistics($period, $listingTypeSID, $filter, $sorting_field, $sorting_order);
					if (!empty($statistics['errors']))
						$errors = $statistics['errors'];
				}
				else 
					$errors[] = 'EMPTY_PARAMETER';
					
				$columnTitle = '';
				if (strstr($filter, 'userGroup_')) {
					$userGroupSID = str_replace('userGroup_', '', $filter);
					if ($userGroups[$userGroupSID]['key'] == 'Employer')
						$columnTitle = 'Company Name';
					else
						$columnTitle = $userGroups[$userGroupSID]['caption'].' Name';
				}
				else {
					$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
					$columnTitle = $fieldInfo['caption'];
				}
					
				if (!$errors && $statistics) {
					$type = SJB_Request::getVar('type', 'csv');
					$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();
					SJB_StatisticsExportController::createExportDirectory();
					$exportProperties['title'] = $columnTitle;
					$exportProperties['regular'] = '';
					if ($listingTypeID == 'Job') 
						$exportProperties['featured'] = '';
					$exportProperties['priority'] = '';
					$exportProperties['total'] = 'Total';
					$exportProperties['percent'] = '%';
					foreach ($listingTypes as $listingType) {
						if ($listingType['id'] == $listingTypeSID) {
							switch ($listingType['key']) {
								case 'Job':
									$featuredTitle = "Number of Featured {$listingType['key']}s Posted";
									$exportProperties['featured'] = $featuredTitle;
								case 'Resume':
									$regularTitle = "Number of Regular {$listingType['key']}s Posted";
									$exportProperties['regular'] = $regularTitle;
									$priorityTitle = "Number of Priority {$listingType['key']}s Posted";
									$exportProperties['priority'] = $priorityTitle;
									break;
								default:
									$regularTitle = 'Number of Regular "'.$listingType['caption'].'" Listings Posted';
									$exportProperties['regular'] = $regularTitle;
									$priorityTitle = 'Number of Priority "'.$listingType['caption'].'" Listings Posted';
									$exportProperties['priority'] = $priorityTitle;
									break;
							}
						}
					}
					switch ($type) {
						case 'csv':
							$exportData = SJB_StatisticsExportController::getListingExportData($statistics, $listingTypeID);
							$fileName = strtolower($listingTypeID).'_statistics.csv';
							SJB_StatisticsExportController::makeCSVExportFile($exportData, $fileName, "{$listingTypeID} Statistics");
							SJB_StatisticsExportController::archiveAndSendExportFile(strtolower($listingTypeID).'_statistics', 'csv');
							break;
						case 'xls':
							$exportData = SJB_StatisticsExportController::getListingExportData($statistics, $listingTypeID);
							$fileName = strtolower($listingTypeID).'_statistics.xls';
							SJB_StatisticsExportController::makeXLSExportFile($exportData, $fileName, "{$listingTypeID} Statistics");
							SJB_StatisticsExportController::archiveAndSendExportFile(strtolower($listingTypeID).'_statistics', 'xls');
							break;
					}
					break;
				}
			case 'search':
				$search = SJB_Request::getVar('search', false);
				$period = SJB_Request::getVar('period', array());
				$filter = SJB_Request::getVar('filter', false);
				$listingTypeSID = SJB_Request::getVar('listingTypeSID', false);
				$sorting_field = SJB_Request::getVar('sorting_field', 'total');
				$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');
				$statistics = array();
				if ($search) {
					$i18n = SJB_I18N::getInstance();
					$from = $i18n->getInput('date', $period['from']);
					$to = $i18n->getInput('date', $period['to']);
					if (!empty($period['from']) && !empty($period['to']) && strtotime($from) > strtotime($to)) 
						$errors[] = 'SELECTED_PERIOD_IS_INCORRECT';
					else {
						if ($filter) {
							$statistics = SJB_Statistics::getListingsStatistics($period, $listingTypeSID, $filter, $sorting_field, $sorting_order);
							if (!empty($statistics['errors']))
								$errors = $statistics['errors'];
						}
						else 
							$errors[] = 'EMPTY_PARAMETER';
					}
				}

				$columnTitle = '';
				if (strstr($filter, 'userGroup_')) {
					$userGroupSID = str_replace('userGroup_', '', $filter);
					if ($userGroups[$userGroupSID]['key'] == 'Employer') {
						$columnTitle = 'Company Name';
					} else {
						$columnTitle = $userGroups[$userGroupSID]['caption'].' Name';
					}
					$tp->assign('link', 'user');
				} else {
					if (in_array($filter, array('Location_Country', 'Location_State', 'Location_City'))) {
						$fieldInfo = SJB_ListingFieldDBManager::getLocationFieldsInfoById($filter);
					} else {
						$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
					}
					$columnTitle = $fieldInfo['caption'];
				}
				
				$i18n = SJB_I18N::getInstance();
				$periodView = array();
				foreach ($period as $key => $value) {
					$periodView[$key] = $i18n->getInput('date', $period[$key]);
				}
				$tp->assign('filter', $filter);
				$tp->assign('search', $search);
				$tp->assign('columnTitle', $columnTitle);
				$tp->assign('listingTypeSID', $listingTypeSID);
				$tp->assign('period', $period);
				$tp->assign('periodView', $periodView);
				$tp->assign('statistics', $statistics);
				$tp->assign('countResult', count($statistics));
				$tp->assign('sorting_field', $sorting_field);
				$tp->assign('sorting_order', $sorting_order);
				break;
		}
		$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();
		$products = SJB_ProductsManager::getAllProductsInfo();
		$acl = SJB_Acl::getInstance();
		foreach ($listingTypes as $key => $listingType) {
			$userGroup = array();
			foreach ($products as $productInfo) {
				if ($acl->isAllowed('post_'.strtolower($listingType['key']), $productInfo['sid'], 'product') && !in_array($productInfo['user_group_sid'], $userGroup)) 
					$userGroup[] = $productInfo['user_group_sid'];
			}
			$listingTypes[$listingType['id']] = $listingType;
			$listingTypes[$listingType['id']]['userGroups'] = $userGroup;
			unset($listingTypes[$key]);
		}

		$tp->assign('userGroups', $userGroups);
		$tp->assign('listingTypes', $listingTypes);
		$tp->assign('errors', $errors);
		$tp->assign('action', $action);
		$tp->display($template);
	}
}
