<?php


class I18NAdmin
{
    /**
     *
     * @var I18NDatasource
     */
	var $data_source;
	
	function setDataSource($data_source) 
	{
		$this->data_source = $data_source;
	}

	/**
	 * @param LangData $langData
	 * @return mixed
	 */
	function addLanguage(LangData $langData)
	{		
		return $this->data_source->addLanguage($langData);		
	}
	
	function &getLanguageData($lang_id) 
	{
		$data =& $this->data_source->getLanguageData($lang_id);
		return $data;
	}
	
	function getLanguagesData() 
	{
		return $this->data_source->getLanguagesData();
	}
	
	function updateLanguage(&$langData) 
	{				
		return $this->data_source->updateLanguage($langData);		
	}
	
	function deleteLanguage($lang_id) 
	{
		return $this->data_source->deleteLanguage($lang_id);
	}

	function addDomain($name) 
	{
		return $this->data_source->addDomain($name);	
	}
	
	function addPhrase(&$phraseData) 
	{
		return $this->data_source->addPhrase($phraseData);	
	}
		
	function &getPhraseData($phrase_id, $domain_id) 
	{
		$phraseData =& $this->data_source->getPhraseData($phrase_id, $domain_id);
		return $phraseData;
	}	
	
	function updatePhrase(&$phraseData) 
	{				
		return $this->data_source->updatePhrase($phraseData);		
	}
	
	function deletePhrase($phrase_id, $domain_id) 
	{
		return $this->data_source->deletePhrase($phrase_id, $domain_id);
	}
	
	function &getDomainPhrases($domainId)
	{
		$data =& $this->data_source->getDomainPhrases($domainId);
		return $data;
	}
	
	function getDomainsData()
	{
		return $this->data_source->getDomainsData();
	}

	function exportLanguage($lang_id)
	{
		return $this->data_source->exportLanguage($lang_id);
	}
}
