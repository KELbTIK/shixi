<?php

require_once('I18N/I18NError.php');

class I18NTranslator
{
	function setContext(&$context)
	{
		$this->context =& $context;
	}
	
	function setDatasource(&$datasource)
	{
		$this->datasource =& $datasource;
	}
	
	function gettext($domain_id, $phrase_id, $mode)
	{
		if (empty($phrase_id)) {
			return '';
		}

		if (empty($domain_id))
			$domain_id = $this->context->getDefaultDomain();

		$lang = $this->context->getLang();
		if (!empty($lang))
			$text = $this->_gettext($domain_id, $phrase_id, $lang);

		if (empty($text))
			$text = $this->_getDecoratedText($domain_id, $phrase_id, $lang, $mode);

		if (empty($text)) {
			$text = $phrase_id;
			if (strpos($text, '$') !== false) {
				$text = preg_replace('/(\$[_a-z]([.]?\w+?)*)/i', '{$1}', $text);
			}
		}
		
		return $text;
	}
	
	function _getDecoratedText($domain_id, $phrase_id, $lang, $mode)
	{
		if (empty($mode)) 
			$mode = $this->context->getDefaultMode();
			
		if ($mode === 'highlight') {
			$p = $this->context->getHighlightedPattern();
			$admin_site_url = $this->context->getAdminSiteUrl();
			$encoded_phrase_id = urlencode($phrase_id);
			return sprintf($p, $domain_id, $phrase_id, $lang, $encoded_phrase_id, $admin_site_url);
		}
		return null;
	}

	function _gettext($domain_id, $phrase_id, $lang)
	{
		$text = $this->datasource->gettext($domain_id, $phrase_id, $lang);
		if (strpos($text, '$') !== false) {
			$text = preg_replace('/(\$[_a-z]([.]?\w+?)*)/i', '{$1}', $text);
		}
		return $text;
	}
	
	function _trigger_error($err)
	{
		return new I18NError($err);
	}
}
