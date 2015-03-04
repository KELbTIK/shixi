<?php

class SJB_Bitly extends SJB_Object
{
	public function SJB_Bitly($bitlyInfo)
	{
		$this->db_table_name = 'settings';
		$this->details = new SJB_BitlyDetails($bitlyInfo);
	}
}
