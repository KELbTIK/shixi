<?php


class SJB_UpdateLanguageAction
{
	/**
	 * @param  SJB_I18N $i18n
	 * @param  $lang_data
	 * @return void
	 */
	function SJB_UpdateLanguageAction(&$i18n, $lang_data)
	{
		$this->i18n =& $i18n;
		$this->lang_data = $lang_data;
	}
	
	function canPerform()
	{
		$this->errors = $this->_validate();
		return empty($this->errors);
	}
	
	function perform()
	{
		return $this->i18n->updateLanguage($this->lang_data);
	} 

	function getErrors()
	{
		return $this->errors;
	}

	function _validate()
	{
		$errors = array();
		
		$validator = $this->i18n->createUpdateLanguageValidator($this->lang_data);
		
		if (!$validator->isValid())
		{
			$errors = $validator->getErrors();
		}
		return $errors;
	}
}

