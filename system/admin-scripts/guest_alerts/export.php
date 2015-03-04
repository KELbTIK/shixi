<?php

class SJB_Admin_GuestAlerts_Export extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_guest_email_alerts');
		return parent::isAccessible();
	}

	public function execute()
	{
		$guestAlert = new SJB_GuestAlert(array());
		$guestAlert->addSubscriptionDateProperty();
		$guestAlert->addStatusProperty();

		$search_form_builder = new SJB_SearchFormBuilder($guestAlert);

		$criteria_saver = new SJB_GuestAlertCriteriaSaver();
		$criteria = $search_form_builder->extractCriteriaFromRequestData($criteria_saver->getCriteria(), $guestAlert);
		$sortingField = SJB_Request::getVar('sorting_field', 'subscription_date');
		$sortingOrder = SJB_Request::getVar('sorting_order', 'DESC');
		$searcher = new SJB_GuestAlertSearcher(false, $sortingField, $sortingOrder);
		$foundGuestAlerts = $searcher->getObjectsSIDsByCriteria($criteria);

		foreach ($foundGuestAlerts as $id => $guestAlertSID) {
			$foundGuestAlerts[$id] = SJB_GuestAlertManager::getGuestAlertInfoBySID($guestAlertSID);
		}

		$type = SJB_Request::getVar('type', 'csv');
		$fileName = 'guest_alerts_' . date('Y-m-d');

		SJB_StatisticsExportController::createExportDirectory();

		switch ($type) {
			case 'csv':
				$ext = 'csv';
				SJB_StatisticsExportController::makeCSVExportFile($foundGuestAlerts, $fileName . '.' . $ext, 'Guest Alerts');
				break;

			default:
			case 'xls':
				$ext = 'xls';
				SJB_StatisticsExportController::makeXLSExportFile($foundGuestAlerts, $fileName . '.' . $ext, 'Guest Alerts');
				break;
		}
		SJB_StatisticsExportController::archiveAndSendExportFile($fileName, $ext);
	}
}
