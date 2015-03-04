<?php

class SJB_TreeUserSearcher
{
	var $table;
	var $property_sid;
	var $property_name;
	var $captions;
	
	function SJB_TreeUserSearcher($property, $captions)
	{
		$this->table = 'user_profile_field_tree';
		$this->captions = $captions;
		$this->property_name = $property;
	}

	function getInfo()
	{
		$sql_result = SJB_DB::query($this->_getSQL());
		return $sql_result ? array_pop($sql_result) : null;	
	}
	
	function _getSQL()
	{
		return 'SELECT ' . $this->_getWhatPart() . ' FROM ' . $this->_getFromPart() . ' WHERE ' . $this->_getWherePart();
	}
	
	function _getWhatPart()
	{
		$max_id = max(array_keys($this->captions));
		return "`{$this->property_name}{$max_id}`.*";
	}
	
	function _getFromPart()
	{
		$result = array();
		for ($table_id = 0; $table_id < count($this->captions) - 1; $table_id++) {
			$next_table_id = $table_id + 1;
			$result[] = "`{$this->table}` `{$this->property_name}$next_table_id` ON `{$this->property_name}$next_table_id`.`parent_sid`=`{$this->property_name}$table_id`.`sid`";
		}
		$join_statement = $result ? ' LEFT JOIN ' . join(' LEFT JOIN ', $result) : '';
		return "`{$this->table}` `{$this->property_name}0`" . $join_statement;
	}
	
	function _getWherePart()
	{
		$result = array();
		foreach($this->captions as $key => $caption)
			$result[] = "`{$this->property_name}{$key}`.`caption` = '{$caption}'";
			
		return join(' AND ', $result);
	}
	
}
