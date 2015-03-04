<?php

class SJB_EmailTemplate extends SJB_Object
{
	function __construct($info = array())
	{
		$this->details = new SJB_EmailTemplateDetails($info);
	}
	
	function getPropertyList()
	{
		$result = array();
		$property_list = array_keys($this->getProperties());
		
		foreach ($property_list as $property_name) {
			$result[$property_name] = $property_name;
		}
		return $result;
	}
}
