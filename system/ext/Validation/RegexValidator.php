<?php


class RegexValidator
{
	function setRegex($regex)
	{
		$this->regex = $regex;
	}

	function isValid($value)
	{
		return (bool)preg_match($this->regex, $value);
	}
}

?>