<?php

class SJB_TreeParser
{
	var $columns	= array();
	var $fields		= array();
	var $trees		= array();
	
	function SJB_TreeParser($columns)
	{
		$this->getRepeatedNames($columns);
		$this->_getRepeatedFields();
		foreach($this->fields as $field)
			$this->trees[$field] = $this->_getFieldStructure($field);
	}
	
	function getTreeColumns()
	{
		return $this->trees;	
	}
	
	function getRepeatedNames($columns)
	{
		foreach($columns as $key => $property_name)
			if (strpos($property_name, '['))
			    $this->columns[$key] = $property_name;	
	}
	
	function _getRepeatedFields()
	{
		foreach($this->columns as $column_name)
			$this->fields[] = substr($column_name, 0, strpos($column_name, '[') );
		$this->fields = array_unique($this->fields);
	}
	
	function _getFieldStructure($field)
	{
		$result = array();
		foreach	($this->columns as $column_position => $column_name)
			if(strpos($column_name, $field) !== false)
				$result[$this->_getColumnLevel($column_name)] = $column_position;
		return $result;
	}
	
	function _getColumnLevel($column)
	{
		return substr($column, strpos($column, '[') + 1, -1);	
	}
}
