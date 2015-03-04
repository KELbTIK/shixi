<?php

class DomainData
{
	var $id;
	
	function setID($id)
	{
		$this->id = $id;
	}
	
	function getID()
	{
		return $this->id;
	}
	
	public static function create()
	{
		return new DomainData();
	}
}

