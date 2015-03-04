<?php


class AndValidator
{
	var $validators = array();
	
	function add(&$validator)
	{
		$this->validators[] = &$validator;
	}
	
	function isValid($value)
	{

		for($i = 0 ; $i < count($this->validators) ; $i++){
			$validator =& $this->validators[$i];
			if(!$validator->isValid($value))
				return false;
		}
		return true;
	}
}

?>