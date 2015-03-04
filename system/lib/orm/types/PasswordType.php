<?php

class SJB_PasswordType extends SJB_Type
{
	function SJB_PasswordType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->default_template = 'password.tpl';
	}

	function isValid() {
		if (is_array($this->property_info['value'])) {
			if ($this->property_info['value']['original'] != $this->property_info['value']['confirmed']) {
				return 'NOT_CONFIRMED';
			}
		}
		return true;
	}

	function isEmpty()
	{
		if (is_array($this->property_info['value'])) {
			return trim($this->property_info['value']['original']) == "" && trim($this->property_info['value']['confirmed']) == "";
		}
		return empty($this->property_info['value']);
	}

	function getSavableValue()
	{
		return $this->property_info['value']['original'];
	}

	function getDisplayValue()
	{
		return null;
	}

	function getSearchValue($template)
	{
		return null;
	}

	function getSQLValue()
	{
		if (is_array($this->property_info['value'])) {
			return md5($this->property_info['value']['original']);
		}
		return md5($this->property_info['value']);
	}
}

