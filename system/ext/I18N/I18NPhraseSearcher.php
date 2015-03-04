<?php


class I18NPhraseSearcher
{
	/**
	 * @var I18NDataSource
	 */
	var $dataSource;
	var $matcher;

	/**
	 * @param I18NDataSource $dataSource
	 */
	function setDataSource(I18NDataSource &$dataSource)
	{
		$this->dataSource =& $dataSource;
	}

	function setMatcher(&$matcher)
	{
		$this->matcher =& $matcher;
	}
	
	function &search(&$criteria)
	{
		$domainsData =& $this->getDomainsData($criteria->getDomainID());
		$phrasesData =& $this->getAllPhrases($domainsData);
		
		$query = $criteria->getPhraseID();
		if(!empty($query))
		{
			$phrasesData =& $this->filterPhrases($query, $phrasesData);
		}
		return $phrasesData;
	}
	
	function &getDomainsData($domain_id)
	{
		if (empty($domain_id))
		{
			$domainsData = $this->dataSource->getDomainsData();
		}
		else
		{
			$domainData =& $this->dataSource->getDomainData($domain_id);
			$domainsData = array(&$domainData);
		}
		return $domainsData;
	}

	function &getAllPhrases(&$domainsData)
	{	
		$phrasesData = array();
		foreach (array_keys($domainsData) as $i)
		{
			$domainData =& $domainsData[$i];
			$domainPhrases =& $this->dataSource->getDomainPhrases($domainData->getID());
			$phrasesData = array_merge($phrasesData, $domainPhrases);
		}
		return $phrasesData;
	}
	
	function &filterPhrases($query, &$phrasesData)
	{
		$this->matcher->setQuery($query);
		
		$filteredPhrasesData = array();
		foreach (array_keys($phrasesData) as $i)
		{
			if ($this->matcher->match($phrasesData[$i]->getID()))
			{
				$filteredPhrasesData[] =& $phrasesData[$i];
			}
		}
		return $filteredPhrasesData;
	}
}

?>