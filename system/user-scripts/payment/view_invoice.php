<?php

class SJB_Payment_ViewInvoice extends SJB_Function
{
	public function isAccessible()
	{
		return SJB_UserManager::isUserLoggedIn();
	}


	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$displayForm = new SJB_Form();
		$displayForm->registerTags($tp);
		$invoiceSid = SJB_Request::getVar('sid', false);
		if (SJB_Request::getVar('error', false)) {
			SJB_FlashMessages::getInstance()->addWarning('TCPDF_ERROR');
		}
		$action = SJB_Request::getVar('action', false);
		$paymentGateway = SJB_Request::getVar('payment_gateway', false);
		$template = 'print_invoice.tpl';
		$currentUserSID = SJB_UserManager::getCurrentUserSID();
		$invoiceInfo = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSid);
		if ($invoiceInfo) {
			if ($currentUserSID == $invoiceInfo['user_sid']){
				$taxInfo = SJB_TaxesManager::getTaxInfoBySID($invoiceInfo['tax_info']['sid']);
				$invoiceInfo = array_merge($invoiceInfo, $_REQUEST);
				if (is_array($taxInfo)) {
					$taxInfo = array_merge($invoiceInfo['tax_info'], $taxInfo);
				} else {
					$taxInfo = $invoiceInfo['tax_info'];
				}
				$invoice = new SJB_Invoice($invoiceInfo);
				$invoice->setSID($invoiceSid);
				$userInfo = SJB_UserManager::getUserInfoBySID($currentUserSID);
				$username = $userInfo['CompanyName'].' '.$userInfo['FirstName'].' '.$userInfo['LastName'];
				$user = SJB_UserManager::getObjectBySID($currentUserSID);
				$productsSIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($userInfo['user_group_sid']);
				$products = array();
				foreach ($productsSIDs as $key => $productSID) {
					$product = SJB_ProductsManager::getProductInfoBySID($productSID);
					$products[$key] = $product;
				}
				$displayForm = new SJB_Form($invoice);
				$displayForm->registerTags($tp);
				$show = true;
				if ($action == 'download_pdf_version' || $action == 'print') {
					$show = false;
				}
				$tp->assign('show', $show);
				$tp->assign('products', $products);
				$tp->assign('invoice_sid', $invoiceSid);
				$tp->assign('invoice_status', $invoiceInfo['status']);
				$tp->assign('username', trim($username));
				$tp->assign('user_sid', $currentUserSID);
				$tp->assign('tax', $taxInfo);
				$userStructure = SJB_UserManager::createTemplateStructureForUser($user);
				$tp->assign('user', $userStructure);
				$tp->assign('include_tax', $invoiceInfo['include_tax']);
				if ($action == 'download_pdf_version') {
					$template = 'invoice_to_pdf.tpl';
					$filename = 'invoice_' . $invoiceSid . '.pdf';
					try {
						SJB_HelperFunctions::html2pdf($tp->fetch($template), $filename);
						exit();
					} catch(Exception $e) {
						SJB_Error::writeToLog($e->getMessage());
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/print-invoice/?sid=' . $invoiceSid . '&action=print&error=TCPDF_ERROR');
					}
				}
			} else {
				SJB_FlashMessages::getInstance()->addError('NOT_OWNER');
			}
		} else {
			SJB_FlashMessages::getInstance()->addError('WRONG_INVOICE_ID_SPECIFIED');
		}
		if ($paymentGateway) {
			$gatewaySID = SJB_PaymentGatewayManager::getSIDByID($paymentGateway);
			$gatewayInfo = SJB_PaymentGatewayManager::getInfoBySID($gatewaySID);
			$tp->assign('gatewayInfo', $gatewayInfo);
		}
		$tp->assign('paymentError', SJB_Request::getVar('payment_error', false));
		$tp->display($template);
	}
}
