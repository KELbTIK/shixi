<?php

class SJB_StringType extends SJB_Type
{
	function SJB_StringType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->default_template = 'string.tpl';
	}
	
	function isEmpty()
	{
		$value_is_empty = false;
		if (is_array($this->property_info['value'])) {
			foreach ($this->property_info['value'] as $field_value) {
				$field_value = $this->applyHtmlFilters($field_value);
				if ($field_value == '') {
					$value_is_empty = true;
					break;
				}
			}
		} else {
			$this->property_info['value'] = trim($this->property_info['value']);
			$field_value = $this->property_info['value'];
			$field_value = $this->applyHtmlFilters($field_value);
			$value_is_empty = ($field_value == '');
		}
		return $value_is_empty;
	}

	function isValid()
	{
		if ($this->hasBadWords())
			return 'HAS_BAD_WORDS';
		if (empty($this->property_info['maxlength']))
			return true;
		if ($this->property_info['id'] == 'ApplicationSettings') {
			if ($this->property_info['value']['add_parameter'] == 1) {
				if (!preg_match("^[\w\._-]+@[\w\._-]+\.\w{2,}\$^ui", $this->property_info['value']['value'])) {
					return 'NOT_VALID_EMAIL_FORMAT';
				}
			}
			if (strlen($this->property_info['value']['value']) <= $this->property_info['maxlength'])
				return true;
		} elseif (strlen($this->property_info['value']) <= $this->property_info['maxlength']) {
			return true;
		}
		return 'DATA_LENGTH_IS_EXCEEDED';
	}
	
	function getPropertyVariablesToAssign()
	{
		$profile_field_as_dv = '';
		if (isset($this->property_info['profile_field_as_dv']) && $this->property_info['profile_field_as_dv'] != ''){
			if (!empty($this->property_info['user_sid'])) {
				$userSID = $this->property_info['user_sid'];
			} else {
				$userSID = SJB_UserManager::getCurrentUserSID();
			}
			if ($this->property_info['parentID']) {
				if (SJB_UserManager::issetFieldByName($this->property_info['parentID']."_".$this->property_info['profile_field_as_dv'])) {
					$profile_field_as_dv = SJB_UserManager::getSystemPropertyValueByObjectSID('users', $userSID, $this->property_info['parentID']."_".$this->property_info['profile_field_as_dv']);
				}
			} else {
				$profile_field_as_dv = SJB_UserManager::getSystemPropertyValueByObjectSID('users', $userSID, $this->property_info['profile_field_as_dv']);
			}
		}
		$value = SJB_HelperFunctions::getClearVariablesToAssign($this->property_info['value']);
		
		if ($this->property_info['id'] == 'ApplicationSettings' && !is_array($value)) {
			$value = array(
				'value' => $value,
				'add_parameter' => ''
			);
		}
		
		return array(
			'id'                  => $this->property_info['id'],
			'useAutocomplete'     => $this->property_info['use_autocomplete'],
			'type'                => $this->property_info['type'],
			'isClassifieds'       => $this->property_info['is_classifieds'],
			'value'               => $value,
			'default_value'       => $this->property_info['default_value'],
			'profile_field_as_dv' => $profile_field_as_dv,
			'hidden'              => $this->property_info['hidden']
		);
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'maxlength',
				'caption'	=> 'Maximum Length', 
				'type'		=> 'integer',
				'length'	=> '20',
				'value'		=> '256',
			    'validators' => array(
					'SJB_PlusValidator',
				),
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
		if ($this->property_info['id'] == 'ApplicationSettings' && !empty($this->property_info['value']['add_parameter']) || is_array($this->property_info['value'])) {
			return $this->property_info['value']['value'];
		}
		
		$this->property_info['value'] = $this->applyHtmlFilters($this->property_info['value']);
		return $this->property_info['value'];
	}

	function getAddParameter()
	{
		if (isset($this->property_info['value']['add_parameter']) && $this->property_info['id'] == 'ApplicationSettings')
			return SJB_DB::quote($this->property_info['value']['add_parameter']);
		return '';
	}

    function getKeywordValue()
	{
		if (!is_array($this->property_info['value']))
			return $this->property_info['value'];
		return '';
	}

	function getKeywordValueForAutocomplete()
	{
		if ($this->property_info['id'] == 'ApplicationSettings')
			return '';

		$value = $this->property_info['value'];
		if (!is_array($value))
			$value = array($value);
        foreach (array_keys($value) as $key)
            $value[$key] = trim(preg_replace('/(\s+|[^\'"_\w\dÀ-ÿ])/ui', ' ', strip_tags($value[$key])));
        return $value;
	}

    function htmlspecialchars_decode($string,$style=ENT_COMPAT)
    {
        $translation = array_flip(get_html_translation_table(HTML_SPECIALCHARS,$style));
        return strtr($string, $translation);
    }
    
	function hasBadWords() 
	{
		if (empty($this->property_info['value']))
			return false;

		$badWords = strtolower(SJB_Settings::getSettingByName('bad_words'));
		if (empty($badWords))
			return false;

		$badWords = preg_split('/\s+/', $badWords, -1, PREG_SPLIT_NO_EMPTY);

		if ($this->property_info['id'] == 'ApplicationSettings') {
			$words = preg_split('/[^\w\d]+/iu', strtolower($this->property_info['value']['value']));
		} else {
			$words = preg_split('/[^\w\d]+/iu', strtolower($this->property_info['value']));
		}
		foreach ($badWords as $badWord) {
		    if (in_array($badWord, $words))
		        return true;
		}
		
		return false;
	}
	
	public static function applyHtmlFilters($string)
	{
		$string = trim($string);
		if (SJB_Settings::getValue('escape_html_tags') == 'htmlpurifier' && SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != 'admin'){
			$filters = str_replace(',', '', SJB_Settings::getSettingByName('htmlFilter')); 
			$string = strip_tags($string, $filters);
		}
		return $string;
	}
}