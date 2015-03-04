<?php

class SJB_Admin_Users_ChooseUser extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		
		$userGroupsInfo = SJB_UserGroupManager::getAllUserGroupsInfo();
		$tp->assign('userGroupsInfo', $userGroupsInfo);
		$tp->display('choose_user_group.tpl');
	}
}
