<?php

class SJB_Payment_CancelRecurring extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$gateway = SJB_PaymentGatewayManager::getObjectByID(SJB_Request::getVar('gateway'), true);

		if (!empty($gateway)) {
			$invoiceID = SJB_Request::getVar('invoiceID', false);
			$cancelSubscriptionResult = $gateway->cancelSubscription(SJB_Request::getVar('subscriptionId'), $invoiceID);
			$errors = array();
			if ($cancelSubscriptionResult !== true) {
				$errors = $cancelSubscriptionResult;
			}
			else {
				SJB_ContractManager::removeSubscriptionId(SJB_Request::getVar('subscriptionId'));
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-products/?cancelRecurringContract=' . SJB_Request::getVar('contractId'));
			}
			$tp->assign('errors', $errors);
			$tp->display('cancel_recurring.tpl');
		}
	}
}

