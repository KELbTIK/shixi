<?php


class SJB_ListItem {
	
	var $field_sid;
	
	var $value;
	
	var $sid;
	
	function setValue($value) {
		
		$this->value = $value;
		
	}
	
	function getValue() {
		
		return $this->value;
		
	}
	
	function setFieldSID($field_sid) {
		
		$this->field_sid = $field_sid;
		
	}
	
	function getFieldSID() {
		
		return $this->field_sid;
		
	}
	
	function setSID($sid) {
		
		$this->sid = $sid;
		
	}
	
	function getSID() {
		
		return $this->sid;
		
	}
}

