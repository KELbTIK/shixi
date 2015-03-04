<?php

class SJB_Admin_PrivateMessages_PmOutbox extends SJB_Function
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

		$user = SJB_UserManager::getUserInfoBySID(SJB_Request::getVar('user_sid', 0, SJB_Request::METHOD_GET));
		$user_id = $user['sid'];

		if (SJB_Request::getVar('pm_action', '', SJB_Request::METHOD_POST) == 'delete') {
			$checked = SJB_Request::getVar('pm_check', array(), SJB_Request::METHOD_POST);
			SJB_PrivateMessage::delete($checked);
		}

		$page = intval(SJB_Request::getVar('page', 1, SJB_Request::METHOD_GET));
		$per_page = 10;
		$total = SJB_PrivateMessage::getTotalOutbox($user_id);
		$max_pages = ceil($total / $per_page);
		if ($max_pages == 0)
			$max_pages = 1;
		if ($max_pages < $page)
			SJB_HelperFunctions::redirect("?user_sid={$user_id}&page={$max_pages}");
		$navigate = SJB_PrivateMessage::getNavigate($page, $total, $per_page);

		$list = SJB_PrivateMessage::getListOutbox($user_id, $page, $per_page);

		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($user['user_group_sid']);
		
		SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupInfo['id']);
		$tp->assign("user_group_info", $userGroupInfo);
		$tp->assign('username', $user['username']);
		$tp->assign('user_sid', $user_id);
		$tp->assign('message', $list);
		$tp->assign('navigate', $navigate);
		$tp->assign('page', $page);

		$tp->display('pm_outbox.tpl');
	}
}
