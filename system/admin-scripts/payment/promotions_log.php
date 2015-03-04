<?php

class SJB_Admin_Payment_PromotionsLog extends SJB_Function
{
	protected $tp;
	protected $errors = array();

	public function isAccessible()
	{
		$this->setPermissionLabel('manage_promotions');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$this->tp = SJB_System::getTemplateProcessor();
		$passedParametersViaUri = SJB_UrlParamProvider::getParams();
		$promotionSID = array_shift($passedParametersViaUri);

		$promotionCodeInfo = SJB_PromotionsManager::getCodeInfoBySID($promotionSID);
		$this->tp->assign('promotionInfo', $promotionCodeInfo);
		if (!$promotionCodeInfo) {
			array_push($this->errors, 'INVALID_ID');
		}

		$page = SJB_Request::getInt('page', 1);
		$itemsPerPage = SJB_Request::getInt('items_per_page', 50);


		$itemsCount = SJB_PromotionsManager::getHistoryCountBySID($promotionSID);
		$this->assignParametersForPagination($page, $itemsPerPage, $itemsCount);

		$promotions = SJB_PromotionsManager::getHistoryBySID($promotionSID, $page, $itemsPerPage);
		SJB_PromotionsManager::preparePromotionsInfoForLog($promotions);

		$currency = SJB_CurrencyManager::getDefaultCurrency();
		$this->tp->assign('currency', $currency);
		$this->tp->assign('promotions', $promotions);
		$this->tp->assign('errors', $this->errors);

		$this->tp->display('promotions_log.tpl');
	}

	/**
	 * @param int $page
	 * @param int $itemsPerPage
	 * @param int $itemsCount
	 */
	public function assignParametersForPagination($page, $itemsPerPage, $itemsCount)
	{
		$pages = array();
		for ($i = $page - 3; $i < $page + 3; $i++) {
			if ($i > 0) {
				$pages[] = $i;
			}
			if ($i * $itemsPerPage >= $itemsCount) {
				break;
			}
		}

		$totalPages = ceil($itemsCount / $itemsPerPage);
		if (empty($totalPages)) {
			$totalPages = 1;
		}

		if (array_search(1, $pages) === false) {
			array_unshift($pages, 1);
		}
		if (array_search($totalPages, $pages) === false) {
			array_push($pages, $totalPages);
		}

		$this->tp->assign('resultsNumber', $itemsCount);
		$this->tp->assign('currentPage', $page);
		$this->tp->assign('items_per_page', $itemsPerPage);
		$this->tp->assign('pages', $pages);
		$this->tp->assign('totalPages', $totalPages);
	}
}
