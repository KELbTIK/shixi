<?php


class SJB_SearchPhraseAction
{
	function SJB_SearchPhraseAction(&$i18n, &$criteria, &$template_processor)
	{
		$this->i18n = $i18n;
		$this->criteria =& $criteria;
		$this->template_processor = $template_processor;
	}
	
	function canPerform()
	{
		return true;
	}
	
	function perform()
	{
		$phraseSearchCriteriaFactory = $this->i18n->getPhraseSearchCriteriaFactory();
		$phraseSearchCriteria = $phraseSearchCriteriaFactory->create($this->criteria);
		
		$phrases =& $this->i18n->searchPhrases($phraseSearchCriteria);
		SJB_Admin_I18n_ManagePhrases::setPhrases($phrases);
		$this->template_processor->assign('phrases', $phrases);
		$this->template_processor->assign('criteria', $this->criteria);
	}
	
	function getErrors()
	{
		return null;
	}
}

