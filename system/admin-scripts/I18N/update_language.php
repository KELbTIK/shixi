<?php

class SJB_Admin_I18n_EditLanguage extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_languages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$params = array();
		$lang_id = SJB_Request::getVar('languageId', null);

		$i18n = SJB_ObjectMother::createI18N();
		if ($i18n->languageExists($lang_id)) {
			$params = $i18n->getLanguageData($lang_id);
			$params['languageId'] = $lang_id;
			if (isset($_REQUEST['action'])) {
				$action_name = $_REQUEST['action'];
				$form_submitted = SJB_Request::getVar('submit');
				$params = array_merge($params, $_REQUEST);

				$action = SJB_LanguageActionFactory::get($action_name, $params);
				if ($action->canPerform()) {
					$action->perform();
					if ($form_submitted == 'save') {
						SJB_WrappedFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-languages/');
					}
				} else {
					$errors = $action->getErrors();
				}
			}
		} else {
			$errors[] = 'LANGUAGE_DOES_NOT_EXIST';
		}

		$template_editor = SJB_ObjectMother::createTemplateEditor();
		$themes = $template_editor->getThemeList();

		$template_processor = SJB_System::getTemplateProcessor();
		$template_processor->assign('themes', $themes);
		$template_processor->assign('lang', $params);
		$template_processor->assign('errors', $errors);
		$template_processor->display('update_language.tpl');
	}
}
