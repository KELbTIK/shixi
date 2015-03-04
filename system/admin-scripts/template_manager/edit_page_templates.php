<?php

class SJB_Admin_TemplateManager_EditPageTemplates extends SJB_Function
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
		$template_name = isset ($_REQUEST['template_name']) ? $_REQUEST['template_name'] : "";
		if (!$template_editor->doesModuleTemplateExists(SJB_System::getSystemSettings('STARTUP_MODULE'), $template_name))
			$template_name = '';

		if (!empty ($template_name)) {
			$menu_path = array(
				array(
					'reference' => "?",
					'name' => 'Page Templates',
				),
				array(
					'name' => $template_name,
					'reference' => "",
				)
			);
			$template_processor->assign("navigation", $menu_path);
			$template_processor->assign("title", "Page Template: {$template_name}");
			$template_processor->display('navigation_menu.tpl');
			echo SJB_System::executeFunction("template_manager", "edit_template", array('module_name' => System::getSystemSettings('STARTUP_MODULE')));
		}
		else
		{
			$menu_path = array(
				array(
					'reference' => "",
					'name' => 'Page Templates',
				),
			);
			$template_processor->assign("navigation", $menu_path);
			$template_processor->assign("title", 'Page Templates');
			$template_processor->display('navigation_menu.tpl');
			echo SJB_System::executeFunction("template_manager", "page_template_list");
		}

	}
}
