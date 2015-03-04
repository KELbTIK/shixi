<?php

class SJB_Admin_Payment_AddInvoice extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_invoices');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$userSID = SJB_Request::getVar('user_sid', false);
		$includeTax = SJB_Request::getVar('include_tax', SJB_Settings::getSettingByName('enable_taxes'));
		$errors = array();
		$invoiceErrors = array();
		$template = 'add_invoice.tpl';
		$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
		if ($userInfo) {
			if (!empty($userInfo['parent_sid'])) {
				$parent_sid = $userInfo['parent_sid'];
				$username = $userInfo['username'].'/'.$userInfo['email'];
			} else {
				$parent_sid = $userSID;
				$username = $userInfo['FirstName'].' '.$userInfo['LastName'].' '.$userInfo['ContactName'].' '.$userInfo['CompanyName'].'/'.$userInfo['email'];
			}
			$formSubmitted = SJB_Request::getVar('action', '') == 'save';
			$productsSIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($userInfo['user_group_sid']);
			$products = array();
			foreach ($productsSIDs as $key => $productSID) {
				$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
				if (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'volume_based') {
					$volumeBasedPricing = $productInfo['volume_based_pricing'];
					$minListings = min($volumeBasedPricing['listings_range_from']);
					$maxListings = max($volumeBasedPricing['listings_range_to']);
					$countListings = array();
					for ($i = $minListings; $i <= $maxListings; $i++) {
						$countListings[$i]['number_of_listings'] = $i;
						for ($j = 1; $j <= count($volumeBasedPricing['listings_range_from']); $j++) {
							if ($i >= $volumeBasedPricing['listings_range_from'][$j] && $i <= $volumeBasedPricing['listings_range_to'][$j]) {
								$countListings[$i]['price'] = $volumeBasedPricing['price_per_unit'][$j];
							}
						}
					}
					$productInfo['count_listings'] = $countListings;
				} elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
					unset($productInfo['volume_based_pricing']);
				}
				$products[$key] = $productInfo;
			}
			$total = SJB_I18N::getInstance()->getInput('float', SJB_Request::getVar('total', 0));
			$taxInfo = SJB_TaxesManager::getTaxInfoByUserSidAndPrice($parent_sid, $total);
			$invoice = new SJB_Invoice($_REQUEST);
			$addForm = new SJB_Form($invoice);
			$addForm->registerTags($tp);
			
			if ($formSubmitted) {
				$invoiceErrors = $invoice->isValid();
				if (empty($invoiceErrors) && $addForm->isDataValid($errors)) {
					$invoice->setFloatNumbersIntoValidFormat();
					$invoice->setPropertyValue('success_page_url', SJB_System::getSystemSettings('USER_SITE_URL') . '/create-contract/');
					SJB_InvoiceManager::saveInvoice($invoice);
					if (SJB_Request::getVar('send_invoice', false)) {
						SJB_Notifications::sendInvoiceToCustomer($invoice->getSID(), $userSID);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/manage-invoices/');
				} else {
					$invoiceDate = SJB_I18N::getInstance()->getInput('date', $invoice->getPropertyValue('date'));
					$invoice->setPropertyValue('date', $invoiceDate);
				}
			} else {
				$invoice->setPropertyValue('date', date('Y-m-d'));
				$invoice->setPropertyValue('status', SJB_Invoice::INVOICE_STATUS_UNPAID);
			}
			
			$invoice->setFloatNumbersIntoValidFormat();
			$tp->assign('username', $username);
			$tp->assign('user_sid', $userSID);
			$tp->assign('products', $products);
			$tp->assign('tax', $taxInfo);
			$tp->assign('include_tax', $includeTax);

		} else {
			$errors[] = 'CUSTOMER_NOT_SELECTED';
			$tp->assign('action', 'add');
			$template = 'errors.tpl';
		}
		$tp->assign("errors", array_merge($errors, $invoiceErrors));
		$tp->display($template);
	}
}
