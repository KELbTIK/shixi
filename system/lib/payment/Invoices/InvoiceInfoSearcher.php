<?php

class SJB_InvoiceInfoSearcher extends SJB_ObjectInfoSearcher
{
	var $limit = false;
	var $sorting_field = false;
	var $sorting_order = false;
	var $inner_join = false;
	var $affectedRows = 0;

	function SJB_InvoiceInfoSearcher($limit = false, $sorting_field = false, $sorting_order = false, $inner_join = false)
	{
		parent::__construct('invoices');
		$this->limit = $limit;
		$this->sorting_field = $sorting_field;
		$this->sorting_order = $sorting_order;
		$this->inner_join = $inner_join;
	}

	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		$searchSqlTranslator = new SJB_InvoiceSearchSQLTranslator($this->table_prefix);
        $sqlString = $searchSqlTranslator->buildSqlQuery($this->criteria, $this->valid_criterion_number, $sorting_fields, $this->inner_join);
      	$where = '';
        if ($this->sorting_field !== false && $this->sorting_order !== false) {
			$sqlString .= $where . "ORDER BY " . $this->sorting_field . " " . $this->sorting_order . " ";
        }

        SJB_DB::queryExec($sqlString);
        $this->affectedRows = SJB_DB::getAffectedRows();
		if ($this->limit !== false){
			if (isset($this->limit['limit'])){
				$sqlString .= "limit " . $this->limit['limit'] . ", " . $this->limit['num_rows'];
			} else {
				$sqlString .= "limit " . $this->limit . ", 100";
			}
		}

		return SJB_DB::query($sqlString);
	}
}
