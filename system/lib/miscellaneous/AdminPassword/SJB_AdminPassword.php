<?php

class SJB_AdminPassword extends SJB_Object
{

	public $details = null;

	function __construct($info = array())
	{
		$this->db_table_name = 'administrator';
		$this->details = new SJB_AdminPasswordDetails($info);
	}

}
