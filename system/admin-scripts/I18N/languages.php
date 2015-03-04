<?php


class SJB_Admin_I18n_ManageLanguages extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_languages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$action = SJB_LanguageActionFactory::get($action_name, $_REQUEST);

			if ($action->canPerform())
				$action->perform();
			else
				$errors = $action->getErrors();
		}

		$i18n = SJB_ObjectMother::createI18N();

		$langs_data = $i18n->getLanguagesData();

		$template_processor = SJB_System::getTemplateProcessor();
		$template_processor->assign('langs', $langs_data);
		$template_processor->assign('errors', $errors);
		$template_processor->display('languages.tpl');
	}
}
