<?php

class SJB_Users_InitCurrentUserStructure extends SJB_Function
{
	public function execute()
	{
		$current_user_info = array('logged_in' => false);

		if (SJB_UserManager::isUserLoggedIn()) {
			SJB_Authorization::updateCurrentUserSession();
			$current_user_info = SJB_UserManager::createTemplateStructureForCurrentUser();
			$current_user_info['logged_in'] = true;
			$current_user_info['new_messages'] = SJB_PrivateMessage::getCountUnreadMessages($current_user_info['id']);
		}
		else { // social plugin
			$userGroups = SJB_UserGroupManager::getAllUserGroupsInfo();
			SJB_System::setCurrentUserGroupsInfo($userGroups);
			SJB_Event::dispatch('Login_Plugin');
		}

		SJB_System::setCurrentUserInfo($current_user_info);
	}
}