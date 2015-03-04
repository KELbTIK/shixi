<?php


class SJB_LanguageIsDefaultValidator
{
	var $context;
	
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	
	function isValid($value)
	{
		return $value == $this->context->getDefaultLang();
	}
}

