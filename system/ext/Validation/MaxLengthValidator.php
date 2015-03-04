<?php


class MaxLengthValidator
{
	function isValid($value)
	{
		return (strlen($value) <= $this->maxLength);
	}
	
	function setMaxLength($maxLength)
	{
		$this->maxLength = $maxLength;
	}
}

?>