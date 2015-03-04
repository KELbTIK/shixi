<?php


class SJB_SetDefaultLanguageAction
{
	function SJB_SetDefaultLanguageAction(&$i18n, $lang_id)
	{
		$this->i18n = $i18n;
		$this->lang_id = $lang_id;
	}
	
	function canPerform()
	{
		$this->validator = $this->i18n->createSetDefaultLanguageValidator($this->lang_id);
		return $this->validator->isValid();
	}
	
	function perform()
	{
		$this->i18n->setDefaultLanguage($this->lang_id);
	}
	
	function getErrors()
	{
		return $this->validator->getErrors();
	}
}

