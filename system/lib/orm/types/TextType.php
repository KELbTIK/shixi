<?php

class SJB_TextType extends SJB_StringType
{
    function SJB_TextType($property_info)
	{       
		parent::SJB_StringType($property_info);
		$this->default_template = !empty($this->property_info['template']) ? $this->property_info['template'] : 'text.tpl';
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'maxlength',
				'caption'	=> 'Maximum Length', 
				'type'		=> 'integer',
				'length'	=> '20',
				'validators' => array(
					'SJB_PlusValidator',
				),
                'is_system' => true,
				),
			array(
				'id'		=> 'template',
				'caption'	=> 'Template', 
				'type'		=> 'string',
				'length'	=> '',
				'is_system' => true,
				),
		);
	}
	
	function getPropertyVariablesToAssign()
	{
		$profile_field_as_dv = '';
		if (isset($this->property_info['profile_field_as_dv']) && $this->property_info['profile_field_as_dv'] != ''){
			$profile_field_as_dv = SJB_UserManager::getSystemPropertyValueByObjectSID('users', SJB_UserManager::getCurrentUserSID(), $this->property_info['profile_field_as_dv']);
		}

		return array(
			'id'                  => $this->property_info['id'],
			'useAutocomplete'     => $this->property_info['use_autocomplete'],
			'type'                => $this->property_info['type'],
			'isClassifieds'       => $this->property_info['is_classifieds'],
			'value'               => $this->property_info['value'],
			'default_value'       => $this->property_info['default_value'],
			'profile_field_as_dv' => $profile_field_as_dv
		);
	}

	function getKeywordValueForAutocomplete()
	{
		if ($this->property_info['id'] == 'keywords')
			return parent::getKeywordValueForAutocomplete();
		return array();
	}

	function getSQLFieldType()
	{
		return 'LONGTEXT NULL';
	}
}