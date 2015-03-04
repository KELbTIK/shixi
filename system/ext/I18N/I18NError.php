<?php


class I18NError
{
	var $error;
	
	function I18NError($error)
	{
		$this->error = $error;
	}
	function getError()
	{
		return $this->error;
	}
}

?>