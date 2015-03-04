<?php

class SJB_Admin_Statistics_GuestAlerts extends SJB_Function
{
	/**
	 * @var SJB_TemplateProcessor
	 */
	public $tp;

	public function isAccessible()
	{
		$this->setPermissionLabel('guest_alerts_reports');
		return parent::isAccessible();
	}

	public function execute()
	{
		$this->tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'search');
		$template = SJB_Request::getVar('template', 'guest_alerts.tpl');
		$errors = array();

		try {
			switch ($action) {
				case 'export':
					$this->export();
					break;
				case 'search':
					$this->search();
					break;
			}
		} catch (Exception $e) {
			array_push($errors, $e->getMessage());
		}

		$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();

		$this->tp->assign('listingTypes', $listingTypes);
		$this->tp->assign('errors', $errors);
		$this->tp->assign('action', $action);
		$this->tp->display($template);
	}

	public function search()
	{
		$search = SJB_Request::getVar('search', false);
		$period = SJB_Request::getVar('period', array());
		$filter = SJB_Request::getVar('filter', false);
		$listingTypeID = SJB_Request::getVar('listingTypeID', false);
		$sorting_field = SJB_Request::getVar('sorting_field', 'total');
		$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');
		$i18n = SJB_I18N::getInstance();

		$statistics = array();
		if ($search) {
			$from = $i18n->getInput('date', $period['from']);
			$to = $i18n->getInput('date', $period['to']);
			if (!empty($period['from']) && !empty($period['to']) && strtotime($from) > strtotime($to)) {
				throw new Exception('SELECTED_PERIOD_IS_INCORRECT');
			}

			if (empty($filter)) {
				throw new Exception('EMPTY_PARAMETER');
			}

			$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
			$statistics = SJB_Statistics::getGuestAlertsStatistics($period, $listingTypeSID, $filter, $sorting_field, $sorting_order);
		}

		$columnTitle = $i18n->gettext('Backend', 'Guest Email');

		$periodView = array();
		foreach ($period as $key => $value) {
			$periodView[$key] = $i18n->getInput('date', $period[$key]);
		}
		$this->tp->assign('filter', $filter);
		$this->tp->assign('search', $search);
		$this->tp->assign('columnTitle', $columnTitle);
		$this->tp->assign('listingTypeID', $listingTypeID);
		$this->tp->assign('period', $period);
		$this->tp->assign('periodView', $periodView);
		$this->tp->assign('statistics', $statistics);
		$this->tp->assign('countResult', count($statistics));
		$this->tp->assign('sorting_field', $sorting_field);
		$this->tp->assign('sorting_order', $sorting_order);
	}

	public function export()
	{
		$period = SJB_Request::getVar('period', array());
		$statisticEvent = SJB_Request::getVar('filter', false);
		$listingTypeID = SJB_Request::getVar('listingTypeID', false);
		$sorting_field = SJB_Request::getVar('sorting_field', 'total');
		$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');

		if (empty($statisticEvent)) {
			throw new Exception('EMPTY_PARAMETER');
		}

		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
		$statistics = SJB_Statistics::getGuestAlertsStatistics($period, $listingTypeSID, $statisticEvent, $sorting_field, $sorting_order);

		if (empty($statistics)) {
			throw new Exception('NOTHING_TO_EXPORT');
		}

		SJB_StatisticsExportController::createExportDirectory();
		$i18N = SJB_I18N::getInstance();
		$exportProperties['title'] = $i18N->gettext('Backend', 'Guest Email');
		$exportProperties['total'] = $i18N->gettext('Backend', 'Total');
		$exportProperties['percent'] = '%';

		$type = SJB_Request::getVar('type', 'csv');
		$fileName = 'guest_' . strtolower($listingTypeID . '_' . $statisticEvent) . '_statistics';
		$exportData = SJB_StatisticsExportController::getGuestAlertsExportData($statistics);
		switch ($type) {
			case 'csv':
				$ext = 'csv';
				SJB_StatisticsExportController::makeCSVExportFile($exportData, $fileName . '.' . $ext, "Guest {$listingTypeID} Alerts Statistics");
				break;

			default:
			case 'xls':
				$ext = 'xls';
				SJB_StatisticsExportController::makeXLSExportFile($exportData, $fileName . '.' . $ext, "Guest {$listingTypeID} Alerts Statistics");
				break;
		}
		SJB_StatisticsExportController::archiveAndSendExportFile($fileName, $ext);
	}
}
