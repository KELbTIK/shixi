<?php

class SJB_PayPalPro extends SJB_PaymentGateway
{
	private $httpParsedResponseAr;
	private $httpParsedResponseOfRecurring;
	private $failedProducts = '';
	public $errors = array();
	public $amountField = 'amount';

	function SJB_PayPalPro($gateway_info)
	{
		parent::SJB_PaymentGateway($gateway_info);
		$this->details = new SJB_PayPalProDetails($gateway_info);
	}

	public function isValid()
	{
		$this->validate();
		return empty($this->errors);
	}

	public function buildTransactionForm($invoice)
	{
		if (count($invoice->isValid()) == 0) {
			return array(
				'url' => $this->getPaypalProFillPaymentCardUrl($invoice),
				'caption' => 'PayPal Pro',
			);
		}

		return null;
	}

	public function makePayment($data)
	{
		$this->sendPaymentToPaypal($data);
		$this->setPaymentStatus();
		$this->continuePaymentProcess();
	}

	public function getPaymentFromCallbackData($callback_data)
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

		$invoice->setCallbackData($callback_data);
		if (!$this->checkPaymentAmount($invoice)) {
			return null;
		}
		$gatewayId = $this->details->getProperty('id');
		$invoice->setPropertyValue('payment_method', $gatewayId->getValue());
		SJB_InvoiceManager::saveInvoice($invoice);
		if (isset($callback_data['http_post_response']['TRANSACTIONID'])) {
			$transactionId = $callback_data['http_post_response']['TRANSACTIONID'];
			$transactionInfo = array(
				'transaction_id' => $transactionId,
				'invoice_sid' => $invoice->getSID(),
				'amount' => $invoice->getPropertyValue('total'),
				'payment_method' => $invoice->getPropertyValue('payment_method'),
				'user_sid' => $invoice->getPropertyValue('user_sid')
			);
			$transaction = new SJB_Transaction($transactionInfo);
			SJB_TransactionManager::saveTransaction($transaction);
		}
		return $invoice;
	}

	private function getGatewayUrl()
	{
		$sub_domain = $this->getPropertyValue('use_sandbox') ? 'api-3t.sandbox' : 'api-3t';
		return "https://{$sub_domain}.paypal.com/nvp";
	}

	private function validate()
	{
		$properties = $this->details->getProperties();
		$user_name = $properties['user_name']->getValue();
		$user_password = $properties['user_password']->getValue();
		$user_signature = $properties['user_signature']->getValue();
		if (empty($user_name)) {
			$this->errors['USER_NAME_IS_NOT_SET'] = 1;
		}
		if (empty($user_password)) {
			$this->errors['USER_PASSWORD_IS_NOT_SET'] = 1;
		}
		if (empty($user_signature)) {
			$this->errors['USER_SIGNATURE_IS_NOT_SET'] = 1;
		}
	}

	private function continuePaymentProcess()
	{
		$this->prepareRequest();
		$this->prepareUri();
		$getPaymentId = SJB_Request::getInt('payment_id', 0, "GET");
		$invoice = SJB_InvoiceManager::getObjectBySID($getPaymentId);
		if ($invoice->isRecurring()) {
			$this->redirectToMyProductsPage($_REQUEST);
		} else {
			$function = new SJB_Payment_Callback(SJB_Acl::getInstance(), array(), null);
			$function->execute();
		}
	}

	private function sendPaymentToPaypal($data)
	{
		$invoiceSid = SJB_Request::getInt('item_number', null);
		if (is_null($invoiceSid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}
		$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSid);
		if ($invoice->isRecurring()) {
			$customerDataString = $this->makeRequestForRecurringPayment($data, $invoice);
			foreach ($customerDataString as $key => $query) {
				$this->httpParsedResponseOfRecurring[] = $this->paypalHttpPost('CreateRecurringPaymentsProfile', $query);
			}
		} else {
			$customerDataString = $this->makeRequestForDirectPayment($data);
			$this->httpParsedResponseAr = $this->paypalHttpPost('DoDirectPayment', $customerDataString);
		}
	}

	private function setPaymentStatus()
	{
		$invoiceSid = SJB_Request::getInt('item_number', null);
		if (is_null($invoiceSid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}
		$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSid);
		$status = false;
		if (is_null($invoice)) {
			$this->errors['NONEXISTED_INVOICE_ID_SPECIFIED'] = 1;
			return null;
		}
		if ($invoice->isRecurring()) {
			foreach ($this->httpParsedResponseOfRecurring as $key => $response) {
				if (in_array(strtoupper($response['ACK']), array('SUCCESS', 'SUCCESSWITHWARNING'))) {
					$status = true;
				}
			}
			if ($status == true) {
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PENDING);
			} else {
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			}
			SJB_InvoiceManager::saveInvoice($invoice);
		} else {
			$invoice->setCallbackData($this->httpParsedResponseAr);
			if (in_array(strtoupper($this->httpParsedResponseAr['ACK']), array('SUCCESS', 'SUCCESSWITHWARNING'))) {
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_VERIFIED);
			} else {
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			}
			SJB_InvoiceManager::saveInvoice($invoice);
		}
	}

	private function prepareRequest()
	{
		if (!empty($this->httpParsedResponseAr)) {
			$_REQUEST['http_post_response'] = $this->httpParsedResponseAr;
		} else {
			$_REQUEST['http_post_response'] = $this->httpParsedResponseOfRecurring;
		}
		unset(
			$_REQUEST['user_name'],
			$_REQUEST['user_password'],
			$_REQUEST['user_signature'],
			$_REQUEST['gateway_url'],
			$_REQUEST['card_type'],
			$_REQUEST['card_number'],
			$_REQUEST['exp_date_mm'],
			$_REQUEST['exp_date_yy'],
			$_REQUEST['csc_value'],
			$_REQUEST['first_name'],
			$_REQUEST['last_name'],
			$_REQUEST['address'],
			$_REQUEST['zip'],
			$_REQUEST['country'],
			$_REQUEST['city'],
			$_REQUEST['state'],
			$_REQUEST['email'],
			$_REQUEST['phone']
		);
	}

	private function prepareUri()
	{
		$_SERVER['REQUEST_URI'] = SJB_System::getSystemSettings('SITE_URL') . "/system/payment/callback/paypal_pro/" . $_REQUEST['item_number'] . '/';
	}

	private function getPaypalProFillPaymentCardUrl($invoice)
	{
		$properties = $this->details->getProperties();
		$get_url = SJB_System::getSystemSettings('SITE_URL') . "/paypal-pro-fill-payment-card/?payment_id={$invoice->getSID()}&gateway_id={$this->getPropertyValue('id')}";
		if ($properties['https']->getValue()) {
			return $this->getHttpsUrl($get_url);
		}
		return $get_url;
	}

	private function getHttpsUrl($get_url)
	{
		return str_replace('http:', 'https:', $get_url);
	}

	private function paypalHttpPost($methodName_, $customer_data_string)
	{
		$gatewayProperties = $this->getGatewayProperties();
		$query_data = "METHOD={$methodName_}&" . http_build_query($gatewayProperties) . $customer_data_string;
		try {
			$httpResponse = $this->doPostRequest($gatewayProperties['API_Endpoint'], $query_data);
			$httpParsedResponseAr = $this->parseHttpResponse($httpResponse);
		} catch (HttpException $e) {
			exit("{$gatewayProperties['API_Endpoint']} $methodName_ failed: " . $e->getMessage());
		}
		return $httpParsedResponseAr;
	}

	private function getGatewayProperties()
	{
		return array(
			'VERSION' => urlencode('74.0'),
			'PWD' => urlencode($this->getPropertyValue('user_password')),
			'USER' => urlencode($this->getPropertyValue('user_name')),
			'SIGNATURE' => urlencode($this->getPropertyValue('user_signature')),
			'API_Endpoint' => $this->getGatewayUrl(),
		);
	}

	private function doPostRequest($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$httpResponse = curl_exec($ch);
		if (!$httpResponse) {
			echo ("CURL error " . curl_error($ch) . '(' . curl_errno($ch) . ')');
		}
		return $httpResponse;
	}

	private function parseHttpResponse($httpResponse)
	{
		$httpResponseAr = explode("&", $httpResponse);
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $value) {
			$tmpAr = explode("=", $value);
			if (sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
		if (!array_key_exists('ACK', $httpParsedResponseAr)) {
			echo ("ACK not found in Response data.");
		}
		return $httpParsedResponseAr;
	}

	private function makeRequestForDirectPayment($getData)
	{
		$expDateMonth = $getData['exp_date_mm'];
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		$customer = array(
			'PAYMENTACTION' => urlencode('Sale'),
			'AMT' => urlencode($getData['amount']),
			'CREDITCARDTYPE' => '',
			'ACCT' => urlencode($getData['card_number']),
			'EXPDATE' => $padDateMonth . $expDateYear = urlencode($getData['exp_date_yy']),
			'CVV2' => urlencode($getData['csc_value']),
			'FIRSTNAME' => urlencode($getData['first_name']),
			'LASTNAME' => urlencode($getData['last_name']),
			'STREET' => urlencode($getData['address']),
			'CITY' => urlencode($getData['city']),
			'STATE' => urlencode($getData['state']),
			'ZIP' => urlencode($getData['zip']),
			'COUNTRYCODE' => urlencode($getData['country']),
			'CURRENCYCODE' => urlencode($getData['currency_code'])
		);
		return '&' . http_build_query($customer);
	}

	private function makeRequestForRecurringPayment($data)
	{
		$query = array();
		$queryForRecurringProducts = $this->getCustomerInfoQueryForRecurring($data, true);
		$queryForOtherProducts = $this->getCustomerInfoQueryForRecurring($data);
		$count = count($queryForOtherProducts) + count($queryForRecurringProducts);
		for ($i = 0; $i < $count; $i++) {
			if (!empty($queryForOtherProducts[$i])) {
				$query[$i] = $queryForOtherProducts[$i];
			}
			elseif (!empty($queryForRecurringProducts[$i])) {
				$query[$i] = $queryForRecurringProducts[$i];
			}
		}
		return $query;
	}

	public function handleRecurringNotification($callbackData)
	{
		$invoiceSid = isset($callbackData['item_number']) ? $callbackData['item_number'] : null;
		$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSid);
		if (is_null($invoiceSid)) {
			return;
		}
		if (is_null($invoice)) {
			return null;
		}
		if (SJB_Array::get($callbackData, 'txn_type') != 'subscr_payment') {
			return;
		}
		$reactivation = false;
		$paymentStatus = $invoice->getStatus();
		if ($paymentStatus == SJB_Invoice::INVOICE_STATUS_PAID) {
			$invoice->setSID(null);
			$invoice->setDate(null);
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			$reactivation = true;
		}
		$invoice->setCallbackData($callbackData);
		if (strcmp($this->isRecurringPaymentVerified($invoice), "VERIFIED") == 0) {
			if (in_array($callbackData['payment_status'], array('Completed', 'Processed'))) {
				$items = $invoice->getPropertyValue('items');
				$userSid = $invoice->getUserSID();
				$subscriptionSID = $callbackData['custom'];
				if (!empty($items['products'])) {
					$recurringProductsInfo = array();
					foreach ($items['products'] as $key => $product) {
						if ($product != -1) {
							$productInfo = $invoice->getItemValue($key);
							if ($paymentStatus == SJB_Invoice::INVOICE_STATUS_PAID && $subscriptionSID == $product) {
								$listingNumber = $productInfo['qty'];
								$contract = new SJB_Contract(array(
									'product_sid' => $product,
									'recurring_id' => $callbackData['subscr_id'],
									'gateway_id' => 'paypal_pro',
									'numberOfListings' => $listingNumber
								));
								$contract->setUserSID($userSid);
								$contractSID = SJB_ContractManager::getContractSIDByRecurringId($callbackData['subscr_id']);
								SJB_ContractManager::deleteAllContractsByRecurringId($callbackData['subscr_id']);
								$contract->setPrice($productInfo['amount']);
								if ($contract->saveInDB()) {
									SJB_ShoppingCart::deleteItemFromCartBySID($productInfo['shoppingCartRecord'], $userSid);
									if ($productInfo['product_type'] == 'banners' && !empty($productInfo['banner_info'])) {
										$bannersObj = new SJB_Banners();
										if (isset($contractSID)) {
											$bannerID = $bannersObj->getBannerIDByContract($contractSID);
											if ($bannerID) {
												$bannersObj->updateBannerContract($contract->getID(), $bannerID);
											}
										} else {
											$bannerInfo = $productInfo['banner_info'];
											$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $userSid, $contract->getID());
											$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
											SJB_AdminNotifications::sendAdminBannerAddedLetter($userSid, $bannerGroup);
										}
									}
									if ($contract->isFeaturedProfile()) {
										SJB_UserManager::makeFeaturedBySID($userSid);
									}
									SJB_Statistics::addStatistics('payment', 'product', $product, false, 0, 0, $userSid, $productInfo['amount']);

									if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($userSid)) {
										SJB_Notifications::sendSubscriptionActivationLetter($userSid, $productInfo, $reactivation);
									}
								}
								$recurringProductsInfo[$key] = $productInfo;
							}
							elseif ($paymentStatus != SJB_Invoice::INVOICE_STATUS_PAID) {
								$listingNumber = $productInfo['qty'];
								if ($subscriptionSID == $product) {
									$contract = new SJB_Contract(array(
										'product_sid' => $product,
										'recurring_id' => $callbackData['subscr_id'],
										'gateway_id' => 'paypal_pro',
										'numberOfListings' => $listingNumber
									));
									$contract->setUserSID($userSid);
									$contract->setPrice($productInfo['amount']);
									if ($contract->saveInDB()) {
										SJB_ShoppingCart::deleteItemFromCartBySID($productInfo['shoppingCartRecord'], $userSid);
										$bannerInfo = $productInfo['banner_info'];
										if ($productInfo['product_type'] == 'banners' && !empty($bannerInfo)) {
											$bannersObj = new SJB_Banners();
											$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $userSid, $contract->getID());
											$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
											SJB_AdminNotifications::sendAdminBannerAddedLetter($userSid, $bannerGroup);
										}
										if ($contract->isFeaturedProfile()) {
											SJB_UserManager::makeFeaturedBySID($userSid);
										}
										SJB_Statistics::addStatistics('payment', 'product', $product, false, 0, 0, $userSid, $productInfo['amount']);

										if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($userSid)) {
											SJB_Notifications::sendSubscriptionActivationLetter($userSid, $productInfo);
										}
									}
								}
							}
						}
					}
					if ($reactivation) {
						$invoice->setNewPropertiesToInvoice($recurringProductsInfo);
					}
					$price = isset($callback_data['payment_gross']) ? $callback_data['payment_gross'] : $invoice->getPropertyValue('total');
					$invoice->SetStatus(SJB_Invoice::INVOICE_STATUS_PAID);
					$gatewayId = $this->details->getProperty('id');
					$invoice->setPropertyValue('payment_method', $gatewayId->getValue());
					SJB_InvoiceManager::saveInvoice($invoice);
					SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());
					$transactionId = $callbackData['txn_id'];
					$transactionInfo = array(
						'transaction_id' => $transactionId,
						'invoice_sid' => $invoice->getSID(),
						'amount' => $price,
						'payment_method' => $invoice->getPropertyValue('payment_method'),
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
		elseif (strcmp($this->isRecurringPaymentVerified($callbackData), "INVALID") == 0) {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			SJB_InvoiceManager::saveInvoice($invoice);
		}
	}

	public function isRecurringPaymentVerified($invoice)
	{
		$callback_data = $invoice->getCallbackData();
		$req = '';

		foreach ($callback_data as $key => $value) {
			$req .= $key . "=" . urlencode($value) . "&";
		}
		if (!empty($callback_data['test_ipn'])) {
			$ipnUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
		} else {
			$ipnUrl = "https://www.paypal.com/cgi-bin/webscr";
		}
		$req .= 'cmd=_notify-validate';

		$headers = array(
			'Content-Type: application/x-www-form-urlencoded',
			'Host: www.paypal.com',
			'Connection: close'
		);
		$ch = curl_init($ipnUrl);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		curl_close($ch);
		$invoice->setVerificationResponse($result);
		return $result;
	}

	public function cancelSubscription($subscriptionId)
	{
		$cancelRecurring = array(
			'PROFILEID' => $subscriptionId,
			'ACTION' => 'Cancel'
		);
		$queryEnd = '&' . http_build_query($cancelRecurring);
		$parsedResponse = $this->paypalHttpPost('ManageRecurringPaymentsProfileStatus', $queryEnd);
		if ("SUCCESS" == strtoupper($parsedResponse["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($parsedResponse["ACK"])) {
			return true;
		} else {
			return urldecode($parsedResponse['L_LONGMESSAGE0']);
		}
	}

	private function redirectToMyProductsPage($callbackData)
	{
		$invoice_sid = isset($callbackData['item_number']) ? $callbackData['item_number'] : null;
		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);
		$paymentStatus = $invoice->getStatus();
		$invoice->setCallbackData($callbackData);
		$items = $invoice->getPropertyValue('items');
		$countOfProducts = 0;
		$gatewayId = $this->details->getProperty('id');
		$invoice->setPropertyValue('payment_method', $gatewayId->getValue());
		$userSid = $invoice->getUserSID();
		if ($paymentStatus == SJB_Invoice::INVOICE_STATUS_PENDING) {
			foreach ($items['products'] as $key => $product) {
				if ("SUCCESS" == strtoupper($callbackData['http_post_response'][$countOfProducts]["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($callbackData['http_post_response'][$countOfProducts]["ACK"])) {
					$product_info = $invoice->getItemValue($key);
					$countOfProducts += 1;
					SJB_ShoppingCart::deleteItemFromCartBySID($product_info['shoppingCartRecord'], $userSid);
				} else {
					$productInfo = SJB_ProductsManager::getProductInfoBySID($product);
					$this->failedProducts = $this->failedProducts . $productInfo['name'] . ',';
					$countOfProducts += 1;
				}
			}
			SJB_InvoiceManager::saveInvoice($invoice);
			$this->failedProducts = substr($this->failedProducts, 0, -1);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-products/?subscriptionComplete=true&failedProducts=' . $this->failedProducts);
		} else {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
			SJB_InvoiceManager::saveInvoice($invoice);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-products/?subscriptionComplete=false');
		}
	}

	private function getCustomerInfoQueryForRecurring($data, $recurring = false)
	{
		$query = array();
		$expDateMonth = $data['exp_date_mm'];
		$padDateMonth = urlencode(str_pad($expDateMonth, 2, '0', STR_PAD_LEFT));
		if ($recurring == false) {
			if (!empty($data['products'])) {
				$dataArray = $data['products'];
			} else {
				return '';
			}
		}
		elseif (!empty($data['recurring'])) {
			$dataArray = $data['recurring'];
		} else {
			return '';
		}
		foreach ($dataArray as $key => $products) {
			$product = new SJB_Product($products, $products['product_type']);
			$expPeriod = $product->getExpirationPeriod();
			$customer = array(
				'TOKEN' => '',
				'CREDITCARDTYPE' => '',
				'ACCT' => urlencode($data['card_number']),
				'EXPDATE' => $padDateMonth . $expDateYear = urlencode($data['exp_date_yy']),
				'FIRSTNAME' => urlencode($data['first_name']),
				'LASTNAME' => urlencode($data['last_name']),
				'PROFILESTARTDATE' => date('Y-n-d') . 'T' . date('G:i:s'),
				'DESC' => urlencode($products['name']),
				'BILLINGPERIOD' => urlencode('Day'),
				'BILLINGFREQUENCY' => urlencode($expPeriod),
				'CURRENCYCODE' => urlencode($data['currency_code']),
				'AMT' => urlencode($products['price']),
				'NOTIFYURL' => urlencode($data['notify_url']),
				'ITEMNUMBER' => urlencode($data['item_number']),
				'CUSTOM' => urlencode($products['sid']),
			);
			if ($recurring === false) {
				$customer['TOTALBILLINGCYCLES'] = urlencode(1);
				if (empty($expPeriod)) {
					$customer['BILLINGFREQUENCY'] = urlencode(30);
				}
			}
			$query[$key] = '&' . http_build_query($customer);
		}
		return $query;
	}

	function getPaymentStatusFromCallbackData($callback_data)
	{
		$isCalledFromIpnListener = isset($callback_data['txn_type']) && $callback_data['txn_type'] == 'subscr_payment';
		if ($isCalledFromIpnListener) {
			return $this->parseIpnStatus($callback_data);
		} else {
			return $this->parseNvpApiStatus($callback_data);
		}
	}

	public function parseIpnStatus($callback_data)
	{
		// https://www.x.com/developers/paypal/documentation-tools/ipn/integration-guide/IPNandPDTVariables
		// payment_status values:
		//   Pending
		//   Completed
		//   Denied
		$payment_status = isset($callback_data['payment_status']) ? strtolower($callback_data['payment_status']) : '';
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

	public function parseNvpApiStatus($callback_data)
	{
		// https://www.x.com/developers/paypal/documentation-tools/api/NVPAPIOverview
		// Acknowledgement status, which is one of the following values:
		//   Success
		//   SuccessWithWarning
		//   Failure
		//   FailureWithWarning
		$ack = isset($callback_data['http_post_response']) && isset($callback_data['http_post_response']['ACK']) ?
			strtolower($callback_data['http_post_response']['ACK']) :
			'';
		switch ($ack) {
			case 'success':
			case 'successwithwarning':
				return 'Successful';
			case 'failure':
			case 'failurewithwarning':
				return 'Error';
		}
		return 'Notification';
	}
}
