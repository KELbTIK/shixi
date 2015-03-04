<?php

/**
 * Plugin Manager class
 *
 *
 * @copyright  2009 SmartJobBoard
 * @version    1
 * @author     janson
 */
class SJB_PluginManager
{
	static $pluginsLoaded = array();
	
	static $pluginsList = array();
	
	
	/**
	 * load all active plugins
	 *
	 * @param string $dir
	 */
  	public static function loadPlugins($dir)
  	{
  		$dh = opendir($dir);
  		if ($dh === false)
  			return;
  			
  		$excludeDirs = array('.', '..');
  			
		while (($file = readdir($dh)) !== false) {
			if (in_array($file, $excludeDirs))
				continue;
				
			$configFile = $dir . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . 'config.ini';
			if (file_exists($configFile)) {
				$config = parse_ini_file($configFile);
				$pluginName = isset($config['name']) ? $config['name'] : '';
				if (SJB_Users_CookiePreferences::isPluginDisabled($pluginName)) {
					continue;
				}
				$active		= isset($config['active']) && ($config['active'] == '1');
				$initFile	= isset($config['init_file']) ? $config['init_file'] : '';
				$config['config_file'] = $configFile;
				$config['group_id'] = isset($config['group'])?str_replace(' ', '_', $config['group']):'';
				// add to plugins list
				self::$pluginsList[$config['name']] = $config;
				
				if ( $active && !empty($initFile) ) {
					$initFilePath = $dir . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $initFile;
					if (file_exists($initFilePath)) {
						require_once($initFilePath);
						self::$pluginsLoaded[] = $config;
					} else {
						SJB_System::$pluginsErrors[] = "'{$pluginName}' plugin '{$initFilePath}' init file not exists or not readable!";
					}
				}
			}
		}
		closedir($dh);
  	}
  	
  	/**
  	 * reload all plugins
  	 *
  	 */
  	public static function reloadPlugins()
  	{
  		self::$pluginsList = array();
  		self::$pluginsLoaded = array();
  		self::loadPlugins(SJB_System::getSystemSettings('PLUGINS_DIR'));
  	}
  	/**
  	 * get list of all plugins
  	 *
  	 * @return array
  	 */
  	public static function getAllPluginsList()
  	{
  		return self::$pluginsList;
  	}
  	
  	public static function getPluginByName($name)
  	{
  		foreach (self::$pluginsList as $plugin) {
  			if ($plugin['name'] == $name) 
  				return $plugin;
  		}
  		return false;
  	}
  	
  	/**
  	 * get config from ini file
  	 *
  	 * @param string $path
  	 * @return array
  	 */
	public static function getPluginConfigFromIniFile($path)
  	{
  		return parse_ini_file($path);
  	}
  	
  	/**
  	 * save config into ini file
  	 *
  	 * @param string $path
  	 * @param array $config
  	 * @return boolean
  	 */
  	public static function savePluginConfigIntoIniFile($path, $config)
  	{
  		$str = '';
  		foreach ($config as $key => $val)
  			$str .= $key . " = \"" . $val ."\"\n";

  		$result = @file_put_contents($path, $str);
  		return $result !== false;
  	}

	/**
	 * @param string $pluginName
	 * @return bool
	 */
	public static function isPluginActive($pluginName)
	{
		$plugin = SJB_PluginManager::getPluginByName($pluginName);
		return ($plugin && $plugin['active'] == '1');
	}

}