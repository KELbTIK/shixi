<?php


class SJB_Admin_Payment_ConfigureGateway extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_payment_gateways');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$templateProcessor = SJB_System::getTemplateProcessor();

		$errors = array();

		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
		$gateway_id = isset($_REQUEST['gateway']) ? $_REQUEST['gateway'] : null;
		$formSubmitted = SJB_Request::getVar('submit');

		$gateway_sid = SJB_PaymentGatewayManager::getSIDByID($gateway_id);

		if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($action)) {
			if ($action == 'deactivate')
				SJB_PaymentGatewayManager::deactivateByID($gateway_id);
			elseif ($action == 'activate')
				SJB_PaymentGatewayManager::activateByID($gateway_id);
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$gateway = SJB_PaymentGatewayManager::createObjectByID($gateway_id, $_REQUEST);
			$gateway->dontSaveProperty('id');
			$gateway->dontSaveProperty('caption');
			$gateway->setSID($gateway_sid);

			if ($gateway->isValid()) {
				if (SJB_PaymentGatewayManager::saveGateway($gateway) !== false) {
					$templateProcessor->assign('gatewaySaved', true);
					if ($formSubmitted == 'save_gateway') {
						$siteUrl = SJB_System::getSystemsettings('SITE_URL') . '/system/payment/gateways/?gatewaySaved=1';
						SJB_HelperFunctions::redirect($siteUrl);
					}
				} else {
					$errors['SETTINGS_SAVED_WITH_PROBLEMS'] = 1;
				}
			} else {
				$errors = $gateway->getErrors();
			}
		}

		$gateway = SJB_PaymentGatewayManager::getObjectByID($gateway_id);
		$gateway_form = new SJB_Form($gateway);
		$gateway_form->registerTags($templateProcessor);
		$gateway_form->makeDisabled('id');
		$gateway_form->makeDisabled('caption');

		$countryCode = $gateway->getPropertyValue('country');
		if (empty($countryCode)) {
			$countryValue = SJB_CountriesManager::getCountrySIDByCountryCode('US');
			$gateway->setPropertyValue('country', $countryValue);
		}

		if (empty($gateway)) {
			$errors['GATEWAY_NOT_FOUND'] = 1;
			$templateProcessor->assign('errors', $errors);
			$templateProcessor->display('configure_gateway.tpl');

			return;
		}

		$gateway_info = SJB_PaymentGatewayManager::getInfoBySID($gateway_sid);
		$form_fields = $gateway_form->getFormFieldsInfo();

		$templateProcessor->assign('gateway', $gateway_info);
		$templateProcessor->assign('form_fields', $form_fields);
		$templateProcessor->assign('errors', $errors);
		$templateProcessor->display('configure_gateway.tpl');
	}
}
