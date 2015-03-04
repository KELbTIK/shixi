<?php


class SJB_Users_PasswordRecovery extends SJB_Function
{
	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$ERRORS = array();
		$message_was_sent = false;

		if (!empty($_REQUEST['email'])) {
			$user_sid = SJB_UserManager::getUserSIDbyEmail($_REQUEST['email']);

			if (!empty($user_sid)) {
				$message_was_sent = SJB_Notifications::sendUserPasswordChangeLetter($user_sid);
			}
			else {
				$ERRORS['WRONG_EMAIL'] = 1;
			}
		}

		if (!$message_was_sent) {
			$email = SJB_Request::getVar('email', '');
			$template_processor->assign('errors', $ERRORS);
			$template_processor->assign('email', $email);
			$template_processor->display('password_recovery.tpl');
		}
		else {
			$template_processor->display('password_change_email_successfully_sent.tpl');
		}

	}
}
