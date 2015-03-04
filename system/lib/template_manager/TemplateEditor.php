<?php

define('E_ACCESS', 'user');

class SJB_TemplateEditor
{
	var $modules;
	var $access_class = E_ACCESS;

	function SJB_TemplateEditor()
	{
		$module_manager = SJB_System::getModuleManager();
		$this->modules = $module_manager->getModuleInfo();
	}

	/**
	 * fetching template
	 *
	 * @param string $tpl_name
	 * @param string $module_name
	 * @param string $theme_name
	 * @return string
	 */
	function loadTemplate($tpl_name, $module_name, $theme_name)
	{
		$path_to_template = SJB_TemplatePathManager :: getAbsoluteTemplatePath($theme_name, $module_name, $tpl_name);
		return file_get_contents($path_to_template);
	}

	/**
	 * put content to file
	 *
	 * @param string $tpl_name
	 * @param string $module_name
	 * @param string $theme_name
	 * @param string $content
	 * @return bool
	 */
	function saveTemplate($tpl_name, $module_name, $theme_name, $content)
	{
		$path_to_template = SJB_TemplatePathManager :: getAbsoluteTemplatePath($theme_name, $module_name, $tpl_name);
		if (!is_dir($path_to_template))
			SJB_Filesystem::mkpath(dirname($path_to_template));
		return SJB_Filesystem::file_put_contents($path_to_template, $content);
	}

	/**
	 * deletes template
	 *
	 * @param string $tpl_name
	 * @param string $module_name
	 * @param string $theme_name
	 * @return bool
	 */
	function deleteTemplate($tpl_name, $module_name, $theme_name)
	{
		$template_path = SJB_TemplatePathManager :: getAbsoluteTemplatePath($theme_name, $module_name, $tpl_name);
		if (is_file($template_path))
		 	if (!unlink($template_path))
				return false;
		return true;
	}

	/**
	 * list of templates
	 *
	 * @param string $module_name
	 * @param string $theme_name
	 * @param bool $returnStructured
	 * @return array
	 */
	public static function getTemplateList($module_name, $theme_name, $returnStructured = false)
	{
		$tpl_path = SJB_TemplatePathManager :: getAbsoluteTemplatePath($theme_name, $module_name, '');
		$system_path = SJB_TemplatePathManager :: getAbsoluteTemplatePath(SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR'), $module_name, '');
		$template_list = array();
		$template_list_theme = array();
		$structuredTemplateList = array();
		$structuredTemplateListTheme = array();
		$dirlist = array($tpl_path);
		$dirlistSystem = array($system_path);
		//Reading directory content
		while (count($dirlistSystem)) {
			$directoryTheme = array_shift($dirlist);
			$directory = array_shift($dirlistSystem);
			if (is_dir($directory)) {
				$dh = opendir($directory);
				if ($dh !== false) {
					while (($file_or_directory = readdir($dh)) !== false) {
						if (is_dir($directory .	$file_or_directory) && $file_or_directory != '.' && $file_or_directory != '..') {
							array_push($dirlistSystem, $directory . $file_or_directory . '/');
						}
						else {
							if (preg_match("/.tpl$/", $file_or_directory)) { //Adding template to template array
								$template_list[] = substr($directory . $file_or_directory, strlen($system_path));
								$structuredTemplateList[substr($directory . $file_or_directory, strlen($system_path))] = array(
									'fileName' => substr($directory . $file_or_directory, strlen($system_path)),
									'themeTemplate' => 0
								);
							}
						}
					}
					closedir($dh);
				}
			}
			if (is_dir($directoryTheme)) {
				$dh = opendir($directoryTheme);
				if ($dh !== false) {
					while (($file_or_directory = readdir($dh)) !== false)
						if (is_dir($directoryTheme .
							$file_or_directory) && $file_or_directory != '.' && $file_or_directory != '..')
							array_push($dirlist, $directoryTheme . $file_or_directory . '/');
						else {
							if (preg_match("/.tpl$/", $file_or_directory)) { //Adding template to template array
								$template_list_theme[] = substr($directoryTheme . $file_or_directory, strlen($tpl_path));
								$structuredTemplateListTheme[substr($directoryTheme . $file_or_directory, strlen($tpl_path))] = array(
									'fileName' => substr($directoryTheme . $file_or_directory, strlen($tpl_path)),
									'themeTemplate' => 1
								);
							}
						}
					closedir($dh);
				}
			}
		}

		if ($returnStructured) {
			$structuredTemplates = array_merge($structuredTemplateList, $structuredTemplateListTheme);
			usort($structuredTemplates, 'self::compareTemplatesList');
			return $structuredTemplates;
		} else {
			$template_list = array_merge($template_list, $template_list_theme);
			sort($template_list);
			return array_unique($template_list);
		}
	}

	/**
	 * Returns modules list
	 *
	 * @return array
	 */
	function getModuleWithTemplatesList()
	{
		$current_theme_name = SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default');
		foreach ($this->modules as $key => $value) {
			$templates = $this->getTemplateList($key, $current_theme_name);
			if ($templates !== false && count($templates) > 0)
				$modules_with_templates[$key] = $value;
		}
		return $modules_with_templates;
	}

	function copyDefaultModuleThemeIfNotExists($module_name)
	{
		if (false === $default_template_list = $this->getTemplateList($module_name, 'default'))
			return false;
		$current_theme_name = SJB_Settings :: getValue('TEMPLATE_USER_THEME', 'default');
		$current_template_list = $this->getTemplateList($module_name, $current_theme_name);

		if ($current_template_list === false)
			$current_template_list = array ();

		foreach ($default_template_list as $template_system_name) {
			if (array_search($template_system_name, $current_template_list) === false) {
				if (false === $this->loadTemplate($template_system_name, $module_name, 'default'))
					return false;
				if (false === $this->saveTemplate($template_system_name, $module_name, $current_theme_name))
					return false;
			}
		}
		return true;
	}

	function doesModuleExists($module_name)
	{
		return isset ($this->modules[$module_name]);
	}

	function doesModuleTemplateExists($module_name, $template_name)
	{
		if (!$this->doesModuleExists($module_name))
			return false;
		$current_user_theme = SJB_Settings :: getValue('TEMPLATE_USER_THEME', 'default');
		$list = $this->getTemplateList($module_name, $current_user_theme);
		return array_search($template_name, $list) !== false;
	}

	/**
	 * screwdriver!!!
	 *
	 * @param unknown_type $module_name
	 * @param unknown_type $theme_name
	 * @param unknown_type $template_name
	 * @return unknown
	 */
	function isTemplateWriteable($module_name, $theme_name, $template_name)
	{
		if (!$this->doesModuleTemplateExists($module_name, $template_name))
			return false;
		$path_to_file = SJB_TemplatePathManager :: getAbsoluteTemplatePath($theme_name, $module_name, $template_name);
		return is_writeable($path_to_file);
	}

	/**
	 * returns theme list
	 * @return array
	 */
	function getThemeList()
	{
		$system_tpl = SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR');
		$themes = array ();
		$templates_path = SJB_TemplatePathManager::getAbsoluteTemplatesPath ();
		if (is_dir($templates_path)) {
			$templates_dir = opendir ($templates_path);
			while (($file_in_dir = readdir ($templates_dir)) !== false) {
				if (($file_in_dir != '.') && ($file_in_dir != '..') && ($file_in_dir != 'CVS') && ($file_in_dir != '.svn') && ($file_in_dir != $system_tpl)){
					if (is_dir($templates_path.$file_in_dir))
					    $themes[] = $file_in_dir;
				}
			}
			closedir ($templates_dir);
		}
		
		return $themes;
	}

	//------------------------------------------------------------------------
	// return true if theme exists
	function doesThemeExists($theme_name)
	{
		$themes = $this->getThemeList();
		return in_array($theme_name, $themes);
	}

	//------------------------------------------------------------------------
	// deletes theme
	function deleteEntireTheme($themeName)
	{
		if ($themeName == 'default') {
			return false;
		}

		SJB_Filesystem :: delete(SJB_TemplatePathManager::getAbsoluteThemePath($themeName));
		SJB_Filesystem :: delete(SJB_TemplatePathManager::getAbsoluteThemeCachePath($themeName));
		SJB_FormBuilderManager::deleteFieldsByTheme($themeName);
		$listingTypes = SJB_ListingTypeDBManager::getAllListingTypesInfo();
		foreach($listingTypes as $listingType) {
			SJB_Settings::deleteSetting("display_layout_{$listingType['id']}_{$themeName}");
		}
		return true;
	}

	//------------------------------------------------------------------------
	// cpoies theme
	function copyEntireTheme($copiedThemeName, $newThemeName)
	{
		$newThemePath = SJB_TemplatePathManager::getAbsoluteThemePath($copiedThemeName);
		$copiedThemePath = SJB_TemplatePathManager::getAbsoluteThemePath($newThemeName);
		SJB_Filesystem :: copy($newThemePath, $copiedThemePath);
		SJB_Filesystem :: mkpath(SJB_TemplatePathManager::getAbsoluteThemeCachePath($newThemeName));
		$themeFields = SJB_FormBuilderManager::getFieldsByTheme($copiedThemeName);
		foreach ($themeFields as $themeField) {
			SJB_FormBuilderManager::insertField($newThemeName, $themeField);
		}
		$screenFile = $copiedThemePath . "main/images/{$copiedThemeName}.png";
		$newScreenFile = $copiedThemePath . "main/images/{$newThemeName}.png";
		if (file_exists($screenFile)) {
			rename($screenFile, $newScreenFile);
		}
		$listingTypes = SJB_ListingTypeDBManager::getAllListingTypesInfo();
		foreach($listingTypes as $listingType) {
			SJB_Settings::addSetting("display_layout_{$listingType['id']}_{$newThemeName}", SJB_Settings::getSettingByName("display_layout_{$listingType['id']}_{$copiedThemeName}"));
		}
	}

	/**
	 * Move template to another destination
	 * 
	 * @param string $tpl_name
	 * @param string $module_name
	 * @param string $theme_name
	 * @param string $newModuleName
	 * @param string $newTplName
	 * @return boolean
	 */
	function moveTemplate($tpl_name, $module_name, $theme_name, $newModuleName, $newTplName)
	{
		$path_to_template = SJB_TemplatePathManager::getAbsoluteTemplatePath($theme_name, $module_name, $tpl_name);
		if (!file_exists($path_to_template)) {
			$theme_name = SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR');
			$path_to_template = SJB_TemplatePathManager::getAbsoluteTemplatePath($theme_name, $module_name, $tpl_name);
			if (!file_exists($path_to_template)) {
				return false;
			}
		}
		$newPath_to_template = SJB_TemplatePathManager::getAbsoluteTemplatePath($theme_name, $newModuleName, $newTplName);
		return rename($path_to_template, $newPath_to_template);
	}



	/**
	 * check if template name is valid
	 * @param string $templName
	 * @return boolean
	 */
	function isTemplateNameValid( &$templName )
	{
		$patterns = array("/^[a-z0-9_]+\.tpl$/i", "/^[a-z0-9_]+$/i");
		foreach ($patterns as $thePatern) {
			if (preg_match($thePatern, $templName)) {
				if ('.tpl' != substr($templName, -4, 4))
					$templName = strtolower($templName) . '.tpl';
				return true;
			}
		}
		return false;
	}


	/**
	 * create template file by theme, modulename, templatename
	 *
	 * @param string $theme
	 * @param string $module_name
	 * @param string $tpl_name
	 * @param array $error
	 * @return boolean
	 */
	function createTemplate( $theme, $module_name, $tpl_name, &$error = array( ) )
	{
		$path_to_template = SJB_TemplatePathManager :: getAbsoluteTemplatePath( $theme, $module_name, $tpl_name );

		if ( !is_dir( $path_to_template ) )
			SJB_Filesystem::mkpath( dirname( $path_to_template ) );

		if ( !file_exists( $path_to_template ) )
			return self::createTemplateFile( $path_to_template );
		else
			$error[] = 'FILE_EXISTS';
		return false;
	}

	/**
	 * create template file by path
	 * 
	 * @param string $filename
	 * @return boolean
	 */
	public static function createTemplateFile( $filename )
	{
		if ( !$handle = fopen( $filename, 'w' ) )
			return false;
		fclose( $handle );
		return true;
	}
	
	public static function getTemplatesByGroupForListType($group)
	{
		$cacheId = 'SJB_TemplateEditor::getTemplatesByGroupForListType' . $group;
		if (SJB_MemoryCache::has($cacheId))
			return SJB_MemoryCache::get($cacheId);
		$emailTemplates = SJB_DB::query("SELECT * FROM `email_templates` WHERE `group` = ?s ORDER BY `name`", $group);
		$listEmails = array();
		foreach ($emailTemplates as $emailTemplate) {
			$listEmails[] = array(
				'id' => $emailTemplate['sid'],
				'caption' => $emailTemplate['name']
			);
		}
		SJB_MemoryCache::set($cacheId, $listEmails);
		return $listEmails;
	}
	
	public static function getTemplateBySID($sid)
	{
		$emailTemplate = SJB_DB::queryValue("SELECT `name` FROM `email_templates` WHERE `sid` = ?s", $sid);
		return $emailTemplate ? $emailTemplate : false;
	}

	private static function compareTemplatesList($a, $b)
	{
		if ($a['fileName'] == $b['fileName']) {
			return 0;
		}
		else if ($a['fileName'] < $b['fileName']) {
			return -1;
		}
		return 1;
	}

}   // class
