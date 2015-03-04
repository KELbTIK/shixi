<?php

class I18NLanguageSettings
{
	/**
	 * @var string
	 */
	public $currentLangID;

	/**
	 * @var I18NDataSource
	 */
	public $datasource;

	/**
	 * @var I18NContext
	 */
	public $context;

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context){
		$this->context = $context;
	}

	/**
	 * @param I18NDataSource $datasource
	 */
	function setDatasource(I18NDataSource $datasource){
		$this->datasource = $datasource;
	}

 	function getDecimalPoint(){
		$langData =& $this->_getLangData();
 		return $langData->getDecimalSeparator();
	}
	function getThousandsSeparator(){
		$langData =& $this->_getLangData();
 		return $langData->getThousandsSeparator();
	}
	function getDecimals(){
		$langData =& $this->_getLangData();
 		return $langData->getDecimals();
	}
	function getDateFormat(){
		$langData =& $this->_getLangData();
 		return $langData->getDateFormat();
	}
	function getTheme(){
		$langData =& $this->_getLangData();
 		return $langData->getTheme();
	}

	/**
	 * @return LangData
	 */
	function &_getLangData(){
 		$langData = $this->datasource->getLanguageData($this->context->getLang());
 		return $langData;
	}

	/**
	 * @return string
	 */
	public function getCurrentLangID()
	{
		return $this->currentLangID;
	}

	/**
	 * @param string $currentLangID
	 */
	public function setCurrentLangID($currentLangID)
	{
		$this->currentLangID = $currentLangID;
	}

}
