<?php

class SJB_Admin_Statistics_GeneralStatistics extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('general_statistics');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'search');
		$filter = SJB_Request::getVar('filter', array());
		$template = SJB_Request::getVar('template', 'general_statistics.tpl');
		$errors = array();

		switch ($action) {
			case 'export':
				$period = SJB_Request::getVar('period', false);
				$groupBy = SJB_Request::getVar('group_by', 'day');
				$statistics = SJB_Statistics::getGeneralStatistics($period, $groupBy, $filter);
				if (!empty($statistics['errors']))
					$errors[] = $statistics['errors'];
					
				if (!$errors && $statistics) {
					$type = SJB_Request::getVar('type', 'csv');
					$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();
					$userGroups = SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions();
					SJB_StatisticsExportController::createExportDirectory();
					switch ($type) {
						case 'csv':
							$exportData = SJB_StatisticsExportController::getGeneralExportData($statistics, $userGroups, $listingTypes, $filter);
							$fileName = 'general_statistics.csv';
							SJB_StatisticsExportController::makeCSVExportFile($exportData, $fileName, 'General Statistics');
							SJB_StatisticsExportController::archiveAndSendExportFile('general_statistics', 'csv');
							break;
						case 'xls':
							$exportData = SJB_StatisticsExportController::getGeneralExportData($statistics, $userGroups, $listingTypes, $filter);
							$fileName = 'general_statistics.xls';
							SJB_StatisticsExportController::makeXLSExportFile($exportData, $fileName, 'General Statistics');
							SJB_StatisticsExportController::archiveAndSendExportFile('general_statistics', 'xls');
							break;
					}
					break;
				}
				
			case 'search':
				$search = SJB_Request::getVar('search', false);
				$period = SJB_Request::getVar('period', false);
				$groupBy = SJB_Request::getVar('group_by', 'day');
				$statistics = array();
				if ($search) {
					$i18n = SJB_I18N::getInstance();
					$from = $i18n->getInput('date', $period['from']);
					$to = $i18n->getInput('date', $period['to']);
					if (!empty($period['from']) && !empty($period['to']) && strtotime($from) > strtotime($to)) 
						$errors[] = 'SELECTED_PERIOD_IS_INCORRECT';
					else {
						if (count($filter) > 1) {
							$statistics = SJB_Statistics::getGeneralStatistics($period, $groupBy, $filter);
							if (!empty($statistics['errors']))
								$errors[] = $statistics['errors'];
						}
						else
							$errors[] = 'EMPTY_PARAMETER';
					}
				}
				$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();
				$userGroups = SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions();
				$listPlugins = SJB_PluginManager::getAllPluginsList();
				
				$tp->assign('listPlugins', $listPlugins);
				$tp->assign('countItems', count($statistics)+2);
				$tp->assign('userGroups', $userGroups);
				$tp->assign('listingTypes', $listingTypes);
				$tp->assign('errors', $errors);
				$tp->assign('groupBy', $groupBy);
				$tp->assign('statistics', $statistics);
				$tp->assign('period', $period);
				break;
		}
		$tp->assign('filter', $filter);
		$tp->assign('action', $action);
		$tp->display($template);
	}
}

