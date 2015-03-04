<?php


class I18NPhraseSearchCriteria
{
	var $phrase_id;
	var $domain_id;
	
	function getPhraseID()
	{
		return $this->phrase_id;
	}
	
	function getDomainID()
	{
		return $this->domain_id;
	}
	
	function setPhraseID($phrase_id)
	{
		$this->phrase_id = $phrase_id;
	}
	
	function setDomainID($domain_id)
	{
		$this->domain_id = $domain_id;
	}
}

?>