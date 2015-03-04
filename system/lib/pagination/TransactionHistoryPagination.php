<?php

class SJB_TransactionHistoryPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'transactions';
		$this->actionsForSelect = array('delete' => 'Delete');

		$fields = array(
			'date'              => array('name' => 'Date'),
			'transaction_id'    => array('name' => 'Transaction Id'),
			'username'          => array('name' => 'Username'),
			'invoice_sid'       => array('name' => 'Description'),
			'payment_method'    => array('name' => 'Payment Method'),
			'amount'            => array('name' => 'Amount'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('date', 'DESC', 100);
	}
}