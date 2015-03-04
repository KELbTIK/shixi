<?php

class SJB_Admin_I18n_ImportLanguage extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('import_languages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$action = SJB_Request::getVar('action', false);

		if ($action && isset($_FILES['lang_file'])) {
			$params = $_REQUEST + $_FILES['lang_file'];
			$action = SJB_LanguageActionFactory::get($action, $params);
			if (@$action->canPerform()) {
				$action->perform();
				SJB_WrappedFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-languages/');
			} else {
				$errors = $action->getErrors();
			}
		}

		$tp->assign('errors', $errors);
		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		$tp->display('import_language.tpl');
	}
}

