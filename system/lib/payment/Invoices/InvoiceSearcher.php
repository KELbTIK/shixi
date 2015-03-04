<?php

class SJB_InvoiceSearcher extends SJB_Searcher
{
	var $infoSearcher = null;

	public function __construct($limit = false, $sorting_field = false, $sorting_order = false, $inner_join = false)
	{
		$this->infoSearcher = new SJB_InvoiceInfoSearcher($limit, $sorting_field, $sorting_order, $inner_join);
		parent::__construct($this->infoSearcher, new SJB_InvoiceManager);
	}
	
	public function getAffectedRows()
	{
		return $this->infoSearcher->affectedRows;
	}
}
