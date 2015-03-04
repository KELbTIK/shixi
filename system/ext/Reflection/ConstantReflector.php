<?php


class ConstantReflector
{
	var $value;
	
	function setValue($value)
	{
		$this->value = $value;
	}
	
	function get()
	{
		return $this->value;
	}
}

?>