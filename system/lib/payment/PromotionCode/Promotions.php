<?php

class SJB_Promotions extends SJB_Object
{
	public $details = null;
	
	function __construct($info = array())
	{
		$this->db_table_name = 'promotions';
		$this->details = new SJB_PromotionsDetails($info);
	}
}