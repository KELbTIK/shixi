<?php

class SJB_DBObjectRequestCreator
{
	var $sid_collection;
	var $property_collection;
	var $table_prefix;

	function SJB_DBObjectRequestCreator($listing_sid_collection)
	{
		$this->sid_collection = $listing_sid_collection;
		$this->_getDetailIDs();	
	}
	
	function getRequest()
	{
		$what_part = $this->_getWhatPart();
		$join_part = $this->_getJoinPart();
		$where_part = $this->_getWherePart();
		return "SELECT $what_part FROM $join_part WHERE $where_part";		
	}
	
	function _getWhatPart()
	{
		$result = "{$this->table_prefix}.*";
		foreach($this->property_collection as $property)
			$result .= ", $property.value as $property";
		return $result;	
	}
	
	function _getJoinPart()
	{
		$result = "{$this->table_prefix}";
		foreach($this->property_collection as $property)
			$result .= " LEFT JOIN {$this->table_prefix}_properties $property ON ({$this->table_prefix}.sid=$property.object_sid AND $property.id='$property')";
		return $result;		
	}
	
	function _getWherePart()
	{
		$sids_list = "'" . join("', '", $this->sid_collection) . "'";
		return "{$this->table_prefix}.sid IN ($sids_list)";	
	}
	
	function _getDetailIDs()
	{
		$property_id_collection = SJB_DB::query("SELECT DISTINCT id FROM {$this->table_prefix}_properties");
		foreach($property_id_collection as $key => $value)
			$property_id_collection[$key] = $value['id'];			
		$this->property_collection = $property_id_collection; 
	}
	
}
