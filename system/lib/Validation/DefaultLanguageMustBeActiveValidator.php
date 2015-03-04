<?php


class SJB_DefaultLanguageMustBeActiveValidator
{
	var $dataReflector;
	var $languageIsNotDefaultValidator;
	
	function setDataReflector($dataReflector)
	{
		$this->dataReflector = $dataReflector;
	}
	
	function setLanguageIsNotDefaultValidator($validator)
	{
		$this->languageIsNotDefaultValidator = $validator;
	}
	
	function isValid($value)
	{
		$languageId = $this->dataReflector->get('languageId');
		return ($this->languageIsNotDefaultValidator->isValid($languageId) || (bool)$value
					|| ($this->dataReflector->get('activeFrontend') && $this->dataReflector->get('activeBackend')));
	}
}

