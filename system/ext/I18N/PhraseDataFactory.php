<?php


class PhraseDataFactory
{
	/**
	 * @param I18NLanguageDataSource $languageDataSource
	 */
	function setLanguageDataSource(I18NLanguageDataSource $languageDataSource)
	{
		$this->languageDataSource = $languageDataSource;
	}

	/**
	 * @param I18NTranslationDataSource $translationDataSource
	 */
	function setTranslationDataSource(I18NTranslationDataSource $translationDataSource)
	{
		$this->translationDataSource = $translationDataSource;
	}
	
	function &create($phrase_id, $domain_id)
	{
		require_once('PhraseData.php');
		
		$phraseData = new PhraseData();
		
		$translations =& $this->getPhraseAllTranslations($phrase_id, $domain_id);
		
		$phraseData->setID($phrase_id);
		$phraseData->setDomainID($domain_id);
		$phraseData->setTranslations($translations);
		
		return $phraseData;
	}
	
	function &getPhraseAllTranslations($phrase_id, $domain_id)
	{
		$langsData = $this->languageDataSource->getLanguagesData();
		foreach (array_keys($langsData) as $i) {
			$langData = $langsData[$i];
			$lang_id = $langData->getID();
			$translationsData[] = $this->translationDataSource->getTranslation($phrase_id, $domain_id, $lang_id);
		}
		return $translationsData;
	}
}

?>