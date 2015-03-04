<?php

class SJB_CountriesPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'countries';
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

