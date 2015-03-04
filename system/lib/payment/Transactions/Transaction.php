<?php

class SJB_Transaction extends SJB_Object
{
	function SJB_Transaction($transaction_info = array())
	{
		$this->details = new SJB_TransactionDetails($transaction_info);
	}

	function setTransactionID($transaction_id)	{ $this->setPropertyValue('transaction_id', $transaction_id); }
	function getTransactionID()     { return $this->getPropertyValue('transaction_id'); 	}
}

class SJB_TransactionSearcher extends SJB_Searcher
{
	var $infoSearcher = null;

	function SJB_TransactionSearcher(SJB_TransactionHistoryPagination $paginator)
	{
		$innerJoin = false;
		if ($paginator->sortingField == 'username') {
			$innerJoin = array('users' => array('sort_field' => 'username', 'join_field' => 'sid', 'join_field2' => 'user_sid', 'main_table' => 'transactions','join' => 'LEFT JOIN'));
		}
		$this->infoSearcher = new SJB_TransactionInfoSearcher(array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage), $paginator->sortingField, $paginator->sortingOrder, $innerJoin);
		parent::__construct($this->infoSearcher, new SJB_TransactionManager);
	}

	function getAffectedRows()
	{
		return $this->infoSearcher->affectedRows;
	}
}

class SJB_TransactionInfoSearcher extends SJB_ObjectInfoSearcher
{
	var $limit = false;
	var $sorting_field = false;
	var $sorting_order = false;
	var $inner_join = false;
	var $affectedRows = 0;

	function SJB_TransactionInfoSearcher($limit = false, $sorting_field = false, $sorting_order = false, $inner_join = false)
	{
		parent::__construct('transactions');
		$this->limit = $limit;
		$this->sorting_field = $sorting_field;
		$this->sorting_order = $sorting_order;
		$this->inner_join = $inner_join;
	}

	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		$searchSqlTranslator = new SJB_SearchSqlTranslator($this->table_prefix);
		$sql_string = $searchSqlTranslator->buildSqlQuery($this->criteria, $this->valid_criterion_number, array($this->sorting_field => $this->sorting_order),  $this->inner_join);
		SJB_DB::query($sql_string);
		$this->affectedRows = SJB_DB::getAffectedRows();
		if ($this->limit !== false){
			if (isset($this->limit['limit'])){
				$sql_string .= " limit " . $this->limit['limit'] . ", ".$this->limit['num_rows'];
			} else {
				$sql_string .= " limit " . $this->limit . ", 100";
			}
		}
		return SJB_DB::query($sql_string);
	}
}

class SJB_TransactionCriteriaSaver extends SJB_CriteriaSaver
{
	function SJB_TransactionCriteriaSaver()
	{
		parent::SJB_CriteriaSaver('TransactionSearcher', new SJB_TransactionManager);
	}
}

