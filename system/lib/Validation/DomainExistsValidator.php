<?php

class SJB_DomainExistsValidator
{
	/**
	 * @var I18NDatasource
	 */
	protected $langDataSource;

	/**
	 * @param I18NDatasource $langDataSource
	 */
	function setLanguageDataSource($langDataSource)
	{
		$this->langDataSource = $langDataSource;
	}
	
	function isValid($value){
		$items = $this->langDataSource->getDomainsData();
		for ($i = 0; $i < count($items); $i++) {
			$item =& $items[$i];
			if ((string)$value === (string)$item->getID())
				return true;
		}
		return false;

	}
}

