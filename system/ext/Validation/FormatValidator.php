<?php


class FormatValidator
{
	function setValidSymbols($validSymbols)
	{
		$this->validSymbols = $validSymbols;
	}
	function setRegex($regex)
	{
		$this->regex = $regex;
	}

	function isValid($format)
	{
		preg_match_all($this->regex, $format, $matches);
		foreach($matches[1] as $symbol)
		{
			if (empty($symbol) || strpos($this->validSymbols, $symbol) === false)
				return false;
		}
		return true;
	}
}

?>