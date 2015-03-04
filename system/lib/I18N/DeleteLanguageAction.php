<?php


class SJB_DeleteLanguageAction
{
	function SJB_DeleteLanguageAction($i18n, $lang_id)
	{
		$this->i18n = $i18n;
		$this->lang_id = $lang_id;
	}

	function canPerform()
	{
		$this->validator = $this->i18n->createDeleteLanguageValidator($this->lang_id);
		return $this->validator->isValid();
	}

	function perform()
	{
		$this->i18n->deleteLanguage($this->lang_id);
	}

	function getErrors()
	{
		return $this->validator->getErrors();
	}
}

