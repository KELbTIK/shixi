<?php


class TranslationData
{
	var $phrase_id;
	var $domain_id;
	var $lang_id;
	var $translation;	
	
	public static function create()
	{
		return new TranslationData();
	}
	
	function getPhraseID() 		{ return $this->phrase_id; }		
	function getDomainID() 		{ return $this->domain_id; }		
	function getLanguageID() 	{ return $this->lang_id; }		
	function getTranslation() 	{ return $this->translation; }	
	
	function setPhraseID($phrase_id) 		{ $this->phrase_id = $phrase_id; }		
	function setDomainID($domain_id) 		{ $this->domain_id = $domain_id; }		
	function setLanguageID($lang_id) 		{ $this->lang_id = $lang_id; }		
	function setTranslation($translation) 	{ $this->translation = $translation; }
}
