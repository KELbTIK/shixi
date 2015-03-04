<?php

class SJB_ListType extends SJB_Type
{
	var $list_values;

	function SJB_ListType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->list_values = isset($property_info['list_values']) ? $property_info['list_values'] : array();
		if (!empty($property_info['display_as']) && $property_info['display_as'] == 'radio_buttons') {
			$this->default_template = 'radiobuttons.tpl';
		} else {
			$this->default_template = 'list.tpl';
		}
	}

	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$profileFieldAsDv = SJB_Array::get($propertyVariables, 'profile_field_as_dv');
		if ($profileFieldAsDv && !isset($this->property_info['parentID'])) {
			$fieldValue = SJB_UserProfileFieldManager::getListItemValueBySID($profileFieldAsDv);
			if ($fieldValue) {
				$listingListItemSID = SJB_ListingFieldManager::getListItemSIDByValue($fieldValue, SJB_Array::get($this->property_info, 'sid'));
				$propertyVariables['profile_field_as_dv'] = $listingListItemSID;
			}
		}
		$defaultValue = SJB_Array::get($propertyVariables, 'default_value');
		if ($defaultValue == 'default_country') {
			$propertyVariables['default_value']  = SJB_Settings::getSettingByName('default_country');
		}
		
		$propertyVariables['hidden'] = $this->property_info['hidden'];
		
		$newPropertyVariables = array(
			'list_values' 		=> $this->list_values,
			'caption'	  		=> $this->property_info['caption'],
			'sort_by_alphabet' 	=> $this->property_info['sort_by_alphabet']
		);
		return array_merge($newPropertyVariables, $propertyVariables);
	}

	function isValid()
	{
		return true;
	}
	
	function getSQLValue()
	{
		return $this->property_info['value'];
	}

    function getKeywordValue()
	{
		$result = '';
		foreach ($this->list_values as $listValue) {
			if ($this->property_info['value'] == $listValue['id']) {
				if (!empty($listValue['Code']) || !empty($listValue['Name'])) {
					$result .= " {$listValue['Code']} ";
					$result .= " {$listValue['Name']} ";
				}
				else
					$result .= " {$listValue['caption']} ";
			}
		}
		return $result;
	}

    function getKeywordValueForAutocomplete()
	{
		$result = array();
		foreach ($this->list_values as $listValue) {
			if ($this->property_info['value'] == $listValue['id']) 
				$result[] = " {$listValue['caption']} ";
		}
		return $result;
	}
	
	function getSQLFieldType()
	{
		return 'TEXT NULL';
	}

}

