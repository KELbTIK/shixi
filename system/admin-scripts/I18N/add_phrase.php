<?php


class SJB_Admin_I18n_AddPhrase extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('translate_phrases');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$params = array();

		$template_processor = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$params = $_REQUEST;

			$action = SJB_PhraseActionFactory::get($action_name, $params, $template_processor);

			if ($action->canPerform()) {
				$action->perform();
				SJB_WrappedFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-phrases/?action=remember_previous_state&result=' . $action->result);
			}
			else
			{
				$errors = $action->getErrors();
			}
		}

		$i18n = SJB_ObjectMother::createI18N();

		$domains = $i18n->getDomainsData();
		$langs = $i18n->getLanguagesData();

		$template_processor->assign('domains', $domains);
		$template_processor->assign('langs', $langs);
		$template_processor->assign('request_data', $_REQUEST);
		$template_processor->assign('errors', $errors);
		$template_processor->display('add_phrase.tpl');

	}
}
