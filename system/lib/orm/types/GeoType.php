<?php

class SJB_GeoType extends SJB_Type
{
	function SJB_GeoType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->default_template = 'geo.tpl';
	}

	function isValid()
	{
		if (isset($this->property_info['hidden']) && $this->property_info['hidden'] || SJB_LocationManager::doesLocationExist($this->property_info['value'])) {
			return true;
		}
		return 'LOCATION_NOT_EXISTS';
	}

    function getKeywordValue()
	{
		return $this->property_info['value'];
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'use_autocomplete',
				'caption'	=> 'Use Autocomplete',
				'type'		=> 'boolean',
				'value'		=> '',
                'is_system' => true,
				),
		);
	}

	function getPropertyVariablesToAssign()
	{
		$profileFieldAsDv = '';
		if (isset($this->property_info['profile_field_as_dv']) && $this->property_info['profile_field_as_dv'] != '') {
			if (!empty($this->property_info['user_sid'])) {
				$userSID = $this->property_info['user_sid'];
			} else {
				$userSID = SJB_UserManager::getCurrentUserSID();
			}
			
			if ($this->property_info['parentID']) {
				if (SJB_UserManager::issetFieldByName($this->property_info['parentID'] . '_' . $this->property_info['profile_field_as_dv'])) {
					$profileFieldAsDv = SJB_UserManager::getSystemPropertyValueByObjectSID('users', $userSID, $this->property_info['parentID'] . '_' . $this->property_info['profile_field_as_dv']);
				}
			} else {
				$profileFieldAsDv = SJB_UserManager::getSystemPropertyValueByObjectSID('users', $userSID, $this->property_info['profile_field_as_dv']);
			}
		}
		
		return array(
			'id'                  => $this->property_info['id'],
			'useAutocomplete'     => $this->property_info['use_autocomplete'],
			'type'                => $this->property_info['type'],
			'isClassifieds'       => $this->property_info['is_classifieds'],
			'value'               => SJB_HelperFunctions::getClearVariablesToAssign($this->property_info['value']),
			'default_value'       => $this->property_info['default_value'],
			'profile_field_as_dv' => $profileFieldAsDv,
			'hidden'              => $this->property_info['hidden']
		);
	}
	
	function getSQLValue()
	{
		return $this->property_info['value'];
	}
}
