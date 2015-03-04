<?php

class SJB_Invoice extends SJB_Object
{
	const INVOICE_STATUS_PAID = 'Paid';
	const INVOICE_STATUS_UNPAID = 'Unpaid';
	const INVOICE_STATUS_PENDING = 'Pending';
	const INVOICE_STATUS_VERIFIED = 'Verified';

	public function SJB_Invoice($invoiceInfo = array())
	{
		$this->details = new SJB_InvoiceDetails($invoiceInfo);
	}

	public function isValid()
	{
		$errors = array();
		$items = $this->getPropertyValue('items');
		foreach ($items['products'] as $key => $product) {
			if ($product == -1) {
				if (empty($items['custom_item'][$key])) {
					$errors[] = 'CUSTOM_ITEM_FIELD_IS_EMPTY';
				}
				if (empty($items['qty'][$key])) {
					$errors[] = 'PRODUCT_QUANTITY_IS_NOT_SET';
				}
				if ($items['price'][$key] === '' || $items['price'][$key] === NULL) {
					$errors[] = 'PRODUCT_PRICE_IS_NOT_SET';
				}
			} elseif (empty($product) || !SJB_ProductsManager::isProductExists($product)) {
				$errors[] = 'PRODUCT_FIELD_IS_EMPTY';
			}
		}
		return $errors;
	}

	public function isRecurring()
	{
		$isRecurring = $this->getPropertyValue('is_recurring');
		return !empty($isRecurring);
	}

	public function getCallbackData()
	{
		return unserialize($this->getPropertyValue('callback_data'));
	}

	public function getStatus()
	{
		return $this->getPropertyValue('status');
	}

	public function getSuccessPageURL()
	{
		return $this->getPropertyValue('success_page_url');
	}

	public function getUserSID()
	{
		$subuser_sid = $this->getPropertyValue('subuser_sid');
		if (!empty($subuser_sid)){
			return $subuser_sid;
		}
		return $this->getPropertyValue('user_sid');
	}

	public function getProductNames()
	{
		$name = array();
		$items = $this->getPropertyValue('items');
		foreach ($items['products'] as $key => $product) {
			if ($product == -1){
				$name[] = $items['custom_item'][$key];
			}
			else{
				$productInfo = SJB_ProductsManager::getProductInfoBySID($product);
				$name[] = $productInfo['name'];
			}
		}
		return implode(", ", $name);
	}

	public function getItemValue($index)
	{
		$info = $this->details->properties['items']->value;

		$item = array(
			'sid' => isset($info['products'][$index])?$info['products'][$index]:null,
			'qty' => isset($info['qty'][$index])?$info['qty'][$index]: null,
			'amount' => isset($info['amount'][$index])?$info['amount'][$index]:0,
			'shoppingCartRecord' => isset($info['custom_info'][$index]['shoppingCartRecord'])?$info['custom_info'][$index]['shoppingCartRecord']:null,
			'custom_info' => isset($info['custom_info'][$index])?$info['custom_info'][$index]: null,
			'banner_info' => isset($info['custom_info'][$index]['banner_info'])?$info['custom_info'][$index]['banner_info']: null
		);

		$productInfo = SJB_ProductsManager::getProductInfoBySID($info['products'][$index]);
		if (empty($productInfo['price'])) {
			$productInfo['price'] = $item['amount'];
		}
		return array_merge($productInfo, $item);
	}

	public function getItemsInfo()
	{
		$info = @unserialize($this->details->properties['serialized_items_info']->value);
		return $info['items'];
	}

	public function setCallbackData($callbackData)
	{
		$this->setPropertyValue('callback_data', serialize($callbackData));
	}

	public function setStatus($status)
	{
		$this->setPropertyValue('status', $status);
	}

	public function setVerificationResponse($response)
	{
		$this->setPropertyValue('verification_response', $response);
	}

	public function setRecurring($recurring)
	{
		$this->setPropertyValue('is_recurring', $recurring);
	}

	public function setDate($date)
	{
		$this->setPropertyValue('date', $date);
	}

	public function setNewPropertiesToInvoice($productsInfo)
	{
		$subTotal = 0;
		$items = array();
		foreach ($productsInfo as $key => $productInfo) {
			$items['products'][$key] = $productInfo['sid'];
			$items['qty'][$key] = !empty($productInfo['number_of_listings']) ? $productInfo['number_of_listings'] : null;
			$items['price'][$key] = $productInfo['price'];
			$items['amount'][$key] = $productInfo['amount'];
			$items['custom_item'][$key] = "";
			if ($productInfo['product_type'] == 'banners' && !empty($productInfo['banner_info'])) {
				$items['custom_info'][$key]['banner_info'] = $productInfo['banner_info'];
			}
			$subTotal += $productInfo['amount'];
		}

		$taxInfo = SJB_TaxesManager::getTaxInfoByUserSidAndPrice($this->getUserSID(), $subTotal);
		$totalPrice = SJB_InvoiceManager::getTotalPrice($subTotal, $taxInfo['tax_amount'], $taxInfo['price_includes_tax']);

		$this->setPropertyValue('total', $totalPrice);
		$this->setPropertyValue('sub_total', $subTotal);
		$this->setPropertyValue('tax_info', $taxInfo);
		$this->setPropertyValue('items', $items);
	}
}
