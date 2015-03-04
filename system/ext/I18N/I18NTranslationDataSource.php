<?php


class I18NTranslationDataSource
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
		$this->tr_admin =& $tr_admin;
	}
	
	function gettext($phrase_id, $domain_id, $lang)
	{
		return $this->tr->get($phrase_id, $domain_id, $lang);
	}
	
	function &getTranslation($phrase_id, $domain_id, $lang_id) 
	{		
		$translation = $this->gettext($phrase_id, $domain_id, $lang_id);
		
		$translationData = TranslationData::create();
		$translationData->setPhraseID($phrase_id);
		$translationData->setDomainID($domain_id);
		$translationData->setLanguageID($lang_id);
		$translationData->setTranslation($translation);
		
		return $translationData;
	}
}
