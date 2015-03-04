<?php

class SJB_PrivateMessages_Read extends SJB_Function
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
			$errors = array();
			$id = SJB_Request::getInt('id', 0, 'GET');
			$action = SJB_Request::getVar('action', '', 'GET');

			if ($id > 0) { // read message
				if (SJB_PrivateMessage::isMyMessage($id)) {
					if ($action == 'delete') {
						SJB_PrivateMessage::delete(array($id));
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/private-messages/inbox/');
					}
					$message = SJB_PrivateMessage::readMessage($id);

					SJB_Authorization::updateCurrentUserSession();
					$current_user_info = SJB_UserManager::createTemplateStructureForCurrentUser();
					$current_user_info['logged_in'] = true;
					$current_user_info['new_messages'] = SJB_PrivateMessage::getCountUnreadMessages($current_user_info['id']);
					SJB_System::setCurrentUserInfo($current_user_info);
					$tp->assign('message', $message);
					$tp->assign('include', 'message_detail.tpl');
				} else {
					$errors['NOT_EXISTS_MESSAGE'] = 1;
				}
			}

			$tp->assign('errors', $errors);
			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($user_id));
			$tp->display('main.tpl');
		} else {
			$tp->assign('return_url', base64_encode(SJB_Navigator::getURIThis()));
			$tp->display('../users/login.tpl');
		}
	}
}
