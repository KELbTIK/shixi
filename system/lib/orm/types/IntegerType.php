<?php

class SJB_IntegerType extends SJB_Type
{
	var $minimum;
	var $maximum;
	
	function SJB_IntegerType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->minimum = isset($property_info['minimum']) ? $property_info['minimum'] : null;
		$this->maximum = isset($property_info['maximum']) ? $property_info['maximum'] : null;
		$this->sql_type 		= 'SIGNED';
		$this->default_template = 'integer.tpl';
	}

	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$newPropertyVariables = array(
						'minimum'	=> $this->minimum,
						'maximum'	=> $this->maximum,
					);
		return array_merge($newPropertyVariables, $propertyVariables);
	}

	function isValid()
	{
		$value = $this->property_info['value'];
		
		$i18n = SJB_I18N::getInstance();
		if ($i18n->isValidInteger($value)) {
			$value = $i18n->getInput('integer', $value);
		}
		else {
			return 'NOT_INT_VALUE';
		}
				
		if (!empty($this->minimum) && is_numeric($this->minimum) && $this->minimum > $value)
			return 'OUT_OF_RANGE';

		if (!empty($this->maximum) && is_numeric($this->maximum) && $this->maximum < $value)
			return 'OUT_OF_RANGE';

		if (!empty($this->property_info['validators'])) {
			foreach ($this->property_info['validators'] as $validator) {
				$isValid = $validator::isValid($this);
				if ($isValid !== true)
					return $isValid;
			}
		}
	    return true;
	}
	
	function getSQLValue()
	{
		$value = SJB_ObjectMother::createI18N()->getInput('integer', $this->property_info['value']);
		return $value ? $value : null;
	}

	public static function getFieldExtraDetails()
	{		
		return array(		
			array(
				'id' => 'minimum',
				'caption' => 'Minimum Value', 
				'type' => 'integer',
				'minimum' => '',
			),
			array(
				'id'		=> 'maximum',
				'caption'	=> 'Maximum Value', 
				'type'		=> 'integer',
				'minimum'	=> '',
			),		
		);		
	}

    function getKeywordValue()
	{
		$i18n = SJB_ObjectMother::createI18N();
		return $i18n->getInput('integer', $this->property_info['value']);
	}
	
	function getSQLFieldType()
	{
		return "INT( 10 ) NULL";
	}
}
