<?php

class SJB_Admin_GuestAlerts_Manage extends SJB_Function
{
	/**
	 * @var array
	 */
	private $errors = array();

	/**
	 * @var SJB_TemplateProcessor
	 */
	private $tp;

	/**
	 * @var SJB_GuestAlertCriteriaSaver
	 */
	public $criteriaSaver;

	public $criteria;


	public function isAccessible()
	{
		$this->setPermissionLabel('manage_guest_email_alerts');
		return parent::isAccessible();
	}

	public function execute()
	{
		$action = SJB_Request::getVar('action_name');
		if (!empty($action)) {
			try {
				$this->callAction($action);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/guest-alerts/');
			} catch (Exception $e) {
				array_push($this->errors, $e->getMessage());
			}
		}

		$this->tp = SJB_System::getTemplateProcessor();

		$this->showSearchForm();
		$this->showResults();
	}

	public function showResults()
	{
		if (SJB_Request::getVar('action', '') == 'search') {
			$_REQUEST['restore'] = 1;
		}

		$paginator = new SJB_GuestAlertsManagePagination();
		$limit = array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage);
		$searcher = new SJB_GuestAlertSearcher($limit, $paginator->sortingField, $paginator->sortingOrder);

		$foundGuestAlerts = $searcher->getObjectsSIDsByCriteria($this->criteria);
		$this->criteriaSaver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());

		foreach ($foundGuestAlerts as $id => $guestAlertSID) {
			$foundGuestAlerts[$id] = SJB_GuestAlertManager::getGuestAlertInfoBySID($guestAlertSID);
		}

		$paginator->setItemsCount($searcher->getAffectedRows());
		$this->tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$this->tp->assign('searchFields', $this->getSearchFieldsForTemplate());
		$this->tp->assign('errors', $this->errors);
		$this->tp->assign('guestAlerts', $foundGuestAlerts);

		$this->tp->display('manage.tpl');
	}

	public function getSearchFieldsForTemplate()
	{
		$searchFields = '';
		foreach ($_REQUEST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $fieldName => $fieldValue)
					$searchFields .= "&{$key}[{$fieldName}]={$fieldValue}";
			}
		}
		return $searchFields;
	}

	public function showSearchForm()
	{
		$guestAlert = new SJB_GuestAlert(array());
		$guestAlert->addSubscriptionDateProperty();
		$guestAlert->addStatusProperty();

		$searchFormBuilder = new SJB_SearchFormBuilder($guestAlert);
		$this->criteriaSaver = new SJB_GuestAlertCriteriaSaver();

		if (isset($_REQUEST['restore'])) {
			$_REQUEST = array_merge($_REQUEST, $this->criteriaSaver->getCriteria());
		}

		$this->criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $guestAlert);
		$searchFormBuilder->setCriteria($this->criteria);
		$searchFormBuilder->registerTags($this->tp);

		$this->tp->display('search_form.tpl');
	}

	public function callAction($action)
	{
		if (! method_exists($this, $action)) {
			$translatedErrorMessage = SJB_I18N::getInstance()->gettext('Backend', 'Action is not defined in system');
			throw new Exception($translatedErrorMessage . ': '. $action);
		}

		$guestAlertsSIDs = SJB_Request::getVar('guestAlerts', array());
		if (is_array($guestAlertsSIDs)) {
			foreach ($guestAlertsSIDs as $guestAlertSID) {
				try {
					$guestAlert = SJB_GuestAlertManager::getObjectBySID($guestAlertSID);
					$this->$action($guestAlert);
				} catch (Exception $e) {
					$translatedErrorMessage = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
					array_push($this->errors, $translatedErrorMessage . ': ' . $guestAlertSID);
				}
			}
		}
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 */
	public function confirm(SJB_GuestAlert $guestAlert)
	{
		$guestAlert->setStatusActiveFromUnconfirmed();
		$guestAlert->update();
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 */
	public function deactivate(SJB_GuestAlert $guestAlert)
	{
		$guestAlert->setStatusInactive();
		$guestAlert->update();
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 * @throws Exception
	 */
	public function activate(SJB_GuestAlert $guestAlert)
	{
		if ($guestAlert->getPropertyValue('status') === SJB_GuestAlert::STATUS_UNSUBSCRIBED) {
			throw new Exception('Unsubscribed Guest Alert can not be activated');
		}
		$guestAlert->setStatusActive();
		$guestAlert->update();
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 */
	public function delete(SJB_GuestAlert $guestAlert)
	{
		SJB_GuestAlertManager::deleteGuestAlertBySID($guestAlert->getSID());
	}
}
