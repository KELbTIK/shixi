<?php

class SJB_Admin_Statistics_Sales extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('sales_reports');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'search');
		$template = SJB_Request::getVar('template', 'sales.tpl');
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
				$sorting_field = SJB_Request::getVar('sorting_field', 'total');
				$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');
				$statistics = array();
				if ($filter) {
					$statistics = SJB_Statistics::getSalesStatistics($period, $filter, $sorting_field, $sorting_order);
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
					$exportProperties['generalColumn'] = $columnTitle;
				}
				elseif ($filter == 'sid') {
					$columnTitle = 'Product Name';
					$exportProperties['generalColumn'] = 'Product Name';
					$exportProperties['product_type'] = 'Product Type';
					$tp->assign('link', 'product');
				}
				else {
					$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
					$exportProperties['generalColumn'] = $fieldInfo['caption'];
				}
				$exportProperties['units_sold'] = 'Units Sold';
				$exportProperties['total'] = 'Income';
				$exportProperties['percent'] = '%';
					
				if (!$errors && $statistics) {
					$type = SJB_Request::getVar('type', 'csv');
					SJB_StatisticsExportController::createExportDirectory();
					switch ($type) {
						case 'csv':
							$exportData = SJB_StatisticsExportController::getSalesExportData($statistics, $exportProperties);
							$fileName = 'sales.csv';
							SJB_StatisticsExportController::makeCSVExportFile($exportData, $fileName, 'App & Views');
							SJB_StatisticsExportController::archiveAndSendExportFile('sales', 'csv');
							break;
						case 'xls':
							$exportData = SJB_StatisticsExportController::getSalesExportData($statistics, $exportProperties);
							$fileName = 'sales.xls';
							SJB_StatisticsExportController::makeXLSExportFile($exportData, $fileName, 'App & Views');
							SJB_StatisticsExportController::archiveAndSendExportFile('sales', 'xls');
							break;
					}
					break;
				}
			case 'search':
				$search = SJB_Request::getVar('search', false);
				$period = SJB_Request::getVar('period', array());
				$filter = SJB_Request::getVar('filter', false);
				$sorting_field = SJB_Request::getVar('sorting_field', 'total');
				$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');
				$statistics = array();
				if($search) {
					if (!empty($period['from']) && !empty($period['to']) && strtotime($period['from']) > strtotime($period['to'])) 
						$errors[] = 'SELECTED_PERIOD_IS_INCORRECT';
					else {
						if ($filter) {
							$statistics = SJB_Statistics::getSalesStatistics($period, $filter, $sorting_field, $sorting_order);
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
				}
				else if ($filter == 'sid') {
					$columnTitle = 'Product Name';
					$tp->assign('link', 'product');
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
				$tp->assign('search', $search);
				$tp->assign('filter', $filter);
				$tp->assign('columnTitle', $columnTitle);
				$tp->assign('period', $period);
				$tp->assign('periodView', $periodView);
				$tp->assign('statistics', $statistics);
				$tp->assign('countResult', count($statistics));
				$tp->assign('sorting_field', $sorting_field);
				$tp->assign('sorting_order', $sorting_order);
				break;
		}
		$tp->assign('userGroups', $userGroups);
		$tp->assign('errors', $errors);
		$tp->assign('action', $action);
		$tp->display($template);
	}
}
