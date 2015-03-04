<?php

class SJB_Admin_Statistics_ApplicationsAndViews extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('applications_and_views_reports');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'search');
		$template = SJB_Request::getVar('template', 'applications_and_views.tpl');
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
				$sorting_field = SJB_Request::getVar('sorting_field', '');
				$sorting_order = SJB_Request::getVar('sorting_order', '');
				$statistics = array();
				
				if ($filter) {
					$statistics = SJB_Statistics::getApplicationsAndViewsStatistics($period, $filter, $sorting_field, $sorting_order);
					if (!empty($statistics['errors']))
						$errors = $statistics['errors'];
				}
				else 
					$errors[] = 'EMPTY_PARAMETER';

				if (strstr($filter, 'userGroup_')) {
					$userGroupSID = str_replace('userGroup_', '', $filter);
					if ($userGroups[$userGroupSID]['key'] == 'Employer') {
						$exportProperties['generalColumn'] = 'Company Name';
						$exportProperties['totalView'] = 'Number of Views';
						$exportProperties['totalApply'] = 'Number of Applications Received';
					}
					else {
						$exportProperties['generalColumn'] = $userGroups[$userGroupSID]['caption'].' Name';
						$exportProperties['totalView'] = 'Number of Jobs Viewed';
						$exportProperties['totalApply'] = 'Number of Applications Made';
					}
				}
				elseif ($filter == 'sid') {
					$exportProperties['generalColumn'] = 'Job Title';
					$exportProperties['companyName'] = 'Company Name';
					$exportProperties['totalView'] = 'Number of Views';
					$exportProperties['totalApply'] = 'Number of Applications Made';
				}
				else {
					$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
					$exportProperties['generalColumn'] = $fieldInfo['caption'];
					$exportProperties['totalView'] = 'Number of Job Views';
					$exportProperties['totalApply'] = 'Number of Applications Received';
				}
					
				if (!$errors && $statistics) {
					$type = SJB_Request::getVar('type', 'csv');
					SJB_StatisticsExportController::createExportDirectory();
					switch ($type) {
						case 'csv':
							$exportData = SJB_StatisticsExportController::getAppAndViesExportData($statistics, $exportProperties);
							$fileName = 'app_and_views_statistics.csv';
							SJB_StatisticsExportController::makeCSVExportFile($exportData, $fileName, 'App & Views');
							SJB_StatisticsExportController::archiveAndSendExportFile('app_and_views_statistics', 'csv');
							break;
						case 'xls':
							$exportData = SJB_StatisticsExportController::getAppAndViesExportData($statistics, $exportProperties);
							$fileName = 'app_and_views_statistics.xls';
							SJB_StatisticsExportController::makeXLSExportFile($exportData, $fileName, 'App & Views');
							SJB_StatisticsExportController::archiveAndSendExportFile('app_and_views_statistics', 'xls');
							break;
					}
					break;
				}
			case 'search':
				$search = SJB_Request::getVar('search', false);
				$period = SJB_Request::getVar('period', array());
				$filter = SJB_Request::getVar('filter', false);
				$sorting_field = SJB_Request::getVar('sorting_field', '');
				$sorting_order = SJB_Request::getVar('sorting_order', '');
				$statistics = array();
				
				if ($search) {
					$i18n = SJB_I18N::getInstance();
					$from = $i18n->getInput('date', $period['from']);
					$to = $i18n->getInput('date', $period['to']);
					if (!empty($period['from']) && !empty($period['to']) && strtotime($from) > strtotime($to)) 
						$errors[] = 'SELECTED_PERIOD_IS_INCORRECT';
					else {
						if ($filter) {
							$statistics = SJB_Statistics::getApplicationsAndViewsStatistics($period, $filter, $sorting_field, $sorting_order);
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
					$columnTitle = 'Job Title';
					$tp->assign('link', 'listing');
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
