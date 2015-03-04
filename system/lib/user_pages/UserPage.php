<?php

class SJB_UserPage
{
	var $pagedata = array();
	var $error = array();
	var $modules = array();
	var $functions = array();
	var $parameters = array();
	var $a_params = array();

	function loadPageDataFromDatabase($uri)
	{
		$this->pagedata = SJB_System::getUserPage($uri);
	}

	function setPageData($pagedata)
	{
		$this->pagedata = $pagedata;
	}

	function getDisplayedPageData()
	{
		$displayed_data = $this->pagedata;
		$displayed_data['parameters'] = $this->serializeParameters($displayed_data['parameters']);
		return $displayed_data;
	}

	function serializeParameters($parameters)
	{
		if (empty($parameters)) {
			return null;
		}
		else {
			$a = array();
			foreach ($parameters as $name => $value) {
				$this->a_params[$name] = $value;
				array_push($a, "{$name}={$value}");
			}
			return join("\n", $a);
		}
	}

	public static function extractPageData($request)
	{
		$page_data = SJB_UserPage::getNullPageData();
		foreach ($request as $key => $value) {
			if ($value != null)
				$page_data[$key] = $value;
		}

		$page_data['parameters'] = SJB_UserPage::unserializeParameters($page_data['parameters']);
		return $page_data;
	}

	public static function unserializeParameters($parameters)
	{
		if (empty($parameters)) {
			return null;
		}
		else {
			$result = array();
			$parameter = explode("\r\n", $parameters);
			foreach ($parameter as $name) {
				list($key, $value) = explode('=', $name);
				$result[$key] = $value;
			}
			return $result;
		}
	}

	public static function getNullPageData()
	{
		$page_data = array(
			'ID' => null,
			'uri' => null,
			'module' => null,
			'function' => null,
			'template' => null,
			'title' => null,
			'parameters' => array(),
			'keywords' => null,
			'access_type' => 'user',
			'description' => '',
		);
		return $page_data;
	}

	function isDataValid()
	{
		if ($this->pagedata['uri'] == '') {
			$this->error['URI_NOT_SPECIFIED'] = "Page URI is not specified";
			return false;
		}
		else {
			if ($this->pagedata['uri'][0] !== '/')
				$this->pagedata['uri'] = '/' . $this->pagedata['uri'];
		}

		$modules = SJB_System::getModulesList();
		$is_exist = 0;
		foreach ($modules as $module) {
			if ($module == $this->pagedata['module']) {
				$is_exist = 1;
				break;
			}
		}
		if ($is_exist == 0) {
			$this->error['MODULE_NOT_SPECIFIED'] = "Page module is not specified";
			return false;
		}
		else {
			$is_exist = 0;
			$functions = SJB_System::getFunctionsList($this->pagedata['module']);
			foreach ($functions as $func) {
				if ($func == $this->pagedata['function']) {
					$is_exist = 1;
					break;
				}
			}
			if ($is_exist == 0) {
				$this->error['FUNCTION_NOT_SPECIFIED'] = "Page function is not specified";
				return false;
			}
		}
		return true;
	}

	function save()
	{
		if ($this->pagedata['ID'] != null) {

			$user_page = SJB_System::getUserPage($this->pagedata['uri']);
			if (!is_null($user_page) && ($user_page['ID'] != $this->pagedata['ID'])) {
				$this->error['PAGE_ALREADY_EXISTS'] = 1;
				return false;
			}

			if (!SJB_System::modifyUserPage($this->pagedata)) {
				$this->error['CHANGE_ERROR'] = 'Cannot change data of User Page';
				return false;
			}
		}
		else {
			if (!SJB_System::addUserPage($this->pagedata)) {
				$this->error['ADD_ERROR'] = 'Cannot add new User Page';
				return false;
			}
		}
		return true;
	}

	function getErrors()
	{
		return $this->error;
	}

	public static function deletePage($uri)
	{
		if (isset($uri) && (!empty($uri))) {
			SJB_System::deleteUserPage($uri);
			return true;
		}
		$error['DELETE_PAGE'] = 'Page URI is not defined';
		return false;
	}

	function loadModulesFunctions()
	{
		$this->modules = SJB_System::getModulesUserList();
		foreach ($this->modules as $module) {
			$functions = SJB_System::getFunctionsUserList($module);
			foreach ($functions as $keyF => $func) {
				$this->functions[$module][$keyF] = $func;
				$params = SJB_System::getParamsList($module, $func);

				if (isset($params[0])) {
					foreach ($params as $keyP => $param)
						$this->parameters[$module][$func][$keyP] = $param;
				}
			}
		}
	}
}
