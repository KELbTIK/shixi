<?php

class SJB_PayPal extends SJB_PaymentGateway
{
	public $errors = array();
	public $amountField = 'payment_gross';

    function SJB_PayPal($gateway_info = array())
	{
		parent::SJB_PaymentGateway($gateway_info);
		$this->details = new SJB_PayPalDetails($gateway_info);
	}

    function isValid()
    {
    	$properties = $this->details->getProperties();
		$email 	= $properties['paypal_account_email']->getValue();
		$cc 	= $properties['currency_code']->getValue();

		$errors = array();

		if (empty($email))
			$errors['EMAIL_IS_NOT_SET'] = 1;
		if (empty($cc))
		 	$errors['CURRENCY_CODE_IS_NOT_SET'] = 1;

		if (empty($errors)) {
			return true;
		}

		$this->errors = array_merge($this->errors, $errors);
		return false;
	}

    function getUrl()
    {
    	$properties = $this->details->getProperties();

		if ( $properties['use_sandbox']->getValue() )
			return 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		return 'https://www.paypal.com/cgi-bin/webscr';
	}

    function buildTransactionForm($invoice)
    {
	    if (count($invoice->isValid()) == 0) {
			$form_fields = $this->getFormFields($invoice);
			$paypal_url = $this->getUrl();
            $form_hidden_fields = "";

            foreach ($form_fields as $name => $value) {
				$value = htmlentities($value, ENT_QUOTES, "UTF-8");
				$form_hidden_fields .= "<input type='hidden' name='{$name}' value='{$value}' />\r\n";
			}

           	$gateway['hidden_fields'] 	= $form_hidden_fields;
           	$gateway['url'] 			= $paypal_url;
           	$gateway['caption']			= 'PayPal';

			return $gateway;
		}
		return null;
	}

	/**
	 * @param  $invoice SJB_Invoice
	 * @return array
	 */
    function getFormFields($invoice)
	{
		$form_fields = array();
		$properties  = $this->details->getProperties();
		$id = $properties['id']->getValue();
		
		if ($invoice->isRecurring()) {
			// hard-coded fields
			$form_fields['cmd'] 			= '_xclick-subscriptions';
			$form_fields['notify_url'] 		= SJB_System::getSystemSettings('SITE_URL') . "/system/payment/notifications/{$id}/";
			$form_fields['return']			= SJB_System::getSystemSettings('SITE_URL') . '/my-products/?subscriptionComplete=true';
			$form_fields['src'] 			= '1';
			$recurringProduct = array();
			$products = array();
			$items = $invoice->getPropertyValue('items');
			if (!empty($items['products'])) {
				foreach ($items['products'] as $key=>$product) {
					if ($product != -1) {
						$productInfo = $invoice->getItemValue($key);
					if (!empty($productInfo['recurring']) && !$recurringProduct)
						$recurringProduct = $productInfo;
					$products[] = $productInfo;
					}
				}
			}
			$taxInfo = $invoice->getPropertyValue('tax_info');
			if ($taxInfo && !$taxInfo['price_includes_tax']) {
				$recurringProduct['price'] += SJB_TaxesManager::getTaxAmount($recurringProduct['price'], $taxInfo['tax_rate'], $taxInfo['price_includes_tax']);
			}

			$product = new SJB_Product($recurringProduct, $recurringProduct['product_type']);
			if (count($products) > 1) {
				$form_fields['a1'] 			= $invoice->getPropertyValue('total');
				$form_fields['p1'] 			= $product->getExpirationPeriod();
				$form_fields['t1'] 			= 'D';
			}

			$form_fields['a3'] 			= $recurringProduct['price'];
			$form_fields['p3'] 			= $product->getExpirationPeriod();
			$form_fields['t3'] 			= 'D';
			$form_fields['custom'] 		= $recurringProduct['sid'];
			$form_fields['no_note'] 			= '1';
			$form_fields['no_shipping'] 		= '1';
		}
		else {
			// hard-coded fields
			$form_fields['cmd'] 			= '_xclick';
			$form_fields['amount'] 			= $invoice->getPropertyValue('total');
			$form_fields['return'] 			= SJB_System::getSystemSettings('SITE_URL') . "/system/payment/callback/{$id}/{$invoice->getSID()}/";
			$form_fields['notify_url']		= SJB_System::getSystemSettings('SITE_URL') . "/system/payment/callback/{$id}/{$invoice->getSID()}/";

		}

		$form_fields['cancel_return'] 	= SJB_System::getSystemSettings('SITE_URL') . "/my-account/";
		$form_fields['rm'] 				= 2; // POST method for call back

		// configuration fields
		$form_fields['business'] 		= $properties['paypal_account_email']->getValue();
		$form_fields['currency_code'] 	= $properties['currency_code']->getValue();

		// payment-related fields
		$form_fields['item_name'] 		= $invoice->getProductNames();
		$form_fields['item_number'] 	= $invoice->getSID();
		return $form_fields;
	}

    function isPaymentVerified($invoice)
    {
		$callback_data = $invoice->getCallbackData();

		$postdata ='';

		foreach ($callback_data as $key => $value) {
			$postdata .= $key . "=" . urlencode($value) . "&";
		}

		$postdata .= "cmd=_notify-validate";

		@set_time_limit(0);

		$paypal_url = $this->getUrl();
		//Define required hesaders for PayPal according to https://www.x.com/node/320404
		$headers = array (
			'Content-Type: application/x-www-form-urlencoded',
			'Host: www.paypal.com',
			'Connection: close'
		);

		$curl = curl_init($paypal_url);
		curl_setopt ($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt ($curl, CURLOPT_HEADER, 0);
		curl_setopt ($curl, CURLOPT_POST, 1);
		curl_setopt ($curl, CURLOPT_POSTFIELDS, $postdata);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt ($curl, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($curl);

		$invoice->setVerificationResponse($response);
		return $response == "VERIFIED";
	}

    function getPaymentFromCallbackData($callback_data)
    {
		$invoice_sid = isset($callback_data['item_number']) ? $callback_data['item_number'] : null;

		if (is_null($invoice_sid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}

		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);

		if (is_null($invoice)) {
			$this->errors['NONEXISTED_INVOICE_ID_SPECIFIED'] = 1;
			return null;
		}

		if ( $invoice->getStatus() != SJB_Invoice::INVOICE_STATUS_UNPAID ) {
			$this->errors['INVOICE_IS_NOT_UNPAID'] = $invoice->getStatus();
			return null;
		}

		$invoice->setCallbackData($callback_data);

		if ($this->isPaymentVerified($invoice) && in_array($callback_data['payment_status'], array('Completed', 'Processed'))) {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_VERIFIED);
		}
		else if ($callback_data['payment_status'] == 'Pending') {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PENDING);
		} else {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
		}

		if (!$this->checkPaymentAmount($invoice)) {
			return null;
		}
	    $id = $this->details->getProperty('id');
		$invoice->setPropertyValue('payment_method', $id->getValue());
	    SJB_InvoiceManager::saveInvoice($invoice);

	    if (isset($callback_data['txn_id'])){
		    $transactionId = $callback_data['txn_id'];
		    $transactionInfo = array(
			    'transaction_id'=> $transactionId,
			    'invoice_sid' => $invoice->getSID(),
			    'amount' => $invoice->getPropertyValue('total'),
			    'payment_method'=> $invoice->getPropertyValue('payment_method'),
			    'user_sid' => $invoice->getPropertyValue('user_sid')
		    );
		    $transaction = new SJB_Transaction($transactionInfo);
		    SJB_TransactionManager::saveTransaction($transaction);
	    }
	    return $invoice;
	}

	/**
	 * Recurring notification handlign function
	 * @param array|null $callback_data Notification data
	 */
	function handleRecurringNotification($callback_data)
    {
	    if (SJB_Array::get($callback_data, 'txn_type') == 'subscr_cancel' || SJB_Array::get($callback_data, 'txn_type') == 'subscr_eot') {
    		SJB_ContractManager::removeSubscriptionId(SJB_Array::get($callback_data, 'subscr_id'));
    		return;
    	}

        if (SJB_Array::get($callback_data, 'txn_type') != 'subscr_payment') {
    		return;
    	}

    	$invoice_sid = isset($callback_data['item_number']) ? $callback_data['item_number'] : null;
	    if (is_null($invoice_sid))
			return;

		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);

		if (is_null($invoice)) {
			return null;
		}

		$reactivation = false;
		$status = $invoice->getStatus();
		if ($invoice->getStatus()== SJB_Invoice::INVOICE_STATUS_PAID) { // Пришёл рекьюринг платёж
			$invoice->setSID(null);
			$invoice->setDate(null);
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			$reactivation = true;
		}

		$invoice->setCallbackData($callback_data);
	    if ($this->isPaymentVerified($invoice) && in_array($callback_data['payment_status'], array('Completed', 'Processed'))) {
			$items = $invoice->getPropertyValue('items');
			$user_sid = $invoice->getUserSID();
			$subscriptionSID = $callback_data['custom'];
			if (!empty($items['products'])) {
				$recurringProductsInfo = array();
				foreach ($items['products'] as $key => $product) {
					if ($product != -1) {
						$productInfo = $invoice->getItemValue($key);
						if ($status == SJB_Invoice::INVOICE_STATUS_PAID && $subscriptionSID == $product) {
							$listingNumber = $productInfo['qty'];
							$contract = new SJB_Contract(array(
								'product_sid' => $product,
								'recurring_id' => $callback_data['subscr_id'],
								'gateway_id' => 'paypal_standard',
								'numberOfListings' => $listingNumber
							));
							$contract->setUserSID($user_sid);
							$contractSID = SJB_ContractManager::getContractSIDByRecurringId($callback_data['subscr_id']);
							SJB_ContractManager::deleteAllContractsByRecurringId($callback_data['subscr_id']);
							$contract->setPrice($productInfo['amount']);
							if ($contract->saveInDB()) {
								SJB_ShoppingCart::deleteItemFromCartBySID($productInfo['shoppingCartRecord'], $user_sid);
								$bannerInfo = $productInfo['banner_info'];
								if ($productInfo['product_type'] == 'banners' && !empty($bannerInfo)) {
									$bannersObj = new SJB_Banners();
									if (isset($contractSID)) {
										$bannerID = $bannersObj->getBannerIDByContract($contractSID);
										if ($bannerID)
											$bannersObj->updateBannerContract($contract->getID(), $bannerID);
									}
									else {
										$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $user_sid, $contract->getID());
										$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
										SJB_AdminNotifications::sendAdminBannerAddedLetter($user_sid, $bannerGroup);
									}
								}
								if ($contract->isFeaturedProfile())
									SJB_UserManager::makeFeaturedBySID($user_sid);
								SJB_Statistics::addStatistics('payment', 'product', $product, false, 0, 0, $user_sid, $productInfo['amount']);

					            if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($user_sid)) {
					                SJB_Notifications::sendSubscriptionActivationLetter($user_sid, $productInfo, $reactivation);
					            }
							}
							$recurringProductsInfo[$key] = $productInfo;
						}
						elseif ($status != SJB_Invoice::INVOICE_STATUS_PAID) {
							$listingNumber = $productInfo['qty'];
							if ($subscriptionSID == $product) {
								$contract = new SJB_Contract(array(
									'product_sid' => $product,
									'recurring_id' => $callback_data['subscr_id'],
									'gateway_id' => 'paypal_standard',
									'numberOfListings' => $listingNumber
								));
							} else {
								$contract = new SJB_Contract(array(
									'product_sid' => $product,
									'gateway_id' => 'paypal_standard',
									'numberOfListings' => $listingNumber
								));
							}
							$contract->setUserSID($user_sid);
							$contract->setPrice($productInfo['amount']);
							if ($contract->saveInDB()) {
								SJB_ShoppingCart::deleteItemFromCartBySID($productInfo['shoppingCartRecord'], $user_sid);
								$bannerInfo = $productInfo['banner_info'];
								if ($productInfo['product_type'] == 'banners' && !empty($bannerInfo) && $contractSID) {
									$bannersObj = new SJB_Banners();
									$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $user_sid, $contract->getID());
									$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
									SJB_AdminNotifications::sendAdminBannerAddedLetter($user_sid, $bannerGroup);
								}
								if ($contract->isFeaturedProfile())
									SJB_UserManager::makeFeaturedBySID($user_sid);
								SJB_Statistics::addStatistics('payment', 'product', $product, false, 0, 0, $user_sid, $productInfo['amount']);

					            if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($user_sid)) {
					                SJB_Notifications::sendSubscriptionActivationLetter($user_sid, $productInfo);
					            }
							}
						}
					}
				}
				if ($reactivation) {
					$invoice->setNewPropertiesToInvoice($recurringProductsInfo);
				}
				$price = isset($callback_data['payment_gross']) ? $callback_data['payment_gross'] : $invoice->getPropertyValue('total');
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PAID);
				$id = $this->details->getProperty('id');
				$invoice->setPropertyValue('payment_method', $id->getValue());
				SJB_InvoiceManager::saveInvoice($invoice);
				SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());

				$transactionID = $callback_data['txn_id'];
				$transactionInfo = array(
					'transaction_id'=> $transactionID,
					'invoice_sid' => $invoice->getSID(),
					'amount' => $price,
					'payment_method'=> $invoice->getPropertyValue('payment_method'),
					'user_sid' => $invoice->getPropertyValue('user_sid')
				);
				$transaction = new SJB_Transaction($transactionInfo);
				SJB_TransactionManager::saveTransaction($transaction);
			}
		} else {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			SJB_InvoiceManager::saveInvoice($invoice);
		}
	}

	public function cancelSubscription($subscriptionId)
	{
		$properties = $this->details->getProperties();
		SJB_HelperFunctions::redirect($this->getUrl() . '?cmd=_subscr-find&alias=' . $properties['paypal_account_email']->getValue());
	}

	function getPaymentStatusFromCallbackData($callback_data)
	{
		// https://www.x.com/developers/paypal/documentation-tools/ipn/integration-guide/IPNandPDTVariables
		// payment_status values:
		//   Pending
		//   Completed
		//   Denied
		$payment_status = isset($callback_data['payment_status'])? strtolower($callback_data['payment_status']) : '';
		switch ($payment_status) {
			case 'pending':
				return 'Pending';
			case 'completed':
				return 'Successful';
			case 'denied':
				return 'Error';
		}
		return 'Notification';
	}
}

