<?php

class SJB_Admin_TemplateManager_EditTemplate extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_templates_and_themes');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$module_name = SJB_Request::getVar('module_name', "", 'GET');
		$template_name = SJB_Request::getVar('template_name', "", 'GET');

		// не работало с юзерской
		if (empty($template_name)) {
			$template_name = SJB_Request::getVar('template_name', '');
		}
		if (empty($module_name)) {
			$module_name = SJB_Request::getVar('module_name', '');
		}

		$theme = SJB_Settings :: getValue('TEMPLATE_USER_THEME', 'default');
		$template_editor = new SJB_TemplateEditor();
		$simple_view = SJB_Request::getVar('simple_view');

		if (!$template_editor->doesModuleExists($module_name)) {
			$tp->assign('ERROR', "MODULE_DOES_NOT_EXIST");
		}
		else if (!$template_editor->doesModuleTemplateExists($module_name, $template_name)) {
			$tp->assign('ERROR', "TEMPLATE_DOES_NOT_EXIST");
		}
		else {
			if (isset($_REQUEST['action'])) {
				$content = SJB_Request::getVar('template_content');
				if ($content) {
					if (SJB_System::getSystemSettings("isDemo")) {
						$tp->assign('ERROR', 'NOT_ALLOWED_IN_DEMO');
					}
					elseif (SJB_System::getIfTrialModeIsOn() && SJB_HelperFunctions::findSmartyRestrictedTagsInContent($tp, $content)) {
						$tp->assign('ERROR', 'Php tags are not allowed');
					}
					else {
						$result = $template_editor->saveTemplate($template_name, $module_name, $theme, $content);

						// if ajax request to save
						if ($simple_view) {
							if ($result) {
								echo '<p class="message">Template saved successfully. </p>';
							} else {
								echo "ERROR WHILE SAVE TEMPLATE";
							}
							exit;
						}

						if ($_REQUEST['action'] == "save_template") {
							SJB_HelperFunctions::redirect("?module_name=" . $module_name);
						}
					}
				}
			}
			echo SJB_System::executeFunction('template_manager', 'add_template');

			$modules = $template_editor->getModuleWithTemplatesList();

			$tp->assign('module_name', $module_name);
			$tp->assign('template_name', $template_name);
			$tp->assign('theme', $theme);
			$tp->assign('display_name', $modules[$module_name]['display_name']);
			$tp->assign('template_display_name', $template_name);

			$path_to_template = SJB_TemplatePathManager::getAbsoluteTemplatePath($theme, $module_name, $template_name);

			if (!file_exists($path_to_template))
				$theme = SJB_System::getSystemSettings('SYSTEM_TEMPLATE_DIR');
			if (false === $template_content = $template_editor->loadTemplate($template_name, $module_name, $theme))
				$tp->assign('ERROR', "CANNOT_FETCH_TEMPLATE");
			else {
				if (!$template_editor->isTemplateWriteable($module_name, $theme, $template_name) && !SJB_System::getSystemSettings("isDemo"))
					$tp->assign('ERROR', "TEMPLATE_IS_NOT_WRITEABLE");
				else
					$tp->assign('template_content', $template_content);
			}

			$list_modules = SJB_System::getModulesUserList();
			$list_functions = array();
			$list_params = array();

			foreach ($list_modules as $module) {
				$functions = SJB_System::getFunctionsUserList($module);
				foreach ($functions as $keyF => $func) {
					$list_functions[$module][$keyF] = $func;
					$params = SJB_System::getParamsList($module, $func);
					if (isset($params[0])) {
						foreach ($params as $keyP => $param)
							$list_params[$module][$func][$keyP] = $param;
					}
				}
			}
			$tp->assign('LIST_MODULES', $list_modules);
			$tp->assign('LIST_FUNCTIONS', $list_functions);
			$tp->assign('LIST_PARAMS', $list_params);
		}

		if ($simple_view) {
			$tp->display('edit_template_simple.tpl');
		} else {
			header('X-XSS-Protection: 0');
			$tp->display('edit_template.tpl');
		}
	}
}
