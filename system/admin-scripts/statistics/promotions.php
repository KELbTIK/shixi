<?php

class SJB_Admin_Statistics_Promotions extends SJB_Function
{
	/**
	 * @var SJB_TemplateProcessor
	 */
	public $tp;

	public function isAccessible()
	{
		$this->setPermissionLabel('promotions_statistics');
		return parent::isAccessible();
	}

	public function execute()
	{
		$this->tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('template', 'promotions.tpl');
		$errors = array();

		try {
			$this->search();
		} catch (Exception $e) {
			array_push($errors, $e->getMessage());
		}

		$this->tp->assign('errors', $errors);
		$this->tp->display($template);
	}

	public function search()
	{
		$action = SJB_Request::getVar('action');
		$period = SJB_Request::getVar('period', array());
		$sorting_field = SJB_Request::getVar('sorting_field', 'usageCount');
		$sorting_order = SJB_Request::getVar('sorting_order', 'DESC');
		$i18n = SJB_I18N::getInstance();

		$statistics = array();
		if ($action) {
			if (!empty($period['from']) && !empty($period['to'])) {
				$from = $i18n->getInput('date', $period['from']);
				$to = $i18n->getInput('date', $period['to']);
				if (strtotime($from) > strtotime($to)) {
					throw new Exception('SELECTED_PERIOD_IS_INCORRECT');
				}
			}
			$statistics = SJB_Statistics::getPromotionsStatistics($period, $sorting_field, $sorting_order);
		}

		$periodView = array();
		foreach ($period as $key => $value) {
			$periodView[$key] = $i18n->getInput('date', $period[$key]);
		}

		$this->tp->assign('currency', SJB_CurrencyManager::getDefaultCurrency());
		$this->tp->assign('action', $action);
		$this->tp->assign('period', $period);
		$this->tp->assign('periodView', $periodView);
		$this->tp->assign('statistics', $statistics);
		$this->tp->assign('countResult', count($statistics));
		$this->tp->assign('sorting_field', $sorting_field);
		$this->tp->assign('sorting_order', $sorting_order);
	}
}
