<?php


class SJB_IntFormatter
{
	var $thousands_separator;
	
	function getOutput($value)
	{
		return number_format($value, 0, ',', $this->thousands_separator);
	}
	
	function getInput($value)
	{
		return str_replace($this->thousands_separator, '', $value);
	}
	
	function isValid($value)
	{
		return preg_match("/^[+-]?\d+(\\" . $this->thousands_separator . "\d{3})*$/", $value);
	}
	
	function setThousandsSeparator($thousands_separator)
	{
		$this->thousands_separator = $thousands_separator;
	}
}

