<?php

class SJB_AuthNetARB extends SJB_PaymentGateway
{
	public $amountField = 'x_amount';

    function SJB_AuthNetARB($gateway_info = array())
	{
		parent::SJB_PaymentGateway($gateway_info);
		$this->details = new SJB_AuthNetSIMDetails($gateway_info);
	}

	function isValid()
	{
		$properties = $this->details->getProperties();

		$cc 				= $properties['currency_code']->getValue();
		$md5_hash 			= $properties['authnet_api_md5_hash_value']->getValue();
        $api_login_id 		= $properties['authnet_api_login_id']->getValue();
		$transaction_key 	= $properties['authnet_api_transaction_key']->getValue();

		$errors = array();

		if ( empty($api_login_id) )		$errors['API_LOGIN_ID_IS_NOT_SET'] = 1;
		if ( empty($transaction_key) )	$errors['TRANSACTION_KEY_IS_NOT_SET'] = 1;
		if ( empty($md5_hash) )			$errors['MD5_HASH_IS_NOT_SET'] = 1;
		if ( empty($cc) )				$errors['CURRENCY_CODE_IS_NOT_SET'] = 1;

		if (empty($errors)) {
			return true;
		}
		$this->errors = array_merge($this->errors, $errors);
		return false;
	}

    function getUrl()
    {
        return $_SERVER['REQUEST_URI']; //TODO: заменить
	}

	function buildTransactionForm($invoice)
	{
		if (count($invoice->isValid()) == 0) {
			$form_fields = $this->getFormFields($invoice);
			$authnet_url = $this->getUrl();
            $form_hidden_fields = "";

            foreach ($form_fields as $name => $value) {
            	$value = htmlentities($value, ENT_QUOTES, "UTF-8");
				$form_hidden_fields .= "<input type='hidden' name='{$name}' value='{$value}' />\r\n";
			}

           	$gateway['hidden_fields'] 	= $form_hidden_fields;
           	$gateway['url'] 			= $authnet_url;
           	$gateway['caption']			= 'Authorize.Net';

			return $gateway;
		}
		return null;
	}

	function getFormFields($invoice)
	{
        $form_fields = array();
        $properties  = $this->details->getProperties();

      	$form_fields['gw'] 		    = $properties['id']->getValue();

        // payment-related fields
        $form_fields['item_number'] 		= $invoice->getSID();
        $form_fields['item_name'] 			= $invoice->getProductNames();
        $form_fields['x_description']       = $invoice->getProductNames();
        $form_fields['x_amount'] 			= $invoice->getPropertyValue('total');
        $form_fields['x_currency_code'] 	= $properties['currency_code']->getValue();

        $user = SJB_UserManager::createTemplateStructureForCurrentUser();
        $form_fields['x_first_name']        = isset($user['FirstName'])?$user['FirstName']:'';
        $form_fields['x_last_name']         = isset($user['LastName'])?$user['LastName']:'';
        $form_fields['x_company']           = isset($user['CompanyName'])?$user['CompanyName']:'';
        $form_fields['x_address']           = isset($user['Location']['Address'])?$user['Location']['Address']:'';
        $form_fields['x_city']              = isset($user['Location']['City'])?$user['Location']['City']:'';
        $form_fields['x_state']             = isset($user['Location']['State'])?$user['Location']['State']:'';
        $form_fields['x_zip']               = isset($user['Location']['ZipCode'])?$user['Location']['ZipCode']:'';
        $form_fields['x_country']           = isset($user['Location']['Country'])?$user['Location']['Country']:'';
        $form_fields['x_email']             = isset($user['Location']['email'])?$user['Location']['email']:'';
        $form_fields['x_phone']             = isset($user['Location']['PhoneNumber'])?$user['Location']['PhoneNumber']:'';

        return $form_fields;
	}

    function isNotificationFromGateway(&$notification_data)
	{
        /*The MD5 Hash value is a random value configured by the merchant in the Merchant Interface. It
        should be stored securely separately from the merchant’s Web server. For more information on how
        to configure this value, see the Merchant Integration Guide
        at http://www.authorize.net/support/merchant/.
        For example, if the MD5 Hash value configured by the merchant in the Merchant Interface is
        “wilson,” and the transaction ID is “9876543210” with an amount of $1.00, then the field order
        used by the payment gateway to generate the MD5 Hash would be as follows:

        wilson98765432101.00

        Note:
        The value passed back for x_amount is formatted with the correct number of decimal
        places used in the transaction. For transaction types that do not include a transaction
        amount, mainly Voids, the amount used by the payment gateway to calculate the MD5
        Hash is “0.00.*/

        $properties  = $this->details->getProperties();
		$local_md5_hash = md5(
			$properties['authnet_api_md5_hash_value']->getValue() .
			$notification_data['x_trans_id'] .
			$notification_data['x_amount']
		);

		return strtoupper($notification_data['x_MD5_Hash']) == strtoupper($local_md5_hash);
	}

    function isNotificationSuccessfull(&$notification_data)
	{
		return $notification_data['x_response_code'] == 1 && $notification_data['x_response_reason_code'] == 1;
	}

    function handleRecurringNotification($notification_data)
    {
        //набор полей такой же как и при callback с Authorize.NET
        //плюс два поля x_subscription_id и x_subscription_paynum
        if (!$this->isNotificationFromGateway($notification_data)) {
            return; //уведомление не от Authorize.NET
        }
        else if ($this->isNotificationSuccessfull($notification_data)){
        	$invoice_sid = null;
	    	if (isset($notification_data['x_invoice_num']))
	    		$invoice_sid = $notification_data['x_invoice_num'];
	
			if (is_null($invoice_sid))
				return;
	
			$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);
			if (is_null($invoice)) {
				return null;
			}
	
			$reactivation = false;
			if ($invoice->getStatus() == SJB_Invoice::INVOICE_STATUS_PAID) { // Пришёл рекьюринг платёж
				$invoice->setSID(null);
				$invoice->setDate(null);
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
				$reactivation = true;
			}

	        $invoice->setCallbackData($notification_data);
			if (!$this->checkPaymentAmount($invoice)) {
				return null;
			}
	        $invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PAID);
			SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());
            $user_sid = $invoice->getPropertyValue('user_sid');
	        $items = $invoice->getPropertyValue('items');
	       	if (!empty($items['products'])) {
		        $recurringProductsInfo = array();
				foreach ($items['products'] as $key => $product) {
					if ($product != -1) {
						$productInfo = $invoice->getItemValue($key);
						$listingNumber =  $productInfo['qty'];
						$contract = new SJB_Contract(array(
							'product_sid' => $product,
							'recurring_id' => $notification_data['x_subscription_id'],
							'gateway_id' => 'authnet_sim',
							'numberOfListings' => $listingNumber
						));
						if (isset($contract)) {
							$contract->setUserSID($user_sid);
							$contractSID = SJB_ContractManager::getContractSIDByRecurringId($notification_data['x_subscription_id']);
							SJB_ContractManager::deleteAllContractsByRecurringId($notification_data['x_subscription_id']);
							$contract->setPrice($productInfo['amount']);
							if ($contract->saveInDB()) {
								SJB_ShoppingCart::deleteItemFromCartBySID($productInfo['shoppingCartRecord'], $user_sid);
								$bannerInfo = $productInfo['banner_info'];
								if ($productInfo['product_type'] == 'banners' && !empty($bannerInfo) && $contractSID) {
									$bannersObj = new SJB_Banners();
									$bannerID = $bannersObj->getBannerIDByContract($contractSID);
									if ($bannerID)
										$bannersObj->updateBannerContract($contract->getID(), $bannerID);
								}

								SJB_Statistics::addStatistics('payment', 'product', $product, false, 0, 0, $user_sid, $productInfo['amount']);

								if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($user_sid)) {
									SJB_Notifications::sendSubscriptionActivationLetter($user_sid, $productInfo, $reactivation);
								}
							}
						}
						if (isset($productInfo['recurring'])) {
							$recurringProductsInfo[$key] = $productInfo;
						}
					}
				}
				if ($reactivation) {
					$invoice->setNewPropertiesToInvoice($recurringProductsInfo);
				}
		        SJB_InvoiceManager::saveInvoice($invoice);
			    if (isset($notification_data['x_trans_id'])) {
					$transactionId = $notification_data['x_trans_id'];
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
			}

        }
        else {
            //уведомление о неуспешном платеже
        }
	}

    function isNullOrEmpty($value)
    {
        return empty($value);
    }

    function validatePayment($payment_data)
    {
        $errors = array();

        if ($this->isNullOrEmpty(trim($payment_data['x_card_num']))) {
            $errors[] = "Card number is empty";
        }

        if ($this->isNullOrEmpty(trim($payment_data['x_exp_date']))) {
            $errors[] = "Expiration date is empty";
        }

        if ($this->isNullOrEmpty(trim($payment_data['x_first_name']))) {
            $errors[] = "First name is empty";
        }

        if ($this->isNullOrEmpty(trim($payment_data['x_last_name']))) {
            $errors[] = "Last name empty";
        }

        return count($errors) == 0 ? true : $errors;
    }

    function createSubscription($payment_data)
    {
        $validation_result = $this->validatePayment($payment_data);
        if ($validation_result !== true) {
            return $validation_result;
        }
        
        $properties  	    = $this->details->getProperties();
        $api_login_id       = $properties['authnet_api_login_id']->getValue();
        $transaction_key    = $properties['authnet_api_transaction_key']->getValue();
        $use_test_account   = $properties['authnet_use_test_account']->getValue();

    	$invoice = SJB_InvoiceManager::getObjectBySID($payment_data['item_number']);
		if (empty($invoice)) {
			return;
		}
        $items = $invoice->getPropertyValue('items');
        $taxInfo = $invoice->getPropertyValue('tax_info');
	    if (!empty($items['products'])) {
		    foreach ($items['products'] as $key => $product) {
			    if ($product != -1) {
				    $product_info = $invoice->getItemValue($key);
				    $payment_data['item_number'] =  $invoice->getSID();
		            $payment_data['item_name'] =  'Payment for product ' . $product_info['name'];
		            $payment_data['x_description'] =  'Payment for product ' . $product_info['name'];
		            $payment_data['x_amount'] = $product_info['amount'];
		            if ($taxInfo && !$taxInfo['price_includes_tax']) {
		            	$payment_data['x_amount'] += SJB_TaxesManager::getTaxAmount($payment_data['x_amount'], $taxInfo['tax_rate'], $taxInfo['price_includes_tax']);
		            }

	                $aimProcessor = new AuthnetAIMProcessor($api_login_id, $transaction_key, $use_test_account);
			        $aimProcessor->setTransactionType('AUTH_CAPTURE');
			        $aimProcessor->setParameter('x_login', $api_login_id);
			        $aimProcessor->setParameter('x_tran_key', $transaction_key);
			        $aimProcessor->setParameter('x_card_num', $payment_data['x_card_num']);
			        $aimProcessor->setParameter('x_amount', $payment_data['x_amount']);
			        $aimProcessor->setParameter('x_exp_date', $payment_data['x_exp_date']);

			        $aimProcessor->process();
			        if (!$aimProcessor->isApproved()) {
			            return array($aimProcessor->getResponseMessage());
			        }

					$recurringID = null;
				    if (!empty($product_info['recurring'])) {
					    $product = new SJB_Product($product_info, $product_info['product_type']);
	                    $expiration_period = $product->getExpirationPeriod();
			            $arbProcessor = new AuthnetARBProcessor($api_login_id, $transaction_key, $use_test_account);
				        $arbProcessor->setParameter('refID', $payment_data['item_number']);
				        $arbProcessor->setParameter('subscrName', $payment_data['x_description']);
				        $arbProcessor->setParameter('interval_length', $expiration_period);
				        $arbProcessor->setParameter('interval_unit', 'days');
				        $arbProcessor->setParameter('startDate', date("Y-m-d", strtotime("+ {$expiration_period} days")));
				        $arbProcessor->setParameter('totalOccurrences', 9999);
				        $arbProcessor->setParameter('trialOccurrences', 0);
				        $arbProcessor->setParameter('amount', $payment_data['x_amount']);
				        $arbProcessor->setParameter('trialAmount', 0.00);
				        $arbProcessor->setParameter('cardNumber', $payment_data['x_card_num']);
				        $arbProcessor->setParameter('expirationDate', $payment_data['x_exp_date']);
				        $arbProcessor->setParameter('orderInvoiceNumber', $payment_data['item_number']);
				        $arbProcessor->setParameter('orderDescription', $payment_data['x_description']);
				        $arbProcessor->setParameter('firstName', $payment_data['x_first_name']);
				        $arbProcessor->setParameter('lastName', $payment_data['x_last_name']);
				        $arbProcessor->setParameter('company', $payment_data['x_company']);
				        $arbProcessor->setParameter('address', $payment_data['x_address']);
				        $arbProcessor->setParameter('city', $payment_data['x_city']);
				        $arbProcessor->setParameter('state', $payment_data['x_state']);
				        $arbProcessor->setParameter('zip', $payment_data['x_zip']);

				        $arbProcessor->createAccount();
				        if (!$arbProcessor->isSuccessful()) {
				            return array($arbProcessor->getResponse());
				        }
					    $recurringID = $arbProcessor->getSubscriberID();
			        }
		            $user_sid = $invoice->getUserSID();
				    $listingNumber = $product_info['qty'];
					$contract = new SJB_Contract(array(
						'product_sid' => $product,
						'recurring_id' => $recurringID,
						'gateway_id' => 'authnet_sim',
						'numberOfListings' => $listingNumber
					));
					$contract->setUserSID($user_sid);
					$contract->setPrice($product_info['amount']);
					if ($contract->saveInDB()) {
						SJB_ShoppingCart::deleteItemFromCartBySID($product_info['shoppingCartRecord'], $user_sid);
						$bannerInfo = $product_info['banner_info'];
						if ($product_info['product_type'] == 'banners' && !empty($bannerInfo)) {
							$bannersObj = new SJB_Banners();
							$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $user_sid, $contract->getID());
							$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
							SJB_AdminNotifications::sendAdminBannerAddedLetter($user_sid, $bannerGroup);
						}
						if ($contract->isFeaturedProfile())
							SJB_UserManager::makeFeaturedBySID($user_sid);
			            if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($user_sid))
			                SJB_Notifications::sendSubscriptionActivationLetter($user_sid, $product_info);
					}
	            }
		    }
		    $invoice->setCallbackData($payment_data);
		    $invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PAID);
		    SJB_InvoiceManager::saveInvoice($invoice);
			SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());
   		}
        return true;
    }

	function getPaymentStatusFromCallbackData($callback_data)
	{
		// http://www.authorize.net/support/merchant/Transaction_Response/Transaction_Response.htm
		// x_response_code values:
		//   1 = Approved
		//   2 = Declined
		//   3 = Error
		//   4 = Held for Review
		$x_response_code = isset($callback_data['x_response_code']) ? $callback_data['x_response_code'] : 0;
		switch ($x_response_code) {
			case 1:
				return 'Successful';
			case 2:
			case 3:
				return 'Error';
			case 4:
				return 'Pending';
		}
		return 'Notification';
	}

    function cancelSubscription($subscriptionId)
    {
        $properties  	    = $this->details->getProperties();
        $api_login_id       = $properties['authnet_api_login_id']->getValue();
        $transaction_key    = $properties['authnet_api_transaction_key']->getValue();
        $use_test_account   = $properties['authnet_use_test_account']->getValue();
        
        $arbProcessor = new AuthnetARBProcessor($api_login_id, $transaction_key, $use_test_account);
        $arbProcessor->setParameter('refID', $subscriptionId);
        $arbProcessor->setParameter('subscrId', $subscriptionId);

        $arbProcessor->deleteAccount();
        if(!$arbProcessor->isSuccessful()){
            return array($arbProcessor->getResponse());
        }

        return true;
    }
}
