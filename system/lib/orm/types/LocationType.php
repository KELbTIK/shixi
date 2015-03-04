<?php

class SJB_LocationType extends SJB_Type
{
	public  $fields;
	private $table_name;
	public  $child;
	
	function SJB_LocationType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->fields = isset($property_info['fields']) ? $property_info['fields'] : array();
		$this->table_name = isset($property_info['table_name']) ? $property_info['table_name'] : 'listings';
		$fields_info = isset($property_info['value']) ? $property_info['value'] : array();
		$this->child = new SJB_Complex($this->fields, $this->table_name, $fields_info);
		$this->default_template = 'location.tpl';
	}
	
	function getPropertyVariablesToAssign()
	{
		$form = new SJB_Form($this->child);
		$form_fields = $form->getFormFieldsInfo();
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$properties = $this->child->getProperties();
		$propertyVariables['value'] = array();
		foreach ($properties as $name => $property) {
			$variablesToAssign = $property->getPropertyVariablesToAssign();
			$form_fields[$name]['hidden'] = $variablesToAssign['hidden'];
			if ($variablesToAssign['value'] === '') {
				continue;
			}
			else if (empty($variablesToAssign['value']) && !empty($variablesToAssign['profile_field_as_dv'])) {
				$propertyVariables['value'][$name] = $variablesToAssign['profile_field_as_dv'];
			}
			else if (empty($variablesToAssign['value']) && !empty($variablesToAssign['default_value'])) {
				$propertyVariables['value'][$name] = $variablesToAssign['default_value'];
			} else {
				$propertyVariables['value'][$name] = $variablesToAssign['value'];
			}
		}

		$newPropertyVariables = array(
			'form_fields'             => $form_fields,
			'caption'                 => $this->property_info['caption'],
			'parentID'                => $this->property_info['id'],
			'useAutocomplete'         => $this->property_info['use_autocomplete'],
			'type'                    => $this->property_info['type'],
			'isClassifieds'           => $this->property_info['is_classifieds'],
			'enable_search_by_radius' => $this->property_info['enable_search_by_radius']
		);
        $propertyVariables = array_merge($newPropertyVariables, $propertyVariables);
		return $propertyVariables;
	}
	
	function isValid()
	{
		$properties = $this->child->getProperties();
		$properties = $properties?$properties:array();
		$errors = array();
		foreach ($properties  as $field) {
			if (!$field->type->isEmpty()) {
				$isValid = $field->type->isValid();
				if ($isValid !== true)
					$errors[$field->caption] = $isValid;
			}
			elseif ($field->is_required) {
				$errors[$field->caption] = 'EMPTY_VALUE';
			}
		}		
		return $errors;
	}
	
	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'enable_search_by_radius',
				'caption'	=> 'Enable search by radius',
				'type'		=> 'boolean',
				'value'		=> '',
                'is_system' => true,
			),
			array(
				'id'		=> 'use_autocomplete',
				'caption'	=> 'Use Autocomplete',
				'type'		=> 'boolean',
				'value'		=> '',
                'is_system' => true,
				),
		);
	}
	
	function getSQLValue()
	{
		$values = explode(' ', $this->property_info['value']);
		$values = array_diff($values, array(''));
		foreach ($values as $key => $value) {
			$value = trim($value);
			$len   = strlen($value);
			if ($len < 4) {
				for ($i = $len; $i < 4; $i++)  {
					$value .= '_';
				}
				$values[$key] = $value;
			}
		}
		$value = implode(' ', $values);
		return $value;
	}

	public function prepareLocationRegistrationFields()
	{
		$propertiesList = $this->child->getPropertyList();
		foreach ($propertiesList as $propertyId) {
			$property = $this->child->getProperty($propertyId);
			if (!$property->type->property_info['is_required'] && !$property->type->property_info['hidden']) {
				$property->type->makeHidden();
				$property->type->property_info['madeHidden'] = true;
			}
		}
	}

	function getSQLFieldType()
	{
		return 'VARCHAR(500) NULL';
	}
	
	function isParent()
	{
		return true;
	}
	
    function getKeywordValue()
	{
		$childProperties = $this->child->getProperties();

		$keywords = '';
		if ($childProperties) {
			/** @var SJB_ObjectProperty $childProperty */
			foreach ($childProperties as $childProperty) {
				$keywords .= $childProperty->getKeywordValue() . ' ';
			}
		}
		return $keywords;
	}
	
	function isEmpty() 
	{
		return false;
	}
}