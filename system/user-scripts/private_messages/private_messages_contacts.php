<?php

class SJB_PrivateMessages_Contacts extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		if (SJB_UserManager::isUserLoggedIn()) {
			$userSID = SJB_UserManager::getCurrentUserSID();
			$action = SJB_Request::getVar('pm_action', null);

			if ($action) {
				$checked = SJB_Request::getVar('pm_check', array(), 'POST');

				switch ($action) {
					case 'delete':
						SJB_PrivateMessage::deleteContact($userSID, $checked);
						break;
					case 'save_contact':
						$error = '';
						$contactSID = SJB_Request::getInt('user_id', 0);
						SJB_PrivateMessage::saveContact($userSID, $contactSID, $error);
						$tp->assign('error', $error);
						$tp->display('contact_save.tpl');
						return true;
						break;
					default :
						break;
				}
			}

			$page = SJB_Request::getInt('page', 1, 'GET');
			$contactsPerPage = SJB_Request::getInt('contactsPerPage', 10);
			SJB_PrivateMessage::deleteNonexistentContacts($userSID);
			$total = SJB_PrivateMessage::getTotalContacts($userSID);
			$totalPages = ceil($total / $contactsPerPage);

			if ($totalPages == 0) {
				$totalPages = 1;
			}
			if (empty($page) || $page <= 0) {
				$page = 1;
			}
			if ($totalPages < $page) {
				SJB_HelperFunctions::redirect("?page={$totalPages}");
			}
			$tp->assign('message_list', SJB_PrivateMessage::getContacts($userSID, $page, $contactsPerPage));
			$tp->assign('contactsPerPage', $contactsPerPage);
			$tp->assign('page', $page);
			$tp->assign('totalPages', $totalPages);
			$tp->assign('include', 'contacts.tpl');

			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($userSID));
		}

		$tp->display('main.tpl');
	}
}
