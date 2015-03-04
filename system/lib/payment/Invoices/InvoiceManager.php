<?php

class SJB_InvoiceManager extends SJB_ObjectManager
{
	public static function saveInvoice($invoice)
	{
		$serializedItemsDetails['items'] = $invoice->getPropertyValue('items');
		$products = isset($serializedItemsDetails['items']['products']) ? $serializedItemsDetails['items']['products'] : array();
		$products = implode(',', $products);
		$invoice->addProperty(
			array ( 'id'		=> 'serialized_items_info',
					'type'		=> 'text',
					'value'		=> serialize($serializedItemsDetails),
					'is_system' => true,
			)
		);
		$invoice->addProperty(
				array ( 'id'		=> 'product_sid',
						'type'		=> 'string',
						'value'		=> $products,
						'is_system' => true,
				)
		);
		$invoice->deleteProperty('items');

		$serializedTaxDetails['tax_info'] = $invoice->getPropertyValue('tax_info');
		$invoice->addProperty(
			array ( 'id'		=> 'serialized_tax_info',
					'type'		=> 'text',
					'value'		=> serialize($serializedTaxDetails),
					'is_system' => true,
			)
		);
		$invoice->deleteProperty('tax_info');

		$user_sid = $invoice->getPropertyValue('user_sid');
		$user_info = SJB_UserManager::getUserInfoBySID($user_sid);
		if (!empty($user_info['parent_sid'])) {
			$invoice->setPropertyValue('subuser_sid', $user_sid);
			$invoice->setPropertyValue('user_sid', $user_info['parent_sid']);
		}

		$dateProperty = $invoice->getProperty('date');
		$value = $dateProperty->getValue();
		if (!$dateProperty->type->getConvertToDBDate() && $value != null) {
			$invoice->setPropertyValue('date', SJB_I18N::getInstance()->getDate($value));
		}

		$invoice->setPropertyValue('sub_total', SJB_I18N::getInstance()->getFloat($invoice->getPropertyValue('sub_total')));
		$invoice->setPropertyValue('total', SJB_I18N::getInstance()->getFloat($invoice->getPropertyValue('total')));
		parent::saveObject('invoices', $invoice);

		if ($value == null) {
			SJB_DB::query('UPDATE `invoices` SET `date`= NOW() WHERE `sid`=?n',$invoice->getSID());
		}
	}

	public static function getInvoiceInfoBySID($invoiceSID)
	{
		$invoice_info = parent::getObjectInfoBySID('invoices', $invoiceSID);
		if (!empty($invoice_info['serialized_items_info'])) {
			$serialized_items_info = unserialize($invoice_info['serialized_items_info']);
			$invoice_info = array_merge($invoice_info, $serialized_items_info);
		}
		if (!empty($invoice_info['serialized_tax_info'])) {
			$serialized_tax_info = unserialize($invoice_info['serialized_tax_info']);
			$invoice_info = array_merge($invoice_info, $serialized_tax_info);
		}
		return $invoice_info;
    }

	public static function getObjectBySID($invoiceSID)
	{
    	$invoiceInfo = SJB_InvoiceManager::getInvoiceInfoBySID($invoiceSID);
    	
		if (is_null($invoiceInfo)) {
    		return null;
		}
    	$invoice = new SJB_Invoice($invoiceInfo);
		$invoice->setSID($invoiceSID);
		return $invoice;
	}

	public static function deleteInvoiceBySID($invoiceSID)
	{
		return SJB_InvoiceManager::deleteObject('invoices', $invoiceSID);
	}

	public static function markPaidInvoiceBySID($invoiceSID)
	{
		return SJB_DB::query("UPDATE `invoices` SET `status` = ?s WHERE `sid` = ?n", SJB_Invoice::INVOICE_STATUS_PAID, $invoiceSID);
	}

	public static function markUnPaidInvoiceBySID($invoiceSID)
	{
		return SJB_DB::query("UPDATE `invoices` SET `status` = ?s WHERE `sid` = ?n", SJB_Invoice::INVOICE_STATUS_UNPAID, $invoiceSID);
	}

	public static function getPaymentForms($invoice)
	{
		$activeGateways = SJB_PaymentGatewayManager::getActivePaymentGatewaysList();
		$gatewaysFormInfo = array();
		foreach ($activeGateways as $gatewayInfo) {
			if ($invoice->isRecurring() && empty($gatewayInfo['recurrable'])) {
				continue;
			}
			$gateway = SJB_PaymentGatewayManager::getObjectByID($gatewayInfo['id'], $invoice->isRecurring());
			$gatewaysFormInfo[$gateway->getPropertyValue('id')] = $gateway->buildTransactionForm($invoice);
		}
		return $gatewaysFormInfo;
	}

	public static function createInvoiceTemplate($invoiceInfo)
	{
		$items = array();
		foreach ($invoiceInfo['items']['products'] as $key=>$productSID){
			if ($productSID > -1) {
				$product_info = SJB_ProductsManager::getProductInfoBySID($productSID);
				$items[$key]['product'] = $product_info['name'];
				$items[$key]['show_qty'] = $product_info['product_type'] == 'post_listings' && $product_info['pricing_type'] == 'volume_based';
			} else {
				$items[$key]['product'] = $invoiceInfo['items']['custom_item'][$key];
				$items[$key]['show_qty'] = true;
			}
			$items[$key]['qty'] = !empty($invoiceInfo['items']['qty'][$key])?$invoiceInfo['items']['qty'][$key]:'unlimited';
			$items[$key]['amount'] = $invoiceInfo['items']['amount'][$key];
		}
		$invoiceInfo['date'] = SJB_I18N::getInstance()->getDate($invoiceInfo['date']);
		$invoiceInfo['items'] = $items;

		if ($invoiceInfo['include_tax']) {
			$invoiceInfo['tax_amount'] = $invoiceInfo['tax_info']['tax_amount'];
			$invoiceInfo['tax_name']   = $invoiceInfo['tax_info']['tax_name'];
			$invoiceInfo['taxSid']     = $invoiceInfo['tax_info']['sid'];
		} else {
			$invoiceInfo['taxSid'] = false;
		}

		return $invoiceInfo;
	}

	public static function getExistingInvoiceSID($userSID, $itemsInfo, $taxInfo, $status, $isRecurring)
	{
		return SJB_DB::queryValue('select `sid` from `invoices` where `status` = ?s  and `user_sid` = ?n and `serialized_items_info` = ?s and `serialized_tax_info` = ?s and `is_recurring` = ?n',
			$status, $userSID, serialize($itemsInfo), serialize($taxInfo), $isRecurring);
	}

	public static function getTotalPrice($subTotal, $taxAmount, $priceIncludesTax)
	{
		if ($priceIncludesTax) {
			$total = $subTotal;
		} else {
			$total = $subTotal + $taxAmount;
		}
		return $total;
	}

	public static function generateInvoice($items, $userSID, $subTotalPrice, $successPageUrl, $isRecurring = false)
	{
		$taxInfo = SJB_TaxesManager::getTaxInfoByUserSidAndPrice($userSID, $subTotalPrice);
		$totalPrice = SJB_InvoiceManager::getTotalPrice($subTotalPrice, $taxInfo['tax_amount'], $taxInfo['price_includes_tax']);
		$invoiceSID = null;
		if ($totalPrice > 0) {
			$invoiceSID = SJB_InvoiceManager::getExistingInvoiceSID($userSID, $items, $taxInfo, SJB_Invoice::INVOICE_STATUS_UNPAID, $isRecurring);
		}
		if (!$invoiceSID) {
			$invoiceInfo = array(
				'user_sid' => $userSID,
				'include_tax' => !empty($taxInfo['sid']) ? 1 : 0,
				'total' => $totalPrice,
				'sub_total' => $subTotalPrice,
				'success_page_url' => $successPageUrl,
				'status' => $totalPrice == 0 ? SJB_Invoice::INVOICE_STATUS_VERIFIED : SJB_Invoice::INVOICE_STATUS_UNPAID,
				'tax_info' => $taxInfo,
				'items' => $items,
				'is_recurring' => $isRecurring,
			);
			$invoice = new SJB_Invoice($invoiceInfo);
			SJB_InvoiceManager::saveInvoice($invoice);
			$invoiceSID = $invoice->getSID();
		}
		return $invoiceSID;
	}

	public static function getInvoicesInfo()
	{
		//TODO: можно ускорить и сделать так же как в листингах
		$res = array();
		$periods = array(
			"Today" => "`i`.`date` >= CURDATE()",
			"This Week" => "`i`.`date` >= FROM_DAYS(TO_DAYS(CURDATE()) - WEEKDAY(CURDATE()))",
			"This Month" => "`i`.`date` >= FROM_DAYS(TO_DAYS(CURDATE()) - DAYOFMONTH(CURDATE()) + 1)");

		foreach ($periods as $key => $value) {
			$res[$key]['paid'] = SJB_DB::query("SELECT IFNULL(SUM(i.total), 0) AS `payment` FROM `invoices` i WHERE {$value} AND `status` = 'Paid'");
			$res[$key]['unpaid'] = SJB_DB::query("SELECT IFNULL(SUM(i.total), 0) AS `payment` FROM `invoices` i WHERE {$value} AND `status` = 'Unpaid'");
		}
		return $res;
	}

	public static function getTotalInvoices()
	{
		$res = SJB_DB::query("SELECT IFNULL(SUM(i.total), 0) AS `payment` FROM `invoices` i WHERE `status` = 'Paid'");
		if (count($res) == 0) {
			return '0';
		}
		$res = array_shift($res);
		return $res['payment'];
	}

	public static function getTotalUnpaidInvoices()
	{
		$res = SJB_DB::query("SELECT IFNULL(SUM(i.total), 0) AS `payment` FROM `invoices` i WHERE `status` = 'Unpaid'");
		if (count($res) == 0) {
			return '0';
		}
		$res = array_shift($res);
		return $res['payment'];
	}
	
	public static function getInvoiceQuantityByProductSID($productSID)
	{
		return SJB_DB::queryValue("SELECT COUNT(*) FROM invoices WHERE FIND_IN_SET(?s, `product_sid`)", $productSID);
	}
}


