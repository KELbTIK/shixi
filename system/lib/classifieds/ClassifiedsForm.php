<?php

class SJB_ClassifiedsForm
{
	var $fields_info = null;
	var $valid_data = null;
	var $display_type = null;
	
	function SJB_ClassifiedsForm($fields_info = null)
	{
		$this->fields_info = $fields_info;
	}
	
	function submit()
	{
		$this->validate();
	}
	
	function validate()
	{
		$this->valid_data = true;
	}
	
	function isValidData()
	{
		return $this->valid_data;
	}
	
	function getFormFields()
	{
		foreach ($this->fields_info as $field_name => $field_info) {
			$field_info['name'] = $field_name;
			$this->fields_info[$field_name]['element'] = SJB_Types::display($field_info, $this->display_type);
			$this->fields_info[$field_name]['caption'] = isset($field_info['caption']) ? $field_info['caption'] : null;
		}
		
		return $this->fields_info;
	}
	
	function reset()
	{
		foreach ($this->fields_info as $field_name => $field_info) {
			$this->fields_info[$field_name]['value'] = '';
		}
	}
}
