<?php

class SJB_PaymentLogPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'payments';
		$this->actionsForSelect = false;
		$this->isCheckboxes = false;

		$fields = array(
			'date'      =>    array('name' => 'Date'),
			'gateway'   => array('name' => 'Gateway'),
			'message'   => array('name' => 'Gateway Response'),
			'status'    =>  array('name' => 'Status'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('date', 'DESC', 50);
	}
}
