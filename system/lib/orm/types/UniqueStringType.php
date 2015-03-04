<?php

class SJB_UniqueStringType extends SJB_StringType
{
	function isValid($addValidParam = false)
	{	
		$this->property_info['addValidParam'] = $addValidParam;
		return  SJB_Type::isValid();
	}
}
