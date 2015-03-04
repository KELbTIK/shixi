<?php


class SJB_TranslationFilterStorage
{
	var $session;
	
	function setSession(&$session)
	{
		$this->session =& $session;
	}
	
	function store($value)
	{
		$this->session->setValue('TRANSLATION_FILTER', $value);
	}
	
	function restore()
	{
		return $this->session->getValue('TRANSLATION_FILTER');
	}
}

