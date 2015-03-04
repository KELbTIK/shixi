<?php

/**
 * @package SystemClasses
 * @subpackage PageConfig
 */

/**
 * @package SystemClasses
 */
class SJB_PageConfig
{
	/**
	 * @var string URI
	 */
	var $uri;
	var $module;
	var $function;
	var $template;
	var $title;
	var $parameters;
	var $keywords;
	var $page_exists;
	var $page_id;
	var $has_raw_output;
	var $numberOfViews;

	var $description;

	function SJB_PageConfig($uri)
	{
		$this->page_exists = false;
		$this->uri = $uri;
	}

	function ExtractPageInfo()
	{
	}
	
	function getPageId()
	{
		return $this->page_id;
	}
	
	function getNumberOfViews()
	{
		return $this->numberOfViews;
	}
	
	public static function getPageConfig($uri)
	{
		if (SJB_PageConfig::isSystemPageRequested ($uri) )
			$requested_page_config = new SJB_SystemPageConfig ($uri);
		else
			$requested_page_config = new SJB_UserPageConfig ($uri);

		$requested_page_config->ExtractPageInfo();
		return $requested_page_config;
	}

	/**
	 * Indicates whether page exists
	 *
	 * @return boolean Is page exists
	 */
	function pageExists()
	{
		return $this->page_exists;
	}

	/**
	 * Indicates whether directory exists
	 *
	 * @return boolean Is directory exists
	 */
	function dirExists()
	{
		return isset($GLOBALS['CONFIG'][$this->uri.'/']);
	}

	function getMainContentModule()
	{
		return $this->module;
	}

	function getMainContentFunction()
	{
		return $this->function;
	}
	
	function setMainContentFunction($function_name)
	{
		$this->function = $function_name;
	}

	function getPageTemplate()
	{
		return $this->template;
	}

	function getParameters()
	{
		return $this->parameters;
	}
	
	function getPageUri()
	{
		return $this->uri;
	}
	
	function getPageTitle()
	{
		return $this->title;
	}
	
	function getPageKeywords()
	{
		return $this->keywords;
	}

	function getPageDescription()
	{
		return $this->description;
	}

	function hasRawOutput()
	{
		return $this->has_raw_output;
	}

	public static function isSystemPageRequested ($uri)
	{
		$system_url_base = 	SJB_System::getSystemSettings("SYSTEM_URL_BASE");
		return strpos ($uri, $system_url_base) === 1;
	}

}


class SJB_UserPageConfig extends SJB_PageConfig
{
	function ExtractPageInfo()
	{
		if ($page_info = SJB_PageManager::extract_page_info ($this->uri, SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE')) ) {
			$this->page_id = $page_info['ID'];
			$this->page_exists = true;

			$this->module = $page_info['module'];
			$this->function = $page_info['function'];
			$this->template = $page_info['template'];
			$this->title = $page_info['title'];
			$this->parameters = unserialize ($page_info['parameters']);
			$this->keywords = $page_info['keywords'];
			
			$this->description = $page_info['description'];

			$uri = $page_info['uri'];
			if ($uri == $this->uri . '/') {
				$this->uri .= '/';
				if (preg_match("/\?/", $_SERVER['REQUEST_URI']) && !(preg_match("/\/\?/", $_SERVER['REQUEST_URI']))) {
					$_SERVER['REQUEST_URI'] = str_replace("?", "/?", $_SERVER['REQUEST_URI']);
				} else {
					$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] . '/';
				}
			}

			$this->has_raw_output = SJB_System::doesFunctionHaveRawOutput($this->module, $this->function);
		}
	}

	function UserPagesConfig($uri)
	{
		$this->access_type = 'user';
		$this->definePageExisting();
	}

	function SetPageConfig($module, $function, $template, $parameters)
	{
		$this->module = $module;
		$this->function = $function;
		$this->template = $template;
		$this->parameters = $parameters;
	}

	/**
	 * @param string $template page template name
	 */
	function setPageTemplate($template)
	{
		$this->template = $template;
	}

	function definePageExisting()
	{
		$this->page_exists = SJB_PageManager::doesPageExists ($this->uri, SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') );
	}

}

class SJB_SystemPageConfig extends SJB_PageConfig
{
	function ExtractPageInfo()
	{
		$page_info = SJB_System::getModuleAndFunctionBySystemURL ($this->uri);

		if (!empty($page_info) && SJB_System::isFunctionAccessible($page_info['module'], $page_info['function'])) {
			$this->module = $page_info['module'];
			$this->function = $page_info['function'];
			$this->parameters = array();
			$this->template = SJB_Settings::getSettingByName('DEFAULT_PAGE_TEMPLATE');
			$this->page_exists = true;
		}

		$this->has_raw_output = SJB_System::doesFunctionHaveRawOutput($this->module, $this->function);
	}

}

