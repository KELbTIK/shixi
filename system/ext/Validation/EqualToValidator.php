<?php


class EqualToValidator
{
	function setBaseValue($base_value)
	{
		$this->base_value = $base_value;
	}
	
	function isValid($value)
	{
		return $value == $this->base_value;
	}
}

?>