<?php

class SJB_Payment_CashPaymentPage extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$gatewayId = SJB_Request::getVar('gatewayId', 'cash_gateway');
		$gateway = SJB_PaymentGatewayManager::getObjectByID($gatewayId);
		if (isset($gateway) && in_array($gatewayId, array('cash_gateway', 'wire_transfer'))) {
			$invoiceSid = SJB_Request::getVar('invoice_sid');
			$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSid);
			if (isset($invoice)) {
				$currentUser = SJB_UserManager::getCurrentUserInfo();
				if ($currentUser['sid'] == $invoice->getPropertyValue('user_sid')) {
					if ($invoice->getStatus() == SJB_Invoice::INVOICE_STATUS_UNPAID) {
						$tp->assign('invoice_sid', $invoiceSid);
						$tp->assign('item_name', $invoice->getProductNames());
						$tp->assign('amount', $invoice->getPropertyValue('total'));
						$tp->assign('user', $currentUser);
						SJB_InvoiceManager::saveInvoice($invoice);
						SJB_ShoppingCart::deleteItemsFromCartByUserSID($currentUser['sid']);
					} else {
						$errors['INVOICE_IS_NOT_UNPAID'] = true;
					}

				} else {
					$errors['NOT_OWNER'] = true;
				}
			} else {
				$errors['INVALID_INVOICE_ID'] = true;
			}
			$template = $gateway->getTemplate();
			$tp->assign('errors', $errors);
		} else {
			$errors['INVALID_GATEWAY'] = true;
			$tp->assign('ERRORS', $errors);
			$template = 'errors.tpl';
		}
		$tp->display($template);
	}
}
