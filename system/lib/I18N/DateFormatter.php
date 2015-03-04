<?php

class SJB_DateFormatter
{
	var $format;
	
	function getOutput($date)
	{
		return strftime($this->format, strtotime($date));
	}
	
	function getInput($date)
	{
		$date = trim($date);
		if (empty($date))
			return '';
		$parsed_date = strptime($date, $this->format);
		return sprintf("%s-%02s-%02s", $parsed_date['tm_year'] + 1900, $parsed_date['tm_mon'] + 1, $parsed_date['tm_mday']);
	}
	
	function isValid($date)
	{
		$parsed_date = strptime($date, $this->format);
		if ($parsed_date === false)
			return false;
		$parsed_date['tm_year'] += 1900;
		$parsed_date['tm_mon'] += 1;
		$timestamp = mktime(0, 0, 0, $parsed_date['tm_mon'], $parsed_date['tm_mday'], $parsed_date['tm_year']);
		$date_to_compare = strftime($this->format, $timestamp);
		return isset($parsed_date['tm_year']) && isset($parsed_date['tm_mon']) && isset($parsed_date['tm_mday']) && $date == $date_to_compare;
	}
	
	function setDateFormat($format)
	{
		$this->format = $format;
	}
}
