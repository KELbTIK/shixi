<?php

class SJB_Admin_Miscellaneous_Currency extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_currencies');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$action = SJB_Request::getVar('action', 'list');
		$submit = SJB_Request::getVar('submit', null);
		$currencyInfo = array();
		$sid = SJB_Request::getVar('sid', null);

		switch ($action) {
			case 'add':
				$tp->assign('button', 'Add');
				break;
			case 'edit':
				$currencyInfo = SJB_CurrencyDBManager::getCurrencyInfoBySID($sid);
				$tp->assign('button', 'Edit');
				$tp->assign('sid', $sid);
				$action = 'add';
				break;
			case 'delete':
				SJB_CurrencyDBManager::deleteCurrencyBySID($sid);
				$action = 'list';
				break;
			case 'default':
				SJB_CurrencyDBManager::makeDefaultCurrencyBySID($sid);
				$action = 'list';
				break;
			case 'deactivate':
				SJB_CurrencyDBManager::updateStatusCurrencyBySID($sid, 0);
				$action = 'list';
				break;
			case 'activate':
				SJB_CurrencyDBManager::updateStatusCurrencyBySID($sid, 1);
				$action = 'list';
				break;
		}

		$currencyInfo = array_merge($currencyInfo, $_REQUEST);
		$currency = new SJB_CurrencyManager($currencyInfo);

		if (isset($sid) && !is_null($sid)) {
			$currency->setSID($sid);
		}
		$addCurrencyForm = new SJB_Form($currency);
		$addCurrencyForm->registerTags($tp);
		$form_submitted = SJB_Request::getVar('click');

		$form_fields = $addCurrencyForm->getFormFieldsInfo();
		$tp->assign("form_fields", $form_fields);
		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$tp->assign
		(
			"METADATA",
			array
			(
				"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields),
			)
		);
		$error = array();
		if ($currency->getPropertyValue('course') <= 0) {
			$currencyCourse = $currency->getProperty('course');
			$error[$currencyCourse->caption] = 'CAN_NOT_EQUAL_NULL';
		}
		switch ($submit) {
			case 'Add':
				if ($addCurrencyForm->isDataValid($errors) && !$error) {
					SJB_CurrencyDBManager::saveCurrency($currency);
					$action = 'list';
				}
				else {
					$errors = array_merge($errors, $error);
					$tp->assign('button', 'Add');
					$action = 'add';
				}
				break;
			case 'Edit':
				if ($addCurrencyForm->isDataValid($errors) && !$error) {
					SJB_CurrencyDBManager::saveCurrency($currency);
					$tp->assign('button', 'Edit');
					$action = 'list';

					if ($form_submitted == 'apply') {
						$action = 'add';
						$tp->assign('sid', $sid);
						$tp->assign('button', 'Edit');
					}
				}
				else {
					$errors = array_merge($errors, $error);
					$tp->assign('button', 'Edit');
					$tp->assign('sid', $_REQUEST['sid']);
					$action = 'add';
				}
				$currency->setFloatNumbersIntoValidFormat();
				break;
		}

		if ($action == 'list')
			$tp->assign('currency', SJB_CurrencyManager::getCurrencyList());
		$tp->assign('action', $action);
		$tp->assign('errors', $errors);
		$tp->display('currency.tpl');
	}
}
