<?php

class SJB_GuestAlertSearcher extends SJB_Searcher
{
	/**
	 * @var SJB_GuestAlertInfoSearcher
	 */
	protected $infoSearcher = null;
	
	public function __construct($limit = false, $sorting_field = false, $sorting_order = false)
	{
		$this->infoSearcher = new SJB_GuestAlertInfoSearcher($limit, $sorting_field, $sorting_order);
		parent::__construct($this->infoSearcher, new SJB_GuestAlertManager());
	}
	
	public function getAffectedRows()
	{
		return $this->infoSearcher->affectedRows;
	}
}
