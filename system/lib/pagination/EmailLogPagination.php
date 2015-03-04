<?php

class SJB_EmailLogPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'emails';
		$this->countActionsButtons = 1;
		$this->popUp = true;
		$this->actionsForSelect = array('resend' => 'Resend');

		$fields = array(
			'date'      => array('name' => 'Date'),
			'subject'   => array('name' => 'Subject'),
			'email'     => array('name' => 'Email'),
			'username'  => array('name' => 'Username'),
			'status'    => array('name' => 'Status'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('date', 'DESC', 50);
	}
}