<?php

class SJB_PasswordCurType extends SJB_Type
{
    function SJB_PasswordCurType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->default_template = 'password_cur.tpl';
	}

	function isEmpty()
	{
		return trim($this->property_info['value']) == ""; 
	}

	function getSavableValue()
	{
       	return $this->property_info['value'];
	}
      
    function getDisplayValue()
	{
		return null;
	}

	function getSQLValue()
	{
		return md5($this->property_info['value']);
	}
    
}

