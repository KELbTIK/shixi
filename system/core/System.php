<?php

class SJB_System
{
	const SJB_APPLICATION_MODE = 'SJB_APPLICATION_MODE';
	const SJB_APPLICATION_TRIAL_MODE = 'trial';
	static $pluginsErrors = array();
	
	public static function boot()
	{
		// force include Base.php file (compatibility and specific functions)
		require_once SJB_BASE_DIR . 'system/core/Base.php';
		require_once SJB_BASE_DIR . 'system/ext/Zend/Loader/ClassMapAutoloader.php';
		$loader = new Zend_Loader_ClassMapAutoloader();
		$loader->registerAutoloadMap(SJB_BASE_DIR . 'system/core/.classmapcache.php');
		$loader->register();

		set_include_path(
			SJB_System::getSystemSettings('EXT_LIBRARY_DIR') . PATH_SEPARATOR .
			SJB_System::getSystemSettings('LIBRARY_DIR') . PATH_SEPARATOR . get_include_path());
	}

	public static function loadSystemSettings($file_name)
	{
		if (!file_exists($file_name)) {
			return false;
		}
		if (is_readable($file_name)) {
			if (!isset($GLOBALS['system_settings'])) {
				$GLOBALS['system_settings'] = array();
			}
			$settings = include($file_name);
			$GLOBALS['system_settings'] = array_merge($GLOBALS['system_settings'], $settings);
			return true;
		}
		die ("index.php"." File {$file_name} isn't readable Cannot read config file");
	}

	public static function getSystemSettings($setting_name)
	{		
		return (isset($GLOBALS['system_settings'][$setting_name])) ? $GLOBALS['system_settings'][$setting_name] : null;
	}
	
	public static function setSystemSettings($setting_name, $value)
	{		
		$GLOBALS['system_settings'][$setting_name] = $value;
	}

	public static function getGlobalTemplateVariables()
	{
		return $GLOBALS['TEMPLATE_VARIABLES'];
	}

	public static function getGlobalTemplateVariable ($variable_name)
	{
		return (isset($GLOBALS['TEMPLATE_VARIABLES'][$variable_name])) ? $GLOBALS['TEMPLATE_VARIABLES'][$variable_name] : null;
	}

	public static function setGlobalTemplateVariable($name, $value, $in_global_array = true)
	{
		if ($in_global_array) {
			$GLOBALS['TEMPLATE_VARIABLES']['GLOBALS'][$name] = $value;
		}
		else {
			$GLOBALS['TEMPLATE_VARIABLES'][$name] = $value;
		}
	}

	/**
	 * @return SJB_ModuleManager
	 */
	public static function getModuleManager()
	{
		return $GLOBALS['System']['MODULE_MANAGER'];
	}

	/**
	 * Get Template Processor
	 *
	 * @return SJB_TemplateProcessor
	 */
	public static function getTemplateProcessor()
	{
		list($module) = SJB_System::getModuleManager()->getCurrentModuleAndFunction();
		if ($module != null) {
			return new SJB_TemplateProcessor(new SJB_TemplateSupplier($module));
		}
		return null;
	}

	public static function setPageTitle($page_title)
	{
		SJB_System::setGlobalTemplateVariable('TITLE', SJB_I18N::getInstance()->gettext('Frontend', $page_title), false);
	}
	
	public static function setCurrentUserInfo($current_user_info)
	{
		SJB_System::setGlobalTemplateVariable('current_user', $current_user_info);
	}
	
	public static function setCurrentUserGroupsInfo($userGroupInfo)
	{
		SJB_System::setGlobalTemplateVariable('user_groups', $userGroupInfo);
	}
	
	public static function getPageTitle()
	{
		return SJB_System::getGlobalTemplateVariable('TITLE');
	}
	
	public static function setPageKeywords($page_keywords)
	{
		SJB_System::setGlobalTemplateVariable('KEYWORDS', $page_keywords, false);
	}
	
	public static function getPageKeywords()
	{
		return SJB_System::getGlobalTemplateVariable('KEYWORDS');
	}

	public static function setPageDescription($page_description)
	{
		SJB_System::setGlobalTemplateVariable('DESCRIPTION', $page_description, false);
	}

	public static function getPageDescription()
	{
		return SJB_System::getGlobalTemplateVariable('DESCRIPTION');
	}

	public static function setPageHead($page_head)
	{
		SJB_System::setGlobalTemplateVariable('HEAD', $page_head, false);
	}

	public static function getPageHead()
	{
		return SJB_System::getGlobalTemplateVariable('HEAD');
	}

	public static function executeFunction($module, $setting, $parameters = array(), $pageID = false)
	{
		return SJB_System::getModuleManager()->executeFunction($module, $setting, $parameters, $pageID);
	}

	public static function init()
	{
		SJB_DB::init(SJB_System::getSystemSettings('DBHOST'), SJB_System::getSystemSettings('DBUSER'), SJB_System::getSystemSettings('DBPASSWORD'), SJB_System::getSystemSettings('DBNAME'));
		ini_set('zlib.output_compression', SJB_System::getSettingByName('gzip_compression'));
		$GLOBALS['fatal_error_reserve_buffer'] = str_repeat('x', 1024 * 200);
		
		ob_start(array('SJB_Error', 'fatalErrorHandler'));
		SJB_Session::init(SJB_System::getSystemSettings('SITE_URL'));
		$sessionId = SJB_DB::queryValue("SELECT `session_id` FROM `user_session_data_storage` WHERE `session_id` = ?s", SJB_Session::getSessionId());
		// if not updated (row not exists) - insert that value
		if (empty($sessionId)) {
			SJB_DB::query("INSERT INTO `user_session_data_storage` SET `last_activity` = NOW(), `session_id` = ?s", SJB_Session::getSessionId());
		} else {
			SJB_DB::query("UPDATE `user_session_data_storage` SET `last_activity` = NOW() WHERE `session_id` = ?s", $sessionId);
		}

		//set timezone
		if (SJB_Settings::getSettingByName('timezone'))
			ini_set('date.timezone', SJB_Settings::getSettingByName('timezone'));
			
		// Set Error Handler and Shutdown function
		set_error_handler(array('SJB_Error', 'errorHandler'));
		register_shutdown_function(array('SJB_System', 'shutdownFunction'));
		
		SJB_System::prepareGlobalArrays();
		SJB_System::setGlobalTemplateVariable('is_ajax', SJB_Request::isAjax());
		SJB_System::setGlobalTemplateVariable('site_url', SJB_System::getSystemSettings('SITE_URL'));
		SJB_System::setGlobalTemplateVariable('user_site_url', SJB_System::getSystemSettings('USER_SITE_URL'));
		SJB_System::setGlobalTemplateVariable('admin_site_url', SJB_System::getSystemSettings('ADMIN_SITE_URL'));
		SJB_System::setGlobalTemplateVariable('radius_search_unit', SJB_System::getSettingByName('radius_search_unit'));
		SJB_System::setGlobalTemplateVariable('settings', SJB_Settings::getSettings());

		ThemeManager::getCurrentTheme();

		SJB_PluginManager::loadPlugins( SJB_System::getSystemSettings('PLUGINS_DIR') );
		SJB_System::setGlobalTemplateVariable('plugins', SJB_PluginManager::getAllPluginsList());
		
		$GLOBALS['System']['MODULE_MANAGER'] = new SJB_ModuleManager();
		SJB_Event::dispatch('moduleManagerCreated');
		$GLOBALS['System']['MODULE_MANAGER']->executeModulesStartupFunctions();
		// define if subadmin loged in and set subamdinmode for templates
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE') && SJB_SubAdmin::getSubAdminSID()) {
			SJB_System::setGlobalTemplateVariable('subAdminSID', SJB_SubAdmin::getSubAdminSID());
		}
		$GLOBALS['uri'] = SJB_Navigator::getURI();
	}
	
	public static function getPage($page_config)
	{
		return SJB_PageConstructor::getPage($page_config);
	}
	
	public static function isUserAccessThisPage()
	{
		$pageID = SJB_PageManager::getPageParentURI(SJB_Navigator::getURI(), SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'), false);
		$access = true;
		$currentUser = SJB_UserManager::getCurrentUser();
		if (!is_null($currentUser)) {
			$access = false;
			$queryParam = '';
			$listingId = SJB_Request::getVar("listing_id", false);
			$passedParametersViaUri = SJB_Request::getVar("passed_parameters_via_uri", false);
			if (!$listingId && $passedParametersViaUri) {
				$passedParametersViaUri = SJB_UrlParamProvider::getParams();
				$listingId = isset($passedParametersViaUri[0])?$passedParametersViaUri[0]:'';
			}
			if ($listingId) {
				$queryParam = " AND `param` = '" . SJB_DB::quote($listingId) . "' ";
			}
			$pageHasBeenVisited = SJB_DB::query("SELECT `param` FROM `page_view` WHERE `id_user` = ?s AND `id_pages` = ?s {$queryParam}", $currentUser->getSID(), $pageID);
			if (!empty($queryParam) && $pageHasBeenVisited || strpos($pageID, 'print') !== false) {
				$access = true;
			} else {
				$contractsId = $currentUser->getContractID();
				$pageAccess = SJB_ContractManager::getPageAccessByUserContracts($contractsId, $pageID);
				$numberOfPagesViewed = SJB_ContractManager::getNumbeOfPagesViewed($currentUser->getSID(), $contractsId, $pageID);
				if (isset($pageAccess[$pageID]) && $pageAccess[$pageID]['count_views'] != '') {
					if ($numberOfPagesViewed < $pageAccess[$pageID]['count_views']) {
						$access = true;
					}
					if ($access === true) {
						$listingTypeSID = null;
						if (is_numeric($listingId)) {
							$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
							if ($listingInfo) {
								$listingTypeSID = $listingInfo['listing_type_sid'];
							}
						}
						$availableContractId = '';
						foreach($contractsId as $contractId) {
							$pageAccessByContract = SJB_ContractManager::getPageAccessByUserContracts(array($contractId), $pageID);
							$viewsLeft = SJB_ContractManager::getNumbeOfPagesViewed($currentUser->getSID(), array($contractId), false, $listingTypeSID);
							if (!empty($pageAccessByContract[$pageID]['count_views']) && $pageAccessByContract[$pageID]['count_views'] > $viewsLeft) {
								$availableContractId = $contractId;
							}
						}
						if (!empty($availableContractId)) {
							SJB_DB::query("INSERT INTO page_view (`id_user` ,`id_pages`, `param`, `contract_id`, `listing_type_sid`) VALUES ( ?n, ?s, ?s, ?n, ?n)", $currentUser->getSID(), $pageID, $listingId, $availableContractId, $listingTypeSID);
						} else {
							$access = false;
						}
					}
				} else {
					$access = true;
				}
			}
		}
		return $access;
	}

	public static function getSystemURLByModuleAndFunction($module, $function, $parameters)
	{
		$params = array();

		foreach ($parameters as $parameter_name => $parameter_value)
			array_push( $params, urlencode($parameter_name) .'='. urlencode($parameter_value) );

		$parameters_str = join('&', $params);
		$site_url = SJB_System::getSystemSettings("SITE_URL");
		$system_url_base = SJB_System::getSystemSettings("SYSTEM_URL_BASE");
		return $site_url . '/' . $system_url_base . '/' . $module . '/' . $function . '?' . $parameters_str;
	}

	public static function getModuleAndFunctionBySystemURL($url)
	{
		list($uri) = explode('?', $url);
		$uriParts = explode('/', $uri);
		$module = SJB_Array::get($uriParts, 2);
		$function = SJB_Array::get($uriParts, 3);
		return array('module' => $module, 'function' => $function);
	}

	public static function getFunctionInfo($module, $function)
	{
		$module_manager = SJB_System::getModuleManager();
		if ($module_manager->doesModuleExists($module)) {
			$module_info = $module_manager->getModuleInfo ($module);
			return ( isset($module_info['functions'][$function]) ) ? $module_info['functions'][$function] : array();
		}
		return array();
	}

	public static function getSystemDefaultTemplate()
	{
		return SJB_System::getSystemSettings('SYSTEM_DEFAULT_TEMPLATE');
	}

	public static function isFunctionAccessible($module, $function)
	{
		return SJB_System::getModuleManager()->isFunctionAccessible($module, $function);
	}

	public static function prepareGlobalArrays()
	{
		// simulating turning off register_globals if it's on
		if (@ini_get ("register_globals")) {
			$unset = array_keys ($_ENV + $_GET + $_POST + $_COOKIE + $_SERVER + $_SESSION);
			foreach ($unset as $rg_var){
				if (isset ($$rg_var))
					unset ($$rg_var);
			}
			unset ($unset);
		}

		switch($_SERVER['REQUEST_METHOD']) {
			case 'POST':
				$_REQUEST = $_POST;
				break;
			case 'GET';
				$_REQUEST = $_GET;
				break;
		}
		
		// turning of 'magic_quotes_runtime' (for outputting information)
		ini_set('magic_quotes_runtime', false);
		// unquoting request data if 'get_magic_quotes_gpc' is turned on
		if (ini_get('magic_quotes_gpc')) {
			SJB_HelperFunctions::unquote ($_REQUEST);
			SJB_HelperFunctions::unquote($_POST);
			SJB_HelperFunctions::unquote($_GET);
		}

		//-- setting temp directory if there isn't available one
		$isCacheDirGood = false;
		foreach (array($_ENV, $_SERVER) as $tab) {
			foreach (array('TMPDIR', 'TEMP', 'TMP', 'windir', 'SystemRoot') as $key) {
				if (isset($tab[$key])) {
					if ($key == 'windir' || $key == 'SystemRoot') {
						$dir = realpath($tab[$key] . '\\temp');
					} else {
						$dir = realpath($tab[$key]);
					}
					if (is_readable($dir) && is_writable($dir)) {
						$isCacheDirGood = true;
						break 2;
					}
				}
			}
		}

		if ($isCacheDirGood === false) {
			$_SERVER['TMP'] = SJB_BASE_DIR . 'system/cache/';
		}
	}

	public static function requireAllFilesInDirectory($dir)
	{
		if (is_dir($dir) && $dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if (is_dir($dir .DIRECTORY_SEPARATOR . $file)) {
					if (($file != '.') && ($file != '..')) {
						SJB_System::requireAllFilesInDirectory($dir . DIRECTORY_SEPARATOR. $file );
					}
				}
				else {
					if (strlen($file) > 4 && strtolower(substr($file, strlen($file) - 4)) == '.php') {
						require_once($dir . DIRECTORY_SEPARATOR . $file);
					}
				}
			}
			closedir($dh);
		}
	}

	public static function doesFunctionHaveRawOutput($module, $function)
	{
		return SJB_System::getModuleManager()->doesFunctionHaveRawOutput($module, $function);
	}

	public static function getPageConfig($uri)
	{
		return SJB_PageConfig::getPageConfig ($uri);
	}

	public static function getUserPage($uri)
	{
		return SJB_PageManager::get_page($uri, 'user');
	}

	public static function modifyUserPage($pageInfo)
	{
		$module_manager = SJB_System::getModuleManager();
		if (!$module_manager->doesFunctionExist($pageInfo['module'], $pageInfo['function'])) {
			return false;
		}
		return SJB_PageManager::update_page($pageInfo);
	}

	public static function deleteUserPage($uri)
	{
		return SJB_PageManager::delete_page($uri);
	}

	public static function addUserPage($pageInfo)
	{
		$module_manager = SJB_System::getModuleManager();
		if (!$module_manager->doesFunctionExist($pageInfo['module'], $pageInfo['function'])) {
			return false;
		}
		return SJB_PageManager::addPage($pageInfo);
	}

	public static function doesUserPageExists($uri)
	{
		return SJB_PageManager::doesPageExists ($uri, 'user');
	}

	public static function getModulesList()
	{
		return SJB_System::getModuleManager()->getModulesList();
	}

	public static function getFunctionsList($module)
	{
		return SJB_System::getModuleManager()->getFunctionsList ($module);
	}

	public static function getParamsList($module, $function)
	{
		return SJB_System::getModuleManager()->getParamsList ($module, $function);
	}

	public static function getFunctionsUserList($module)
	{
		$module_manager = SJB_System::getModuleManager();
		$func_list = $module_manager->getFunctionsList ($module);
		$user_func_list = array();
		foreach ($func_list as $func) {
			if (($module_manager->getFunctionType($module, $func) == 'user') && ($module_manager->getFunctionAccessSystem($module, $func) == false)) {
				$user_func_list[] = $func;
			}
		}
		return $user_func_list;
	}

	public static function getModulesUserList()
	{
		$module_manager = SJB_System::getModuleManager();
		$module_list = $module_manager->getModulesList();
		$user_module_list = array();
		foreach ($module_list as $module) {
			if (isset($func_list)) {
				unset($func_list);
			}
			$is_user = 0;
			$func_list = $module_manager->getFunctionsList($module);

			foreach ($func_list as $func) {
				if ($module_manager->getFunctionType($module, $func) == 'user' && ($module_manager->getFunctionAccessSystem($module, $func) == false)) {
					$is_user = 1;
					break;
				}
			}

			if ($is_user == 1) {
				$user_module_list[] = $module;
			}
		}
		return $user_module_list;
	}

	public static function getURI()
	{
		return SJB_Navigator::getURI();
	}

	public static function getRegisteredCommands()
	{
		return SJB_System::getModuleManager()->getRegisteredCommands();
	}

	public static function getCommandScriptAbsolutePath($module, $command)
	{
		return SJB_System::getModuleManager()->getCommandScriptAbsolutePath($module, $command);
	}

	public static function getModuleOfCommand($command)
	{		
		return SJB_System::getModuleManager()->getModuleOfCommand($command);
	}
	
	public static function getSettingsFromFile($file_name, $setting_name)
	{
		$settings = require($file_name);
		return isset($settings[$setting_name]) ? $settings[$setting_name] : null;
	}

	public static function getSettingByName($setting_name)
	{
		return SJB_Settings::getSettingByName($setting_name);
	}

	public static function doesParentUserPageExist($uri)
	{
		return SJB_PageManager::doesParentPageExist($uri, SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'));
	}

	public static function getUserPageParentURI($uri)
	{
		return SJB_PageManager::getPageParentURI($uri, SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'));
	}

	/**
	 * Shutdown function
	 */
	public static function shutdownFunction()
	{
		// get errors handle settings
		$errorLogging = SJB_System::getSettingByName('error_logging');
		$errorLevel = SJB_System::getSettingByName('error_log_level');

		if ($errorLogging) {
			$errors = SJB_Error::getRuntimeErrors($errorLevel);
			if (!empty($errors)) {
				SJB_Error::writeToLog($errors);
			}
		}
	}

	/**
	 * check if trial mode of site is on
	 * @static
	 * @return bool
	 */
	public static function getIfTrialModeIsOn()
	{
		return getenv(self::SJB_APPLICATION_MODE) === self::SJB_APPLICATION_TRIAL_MODE;
	}
}
