<?php


class SJB_PhraseExistsValidator
{
	function setLanguageDataSource(&$langDataSource)
	{
		$this->langDataSource =& $langDataSource;
	}
	
	function setDataReflector(&$dataReflector)
	{
		$this->dataReflector =& $dataReflector;
	}
	
	function isValid($value)
	{
		$domainId = $this->dataReflector->get('domainId');
		$phrases =& $this->langDataSource->getDomainPhrases($domainId);
		for ($i = 0; $i < count($phrases); $i++)
		{
			$phrase =& $phrases[$i];
			if (strcmp($value, $phrase->getID()) === 0) {
				return true;
			}
		}
		return false;
	}
}

