<?php

class SJB_Admin_TemplateManager_TemplateList extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_templates_and_themes');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();

		$template_editor = new SJB_TemplateEditor();
		$template_processor->assign('ERROR', "OK");
		$module_name = SJB_Request::getVar('module_name', '');
		$template_name = SJB_Request::getVar('template_name', '');
		$template_processor->assign('module_name', $template_editor->doesModuleExists($module_name) ? $module_name : "");
		$template_processor->assign('template_name', $template_editor->doesModuleTemplateExists($module_name, $template_name) ? $template_name : "");

		if (!$template_editor->doesModuleExists($module_name)) {
			$template_processor->assign('ERROR', "MODULE_DOES_NOT_EXIST");
		}
		else {
			if (!$template_editor->copyDefaultModuleThemeIfNotExists($module_name))
				$template_processor->assign('ERROR', "CANNOT_COPY_THEME");
			$modules = $template_editor->getModuleWithTemplatesList();

			$template_processor->assign('display_name', $modules[$module_name]['display_name']);
			$template_processor->assign('module_name', $module_name);
			$template_processor->assign('template_list', $template_editor->getTemplateList($module_name, SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default'), true));
		}
		$template_processor->display('template_list.tpl');
	}
}


