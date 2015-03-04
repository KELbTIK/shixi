<?php


class Translation2AdminWrapper
{
	/**
	 * @var I18NAdminRepository
	 */
	var $repo;

	/**
	 * @var I18NContext
	 */
	var $context;

	/**
	 * @param I18NAdminRepository $repo
	 */
	function setRepository(I18NAdminRepository $repo)
	{
		$this->repo = $repo;
	}

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context)
	{
		$this->context =& $context;
	}
	
	function get($phrase_id, $domain_id, $lang_id)
	{					
		$trAdmin = $this->repo->get($lang_id);
		return $trAdmin->get($phrase_id, $domain_id, $lang_id);
	}

	/**
	 * @param array $lang_data
	 * @return mixed
	 */
	function addLang($lang_data)
	{		
		$default_lang = $this->context->getDefaultLang();
		$trDefaultLanguageAdmin = $this->repo->get($default_lang);
		$trAdmin =& $this->repo->create($lang_data['lang_id']);
		$res = $trAdmin->addLang($lang_data);
		$domains = $trDefaultLanguageAdmin->getPageNames();

		foreach($domains as $domain)
		{
			$phrases = $trDefaultLanguageAdmin->getRawPage($domain, $default_lang);
			foreach(array_keys($phrases) as $phrase_id)
			{
				$trAdmin->add($phrase_id, $domain, array($lang_data['lang_id'] => null));
			}
		}
		return $res;
	}

	/**
	 * @param array $lang_data
	 * @return mixed
	 */
	function updateLang($lang_data)
	{
		$trAdmin = $this->repo->get($lang_data['lang_id']);
		return $trAdmin->updateLang($lang_data);
	}
	
	function getLang($lang_id, $format)
	{
		$trAdmin = $this->repo->get($lang_id);
		return $trAdmin->getLang($lang_id, $format);
	}
	
	function removeLang($lang_id)
	{		
		return $this->repo->remove($lang_id);
	}
	
	function getLangs($format)
	{
		$lang_list = $this->repo->getLangList();
		
		$langs_data = array();
		
		foreach ($lang_list as $lang_id)
		{
			$langs_data[$lang_id] = $this->getLang($lang_id, $format);
		}
		
		return $langs_data;
	}
	
	function add($phrase_id, $domain_id, $translations) 
	{
		$result = true;
		
		foreach ($translations as $lang_id => $translation)
		{
			$translation = array($lang_id => $translation);
			
			$trAdmin = $this->repo->get($lang_id);
			$result &= $trAdmin->add($phrase_id, $domain_id, $translation);
		}
		
		return $result;
	}
	
	function addPage($pageID, $langs_data)
	{
		foreach ($langs_data as $lang_data) {
			$trAdmin = $this->repo->get($lang_data['id']);
			$trAdmin->addPage($pageID);
		}	
	}
	
	function update($phrase_id, $domain_id, $translations) 
	{
		$result = true;
		
		foreach ($translations as $lang_id => $translation)
		{
			$translation = array($lang_id => $translation);
			
			$trAdmin = $this->repo->get($lang_id);
			$result &= $trAdmin->update($phrase_id, $domain_id, $translation);
		}
		
		return $result;
	}
	
	function remove($phrase_id, $domain_id) 
	{
		$result = true;
		
		$lang_list = $this->repo->getLangList();
		
		foreach ($lang_list as $lang_id)
		{
			$trAdmin = $this->repo->get($lang_id);
			$result &= $trAdmin->remove($phrase_id, $domain_id);
		}
		
		return $result;
	}
	
	function getPageNames()
	{
		$lang_id = $this->context->getDefaultLang();		
		$trAdmin = $this->repo->get($lang_id);
		return $trAdmin->getPageNames();
	}
	
	function getRawPage($domain_id, $lang_id)
	{
		$trAdmin = $this->repo->get($lang_id);
		return $trAdmin->getRawPage($domain_id, $lang_id);
	}

	function exportLang($lang_id)
	{
		$trAdmin = $this->repo->get($lang_id);
		return $trAdmin->storage->exportLang();
	}
}
