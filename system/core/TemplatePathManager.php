<?php

class SJB_TemplatePathManager
{
	
	public static function getAbsoluteThemeCachePath($theme)
	{
		return SJB_Path::combine(SJB_System::getSystemSettings('COMPILED_TEMPLATES_DIR'),'user',$theme);
	}

	public static function getAbsoluteTemplatesPath ()
	{
		$up_path = (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == 'admin') ? '../' : '';
		return $up_path . SJB_System::getSystemSettings('TEMPLATES_DIR');
	}

	public static function getAbsoluteThemePath ($theme)
	{
		return SJB_TemplatePathManager::getAbsoluteTemplatesPath() . $theme . '/';
	}

	public static function getAbsoluteModuleTemplatesPath ($theme, $module)
	{
		return SJB_TemplatePathManager::getAbsoluteThemePath($theme).$module.'/';
	}

	public static function getAbsoluteTemplatePath ($theme, $module, $template)
	{
		return SJB_TemplatePathManager::getAbsoluteModuleTemplatesPath ($theme, $module).$template;
	}

	public static function getAbsoluteImagePath ($theme, $module, $image = '')
	{
		return SJB_TemplatePathManager::getAbsoluteModuleTemplatesPath ($theme, $module) . 'images/' . $image;
	}

}
