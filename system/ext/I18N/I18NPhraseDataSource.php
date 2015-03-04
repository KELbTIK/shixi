<?php


class I18NPhraseDataSource
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
	 * @var PhraseDataFactory
	 */
	var $phraseDataFactory;

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
	 * @param PhraseDataFactory $phraseDataFactory
	 */
	function setPhraseDataFactory(PhraseDataFactory $phraseDataFactory)
	{
		$this->phraseDataFactory = $phraseDataFactory;
	}
	
	function &getPhraseData($phrase_id, $domain_id)
	{						
		$phraseData =& $this->phraseDataFactory->create($phrase_id, $domain_id);
		
		return $phraseData;
	}
	
	function addPhrase(&$phraseData)
	{		
		$phrase_data = $this->_get_savable_phrase_data_structure($phraseData);
		
		return $this->tr_admin->add($phrase_data['id'], $phrase_data['domain'], $phrase_data['translations']);
	}
	
	function updatePhrase(&$phraseData)
	{		
		$phrase_data = $this->_get_savable_phrase_data_structure($phraseData);
		
		return $this->tr_admin->update($phrase_data['id'], $phrase_data['domain'], $phrase_data['translations']);
	}
	
	function _get_savable_phrase_data_structure(&$phraseData)
	{		
		$translations = array();
		$translationsData = $phraseData->getTranslations();
		
		foreach ($translationsData as $key => $value)
		{
			$translationData =& $translationsData[$key];
			
			$translations[$translationData->getLanguageID()] = $translationData->getTranslation();
		}
		
		return array
		(
			'id' 			=> $phraseData->getID(),
			'domain' 		=> $phraseData->getDomainID(),
			'translations' 	=> $translations,
		);
	}
	
	function deletePhrase($phrase_id, $domain_id)
	{			
		return $this->tr_admin->remove($phrase_id, $domain_id);
	}
	
	function &getDomainPhrases($domainId)
	{
		$page = $this->tr->getRawPage($domainId, $this->context->getDefaultLang());
		$phrases = array();
		foreach (array_keys($page) as $phraseId)
		{
			$phrases[] =& $this->getPhraseData($phraseId, $domainId);
		}
		return $phrases;
	}
}

