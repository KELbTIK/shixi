<?php

class SJB_Admin_Payment_EditInvoice extends SJB_Function
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
		$template = 'edit_invoice.tpl';
		$errors = array();
		$invoiceErrors  = array();
	    $invoiceSID = SJB_Request::getVar('sid', false);
		$action = SJB_Request::getVar('action', false);
		$tcpdfError = SJB_Request::getVar('error', false);
		if ($tcpdfError) {
			$invoiceErrors[] = $tcpdfError;
		}
		$invoiceInfo = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSID);
		$user_structure = null;
		if ($invoiceInfo) {
			$product_info = array();
			if (array_key_exists('custom_info', $invoiceInfo['items'])) {
				$product_info = $invoiceInfo['items']['custom_info'];
			}
			$invoiceInfo = array_merge($invoiceInfo, $_REQUEST);
			$invoiceInfo['items']['custom_info'] = $product_info;
			$includeTax = $invoiceInfo['include_tax'];
			$invoice = new SJB_Invoice($invoiceInfo);
			$invoice->setSID($invoiceSID);
			$userSID = $invoice->getPropertyValue('user_sid');
			$userExists = SJB_UserManager::isUserExistsByUserSid($userSID);
			$subUserSID = $invoice->getPropertyValue('subuser_sid');
			if (!empty($subUserSID)) {
				$userInfo = SJB_UserManager::getUserInfoBySID($subUserSID);
				$username = $userInfo['username'].'/'.$userInfo['email'];
			} else {
				$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
				$username = $userInfo['FirstName'].' '.$userInfo['LastName'].' '.$userInfo['ContactName'].' '.$userInfo['CompanyName'].'/'.$userInfo['email'];
			}
			$taxInfo = $invoice->getPropertyValue('tax_info');

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
				}
				$products[$key] = $productInfo;
			}

			$addForm = new SJB_Form($invoice);
			$addForm->registerTags($tp);
			$tp->assign('userExists', $userExists);
			$tp->assign('products', $products);
			$tp->assign('invoice_sid', $invoiceSID);
			$tp->assign('include_tax', $includeTax);
			$tp->assign('username', trim($username));
			if ($action) {
				switch ($action) {
					case 'save':
					case 'apply':
						$invoiceErrors = $invoice->isValid();
						
						if (empty($invoiceErrors) && $addForm->isDataValid($errors)) {
							$invoice->setFloatNumbersIntoValidFormat();
							SJB_InvoiceManager::saveInvoice($invoice);
							if ($action == 'save') {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/manage-invoices/');
							}
						} else {
							$invoiceDate = SJB_I18N::getInstance()->getInput('date', $invoice->getPropertyValue('date'));
							$invoice->setPropertyValue('date', $invoiceDate);
						}
						
						$invoice->setFloatNumbersIntoValidFormat();
						$taxInfo['tax_amount'] =  SJB_I18N::getInstance()->getInput('float', $taxInfo['tax_amount']);
						break;
					case 'print':
					case 'download_pdf_version':
						$user = SJB_UserManager::getObjectBySID($userSID);
						$user_structure = SJB_UserManager::createTemplateStructureForUser($user);
						$template = 'print_invoice.tpl';
						$username = SJB_Array::get($user_structure, 'CompanyName').' '.SJB_Array::get($user_structure, 'FirstName').' '.SJB_Array::get($user_structure, 'LastName');
						$tp->assign('username', trim($username));
						$tp->assign('user', $user_structure);
						$tp->assign('tax', $taxInfo);
						if ($action == 'download_pdf_version') {
							$template = 'invoice_to_pdf.tpl';
							$filename = 'invoice_' . $invoiceSID . '.pdf';
							try {
								SJB_HelperFunctions::html2pdf($tp->fetch($template), $filename);
								exit();
							} catch(Exception $e) {
								SJB_Error::writeToLog($e->getMessage());
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/edit-invoice/?sid=' . $invoiceSID . '&error=TCPDF_ERROR');
							}
						}
						break;
					case 'send_invoice':
						$result = SJB_Notifications::sendInvoiceToCustomer($invoiceSID, $userSID);
						if ($result) {
							echo SJB_I18N::getInstance()->gettext("Backend", "Invoice successfully sent");
						} else {
							echo SJB_I18N::getInstance()->gettext("Backend", "Invoice not sent");
						}
						exit;
						break;
				}
			}
			$transactions = SJB_TransactionManager::getTransactionsByInvoice($invoiceSID);
			$tp->assign('tax', $taxInfo);
			$tp->assign('transactions', $transactions);
		} else {
			$tp->assign('action', 'edit');
			$errors[] = 'WRONG_INVOICE_ID_SPECIFIED';
			$template = 'errors.tpl';
		}
		$tp->assign("errors", array_merge($errors, $invoiceErrors));
		$tp->display($template);
	}
}
