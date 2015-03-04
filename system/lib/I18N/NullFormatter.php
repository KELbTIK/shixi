<?php


class SJB_NullFormatter
{
	function getOutput($value)
	{
		return $value;
	}
	
	function getInput($value)
	{
		return $value;
	}
	
	function isValid($value)
	{
		return true;
	}
}

