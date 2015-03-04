<?php

/**
 * @package SystemClasses
 * @subpackage PathManager
 */
class SJB_PathManager
{

	/**
	 * returns function scripts path
	 *
	 * @param string $module_name Module name
	 * @param string $function_script function script name
	 * @return string Path to script
	 */
	public static function getAbsoluteFunctionScriptPath($module_name, $function_script)
	{
		return SJB_System::getSystemSettings('SCRIPTS_DIR') . $module_name . '/' .  $function_script;
	}

	/**
	 * returns absolute access path
	 *
	 * @param string $module_name Module name
	 * @param string $access_class Function access class
	 */
	public static function getAbsoluteAccessPath($module_name, $access_class)
	{
		return SJB_PathManager::getAbsoluteModulePath($module_name) . $access_class . '/';
	}

	/**
	 * returns absolute access path
	 *
	 * @param string $module_name Module name
	 * @return string Path to module
	 */
	public static function getAbsoluteModulePath($module_name)
	{
		return SJB_PathManager::getAbsoluteModulesPath() . $module_name . '/';
	}

	/**
	 * returns modules directory
	 *
	 * @param string $module_name Module name
	 * @return string Modules directory
	 */
	public static function getAbsoluteModulesPath()
	{
		return SJB_System::getSystemSettings('MODULES_DIR');
	}
	
	public static function getAbsoluteCommandsPath($module_name) {
		return SJB_System::getSystemSettings('LIBRARY_DIR') . $module_name . '/commands/';
	}

	public static function getAbsoluteThemesPath($access_type)
	{
		return SJB_BASE_DIR . ($access_type == 'admin' ? 'admin' : '') . 'templates/';
	}
}
