<?php

class SJB_PrivateMessages_Main extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('use_private_messages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		if (SJB_UserManager::isUserLoggedIn()) {
			$user_id = SJB_UserManager::getCurrentUserSID();
			$unread = SJB_PrivateMessage::getCountUnreadMessages($user_id);
			$tp->assign('unread', $unread);
			$tp->assign('include', '');
		}

		$tp->display('main.tpl');
	}
}