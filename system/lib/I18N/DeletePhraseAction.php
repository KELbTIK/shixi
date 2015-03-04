<?php


class SJB_DeletePhraseAction
{
	function SJB_DeletePhraseAction($i18n, $phrase, $domain)
	{
		$this->i18n = $i18n;
		$this->phrase = $phrase;
		$this->domain = $domain;
		$this->errors = $this->_validate();
		$this->result = '';
	}

	function canPerform()
	{
		return empty($this->errors);
	}

	function perform()
	{
		$this->i18n->deletePhrase($this->phrase, $this->domain);
		$this->result = 'deleted';
	}

	function getErrors()
	{
		return $this->errors;
	}

	function _validate()
	{
		$errors = array();
		
		if(!$this->i18n->phraseExists($this->phrase, $this->domain))
		{
			$errors[] = "PHRASE_DOES_NOT_EXIST";
		}
		
		return $errors;
	}
}

