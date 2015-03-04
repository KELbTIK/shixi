<?php

class SJB_PrivateMessages_AjSend extends SJB_Function
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
			$info = '';

			$to = SJB_Request::getVar('to', '', 'GET');

			// POST and check for errors form_to form_subject form_message
			if (isset($_POST['act'])) {
				$to_user_name = SJB_Request::getVar('form_to', '', 'POST');
				$to_user_info = null;
				if (intval($to_user_name))
					$to_user_info = SJB_UserManager::getUserInfoBySID($to_user_name);

				// в функции compose private message функцию отправки
				// сообщения по имени пользователя оставить рабочей
				if (is_null($to_user_info))
					$to_user_info = SJB_UserManager::getUserInfoByUserName($to_user_name);

				$cc = SJB_Request::getVar('cc', false);

				if ($cc !== false) {
					if (intval($cc))
						$cc_info = SJB_UserManager::getUserInfoBySID($cc);

					// в функции compose private message функцию отправки
					// сообщения по имени пользователя оставить рабочей
					if (is_null($cc_info))
						$cc_info = SJB_UserManager::getUserInfoByUserName($cc);

					if (!empty($cc_info))
						$cc = $cc_info['sid'];
				}

				$to_user = (isset($to_user_info['sid']) ? $to_user_info['sid'] : 0);
				$subject = (isset($_POST['form_subject']) ? strip_tags($_POST['form_subject']) : '');
				$message = (isset($_POST['form_message']) ? SJB_PrivateMessage::cleanText($_POST['form_message']) : '');

				$save = (isset($_POST['form_save']) ? ($_POST['form_save'] == 1 ? true : false) : false);

				if ($to_user == 0)
					$errors['form_to'] = 'Please enter correct username';
				if (empty($subject))
					$errors['form_subject'] = 'Please, enter message subject';
				if (empty($message))
					$errors['form_message'] = 'Please, enter message';

				if (count($errors) == 0) {
					$anonym = SJB_Request::getVar('anonym');
					SJB_PrivateMessage::sendMessage($user_id, $to_user, $subject, $message, $save, false, $cc, $anonym);
					// save to contacts
					if (!$anonym) {
						SJB_PrivateMessage::saveContact($user_id, $to_user);
						SJB_PrivateMessage::saveContact($to_user, $user_id);
					}

					echo '<p class="message">' . SJB_I18N::getInstance()->gettext(null, 'The message was sent successfully') . '</p>';
					exit();
				}
			}

			$display_to = '';

			// get display name for 'Message to' field
			SJB_UserManager::getComposeDisplayName($to, $display_to);

			$tp->assign('errors', $errors);
			$tp->assign('info', $info);
			$tp->assign('to', $to);
			$tp->assign('display_to', $display_to);
			$tp->assign('anonym', SJB_Request::getVar('anonym'));
			$tp->assign('cc', SJB_Request::getVar('cc', ''));
			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($user_id));
			$tp->display('new_message_ajax.tpl');
		} else {
			$tp->assign('return_url', base64_encode(SJB_Navigator::getURIThis()));
			$tp->assign('ajaxRelocate', true);
			$tp->display('../users/login.tpl');
		}

	}
}
