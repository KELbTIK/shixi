<?php

class SJB_GuestAlertInfoSearcher extends SJB_ObjectInfoSearcher
{
	protected $limit = false;
	protected $sorting_field = false;
	protected $sorting_order = false;
	protected $inner_join = false;
	public $affectedRows = 0;

	function __construct($limit = false, $sorting_field = false, $sorting_order = false)
	{
		parent::__construct('guest_alerts');
		$this->limit = $limit;
		$this->sorting_field = $sorting_field;
		$this->sorting_order = $sorting_order;
	}

	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		$SearchSqlTranslator = new SJB_GuestAlertSearchSQLTranslator($this->table_prefix);
        $sql_string = $SearchSqlTranslator->buildSqlQuery( $this->criteria, $this->valid_criterion_number, $sorting_fields, $this->inner_join );

        if ($this->sorting_field !== false && $this->sorting_order !== false){
        	$sql_string .= ' ORDER BY ' . $this->sorting_field . ' '.$this->sorting_order.' ';
        }
        
        SJB_DB::queryExec($sql_string);
        $affectedRows = SJB_DB::getAffectedRows();

		if ($this->limit !== false)
		if (isset($this->limit['limit']))
			$sql_string .= 'limit ' . $this->limit['limit'] . ', ' . $this->limit['num_rows'];
		else
			$sql_string .= 'limit ' . $this->limit . ', 100';
		
		$sql_results = SJB_DB::query($sql_string);
		$result = array();
	    foreach ($sql_results as $sql_result) {
			if ($this->valid_criterion_number == 0 || $sql_result['countRows'] == $this->valid_criterion_number)
				$result[]['object_sid'] = $sql_result['object_sid'];
		}
		$this->affectedRows = $affectedRows - (SJB_DB::getAffectedRows() - count($result));

		return $result;
	}
	
}
