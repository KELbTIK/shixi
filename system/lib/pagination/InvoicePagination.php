<?php

class SJB_InvoicePagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'invoices';
		$this->countActionsButtons = 2;
		$actionsForSelect = array(
			'paid'   => array('name' => 'Mark Paid'),
			'unpaid' => array('name' => 'Mark Unpaid'),
			'delete' => array('name' => 'Delete'),
		);
		$this->setActionsForSelect($actionsForSelect);

		$fields = array(
			'sid'               => array('name' => 'Invoice #'),
			'username'          => array('name' => 'Customer Name'),
			'date'              => array('name' => 'Date'),
			'payment_method'    => array('name' => 'Payment Method'),
			'total'             => array('name' => 'Total'),
			'status'            => array('name' => 'Status'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('sid', 'DESC', 10);
	}


}

