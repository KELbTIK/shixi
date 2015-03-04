<?php

class SJB_GuestAlertsManagePagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'guest alerts';
		$this->countActionsButtons = 2;
		$actionsForSelect = array(
			'activate'      => array('name' => 'Activate'),
			'deactivate'    => array('name' => 'Deactivate'),
			'confirm'       => array('name' => 'Confirm'),
			'delete'        => array('name' => 'Delete')
		);
		$this->setActionsForSelect($actionsForSelect);

		$fields = array(
			'sid'               => array('name' => 'ID'),
			'email'             => array('name' => 'Email'),
			'alert_type'        => array('name' => 'Alert Type', 'isSort' => false),
			'email_frequency'   => array('name' => 'Email frequency'),
			'subscription_date' => array('name' => 'Subscription date'),
			'status'            => array('name' => 'Status'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('subscription_date', 'DESC', 50);
	}


}

