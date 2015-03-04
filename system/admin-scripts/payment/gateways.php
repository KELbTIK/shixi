<?php


class SJB_Admin_Payment_Gateways extends SJB_Function
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
		$template_processor = SJB_System::getTemplateProcessor();
		$errors = array();


		$action = SJB_Request::getVar('action', null);
		$gateway_id = SJB_Request::getVar('gateway', null);

		if (!empty($action) && !empty($gateway_id)) {
			if ($action == 'deactivate')
				SJB_PaymentGatewayManager::deactivateByID($gateway_id);
			elseif ($action == 'activate')
				SJB_PaymentGatewayManager::activateByID($gateway_id);
		}

		$list_of_gateways = SJB_PaymentGatewayManager::getPaymentGatewaysList();
		$template_processor->assign('gatewaySaved', SJB_Request::getVar('gatewaySaved', false));
		$template_processor->assign('gateways', $list_of_gateways);
		$template_processor->assign('errors', $errors);
		$template_processor->display('payment_gateways_list.tpl');
	}
}
