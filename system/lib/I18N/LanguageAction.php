<?php


class SJB_LanguageAction
{
	function SJB_LanguageAction()
	{
		$this->errors = array();
	}
	
	function perform()
	{		
	}

	function canPerform()
	{
		if (SJB_System::getSystemSettings("isDemo"))
			$this->errors[] = "You don't have permissions for it. This is a Demo version of the software.";
		else
			$this->errors[] = 'UNKNOWN_ACTION';
		return false;
	}

	function getErrors()
	{
		return $this->errors;
	}
}

