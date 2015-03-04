<?php

class SJB_Admin_TemplateManager_PageTemplateList extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_templates_and_themes');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateprocessor();

		$template_name = isset ($_REQUEST['template_name']) ? $_REQUEST['template_name'] : "";
		$template_editor = new SJB_TemplateEditor();
		$template_processor->assign('ERROR', '');
		$theme = SJB_Settings :: getValue('TEMPLATE_USER_THEME', 'default');
		if (isset ($_REQUEST['action'])) {
			if ($_REQUEST['action'] == 'create_page_template') {
				if (!isset ($_REQUEST['new_template_name']))
					$_REQUEST['new_template_name'] = '';
				$_REQUEST['new_template_name'] = preg_replace("~.tpl$~iu", "", $_REQUEST['new_template_name']);
				if (empty ($_REQUEST['new_template_name'])) {
					$template_processor->assign('ERROR', 'EMPTY_TEMPLATE_NAME');
				}
				else
					if (preg_match("~\W~", $_REQUEST['new_template_name'])) {
						$template_processor->assign('ERROR', 'WRONG_FILENAME');
					}
					else
					{
						if (true !== $result = $template_editor->saveTemplate($_REQUEST['new_template_name'] . ".tpl", SJB_System::getSystemSettings('STARTUP_MODULE'), $theme, "<html>\n<head>\n<title>{\$TITLE}</title>\n</head>\n<body>\n{\$MAIN_CONTENT}\n</body>\n</html>"))
							$template_processor->assign('ERROR', 'CANNOT_SAVE_FILE');
						else
							SJB_HelperFunctions::redirect("?");
					}
			}
			if ($_REQUEST['action'] == 'delete_template') {
				$template_editor->deleteTemplate($_REQUEST['del_template_name'], SJB_System::getSystemSettings('STARTUP_MODULE'), $theme);
				SJB_HelperFunctions::redirect("?");
			}
		}
		$template_processor->assign('new_template_name', isset ($_REQUEST['new_template_name']) ? $_REQUEST['new_template_name'] : "");
		$template_processor->assign('template_name', $template_editor->doesModuleTemplateExists(SJB_System::getSystemSettings('STARTUP_MODULE'), $template_name) ? $template_name : "");
		$template_processor->assign('STARTUP_MODULE', SJB_System::getSystemSettings('STARTUP_MODULE'));
		$template_processor->assign('template_list', $template_editor->getTemplateList(SJB_System::getSystemSettings('STARTUP_MODULE'), $theme));
		$template_processor->display('page_template_list.tpl');
	}
}
