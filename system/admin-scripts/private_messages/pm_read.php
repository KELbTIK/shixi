<?php

class SJB_Admin_PrivateMessages_PmRead extends SJB_Function
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

		$action = SJB_Request::getVar('action', '', SJB_Request::METHOD_GET);
		$mess_id = intval(SJB_Request::getVar('mess', 0, SJB_Request::METHOD_GET));
		$return_to = SJB_Request::getVar('from', 'in', SJB_Request::METHOD_GET);
		$page = intval(SJB_Request::getVar('page', 1, SJB_Request::METHOD_GET));

		$user = SJB_UserManager::getUserInfoBySID(SJB_Request::getVar('user_sid'));
		$user_id = $user ['sid'];

		if ($action == 'delete') {
			SJB_DB::query("DELETE FROM `private_message` WHERE `id` = '{$mess_id}'");

			$per_page = 10;
			if ($return_to == 'in')
				$total = SJB_PrivateMessage::getTotalInbox($user_id);
			else
				$total = SJB_PrivateMessage::getTotalOutbox($user_id);
			$max_pages = ceil($total / $per_page);
			if ($max_pages == 0)
				$max_pages = 1;
			if ($max_pages < $page)
				$page = $max_pages;

			$site_url = SJB_System::getSystemSettings('SITE_URL');
			SJB_HelperFunctions::redirect($site_url . '/private-messages/pm-' . ($return_to == 'in' ? 'inbox' : 'outbox') . "/?user_sid={$user_id}&page={$page}");
		}

		$message = SJB_PrivateMessage::ReadMessage($mess_id, true);

		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($user['user_group_sid']);
		
		SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupInfo['id']);
		$tp->assign("user_group_info", $userGroupInfo);
		$tp->assign('returt_to', $return_to);
		$tp->assign('username', $user['username']);
		$tp->assign('user_sid', $user_id);
		$tp->assign('message', $message);
		$tp->assign('page', $page);

		$tp->display('pm_read.tpl');

	}
}
