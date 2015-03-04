<?php

class SJB_PaymentLog extends SJB_Object
{
	function SJB_PaymentLog($payment_info)
	{
		$this->db_table_name = 'payment_log';
		$this->details = new SJB_PaymentLogDetails($payment_info);
	}

	public static function writeToLog($payment, $result = false)
	{
		if (SJB_Settings::getSettingByName('notification_payment') != $payment->recipient_payment)
			$username = SJB_UserManager::getUserSIDbyPayment($payment->recipient_payment);
		if (!$username) {
			$admin = SJB_SubAdminManager::getUserSIDbyPayment($payment->recipient_payment);
			$admin = $admin ? $admin : 'admin';
		}
		$status = 'Delivered';
		if (!$result)
			$status = 'Undelivered';
		SJB_DB::query("INSERT INTO `payment_log` (`date`, `gateway`, `message`, `status`) VALUES (NOW(), ?s, ?s, ?s, ?s, ?s, ?s)", $payment->gateway, $payment->text, $status);
	}
}


class SJB_PaymentLogDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;

	function SJB_PaymentLogDetails($payment_info)
	{
		$details_info = self::getDetails();
		foreach ($details_info as $detail_info) {
			$detail_info['value'] = '';
			if (isset($payment_info[$detail_info['id']]))
				$detail_info['value'] = $payment_info[$detail_info['id']];

			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}

	public static function getDetails()
	{
		$details = array(
			array(
				'id' => 'date',
				'caption' => 'Date',
				'type' => 'date',
				'length' => '20',
				'is_required' => false,
				'is_system' => true,
				'order' => null,
			),
			array(
				'id' => 'gateway',
				'caption' => 'Gateway',
				'type' => 'list',
				'length' => '20',
				'list_values' => array(
					array('id' => '2Checkout',
						'caption' => '2Checkout'),
					array('id' => 'Authorize.Net SIM',
						'caption' => 'Authorize.Net SIM'),
					array('id' => 'Cash Payment',
						'caption' => 'Cash Payment'),
					array('id' => 'PayPal Standard',
						'caption' => 'PayPal Standard'),
					array('id' => 'Wire Transfer',
						'caption' => 'Wire Transfer'),
				),
				'is_required' => false,
				'is_system' => true,
				'order' => null,
			),
			array(
				'id' => 'message',
				'caption' => 'Message',
				'type' => 'text',
				'length' => '20',
				'is_required' => false,
				'is_system' => true,
				'order' => null,
			),
			array(
				'id' => 'status',
				'caption' => 'Status',
				'type' => 'list',
				'list_values' => array(
					array('id' => 'Pending',
						'caption' => 'Pending'),
					array('id' => 'Successful',
						'caption' => 'Successful'),
					array('id' => 'Error',
						'caption' => 'Error'),
					array('id' => 'Notification',
						'caption' => 'Notification'),
				),
				'is_required' => false,
				'is_system' => true,
				'order' => null,
			),
		);

		return $details;
	}
}

class SJB_PaymentLogManager extends SJB_ObjectManager
{
	public static function getPaymentLogInfoBySID($payment_sid)
	{
		return SJB_ObjectDBManager::getObjectInfo("payment_log", $payment_sid);
	}

	public static function recordPaymentLog($status, $gateway_caption, $gateway_response)
	{
		$obj = new SJB_PaymentLog(array(
			'date' => SJB_I18N::getInstance()->getDate(date('Y-m-d H:i:s')),
			'gateway' => $gateway_caption,
			'message' => print_r($gateway_response, true),
			'status' => $status,
		));
		SJB_ObjectDBManager::saveObject("payment_log", $obj);
	}
}

class SJB_PaymentLogCriteriaSaver extends SJB_CriteriaSaver
{
	function SJB_PaymentLogCriteriaSaver($searchId = 'PaymentSearcher')
	{
		$searchId = 'PaymentSearcher_' . $searchId;
		parent::SJB_CriteriaSaver($searchId, new SJB_PaymentLogManager);
	}
}

class SJB_PaymentLogSearcher extends SJB_Searcher
{
	/**
	 * @var null|\SJB_PaymentLogInfoSearcher
	 */
	var $infoSearcher = null;

	function SJB_PaymentLogSearcher($current_page, $items_per_page, $sorting_field = false, $sorting_order = false)
	{
		$this->infoSearcher = new SJB_PaymentLogInfoSearcher($current_page, $items_per_page, $sorting_field, $sorting_order);
		parent::__construct($this->infoSearcher, new SJB_PaymentLogManager);
	}

	function getAffectedRows()
	{
		return $this->infoSearcher->affectedRows;
	}
}

class SJB_PaymentLogInfoSearcher extends SJB_ObjectInfoSearcher
{
	public $current_page;
	public $items_per_page;
	public $sorting_field = false;
	public $sorting_order = false;
	public $affectedRows = 0;

	function SJB_PaymentLogInfoSearcher($current_page, $items_per_page, $sorting_field = false, $sorting_order = false)
	{
		parent::__construct('payment_log');
		$this->current_page = $current_page;
		$this->items_per_page = $items_per_page;
		$this->sorting_field = $sorting_field;
		$this->sorting_order = $sorting_order;
	}

	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		$SearchSqlTranslator = new SJB_PaymentLogSearchSQLTranslator($this->table_prefix);
		$sql_string = $SearchSqlTranslator->buildSqlQuery($this->criteria, $this->valid_criterion_number, array($this->sorting_field => $this->sorting_order));
		SJB_DB::query($sql_string);
		$this->affectedRows = SJB_DB::getAffectedRows();
		$paging_offset = ($this->current_page - 1) * $this->items_per_page;
		$sql_string .= " LIMIT {$paging_offset}, {$this->items_per_page}";
		return SJB_DB::query($sql_string);
	}
}

class SJB_PaymentLogSearchSQLTranslator extends SJB_SearchSqlTranslator
{
	private $sql = ' ';

	public function _getWhereSystemStatement($criteria)
	{
		foreach ($criteria as $property_criteria) {
			foreach ($property_criteria as $criterion) {
				$this->setSQLStringFromCriterion($criterion);
			}
		}
		return 'WHERE 1 ' . $this->sql;
	}

	private function setSQLStringFromCriterion($criterion)
	{
		if ($criterion->isValid()) {
			if ($criterion->property_name == 'keywords') {
				$criterion->property_name = 'gateway';
				$gateway_keywords = $criterion->getSystemSQL();
				$criterion->property_name = 'message';
				$message_keywords = $criterion->getSystemSQL();
				$this->sql .= " AND ({$gateway_keywords} OR {$message_keywords}) ";
			} else
				$this->sql .= " AND `{$this->object_table_prefix}`.{$criterion->getSystemSQL()} ";
		}
	}

}