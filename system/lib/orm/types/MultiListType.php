<?php

/*
 * $Id: MultiListType.php 9806 2014-09-15 05:50:06Z furiae $
 */

class SJB_MultiListType extends SJB_Type
{
	
	var $list_values;

	function SJB_MultiListType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->list_values = isset($property_info['list_values']) ? $property_info['list_values'] : array();
		if (!empty($property_info['display_as']) && $property_info['display_as'] == 'checkboxes') {
			$this->default_template = 'checkboxes.tpl';
		} else {
			$this->default_template = 'multilist.tpl';
		}
	}

	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$propertyVariables['choiceLimit'] = SJB_Array::get($this->property_info, 'choiceLimit');
		$displayListValues = array();
		$listValues = array(); 
		foreach ($this->list_values as $key => $list_values) {
			$displayListValues[$list_values['id']] = $list_values['caption'];
			$listValues[$key]['id'] = $list_values['id'];
			$listValues[$key]['caption'] = $list_values['caption'];
		}

		$arrValues = array(
			'value' => $this->property_info['value'],
			'default_value' => $this->property_info['default_value'],
			'profile_field_as_dv' => $this->property_info['profile_field_as_dv']);
		$value = array();

		foreach ($arrValues as $valueID => $arrValue) {
			if (is_array($arrValue)) {
				$value[$valueID] = $this->cleanRemovedItems($arrValue, $displayListValues);
			} else {
				$itemSIDs = explode(',', $arrValue);
				$value[$valueID] = $this->cleanRemovedItems($itemSIDs, $displayListValues);
			}
		}

		$newPropertyVariables = array(	
			'value'		 		  => $value['value'],
			'display_list_values' => $displayListValues,
			'default_value'		  => $value['default_value'],
			'profile_field_as_dv' => $value['profile_field_as_dv'],
			'list_values' 		  => $listValues,
			'caption'	  		  => $this->property_info['caption'],
			'no_first_option'	  => $this->property_info['no_first_option'],
			'comment' 			  => $this->property_info['comment'],
			'sort_by_alphabet'    => $this->property_info['sort_by_alphabet']
		);
		return array_merge($propertyVariables, $newPropertyVariables);
	}

	function isValid()
	{
		return true;
	}
	
	function getSQLValue()
	{
		if (is_array($this->property_info['value'])) {
			$str = implode(',', $this->property_info['value']);
		} else {
			$str = (string) $this->property_info['value'];
		}
		
		return $str;
	}

    function getKeywordValue()
    {
		$result = '';
		if (!is_array($this->property_info['value'])) {
			$this->property_info['value'] = explode(',', $this->property_info['value']);
		}
		foreach ($this->list_values as $listValue) {
			if (in_array($listValue['id'], $this->property_info['value'])) {
				$result .= " {$listValue['caption']} ";
			}
		}
		return $result;
	}

	function getKeywordValueForAutocomplete()
	{
		$result = array();

		if (!is_array($this->property_info['value'])) {
			$this->property_info['value'] = explode(',', $this->property_info['value']);
		}
		foreach ($this->list_values as $listValue) {
			if (in_array($listValue['id'], $this->property_info['value'])) {
				$result[] = " {$listValue['caption']} ";
			}
		}
		return $result;
	}

	function getSQLFieldType()
	{
		return 'TEXT NULL';
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'choiceLimit',
				'caption'	=> 'Number of Values Allowed to Select',
				'type'		=> 'integer',
				'minimum'	=> '0',
				'is_system'	=> true
			),
		);
	}

	/**
	 * @param $arrValue
	 * @param $displayListValues
	 * @return array
	 */
	private function cleanRemovedItems($arrValue, $displayListValues)
	{
		foreach ($arrValue as $key => $itemSID) {
			if (!is_array($arrValue) && !array_key_exists($itemSID, $displayListValues)) {
				unset($arrValue[$key]);
			}
		}
		if (!empty($arrValue)) {
			return $arrValue;
		}
		return '';
	}
}

