<?php

class SJB_Taxes extends SJB_Object
{
	public $details = null;
	
	function __construct($info = array())
	{
		$this->db_table_name = 'taxes';
		$this->details = new SJB_TaxesDetails($info);

	}
}
