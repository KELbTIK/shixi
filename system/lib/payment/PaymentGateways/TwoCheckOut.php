<?php

class SJB_TwoCheckOut extends SJB_PaymentGateway
{
	public $amountField = 'total';

	function SJB_TwoCheckOut($gateway_info = array())
	{
		parent::SJB_PaymentGateway($gateway_info);
		$this->details = new SJB_TwoCheckOutDetails($gateway_info);
	}

	function isValid()
	{
		$properties = $this->details->getProperties();
		$two_co_account_id = $properties['2co_account_id']->getValue();
		return !empty($two_co_account_id);
	}

	function getUrl()
	{
		$isSandbox = $this->details->getProperty('sandbox')->value;
		if ($isSandbox) {
			return 'https://sandbox.2checkout.com/checkout/purchase';
		}
		return 'https://www.2checkout.com/checkout/purchase';
	}

	function buildTransactionForm($invoice)
	{
		if (count($invoice->isValid()) == 0) {
			$two_checkout_url = $this->getUrl();
			$form_fields = $this->getFormFields($invoice);
			$form_hidden_fields = '';
			foreach ($form_fields as $name => $value){
				$value = htmlentities($value, ENT_QUOTES, "UTF-8");
				$form_hidden_fields .= "<input type='hidden' name='{$name}' value='{$value}' />\r\n";
			}

			$gateway['hidden_fields'] 	= $form_hidden_fields;
			$gateway['url'] 			= $two_checkout_url;
			$gateway['caption']			= '2Checkout';

			return $gateway;
		}
		return null;
	}

	function getFormFields($invoice)
	{
		$form_fields = array();
		$properties  = $this->details->getProperties();
		$id = $properties['id']->getValue();

		$form_fields['sid'] =  $properties['2co_account_id']->getValue();
		$form_fields['mode'] = '2CO';
		$form_fields['merchant_order_id'] 	= $invoice->getSID();
		$i = 1;
		$items = $invoice->getPropertyValue('items');
		$taxInfo = $invoice->getPropertyValue('tax_info');

		foreach ($items['products'] as $key => $product) {
			if ($product == -1) {
				$form_fields['li_'.$i.'_name'] = $items['custom_item'][$key];
			} else {
				$productInfo = $invoice->getItemValue($key);
				$form_fields['li_'.$i.'_name'] = $productInfo['name'];
				$form_fields['li_'.$i.'_product_id'] = $product;
				if ($invoice->isRecurring() && !empty($productInfo['recurring'])) {
					$form_fields['li_'.$i.'_duration'] = 'Forever';
					if (!empty($productInfo['period_name']) && $productInfo['period_name'] != 'unlimited') {
						$period = $productInfo['period']." ".ucwords($productInfo['period_name']);
						$form_fields['li_'.$i.'_recurrence'] = $period;
					} else
						$form_fields['li_'.$i.'_recurrence'] = " Week";
				}
			}
			$form_fields['li_'.$i.'_type'] = 'product';
			if ($items['qty'][$key] > 0) {
				$form_fields['li_'.$i.'_quantity'] 	= 1;
				$form_fields['li_'.$i.'_price'] = sprintf('%.02f', $items['amount'][$key]);
				if ($taxInfo && !$taxInfo['price_includes_tax']) {
					$form_fields['li_'.$i.'_price'] += SJB_TaxesManager::getTaxAmount($form_fields['li_'.$i.'_price'], $taxInfo['tax_rate'], $taxInfo['price_includes_tax']);
				}
			} else {
				$form_fields['li_'.$i.'_quantity'] 	= $items['qty'][$key];
				$form_fields['li_'.$i.'_price'] = sprintf('%.02f', $items['price'][$key]);
				if ($taxInfo && !$taxInfo['price_includes_tax']) {
					$form_fields['li_'.$i.'_price'] += SJB_TaxesManager::getTaxAmount($form_fields['li_'.$i.'_price'], $taxInfo['tax_rate'], $taxInfo['price_includes_tax']);
				}
			}
			$form_fields['li_'.$i.'_tangible'] = 'N';
			$i++;
		}

		$user = SJB_UserManager::createTemplateStructureForCurrentUser();
		$form_fields['first_name']        = isset($user['FirstName'])	? $user['FirstName'] : '';
		$form_fields['last_name']         = isset($user['LastName'])	? $user['LastName'] : '';
		$form_fields['street_address']    = isset($user['Location']['Address'])		? $user['Location']['Address'] : '';
		$form_fields['city']              = isset($user['Location']['City'])		? $user['Location']['City'] : '';
		$form_fields['state']             = isset($user['Location']['State'])		? $user['Location']['State'] : '';
		$form_fields['zip']               = isset($user['Location']['ZipCode'])		? $user['Location']['ZipCode'] : '';
		$form_fields['country']           = isset($user['Location']['Country'])		? $user['Location']['Country'] : '';
		$form_fields['email']             = isset($user['email'])		? $user['email'] : '';
		$form_fields['phone']             = isset($user['PhoneNumber'])	? $user['PhoneNumber'] : '';
		$form_fields['x_receipt_link_url']		= SJB_System::getSystemSettings('SITE_URL') . "/system/payment/callback/{$id}/{$invoice->getSID()}/";

		return $form_fields;
	}

	function getPaymentFromCallbackData($callback_data)
	{
		$invoiceStatus = SJB_Invoice::INVOICE_STATUS_PENDING;
		$invoice_sid = isset($callback_data['merchant_order_id']) ? $callback_data['merchant_order_id'] : null;

		if (is_null($invoice_sid)) {
			$this->errors['INVOICE_ID_IS_NOT_SET'] = 1;
			return null;
		}

		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);

		if (is_null($invoice)) {
			$this->errors['NONEXISTED_INVOICE_ID_SPECIFIED'] = 1;
			return null;
		}

		if ($invoice->getStatus() != SJB_Invoice::INVOICE_STATUS_UNPAID) {
			$this->errors['INVOICE_IS_NOT_UNPAID'] = $invoice->getStatus();
			return null;
		}

		if ($callback_data['key'] != $this->getMD5key($invoice)) {
			$this->errors['INVOICE_STATUS_NOT_VERIFIED'] = 1;
			return null;
		}

		if ($callback_data['credit_card_processed'] != 'Y'
			|| (isset($callback_data['fraud_status']) && $callback_data['fraud_status'] != 'pass')) {
			$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
		} else {
			$invoice->setStatus($invoiceStatus);
		}

		if (!$this->checkPaymentAmount($invoice)) {
			return null;
		}
		$id = $this->details->getProperty('id');
		$invoice->setPropertyValue('payment_method', $id->getValue());
		SJB_InvoiceManager::saveInvoice($invoice);

		if (isset($callback_data['sale_id'])){
			$transactionId = $callback_data['sale_id'];
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

	function handleRecurringNotification($callback_data)
	{
		if (!isset($callback_data['sale_id']) || !isset($callback_data['vendor_id']) || !isset($callback_data['invoice_id'])) {
			return;
		}
		$properties		= $this->getProperties();
		$secret_word	= $properties['secret_word']->getValue();
		$expected_md5	= strtoupper(md5($callback_data['sale_id'] . $callback_data['vendor_id'] . $callback_data['invoice_id'] . $secret_word));

		if ((!isset($callback_data['md5_hash'])) || ($callback_data['md5_hash'] != $expected_md5)) {
			return; //платеж не от 2Checkout
		}

		$invoice_sid = null;
		if (isset($callback_data['vendor_order_id']))
			$invoice_sid = $callback_data['vendor_order_id'];

		if (is_null($invoice_sid))
			return;

		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);
		if (is_null($invoice))
			return null;

		switch ($callback_data['message_type']) {
			case 'RECURRING_INSTALLMENT_SUCCESS':

				if (empty($callback_data['sale_id'])) {
					return null;
				}

				$vendorApi = $this->initAPI();

				if (!$saleDetails = $this->getSaleDetails($vendorApi, $callback_data['sale_id'])) {
					return null;
				}

				$itemCount = $callback_data['item_count'];
				$userSid = $invoice->getUserSID();
				$paymentHandler = new SJB_PaymentHandler($invoice->getSID(), '2checkout');
				$items = $invoice->getPropertyValue('items');
				if (!empty($items['products'])) {
					$recurringProductsInfo = array();
					for ($i = 1; $i < $itemCount+1; $i++) {
						if (!empty($callback_data['item_id_' . $i])) {
							$invoice->setSID(null);
							$invoice->setDate(null);
							$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
							$reactivation = true;

							foreach ($items['products'] as $key => $product) {
								if ($product == $callback_data['item_id_' . $i]) {
									$productInfo = $invoice->getItemValue($key);
									$recurringID = !empty($callback_data['sale_id']) ? $callback_data['sale_id'] : false;
									$recurringProductsInfo[$key] = $productInfo;
									$paymentHandler->setProduct($productInfo);
									$paymentHandler->setRecurringID($recurringID);
									$invoiceID = 0;
									if ($saleDetails && is_array($saleDetails)) {
										foreach ($saleDetails as $lineitems) {
											if ($product == $lineitems->vendor_product_id) {
												$invoiceID = $lineitems->invoice_id;
											}
										}
									}
									$paymentHandler->createContract($userSid, $invoiceID, $reactivation, 'active');
								}
							}
						}
					}
					$invoice->setNewPropertiesToInvoice($recurringProductsInfo);
					$invoice->setCallbackData($callback_data);
					$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PAID);
					$id = $this->details->getProperty('id');
					$invoice->setPropertyValue('payment_method', $id->getValue());
					SJB_InvoiceManager::saveInvoice($invoice);
					SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());
					$this->processTransaction($callback_data, $invoice);
				}

				break;
			case 'ORDER_CREATED':
			case 'FRAUD_STATUS_CHANGED':

				if (empty($callback_data['fraud_status']) || (!in_array($callback_data['fraud_status'], array('pass','wait')))) {
					return;
				} else {
					$fraudStatus = $callback_data['fraud_status'];
				}

				$saleDetails = false;
				if ($callback_data['sale_id']) {
					$vendorApi = $this->initAPI();
					if (!$saleDetails = $this->getSaleDetails($vendorApi, $callback_data['sale_id'])) {
						return null;
					}
				}

				$itemCount = $callback_data['item_count'];
				$user_sid = $invoice->getUserSID();
				$paymentHandler = new SJB_PaymentHandler($invoice->getSID(), '2checkout');
				$items = $invoice->getPropertyValue('items');
				if (!empty($items['products'])) {
					$reactivation = false;
					for ($i = 1; $i < $itemCount+1; $i++) {
						if (!empty($callback_data['item_id_'.$i])) {
							foreach ($items['products'] as $key => $product) {
								$recurring = !empty($callback_data['item_rec_install_billed_' . $i]) ? true : false;
								if ($product == $callback_data['item_id_' . $i]) {
									$productInfo = $invoice->getItemValue($key);
									$recurringID = false;
									if ($recurring) {
										$recurringID = !empty($callback_data['sale_id']) ? $callback_data['sale_id'] : false;
										$recurringProductsInfo[$key] = $productInfo;
									}
									$paymentHandler->setProduct($productInfo);
									$paymentHandler->setRecurringID($recurringID);
									$invoiceID = 0;
									if ($saleDetails && is_array($saleDetails)) {
										foreach ($saleDetails as $lineitems) {
											if ($product == $lineitems->vendor_product_id) {
											$invoiceID = $lineitems->invoice_id;
											}
										}
									}
									$status = 'active';
									if ($fraudStatus != 'pass') {
										$status = 'pending';
									}
									$paymentHandler->createContract($user_sid, $invoiceID, $reactivation, $status);
								}
							}
						} else {
							foreach ($items['products'] as $product) {
								if ($product == -1) {
									$type = $items['custom_info'][1]['type'];
									$paymentHandler->setProduct($items['custom_info'][1]);
									switch ($type) {
										case 'featuredListing':
											$paymentHandler->makeFeatured($invoice);
											break;
										case 'priorityListing':
											$paymentHandler->makePriority($invoice);
											break;
										case 'activateListing':
											$paymentHandler->activateListing($invoice);
											break;
									}
								}
							}
						}
					}
				} else {
					if (empty($fraudStatus) || $fraudStatus != 'pass') {
						return;
					}
				}
				$invoice->setCallbackData($callback_data);

				if ($fraudStatus == 'pass') {
					$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PAID);
				}

				$id = $this->details->getProperty('id');
				$invoice->setPropertyValue('payment_method', $id->getValue());
				SJB_InvoiceManager::saveInvoice($invoice);

				if ($fraudStatus != 'pass') {
					return;
				}

				SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());
				$this->processTransaction($callback_data, $invoice);
				break;
			case 'RECURRING_INSTALLMENT_FAILED':
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
				SJB_InvoiceManager::saveInvoice($invoice);
				break;
			case 'REFUND_ISSUED':
				$itemCount = $callback_data['item_count'];
				$user_sid = $invoice->getUserSID();
				$paymentHandler = new SJB_PaymentHandler($invoice->getSID(), '2checkout');
				$items = $invoice->getPropertyValue('items');
				if (!empty($items['products'])) {
					for($i = 1; $i <= $itemCount; $i++) {
						if (!empty($callback_data['item_id_'.$i])) {
							$paymentHandler->setProduct($callback_data['item_id_'.$i]);
							$paymentHandler->deleteContract($callback_data['invoice_id'], $callback_data['item_id_'.$i], $user_sid);
						}
					}
					foreach ($items['products'] as $product) {
						if ($product == -1) {
							$type = $items['custom_info'][1]['type'];
							$paymentHandler->setProduct($items['custom_info'][1]);
							switch ($type) {
								case 'featured_listing':
									$paymentHandler->unmakeFeatured($user_sid, $invoice->getPropertyValue('total'));
									break;
								case 'priority_listing':
									$paymentHandler->unmakePriority($user_sid, $invoice->getPropertyValue('total'));
									break;
								case 'activate_listing':
									$paymentHandler->deactivateListing($user_sid, $invoice->getPropertyValue('total'));
									break;
								default:
									break;
							}
						}
					}
				}
				$invoice->setCallbackData($callback_data);
				$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_UNPAID);
				SJB_InvoiceManager::saveInvoice($invoice);
				break;
			case 'RECURRING_STOPPED':
			case 'RECURRING_COMPLETE':
			default:
				break;
		}
	}

	function getMD5key($invoice)
	{
		$properties  = $this->details->getProperties();

		$secret_word 		= $properties['secret_word']->getValue();
		$twoco_account_id 	= $properties['2co_account_id']->getValue();

		$total = sprintf('%.02f',$invoice->getPropertyValue('total'));
		$invoice_sid = SJB_Request::getVar('order_number', '1');

		return strtoupper(md5($secret_word . $twoco_account_id . $invoice_sid . $total));
	}

	function cancelSubscription($subscriptionId, $invoiceID = false)
	{
		if (!$invoiceID) {
			return 'lineitem_id was not found in response';
		}

		$vendorApi = $this->initAPI();

		$result = $vendorApi->detailSale(array('sale_id' => $subscriptionId));

		if (!$result['success']) {
			return $result['response'];
		}

		$response = $result['response'];
		if ($response->response_code != 'OK') {
			return $response->response_message;
		}

		$invoicesCount = count($response->sale->invoices) - 1;
		if (!isset($response->sale->invoices) || !is_array($response->sale->invoices[$invoicesCount]->lineitems)) {
			return 'lineitem_id was not found in response';
		}

		$lineitemId = false;
		foreach ($response->sale->invoices[$invoicesCount]->lineitems as $lineitems) {
			if ($invoiceID == $lineitems->invoice_id) {
				$lineitemId = $lineitems->lineitem_id;
				break;
			}
		}

		if (empty($lineitemId)) {
			return 'lineitem_id was not found in response';
		}

		$result = $vendorApi->stopLineitemRecurring(array('lineitem_id' => $lineitemId));
		if (!$result['success']) {
			return $result['response'];
		}

		$response = $result['response'];
		if ($response->response_code != 'OK') {
			return $response->response_message;
		}

		return true;
	}

	function getPaymentStatusFromCallbackData($callbackData)
	{
		if (isset($callbackData['fraud_status'])) {
			$fraudStatus = $callbackData['fraud_status'];
			switch ($fraudStatus) {
				case 'wait':
					return 'Pending';
				case 'fail':
					return 'Error';
			}
		}

		// https://www.2checkout.com/static/va/documentation/INS/index.html#ins_message_parameters
		// invoice_status values:
		//   approved
		//   pending
		//   deposited
		//   declined
		$invoiceStatus = isset($callbackData['invoice_status']) ? strtolower($callbackData['invoice_status']) : '';
		switch ($invoiceStatus) {
			case 'approved':
			case 'deposited':
				return 'Successful';
			case 'pending':
				return 'Pending';
			case 'declined':
				return 'Error';
		}
		return 'Notification';
	}

	/**
	 * @param $callbackData
	 * @param $invoice
	 */
	public function processTransaction($callbackData, $invoice)
	{
		$transactionID = $callbackData['sale_id'];
		$transactionInfo = array(
			'transaction_id' => $transactionID,
			'invoice_sid'    => $invoice->getSID(),
			'amount'         => $invoice->getPropertyValue('total'),
			'payment_method' => '2checkout',
			'user_sid'       => $invoice->getPropertyValue('user_sid')
		);
		$transaction = new SJB_Transaction($transactionInfo);
		SJB_TransactionManager::saveTransaction($transaction);
	}

	/**
	 * @return TwoCheckoutVendorAPI
	 */
	private function initAPI()
	{
		$properties  = $this->getProperties();
		$isSandbox = $properties['sandbox']->getValue();
		if ($isSandbox) {
			$apiUrl = 'https://sandbox.2checkout.com/api/';
		} else {
			$apiUrl = 'https://www.2checkout.com/api/';
		}
		$apiUsername = $properties['2co_api_user_login']->getValue();
		$apiPassword = $properties['2co_api_user_password']->getValue();

		return new TwoCheckoutVendorAPI($apiUrl, $apiUsername, $apiPassword);
	}

	/**
	 * @param $vendorApi TwoCheckoutVendorAPI
	 * @param $saleId
	 * @return array
	 */
	private function getSaleDetails($vendorApi, $saleId)
	{
		$saleDetails = array();
		$result = $vendorApi->detailSale(array('sale_id' => $saleId));

		if ($result['success']) {
			$response = $result['response'];
			if ($response->response_code == 'OK') {
				$invoicesCount = count($response->sale->invoices) - 1;
				if (isset($response->sale->invoices[$invoicesCount]->lineitems)) {
					$saleDetails = $response->sale->invoices[$invoicesCount]->lineitems;
				}
			}
		}

		return $saleDetails;
	}

}
