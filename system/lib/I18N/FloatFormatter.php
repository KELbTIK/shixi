<?php


class SJB_FloatFormatter
{
	var $thousands_separator;
	var $decimals;
	var $decimal_point;
	
	function getOutput($value)
	{
		if (is_numeric($value)) {
			return number_format($value, $this->decimals, $this->decimal_point, $this->thousands_separator);
		}
		return $value;
	}
	
	function getInput($value)
	{
		$value = str_replace($this->thousands_separator, '', $value);
		$value = str_replace($this->decimal_point, '.', $value);
		return $value;
	}
	
	function isValid($value)
	{
		if (empty($this->decimal_point) && is_numeric($value))
			return true;
		
		if (empty($this->decimal_point))
			$this->decimal_point = '.';
		
		return preg_match("/^[+-]?\d+(\\" . $this->thousands_separator . "\d{3})*(\\" . $this->decimal_point . "\d+)?$/", $value);
	}
	
	function setThousandsSeparator($separator)
	{
		$this->thousands_separator = $separator;
	}
	
	function setDecimals($decimals)
	{
		$this->decimals = $decimals;
	}
	
	function setDecimalPoint($decimal_point)
	{
		$this->decimal_point = $decimal_point;
	}
}

