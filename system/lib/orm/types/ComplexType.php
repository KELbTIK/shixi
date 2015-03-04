<?php

class SJB_ComplexType extends SJB_Type
{
	var $fields;
	var $table_name;
	var $complex;

	function SJB_ComplexType($property_info)
	{
		parent::SJB_Type($property_info);

		if (is_string($property_info['value'])) {
			$property_info['value'] = unserialize($property_info['value']);
			$this->setValue($property_info['value']);
		}

		$this->fields = isset($property_info['fields']) ? $property_info['fields'] : array();
		$this->table_name = isset($property_info['table_name']) ? $property_info['table_name'] : 'listings';
		$fields_info = isset($property_info['value']) ? $property_info['value'] : array();
		$this->complex = new SJB_Complex($this->fields, $this->table_name, $fields_info);
		$this->default_template = 'complex.tpl';
	}

	function getPropertyVariablesToAssign()
	{
		$form = new SJB_Form($this->complex);
		$form_fields = $form->getFormFieldsInfo();
		$propertyVariables = parent::getPropertyVariablesToAssign();
        $complexElements = array(1 => '');
		$newPropertyVariables = array(
						'form_fields' => $form_fields,
						'caption'	  => $this->property_info['caption'],
                        'complexElements' => $complexElements
					);
        $propertyVariables = array_merge($newPropertyVariables, $propertyVariables);
        if (!empty($propertyVariables['value'])) {
            if (is_string($propertyVariables['value']))
                $propertyVariables['value'] = unserialize($propertyVariables['value']);
            if (is_array($propertyVariables['value'])) {
                foreach ($propertyVariables['value'] as $complexElement) {
                    if (is_array($complexElement))
                        foreach ($complexElement as $key => $val)
                            if (!isset($complexElements[$key]))
                                $complexElements[$key] = '';
                }
            }
            $propertyVariables['complexElements'] = $complexElements;
        }
		return $propertyVariables;
	}

	function isValid()
	{
		$properties = $this->complex->getProperties();
		$properties = $properties?$properties:array();
		$errors = array();
		foreach ($properties  as $field) {
			$field->type->setComplexParent($this->property_info['id']);
			if ($field->type->getType() === 'date')
                $field->type->setConvertToDBDate(true);

			$values = $field->value;
			if (is_array($values)) {
				foreach ($values as $value) {
					$field->type->property_info['value'] = $value;
					$is_valid = $field->isValid();
					if ($is_valid !== true)
						$errors[$field->caption] = $is_valid;
				}
			}
			else {
				$field->type->property_info['value'] = $field->value;
				$is_valid = $field->isValid();
				if ($is_valid !== true)
					$errors[$field->caption] = $is_valid;
			}
			$field->type->property_info['value'] = $values;
		}
		return $errors;
	}
	
	function getSQLValue()
	{
        $properties = $this->complex->getProperties();
		foreach ($properties  as $field) {
            if ($field->type->getType() === 'date') {
                $field->type->setConvertToDBDate(true);
                $i18n = SJB_I18N::getInstance();
                $values = $field->value;
				if ( empty($values) || ! is_array($values))
					continue;
                foreach ($values as $key => $value)
                    $this->property_info['value'][$field->id][$key] = $i18n->getInput('date', $value);
            }
			if ($field->type->getType() === 'file') {
				$file_id = $this->property_info['id'] . "_" .$this->object_sid;
				$upload_manager = new SJB_UploadFileManager();
				$upload_manager->setFileGroup("files");
				$upload_manager->setUploadedFileID($file_id);
				$uploadFilesResults = $upload_manager->uploadFiles($this->property_info['id'], $field->id);


				$oldVals = isset($this->property_info['value'][$field->id])&& is_array($this->property_info['value'][$field->id]) ? $this->property_info['value'][$field->id] : array();

				$this->property_info['value'][$field->id] = array();

				foreach ($uploadFilesResults as $key => $value) {
					if (empty($value) && !empty($oldVals[$key]))
						$this->property_info['value'][$field->id][$key] = $oldVals[$key];
					else
                   		$this->property_info['value'][$field->id][$key] = $value;
                }

				$field->setValue($this->property_info['value'][$field->id]);
			}
		}
		return serialize($this->property_info['value']);
	}

    function getKeywordValue()
	{
		$complexProperties = $this->complex->getProperties();
		$keywords = '';
		if ($complexProperties) {
			/** @var SJB_ObjectProperty $complexProperty */
			foreach ($complexProperties as $complexProperty) {
				$fieldValues = $complexProperty->getValue();
				if (!empty($fieldValues) && is_array($fieldValues)) {
					foreach ($fieldValues as $complexEnum => $value) {
						if ($complexProperty->getType() == 'date') {
							$value = SJB_I18N::getInstance()->getDate($value);
						}
						$complexProperty->setValue($value);
						$complexProperty->setComplexEnum($complexEnum);
						$keywords .= $complexProperty->getKeywordValue() . ' ';
					}
					$complexProperty->setValue($fieldValues);
				}
			}
		}

		return $keywords;
	}
	
	function isEmpty() 
	{
		return false;
	}
	
	function isComplex()
	{
		return true;
	}

    function getKeywordValueForAutocomplete()
	{
        $keywords = array();
        $properties = $this->complex->getProperties();
		foreach ($properties as $property) {
            $propKeywords = $property->getKeywordValueForAutocomplete();
            if (is_array($propKeywords)) {
                foreach ($propKeywords as $propKeyword) {
                    if (!empty($propKeyword))
                        $keywords[] = $propKeyword;
                }
            }
            elseif (!empty($propKeywords))
                $keywords[] = $propKeywords;
        }

        return $keywords;
	}
	
}

class SJB_Complex extends SJB_Object
{
	function SJB_Complex($details_info, $table_name, $fields_info = array()) 
	{
		$this->db_table_name = $table_name;
		$this->details = new SJB_ComplexDetails($details_info, $fields_info);
	}
} 

class SJB_ComplexDetails extends SJB_ObjectDetails 
{
	var $properties = array();
	var $details;
	
	function SJB_ComplexDetails($details_info, $fields_info = array())
	{
		$sort_array = array();
		$sorted_details_info = array();
		foreach ($details_info as $index => $property_info) {
			$sort_array[$index] = $property_info['order'];
		}

		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

        foreach ($sort_array as $index => $value) {
			$sorted_details_info[$index] = $details_info[$index];
		}

		foreach ($sorted_details_info as $detail_info) {
			$detail_info['value'] = null;
			if (isset($fields_info[$detail_info['id']])) {
				$detail_info['value'] = $fields_info[$detail_info['id']];
			}
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}

	public static function getDetails()
	{
		return array();
	}


}
 
