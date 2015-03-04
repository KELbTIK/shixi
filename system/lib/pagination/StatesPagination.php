<?php

class SJB_StatesPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'states/regions';
		$this->fields = null;
		$actionsForSelect = array(
			'activate'      => array('name' => 'Activate'),
			'deactivate'    => array('name' => 'Deactivate'),
			'delete'        => array('name' => 'Delete')
		);
		$this->setActionsForSelect($actionsForSelect);
		
		parent::__construct(null, null, 20);
	}
}

