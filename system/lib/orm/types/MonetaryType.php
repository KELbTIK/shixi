<?php

class SJB_MonetaryType extends SJB_Type
{
	var $currency_values;
	
	function SJB_MonetaryType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->sql_type = 'DECIMAL';
		$this->currency_values = isset ( $property_info ['currency_values'] ) ? $property_info ['currency_values'] : array ();
		if (isset($this->property_info['value']) && !is_array($this->property_info['value'])){
			$value = $this->property_info['value'];
			unset($this->property_info['value']);
			$this->property_info['value']['value'] = $value;
		}
		$this->default_template = 'monetary.tpl';
	}
	
	function getPropertyVariablesToAssign()
	{
		$value = array('value' => '');
		$numeric = 0;
		if ($this->property_info['value'] != '') {
			if (isset($this->property_info['value']['1'])) {
				$this->property_info['value'] = $this->property_info['value']['1'];
			}
			$value = array('value' => htmlentities($this->property_info['value']['value'], ENT_QUOTES, "UTF-8"), 'currency' => $this->property_info['value']['add_parameter']);
		}
		if (is_numeric($value))
			$numeric = 1;

		$default_value = $this->getDefaultValueForPropertyVariable();
		return array(	'id' 	=> $this->property_info['id'],
						'value'	=> $value,
						'numeric'	=> $numeric,
						'default_value' => $default_value,
						'list_currency' => $this->currency_values,
					);
	}

	protected function getDefaultValueForPropertyVariable()
	{
		if (!empty($this->property_info['add_parameter']))
			$default_value = array(
				'value' => $this->property_info['default_value'],
				'add_parameter' => $this->property_info['add_parameter']
			);
		else
			$default_value = $this->property_info['default_value'];

		return $default_value;
	}

	function isValid()
	{
		$i18n = SJB_ObjectMother::createI18N();
		$value = $this->property_info['value']['value'];
		if ($i18n->isValidFloat($value)) {
			if (!empty($this->property_info['value']['add_parameter']))
				return true;
			else 
				return 'CURRENCY_SIGN_IS_EMPTY';
		}
		if (is_string($this->property_info['value']['value'])) {
			if ($this->hasBadWords()) {
				return 'HAS_BAD_WORDS';
			}
			return true;
		}
		return 'NOT_VALID_ID_VALUE';
	}

	public static function getFieldExtraDetails()
	{
		return array();
	}

	function getSQLValue()
	{
		$i18n  = SJB_ObjectMother::createI18N();
		$value = $this->property_info['value']['value'];
		if ($i18n->isValidFloat($value)) {
			return $i18n->getInput('float', $this->property_info['value']['value']);
		}
		
		return $this->property_info['value']['value'];
	}
	
	function getAddParameter()
	{
		if (isset($this->property_info['value']['add_parameter']))
			return SJB_DB::quote($this->property_info['value']['add_parameter']);
		return '';
	}
	
    function getKeywordValue()
    {
		return '';
	}
	
	function isEmpty()
	{
		$value_is_empty = false;
	    if (is_array($this->property_info['value'])) {
	    	if (trim($this->property_info['value']['value']) == '')
 				$value_is_empty = true;
	    } else {
	    	$value_is_empty = true; 
	    }
	    return $value_is_empty;
	}
	
	function hasBadWords() 
	{
		if (empty($this->property_info['value']['value']))
			return false;

		$badWords = strtolower(SJB_Settings::getSettingByName('bad_words'));
		if (empty($badWords))
			return false;

		$badWords = preg_split('/\s+/', $badWords, -1, PREG_SPLIT_NO_EMPTY);
		
		$words = preg_split('/[^\w\d]+/iu', $this->property_info['value']['value']);
		foreach ($badWords as $badWord) {
		    if (in_array($badWord, $words))
		        return true;
		}
		
		return false;
	}
	
}