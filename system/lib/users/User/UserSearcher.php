<?php

class SJB_UserSearcher extends SJB_Searcher
{
	/**
	 * @var null|\SJB_UserInfoSearcher
	 */
	var $infoSearcher = null;
	
	public function __construct($limit = false, $sorting_field = false, $sorting_order = false, $inner_join = false, $limitByPHP = false)
	{
		$this->infoSearcher = new SJB_UserInfoSearcher($limit, $sorting_field, $sorting_order, $inner_join, $limitByPHP);
		parent::__construct($this->infoSearcher, new SJB_UserManager);
	}
	
	public function getAffectedRows()
	{
		return $this->infoSearcher->affectedRows;
	}
}
