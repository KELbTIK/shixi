<?php
class SJB_PrivateMessages_Send extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('use_private_messages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$info = '';
		if (SJB_UserManager::isUserLoggedIn()) {
			$user_id = SJB_UserManager::getCurrentUserSID();
			$to = SJB_Request::getVar('to');

			// POST and check for errors form_to form_subject form_message
			if (isset($_POST['form_to'])) {
				$to_user_name = SJB_Request::getVar('form_to', null, 'POST');
				$to_user_info = null;

				// trying to get user info by user id
				if (intval($to_user_name)) {
					$to_user_info = SJB_UserManager::getUserInfoBySID($to_user_name);
				}

				/*
				 * в функции compose private message функцию отправки
				 * сообщения по имени пользователя оставить рабочей
				 */
				if (is_null($to_user_info)) {
					$to_user_info = SJB_UserManager::getUserInfoByUserName($to_user_name);
				}

				// trying to get user info by user id
				if (intval($to_user_name)) {
					$to_user_info = SJB_UserManager::getUserInfoBySID($to_user_name);
				}

				/*
				 * в функции compose private message функцию отправки
				 * сообщения по имени пользователя оставить рабочей
				 */
				if (is_null($to_user_info)) {
					$to_user_info = SJB_UserManager::getUserInfoByUserName($to_user_name);
				}

				$to_user = (isset($to_user_info['sid']) ? $to_user_info['sid'] : 0);
				$subject = (isset($_POST['form_subject']) ? strip_tags($_POST['form_subject']) : '');
				$message = (isset($_POST['form_message']) ? SJB_PrivateMessage::cleanText($_POST['form_message']) : '');

				$save = (isset($_POST['form_save']) ? true : false);

				if ($to_user == 0) {
					$errors['form_to'] = 'You specified wrong username';
				}
				if (empty($subject)) {
					$errors['form_subject'] = 'Please, enter message subject';
				}
				if (empty($message)) {
					$errors['form_message'] = 'Please, enter message';
				}

				if (count($errors) == 0) {
					$anonym = SJB_Request::getVar('anonym');
					SJB_PrivateMessage::sendMessage($user_id, $to_user, $subject, $message, $save, false, false, $anonym);
					$info = 'The message was sent successfully';
					$to = '';
					// save to contacts
					if (!$anonym) {
						SJB_PrivateMessage::saveContact($user_id, $to_user);
						SJB_PrivateMessage::saveContact($to_user, $user_id);
					}
				} else {
					$tp->assign("form_to", htmlentities($to_user_name, ENT_QUOTES, "UTF-8"));
					$tp->assign("form_subject", htmlentities($subject, ENT_QUOTES, "UTF-8"));
					$tp->assign("form_message", $message);
					$tp->assign("form_save", $save);
					$tp->assign("errors", $errors);
				}
			}

			$display_to = '';
			// get display name for "Message to" field
			SJB_UserManager::getComposeDisplayName($to, $display_to);

			$tp->assign('info', $info);
			$tp->assign('to', $to);
			$tp->assign('anonym', SJB_Request::getVar('anonym'));
			$tp->assign('display_to', $display_to);
			$tp->assign('include', 'new_message.tpl');
			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($user_id));
			$tp->display('main.tpl');
		}
		else {
			$tp->assign('return_url', base64_encode(SJB_Navigator::getURIThis()));
			$tp->assign('ajaxRelocate', true);
			$tp->display('../users/login.tpl');
		}

	}
}
