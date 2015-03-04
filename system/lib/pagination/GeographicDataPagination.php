<?php

class SJB_GeographicDataPagination extends SJB_Pagination
{
	public function __construct()
	{
		$this->item = 'ZipCodes';
		$this->countActionsButtons = 2;
		$this->actionsForSelect = array('delete' => 'Delete');

		$fields = array(
			'name'          => array('name' => 'Zip Code'),
			'longitude'     => array('name' => 'Longitude'),
			'latitude'      => array('name' => 'Latitude'),
			'city'          => array('name' => 'City'),
			'state'         => array('name' => 'State'),
			'state_code'    => array('name' => 'State Code'),
			'country_name'  => array('name' => 'Country'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('name', 'ASC', 100);
	}

	public function setUniqueUrlParam($urlParam)
	{
		$this->uniqueUrlParams = $urlParam;
	}
}
