<?php


class SJB_LanguageIsActiveValidator
{
	var $langDataSource;
	var $languageExistsValidator;
	
	function setLanguageDataSource(&$langDataSource)
	{
		$this->langDataSource =& $langDataSource;
	}
	
	function setLanguageExistsValidator($validator)
	{
		$this->languageExistsValidator = $validator;
	}
	
	function isValid($value)
	{
		if (!$this->languageExistsValidator->isValid($value)) {
			return false;
		}
		
		$langData =& $this->langDataSource->getLanguageData($value);

		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')) {
			return $langData->getActiveBackend();
		}
		return $langData->getActiveFrontend();
	}	
}

