<?php

class SJB_UserProfileField extends SJB_Object {

	var $user_group_sid;
	var $field_type;
	var $order;
	
	function SJB_UserProfileField($user_profile_field_info = null)
	{
		$this->db_table_name = 'user_profile_fields';
		$this->details = new SJB_UserProfileFieldDetails($user_profile_field_info);
		$this->field_type = isset($user_profile_field_info['type']) ? $user_profile_field_info['type'] : null;
		$this->order = isset($user_profile_field_info['order']) ? $user_profile_field_info['order'] : null;
	}
	
	function setUserGroupSID($user_group_sid)
	{
		$this->user_group_sid = $user_group_sid;
	}
	
	function getUserGroupSID()
	{
		return $this->user_group_sid;
	}

    function getFieldType()
    {
		return $this->field_type;
	}
	
	function getOrder()
	{
		return $this->order;
	}
	
	function addInfillInstructions($value='')
	{
		$this->addProperty($this->details->getInfillInstructions($value));
	}
	
	function addParentSID($value='')
	{
		$this->addProperty($this->details->getParentSID($value));
	}

	function addDisplayAsProperty($value)
	{
		$this->addProperty($this->details->getDisplayAsProperty($value, $this->getFieldType()));
	}
}

