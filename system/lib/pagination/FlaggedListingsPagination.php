<?php

class SJB_FlaggedListingsPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'flags';
		$this->popUp = true;
		$actionsForSelect = array(
			'remove'        => array('name' => 'Remove Flag'),
			'deactivate'    => array('name' => 'Deactivate Listing'),
			'delete'        => array('name' => 'Delete Listing'),
		);
		$this->setActionsForSelect($actionsForSelect);

		$fields = array(
			'sid'           => array('name' => 'ID'),
			'title'         => array('name' => 'Title'),
			'active'        => array('name' => 'Status'),
			'username'      => array('name' => 'Listing Owner'),
			'flag_user'     => array('name' => 'Flagged By'),
			'date'          => array('name' => 'Flag Date'),
			'flag_reason'   => array('name' => 'Flag Reason'),
			'comment'       => array('name' => 'Comment'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('date', 'DESC', 20);
	}
}

