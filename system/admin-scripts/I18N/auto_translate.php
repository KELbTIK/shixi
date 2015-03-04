<?php

class SJB_Admin_I18n_AutoTranslate extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_languages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$template_processor = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$action = SJB_PhraseActionFactory::get($action_name, $_REQUEST, $template_processor);
			if ($action->canPerform()) {
				$action->perform();
				$template_processor->display('refresh_opener_and_close_popup.tpl');
				return;
			}
			else {
				$errors = $action->getErrors();
			}
		}

		$phrase_id = SJB_Request::getVar('phrase', null);
		$domain_id = SJB_Request::getVar('domain', null);

		$i18n = SJB_ObjectMother::createI18N();

		$langs = $i18n->getLanguagesData();
		$template_processor->assign('langs', $langs);
		$template_processor->assign('errors', $errors);

		if (!$i18n->phraseExists($phrase_id, $domain_id)) {
			$domains = $i18n->getDomainsData();
			$template_processor->assign('domains', $domains);
			$template_processor->assign('request_data', $_REQUEST);
			$template_processor->display('add_phrase.tpl');
		}
		else {
			$phrase_data = $i18n->getPhraseData($phrase_id, $domain_id);
			$template_processor->assign('phrase', $phrase_data);
			$template_processor->display('update_phrase.tpl');
		}
	}
}
