<?php

class SJB_PollsManagePagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'polls';
		$this->countActionsButtons = 2;
		$actionsForSelect = array(
			'activate'      => array('name' => 'Activate'),
			'deactivate'    => array('name' => 'Deactivate'),
			'delete'        => array('name' => 'Delete')
		);
		$this->setActionsForSelect($actionsForSelect);

		$fields = array(
			'sid'           => array('name' => 'ID'),
			'question'      => array('name' => 'Poll Question'),
			'user_group'    => array('name' => 'User Group'),
			'start_date'    => array('name' => 'Start Date'),
			'end_date'      => array('name' => 'Expiration Date'),
			'status'        => array('name' => 'Status'),
			'language'      => array('name' => 'Language'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('sid', 'DESC', 10);
	}


}

