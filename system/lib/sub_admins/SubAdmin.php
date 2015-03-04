<?php

class SJB_SubAdminProp extends SJB_Object
{
	function __construct($user_info)
	{
		$this->details = new SJB_SubAdminDetails($user_info);
	}

	function getUserName()
	{
		return $this->details->properties['username']->value;
	}

	function isSavedInDB()
	{
		$sid = $this->getSID();
		return !empty($sid);
	}
}

