<?php

class SJB_Payment_PaymentPage extends SJB_Function
{
	public function execute()
	{
		$invoiceSID = SJB_Request::getVar('invoice_sid', null, 'default', 'int');
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$checkPaymentErrors = array();
		$currentUser = SJB_UserManager::getCurrentUser();
		if ($action == 'pay_for_products') {
			$subscribe = SJB_Request::getVar('subscribe', false);
			$subTotalPrice = SJB_Request::getVar('sub_total_price', 0);
			$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
			$codeInfo = array();$index = 1;
			$items = array();
			foreach ($products as $product) {
				$product_info = unserialize($product['product_info']);
				$items['products'][$index] = $product_info['sid'];
				$qty = !empty($product_info['number_of_listings'])?$product_info['number_of_listings']:null;
				if ($qty > 0) {
					$items['price'][$index] = round($product_info['price']/$qty, 2);
				} else {
					$items['price'][$index] = round($product_info['price'], 2);
				}
				$items['amount'][$index] = $product_info['price'];
				$items['custom_item'][$index] = "";
				$items['qty'][$index] = $qty;
				$items['custom_info'][$index]['shoppingCartRecord'] = $product['sid'];
				if ($product_info['product_type'] == 'banners' && !empty($product_info['banner_info'])) {
					$items['custom_info'][$index]['banner_info'] = $product_info['banner_info'];
				}
				$index++;
				SJB_PromotionsManager::preparePromoCodeInfoByProductPromoCodeInfo($product_info, $codeInfo);
			}
			$userSID = $currentUser->getSID();
			$invoiceSID = SJB_InvoiceManager::generateInvoice($items, $userSID, $subTotalPrice, SJB_System::getSystemSettings('SITE_URL') . "/create-contract/", (bool)$subscribe);
			SJB_PromotionsManager::addCodeToHistory($codeInfo, $invoiceSID, $userSID);
		}
		$gatewayId = SJB_Request::getVar('gw', false);
		if (SJB_Request::$method == SJB_Request::METHOD_POST && !$action && $gatewayId == 'authnet_sim') {
			if (isset($_REQUEST['submit'])) {
				$gateway = SJB_PaymentGatewayManager::getObjectByID($gatewayId, true);
				$subscriptionResult = $gateway->createSubscription($_REQUEST);
				if ($subscriptionResult !== true) {
					$tp->assign('form_submit_url', $_SERVER['REQUEST_URI']);
					$tp->assign('form_data_source', $_REQUEST);
					$tp->assign('errors', $subscriptionResult);
					$tp->display('recurring_payment_page.tpl');
				} else {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-products/?subscriptionComplete=true');
				}
			} else {
				$tp->assign('form_submit_url', $_SERVER['REQUEST_URI']);
				$tp->assign('form_data_source', $_REQUEST);
				$tp->display('recurring_payment_page.tpl');
			}
		}
		else if (!is_null($invoiceSID)) {
				$invoice_info = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSID);
				$invoice = new SJB_Invoice($invoice_info);
				if (SJB_PromotionsManager::isPromoCodeExpired($invoiceSID)) {
					$checkPaymentErrors['PROMOTION_TOO_MANY_USES'] = true;
				} else {
					$invoice->setSID($invoiceSID);
					if (count($invoice->isValid($invoiceSID)) == 0) {
						$invoiceUserSID = $invoice->getPropertyValue('user_sid');
						$currentUserSID = SJB_UserManager::getCurrentUserSID();
						if ($invoiceUserSID === $currentUserSID) {
							$payment_gateway_forms = SJB_InvoiceManager::getPaymentForms($invoice);
							$tp->assign('productsNames', $invoice->getProductNames());
							$tp->assign('gateways', $payment_gateway_forms);
							$tp->assign('invoice_info', $invoice_info);
						} else {
							$checkPaymentErrors['NOT_OWNER'] = true;
						}
					} else {
						$checkPaymentErrors['WRONG_INVOICE_PARAMETERS'] = true;
					}
				}
				$tp->assign('checkPaymentErrors', $checkPaymentErrors);
				$tp->display('invoice_payment_page.tpl');
		} else {
			$tp->display('recurring_payment_page.tpl');
		}
	}
}
