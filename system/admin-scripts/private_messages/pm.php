<?php

class SJB_Admin_PrivateMessages_PmMain extends SJB_Function
{
	public function isAccessible()
	{
		$userSid = SJB_Request::getVar('user_sid', null);
		$userGroupID = SJB_UserGroupManager::getUserGroupIDByUserSID($userSid);
		$this->setPermissionLabel('manage_' . strtolower($userGroupID));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$user = SJB_UserManager::getUserInfoBySID(SJB_Request::getVar('user_sid'));
		$user_id = $user['sid'];

		$total_in = SJB_PrivateMessage::getTotalInbox($user_id);
		$total_out = SJB_PrivateMessage::getTotalOutbox($user_id);
		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($user['user_group_sid']);
		
		SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupInfo['id']);
		$tp->assign('username', $user['username']);
		$tp->assign("user_group_info", $userGroupInfo);
		$tp->assign('user_sid', $user_id);
		$tp->assign('total_in', $total_in);
		$tp->assign('total_out', $total_out);

		$tp->display('main.tpl');
	}
}
