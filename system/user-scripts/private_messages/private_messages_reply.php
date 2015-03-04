<?php

class SJB_PrivateMessages_Reply extends SJB_Function
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
			$id = SJB_Request::getInt('id', 0, 'GET');
			if ($id > 0) {
				$errors = array();
				if (isset ($_POST ['form_to'])) {
					$to_user_name = (isset($_POST['form_to']) ? strip_tags($_POST['form_to']) : '');
					$to_user_info = SJB_UserManager::getUserInfoByUserName($to_user_name);
					$to_user = (isset ($to_user_info['sid']) ? $to_user_info['sid'] : 0);
					$subject = (isset ($_POST['form_subject']) ? strip_tags($_POST['form_subject']) : '');
					$text = (isset ($_POST['form_message']) ? SJB_PrivateMessage::cleanText($_POST['form_message']) : '');
					$save = (isset ($_POST['form_save']) ? true : false);
					$reply_id = (isset ($_POST['reply_id']) ? $_POST['reply_id'] : false);

					if ($to_user == 0)
						$errors['form_to'] = 'You specified wrong username';
					if (empty ($subject))
						$errors['form_subject'] = 'Please, enter message subject';
					if (empty ($text))
						$errors['form_message'] = 'Please, enter message';

					if (count($errors) == 0) {
						$anonym = SJB_Request::getVar('anonym');
						SJB_PrivateMessage::sendMessage($user_id, $to_user, $subject, $text, $save, $reply_id, false, $anonym);
						// save to contacts
						if (!$anonym) {
							SJB_PrivateMessage::saveContact($user_id, $to_user);
							SJB_PrivateMessage::saveContact($to_user, $user_id);
						}
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/private-messages/inbox/');
					} else {
						$message ['to_name'] = htmlentities($to_user_name, ENT_QUOTES, "UTF-8");
						$message ['subject'] = htmlentities($subject, ENT_QUOTES, "UTF-8");
						$message ['message'] = $text;
						$tp->assign('save', $save);
					}

				} else {

					if (SJB_PrivateMessage::isMyMessage($id))
						$message = SJB_PrivateMessage::readMessage($id);
					else {
						$errors['NOT_EXISTS_MESSAGE'] = 1;
						$message = '';
					}

					if ($message) {
						$message ['to_name'] =  htmlentities($message ['from_name'], ENT_QUOTES, "UTF-8");
						$message ['subject'] = 'RE: ' . htmlentities($message ['subject'], ENT_QUOTES, "UTF-8");
						$message ['message'] = '<p>&nbsp;</p><blockquote class="pmQuote">' . $message ['message'] . '</blockquote>';
					}
					$tp->assign('reply_id', $id);
				}
			}

			$tp->assign('errors', $errors);
			$tp->assign('include', 'reply_message.tpl');
			$tp->assign('message', $message);

			$tp->assign('unread', SJB_PrivateMessage::getCountUnreadMessages($user_id));
			$tp->display('main.tpl');

		} else {
			$tp->assign('return_url', base64_encode(SJB_Navigator::getURIThis()));
			$tp->display('../users/login.tpl');
		}
	}
}
