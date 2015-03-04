<?php

class SJB_UserProfileDisplayListController extends SJB_DisplayListController
{
	function SJB_UserProfileDisplayListController($input_data)
	{
		parent::SJB_DisplayListController($input_data, new SJB_UserProfileFieldManager, new SJB_UserProfileFieldListItemManager);
	}

	function _getTypeInfo()
	{
		return SJB_UserGroupManager::getUserGroupInfoBySID($this->field->getUserGroupSID());
	}

	function _getTypeSID()
	{
		return $this->field->getUserGroupSID();
	}
}
