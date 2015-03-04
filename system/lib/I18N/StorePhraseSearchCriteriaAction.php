<?php


class SJB_StorePhraseSearchCriteriaAction
{
	var $errors = array();
	var $storage = null;
	var $criteria = null;
	
	function SJB_StorePhraseSearchCriteriaAction(&$storage, &$criteria)
	{
		$this->storage =& $storage;
		$this->criteria =& $criteria;
	}
	
	function canPerform()
	{
		return true;
	}
	
	function perform()
	{		
		$this->storage->store($this->criteria);
	}

	function getErrors()
	{
		return $this->errors;
	}
}

