<?php


require_once 'I18N/DomainData.php';

class I18NDomainDataSource
{
	/**
	 * @var I18NContext
	 */
	var $context;

	/**
	 * @var Translation2AdminWrapper
	 */
	var $tr;

	/**
	 * @var Translation2AdminWrapper
	 */
	var $tr_admin;

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context)
	{
		$this->context = $context;
	}

	/**
	 * @param Translation2AdminWrapper $tr
	 */
	function setTranslator(Translation2AdminWrapper $tr)
	{
		$this->tr = $tr;
	}

	/**
	 * @param Translation2AdminWrapper $tr_admin
	 */
	function setTrAdmin(Translation2AdminWrapper $tr_admin)
	{
		$this->tr_admin = $tr_admin;
	}
	
	function getDomainsData()
	{
		$domainIDs = $this->tr_admin->getPageNames();
		
		$domains = array();
		foreach ($domainIDs as $domainId) {
			$domain = DomainData::create();
			$domain->setID($domainId);
			$domains[] = $domain;
		}
		return $domains;
	}
	
	function &getDomainData($domain_id)
	{
		$domainData = DomainData::create();
		$domainData->setID($domain_id);
		return $domainData;
	}
	
	function addDomain($name)
	{
		$i18n = SJB_ObjectMother::createI18N();
		$langs_data = $i18n->getLanguagesData();
		return $this->tr_admin->addPage($name, $langs_data);
	}
}

