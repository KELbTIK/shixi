<?php

class SJB_Admin_TemplateManager_AddTemplate extends SJB_Function
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
		$modules = $template_editor->getModuleWithTemplatesList();

		ksort($modules);

		global $error;

		$template_processor->assign('module_name', SJB_Request::getVar('module_name', '', 'GET'));
		$template_processor->assign('template_name', SJB_Request::getVar('template_name', '', 'GET'));
		$template_processor->assign('tpl_error', $error);
		$template_processor->assign('module_list', $modules);
		$template_processor->display('add_template.tpl');
	}
}