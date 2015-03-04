<?php

class SJB_Payment_Callback extends SJB_Function
{
	public function execute()
	{
		$request_uri = $_SERVER['REQUEST_URI'];
		$template_processor = SJB_System::getTemplateProcessor();
		$callback_page_uri = '';
		preg_match('#.*/system/payment/callback/([^/?]+)#', $request_uri, $mm);
		if (!empty($mm)) {
			$gateway_id = $mm[1];
	        $redirectPage = $callback_page_uri.$gateway_id."/";

	        preg_match("(.*$redirectPage([^/]*)/?)", $request_uri, $invoice_sid);
	        $invoice_sid = !empty($invoice_sid[1]) ? $invoice_sid[1] : '';
	        $redirectPage = $callback_page_uri.$gateway_id."/".$invoice_sid;

	        preg_match("(.*$redirectPage([^/]*)/?)", $request_uri, $tt);
			$redirectPage = !empty($tt[1]) ? $tt[1] : '';

	        $invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);
	        if (!empty($invoice) && $invoice->getStatus() == SJB_Invoice::INVOICE_STATUS_PAID) {
	            SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/payment-completed/");
	        }

			$gateway = SJB_PaymentGatewayManager::getObjectByID($gateway_id);
			$gateway_caption = $gateway->getPropertyValue('caption');
			$invoice = $gateway->getPaymentFromCallbackData($_REQUEST);
			SJB_PaymentLogManager::recordPaymentLog($gateway->getPaymentStatusFromCallbackData($_REQUEST), $gateway_caption, $_REQUEST);

			if (is_null($invoice)) {
				$errors = $gateway->getErrors();
				$template_processor->assign('errors', $errors);
				$template_processor->display('callback_payment_page.tpl');
			} else {
				$status = $invoice->getStatus();
				if ($status == SJB_Invoice::INVOICE_STATUS_VERIFIED) {
					SJB_Statistics::addStatisticsFromInvoice($invoice);
					
					$success_url = $invoice->getSuccessPageURL();
					$page = empty($redirectPage) ? '' : '&' . $redirectPage;
					SJB_HelperFunctions::redirect($success_url . '?invoice_sid=' . $invoice->getSID() . $page);
				} elseif ($status == SJB_Invoice::INVOICE_STATUS_PENDING) {
					$template_processor->assign('message', 'INVOICE_WAITING');
					$template_processor->display('callback_payment_page.tpl');
				} else {
					SJB_InvoiceManager::markUnPaidInvoiceBySID($invoice_sid);
					$payment_error = 1;
					if ($gateway_id == 'paypal_pro') {
						$httpPostResponse = SJB_Request::getVar('http_post_response', false);
						if (!empty($httpPostResponse['L_SHORTMESSAGE0']) && urldecode($httpPostResponse['L_SHORTMESSAGE0']) == 'Authentication/Authorization Failed') {
							$payment_error = 2;
						}
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/view-invoice/?sid=".$invoice_sid."&payment_error=".$payment_error."&payment_gateway=".$gateway_id);
				}
			}
		} else {
			$errors['INVOICE_ID_IS_NOT_SET'] = 1;
			$template_processor->assign('errors', $errors);
			$template_processor->display('callback_payment_page.tpl');
		}
	}
}
