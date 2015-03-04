<?php

class SJB_Admin_Payment_PaymentLog extends SJB_Function
{
	private $restore;
	private $action;
	private $searchingCriteria;
	private $template;
	private $pagesForViewing = array();
	/**
	 * @var SJB_PaymentLogPagination
	 */
	private $paginator;
	/**
	 * @var SJB_TemplateProcessor
	 */
	private $templateProcessor;
	/**
	 * @var SJB_SearchFormBuilder
	 */
	private $searchFormBuilder;
	/**
	 * @var SJB_PaymentLogCriteriaSaver
	 */
	private $criteriaSaver;
	/**
	 * @var SJB_PaymentLogSearcher
	 */
	private $searcher;

	public function isAccessible()
	{
		$this->setPermissionLabel('payment_log');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$this->init();
		$this->dispatch();
	}

	private function init()
	{
		$this->action = SJB_Request::getVar('action', 'search');
		$this->restore = SJB_Request::getVar('restore', false);
		$this->paginator = new SJB_PaymentLogPagination();

		$this->templateProcessor = SJB_System::getTemplateProcessor();
		$this->searcher = new SJB_PaymentLogSearcher($this->paginator->currentPage, $this->paginator->itemsPerPage, $this->paginator->sortingField, $this->paginator->sortingOrder);
		$this->criteriaSaver = new SJB_PaymentLogCriteriaSaver();
		$paymentLogItem = new SJB_paymentLog($_REQUEST);
		$this->searchFormBuilder = new SJB_SearchFormBuilder($paymentLogItem);
		$this->searchingCriteria = $this->searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $paymentLogItem);
	}

	private function dispatch()
	{
		switch ($this->action) {
			case 'display_message':
				$this->displayItem();
				break;

			case 'search':
				$this->displaySearchResults();
				break;
		}
	}

	private function displayItem()
	{
		$sid = SJB_Request::getVar('sid', false);
		$paymentLogItem = SJB_PaymentLogManager::getPaymentLogInfoBySID($sid);
		$this->templateProcessor->assign('paymentLogItem', $paymentLogItem);
		$this->templateProcessor->display('payment_log_details.tpl');
	}

	private function displaySearchResults()
	{
		$this->initSearchingForm();
		$this->criteriaSaver->setSession($_REQUEST, $this->searcher->getFoundObjectSIDs());
		$foundPaymentLogs = $this->searchLogsFromCriteria();
		$foundPaymentLogs = $this->searchPaymentLogs($foundPaymentLogs);
		$this->paginator->setItemsCount($this->searcher->getAffectedRows());

		$this->assignParametersAndDisplayLog($foundPaymentLogs);
	}

	private function initSearchingForm()
	{
		if ($this->restore) {
			$this->loadCriteriaAndMergeWithRequest();
		}
		$this->setTemplate('payment_log.tpl');
		$this->displaySearchForm();
	}

	private function loadCriteriaAndMergeWithRequest()
	{
		$_REQUEST = array_merge($_REQUEST, $this->criteriaSaver->getCriteria());
	}

	private function setTemplate($template)
	{
		$this->template = $template;
	}

	private function displaySearchForm()
	{
		$this->searchFormBuilder->setCriteria($this->searchingCriteria);
		$this->searchFormBuilder->registerTags($this->templateProcessor);
		$this->templateProcessor->display("payment_log_search_form.tpl");
	}

	private function searchLogsFromCriteria()
	{
		$order_info = array('sorting_field' => $this->paginator->sortingField,
			'sorting_order' => $this->paginator->sortingOrder);
		$this->criteriaSaver->setSessionForOrderInfo($order_info);
		$this->criteriaSaver->setSessionForCurrentPage($this->paginator->currentPage);
		$this->criteriaSaver->setSessionForListingsPerPage($this->paginator->itemsPerPage);
		return $this->searcher->getObjectsSIDsByCriteria($this->searchingCriteria);
	}

	private function searchPaymentLogs($foundPaymentLogs)
	{
		foreach ($foundPaymentLogs as $id => $paymentLogSID) {
			$paymentLogInfo = SJB_PaymentLogManager::getPaymentLogInfoBySID($paymentLogSID);
			$foundPaymentLogs[$id] = $paymentLogInfo;
		}
		return $foundPaymentLogs;
	}

	private function getFields()
	{
		$searchFields = '';
		foreach ($_REQUEST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $fieldName => $fieldValue) {
					$searchFields .= "&{$key}[{$fieldName}]={$fieldValue}";
				}
			}
		}
		return $searchFields;
	}

	private function assignParametersAndDisplayLog($foundPayments)
	{
		$this->templateProcessor->assign('paginationInfo', $this->paginator->getPaginationInfo());
		$this->templateProcessor->assign("found_payments", $foundPayments);
		$this->templateProcessor->assign("searchFields", $this->getFields());
		$this->templateProcessor->display('payment_log.tpl');
	}
}
