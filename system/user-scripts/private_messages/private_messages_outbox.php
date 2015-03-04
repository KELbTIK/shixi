<?php

class SJB_PrivateMessages_Outbox extends SJB_Function
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

			if (SJB_Request::getVar('pm_action', '', SJB_Request::METHOD_POST) == 'delete') {
				$checked = SJB_Request::getVar('pm_check', array(), SJB_Request::METHOD_POST);
				SJB_PrivateMessage::delete($checked);
			}

			$page = intval(SJB_Request::getVar('page', 1, SJB_Request::METHOD_GET));
			$messagesPerPage = SJB_Request::getInt('messagesPerPage', 10);
			$total = SJB_PrivateMessage::getTotalOutbox($user_id);
			$totalPages = ceil($total / $messagesPerPage);

			if ($totalPages == 0) {
				$totalPages = 1;
			}
			if (empty($page) || $page <= 0) {
				$page = 1;
			}
			if ($totalPages < $page) {
				SJB_HelperFunctions::redirect("?page={$totalPages}");
			}
			$list = SJB_PrivateMessage::getListOutbox($user_id, $page, $messagesPerPage);

			$tp->assign('message_list', $list);
			$tp->assign('messagesPerPage', $messagesPerPage);
			$tp->assign('page', $page);
			$tp->assign('totalPages', $totalPages);
			$tp->assign('include', 'list_outbox.tpl');

			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($user_id));
		}

		$tp->display('main.tpl');
	}
}
