<?php

class SJB_PrivateMessages_Inbox extends SJB_Function
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
			$action = SJB_Request::getVar('pm_action', SJB_Request::METHOD_POST, false);
			if ($action) {
				$checked = SJB_Request::getVar('pm_check', SJB_Request::METHOD_POST, array());;
				switch ($action) {
					case 'mark':
						SJB_PrivateMessage::markAsRead($checked);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/private-messages/inbox/');
						break;

					case 'delete':
						SJB_PrivateMessage::delete($checked);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/private-messages/inbox/');
						break;

					default :
						break;
				}
			}

			$page = SJB_Request::getInt('page', 1, 'GET');
			$messagesPerPage = SJB_Request::getInt('messagesPerPage', 10);
			$total = SJB_PrivateMessage::getTotalInbox($user_id);
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

			$tp->assign('message_list', SJB_PrivateMessage::getListInbox($user_id, $page, $messagesPerPage));
			$tp->assign('include', 'list_inbox.tpl');
			$tp->assign('messagesPerPage', $messagesPerPage);
			$tp->assign('page', $page);
			$tp->assign('totalPages', $totalPages);

			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($user_id));
		}

		$tp->display('main.tpl');
	}
}
