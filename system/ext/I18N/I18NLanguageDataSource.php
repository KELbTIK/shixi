<?php


class I18NLanguageDataSource
{
	/**
	 * @var I18NContext
	 */
	var $context;

	/**
	 * @var Translation2AdminWrapper
	 */
	var $tr;
	
	/**
	 * @var Translation2AdminWrapper
	 */
	var $tr_admin;

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context)
	{
		$this->context = $context;
	}

	/**
	 * @param Translation2AdminWrapper $tr
	 */
	function setTranslator(Translation2AdminWrapper $tr)
	{
		$this->tr = $tr;
	}

	/**
	 * @param Translation2AdminWrapper $tr_admin
	 */
	function setTrAdmin(Translation2AdminWrapper $tr_admin)
	{
		$this->tr_admin = $tr_admin;
	}

	/**
	 * @param LangData $langData
	 * @return mixed
	 */
	function addLanguage(LangData $langData)
	{	
		$lang_data = array
		(
			'lang_id'    => $langData->getID(),
			'name'       => $langData->getCaption(),
			'meta'       => $langData->getMeta(),
			'error_text' => $langData->getErrorText(),
			'encoding'   => 'utf-8',
		);
		
		return $this->tr_admin->addLang($lang_data);
	}
	
	function getLanguageData($lang_id)
	{				
		$lang_data = $this->tr_admin->getLang($lang_id, 'array');
		return LangData::createLangDataFromServer($lang_data);
	}

	/**
	 * @return array
	 */
	function getLanguagesData()
	{		
		$langsData = array();
		$langs_data = $this->tr_admin->getLangs('array');

		foreach($langs_data as $lang_data) {
			$langsData[] = LangData::createLangDataFromServer($lang_data);
		}
		return $langsData;
	}
	
	function updateLanguage(&$langData)
	{
		$lang_data = array
		(
			'lang_id'    => $langData->getID(),
			'name'       => $langData->getCaption(),
			'meta'       => $langData->getMeta(),
			'error_text' => $langData->getErrorText(),
			'encoding'   => 'utf-8',
		);
		
		return $this->tr_admin->updateLang($lang_data);
	}
	
	function deleteLanguage($lang_id)
	{
		return $this->tr_admin->removeLang($lang_id);
	}

	function exportLanguage($lang_id)
	{
		return $this->tr_admin->exportLang($lang_id);
	}
}
