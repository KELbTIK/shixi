<?php

class SJB_CaptchaType extends SJB_Type
{
	
	var $captchaImg;
	
    function SJB_CaptchaType($property_info)
    {
    	SJB_Event::dispatch('getPropertyInfo', $property_info, true);
		parent::SJB_Type($property_info);
		$this->default_template = 'captcha.tpl';
	}
	function getPropertyVariablesToAssign()
	{
		$propertyVariables = array('type'=>'kCaptcha', 'windowType'=>!empty($this->property_info['windowType'])?$this->property_info['windowType']:false);
		SJB_Event::dispatch('getCaptchaProperties', $propertyVariables, true);
		return $propertyVariables;
	}

	function isValid()
	{
		$property_info = $this->property_info;
		$property_info['type'] = 'kCaptcha';
		SJB_Event::dispatch('captchaValidation', $property_info, true);
		if ($property_info['type'] == 'kCaptcha') {
			$this->captchaImg = SJB_Session::getValue('captcha_keystring');
			if ($this->captchaImg != $this->property_info['value'])
				return 'NOT_VALID';
		}
		else {
			if ($property_info === false)
				return 'NOT_VALID';
		}
		return true;
	}
	
	function getSQLValue()
	{
		return intval($this->property_info['value']);
	}
}

