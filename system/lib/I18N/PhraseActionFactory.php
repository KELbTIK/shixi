<?php


class SJB_PhraseActionFactory
{
	public static function get($action, $params, $template_processor)
	{
		$i18n = SJB_ObjectMother::createI18N();
		$storage = SJB_InfoStorage::createTranslationFilterStorage();
		if (SJB_System::getSystemSettings("isDemo")) {
			$blockedActions = array("add_phrase", "update_phrase", "delete_phrase");
			if (in_array($action, $blockedActions)) {
				$phraseAction = new SJB_PhraseAction();
				return $phraseAction;
			}
		}
		
		switch ($action)
		{
			case "search_phrases":
				
				$searchPhraseAction = new SJB_SearchPhraseAction($i18n, $params, $template_processor);
				$storePhraseSearchCriteriaAction = new SJB_StorePhraseSearchCriteriaAction($storage, $params);
				
				$phraseAction = new SJB_SerialActionBatch();
				$phraseAction->addAction($searchPhraseAction);
				$phraseAction->addAction($storePhraseSearchCriteriaAction);
				$phraseAction->result = '';
				break;
				
			case "remember_previous_state":
				
				// Criteria are passed by reference to be accessible in the
				// RestorePhraseSearchCriteriaAction and the SearchPhraseAction.
				// So in the RestorePhraseSearchCriteriaAction it can be got from the storage
				// and in the SearchPhraseAction it can be used to search phrases.
				$criteria = null;
				$searchPhraseAction = new SJB_SearchPhraseAction($i18n, $criteria, $template_processor);
				$restorePhraseSearchCriteriaAction = new SJB_RestorePhraseSearchCriteriaAction($storage, $criteria);
				
				$phraseAction = new SJB_SerialActionBatch();
				$phraseAction->addAction($restorePhraseSearchCriteriaAction);
				$phraseAction->addAction($searchPhraseAction);
				$phraseAction->result = '';
				break;
				
			case "add_phrase":
				$phraseAction = new SJB_AddPhraseAction($i18n, $params);
				$phraseAction->result = 'added';
				break;
				
			case "update_phrase":
				$phraseAction = new SJB_UpdatePhraseAction($i18n, $params);
				$phraseAction->result = 'saved';
				break;
				
			case "delete_phrase": 
				
				$phrase = isset($params['phrase']) ? $params['phrase'] : null;
				$domain = isset($params['domain']) ? $params['domain'] : null;
				
				// see remember_previous_state
				$criteria = null;
				$searchPhraseAction = new SJB_SearchPhraseAction($i18n, $criteria, $template_processor);
				$restorePhraseSearchCriteriaAction = new SJB_RestorePhraseSearchCriteriaAction($storage, $criteria);
				$deletePhraseAction = new SJB_DeletePhraseAction($i18n, $phrase, $domain);
				
				$phraseAction = new SJB_SerialActionBatch();
				$phraseAction->addAction($deletePhraseAction);
				$phraseAction->addAction($restorePhraseSearchCriteriaAction);
				$phraseAction->addAction($searchPhraseAction);
				$phraseAction->result = 'deleted';
				break;
				
			default:
				$phraseAction = new SJB_PhraseAction();
				break;
		}
		
		return $phraseAction;
	}
}
