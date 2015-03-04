<?php

class SJB_Admin_TemplateManager_ThemeEditor extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_templates_and_themes');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template_editor = new SJB_TemplateEditor();
		$setNewTheme = SJB_Request::getVar('theme', false);
		$theme = SJB_Request::getVar('theme', SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default'));

		if ($setNewTheme) {
			if (SJB_System::getSystemSettings("isDemo")) {
				$tp->assign('ERROR', 'ACCESS_DENIED');
			}
			else {
				SJB_Settings::setValue('TEMPLATE_USER_THEME', $theme);
				SJB_Settings::setValue('CURRENT_THEME', $theme);
			}
		}

		if (!$template_editor->doesThemeExists(SJB_Settings::getValue('TEMPLATE_USER_THEME', 'default'))) {
			SJB_Settings::setValue('CURRENT_THEME', 'default');
			SJB_Settings::setValue('TEMPLATE_USER_THEME', 'default');
			$theme = 'default';
		}
		else if ($setNewTheme && !SJB_System::getSystemSettings("isDemo")) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/edit-themes/');
		}

		$tp->assign('theme_list', $template_editor->getThemeList());
		$tp->assign('theme', $theme);

		if (isset($_REQUEST['action'])) {
			if (SJB_System::getSystemSettings("isDemo")) {
				$tp->assign('ERROR', 'ACCESS_DENIED');
			}
			else {
				switch (SJB_Request::getVar("action")) {
					case "copy_theme":
						if (isset($_REQUEST['copy_from_theme'], $_REQUEST['new_theme'])
								&& $template_editor->doesThemeExists($_REQUEST['copy_from_theme'])
								&& !$template_editor->doesThemeExists($_REQUEST['new_theme'])
								&& !empty($_REQUEST['new_theme'])
						) {
							$template_editor->copyEntireTheme($_REQUEST['copy_from_theme'], $_REQUEST['new_theme']);
							SJB_HelperFunctions::redirect("?theme=" . $_REQUEST['new_theme']);
						}
						else {

							if ($template_editor->doesThemeExists(SJB_Request::getVar('new_theme', '')))
								$tp->assign('ERROR', 'ALREADY_EXISTS');
							if (empty($_REQUEST['new_theme']))
								$tp->assign('ERROR', 'EMPTY_NAME');
						}

						break;

					case "delete_theme":
						if (isset($_REQUEST['theme_name']) && $template_editor->doesThemeExists($_REQUEST['theme_name'])) {
							$template_editor->deleteEntireTheme($_REQUEST['theme_name']);
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/edit-themes/');
						}
						break;
				}
			}
		}

		$tp->display('theme_editor.tpl');
	}
}
