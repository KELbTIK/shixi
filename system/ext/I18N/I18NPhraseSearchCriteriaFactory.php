<?php


class I18NPhraseSearchCriteriaFactory
{
	function create($criteria)
	{
		require_once('I18N/I18NPhraseSearchCriteria.php');
		$phrase_id = isset($criteria['phrase_id']) ? $criteria['phrase_id'] : null;
		$domain_id = isset($criteria['domain']) ? $criteria['domain'] : null;
		$phraseSearchCriteria = new I18NPhraseSearchCriteria();
		$phraseSearchCriteria->setPhraseID($phrase_id);
		$phraseSearchCriteria->setDomainID($domain_id);
		return $phraseSearchCriteria;
	}
}

