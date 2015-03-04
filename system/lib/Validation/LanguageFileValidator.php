<?php


class SJB_LanguageFileValidator
{
	var $dataReflector = null;
	var $errors = array();
	
	function setDataReflector(&$dataReflector)
	{
		$this->dataReflector =& $dataReflector;
	}
	
	function isValid($value)
	{
		$trAdminFactory = new Translation2AdminFactory();
		$trAdmin = $trAdminFactory->createTrAdmin($value);
		
		if (strpos(strtolower(get_class($trAdmin)), 'translation2_admin') === false)
		{
			$errors[] = 'UPLOADED_LANG_FILE_STRUCTURE_IS_INVALID';
			SJB_Logger::error('UPLOADED_LANG_FILE_STRUCTURE_IS_INVALID');
			return false;
		}
		
		$file_langs_list = $trAdmin->getLangs();
		$import_lang_id = (string) $this->dataReflector->get('languageId');
		
		if (!array_key_exists($import_lang_id, $file_langs_list))
		{
			$errors[] = 'UPLOADED_LANG_FILE_DOESNOT_HAVE_NECESSARY_LANGUAGE';
			SJB_Logger::error('UPLOADED_LANG_FILE_DOESNOT_HAVE_NECESSARY_LANGUAGE');
			return false;
		}
		
		return true;
	}
	
	function getErrors()
	{
		return array();
	}
}

