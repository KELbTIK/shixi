<?php

class SJB_Admin_I18n_EditPhrase extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('translate_phrases');
		return parent::isAccessible();
	}

	public function execute()
	{
		$errors = array();
		$phrase_data = array();
		$result = '';

		$phrase_id = isset($_REQUEST['phrase']) ? $_REQUEST['phrase'] : null;
		$domain_id = isset($_REQUEST['domain']) ? $_REQUEST['domain'] : null;
		$lang_id = isset($_REQUEST['current_lang']) ? $_REQUEST['current_lang'] : null;

		$i18n = SJB_ObjectMother::createI18N();
		$template_processor = SJB_System::getTemplateProcessor();

		if ($i18n->phraseExists($phrase_id, $domain_id)) {
			$phrase_data = $i18n->getPhraseData($phrase_id, $domain_id);

			if (isset($_REQUEST['action'])) {
				$action_name = $_REQUEST['action'];
				$params = $_REQUEST;

				$action = SJB_PhraseActionFactory::get($action_name, $params, $template_processor);

				if ($action->canPerform()) {
					$action->perform();
					$result = $action->result;
				}
				else
				{
					$errors = $action->getErrors();
					$phrase_data = array_merge($phrase_data, $_REQUEST);
				}
			}
		}
		else $errors[] = 'PHRASE_NOT_EXISTS';

		$domains = $i18n->getDomainsData();
		$langs = $i18n->getLanguagesData();

		$template_processor->assign('result', $result);
		$template_processor->assign('phrase', $phrase_data);
		$template_processor->assign('domains', $domains);
		$template_processor->assign('langs', $langs);
		$template_processor->assign('chosen_lang', $lang_id);
		$template_processor->assign('errors', $errors);
		$template_processor->display('update_phrase.tpl');

	}
}
