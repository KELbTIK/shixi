<?php


class NotEmptyValidator
{
	function isValid($value)
	{
		return !(empty($value) && $value !== '0');
	}
}

?>