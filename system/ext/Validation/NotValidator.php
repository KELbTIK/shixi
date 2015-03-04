<?php


class NotValidator
{
	function setValidator(&$validator)
	{
		$this->validator =& $validator;
	}

	function isValid($value)
	{
		return !$this->validator->isValid($value);
	}
}

?>