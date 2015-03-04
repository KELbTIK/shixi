<?php

class SJB_Admin_I18n_AddLanguage extends SJB_Function
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

		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$params = $_REQUEST;
			$action = SJB_LanguageActionFactory::get($action_name, $params);

			if ($action->canPerform()) {
				$action->perform();
				SJB_WrappedFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-languages/');
			} else {
				$errors = $action->getErrors();
			}
		}

		$template_processor = SJB_System::getTemplateProcessor();
		$template_processor->assign('request_data', $params);
		$template_processor->assign('errors', $errors);
		$template_processor->display('add_language.tpl');
	}
}
