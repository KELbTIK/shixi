<?php


require_once 'I18N/TranslationData.php';

class PhraseData
{
	var $id;
	var $domain_id;
	var $translations;
	
	function createPhraseDataFromServer($phrase_id, $domain_id, $translations) 
	{
		$phraseData = new PhraseData();
		$phraseData->setID($phrase_id);
		$phraseData->setDomainID($domain_id);		
		$phraseData->setTranslations($translations);
		return $phraseData;	
	}
	
	public static function createPhraseDataFromClient($phrase_data)
	{
		$phraseData = new PhraseData();
		
		$phraseData->setID($phrase_data['phrase']);
		$phraseData->setDomainID($phrase_data['domain']);
		
		$translationsData = array();
		
		foreach ($phrase_data['translations'] as $lang_id => $translation) {
			$translationData = TranslationData::create();
			$translationData->setPhraseID($phrase_data['phrase']);
			$translationData->setDomainID($phrase_data['domain']);
			$translationData->setLanguageID($lang_id);
			$translationData->setTranslation($translation);
			
			$translationsData[] = $translationData;
		}
		
		$phraseData->setTranslations($translationsData);
		
		return $phraseData;		
	}
	
	function create()
	{
		return new PhraseData();
	}
		
	function setID($id) 					{ $this->id = $id; }	
	function setDomainID($domain_id) 		{ $this->domain_id = $domain_id; }	
	function setTranslations($translations) { $this->translations = $translations; }
	
	function getID() 			{ return $this->id; }	
	function getDomainID() 		{ return $this->domain_id; }	
	function getTranslations() 	{ return $this->translations; }
}

?>