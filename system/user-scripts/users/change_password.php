<?php


class SJB_Users_ChangePassword extends SJB_Function
{
	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$username = SJB_Request::getVar('username', null);
		$verification_key = SJB_Request::getVar('verification_key', null);
		$ERRORS = array();
		$password_was_changed = false;
		$user_info = SJB_UserManager::getUserInfoByUserName($username);

		if (empty($user_info)) {
			$ERRORS['EMPTY_USERNAME'] = 1;
		}
		elseif (empty($verification_key)) {
			$ERRORS['EMPTY_VERIFICATION_KEY'] = 1;
		}
		elseif ($user_info['verification_key'] != $verification_key) {
			$ERRORS['WRONG_VERIFICATION_KEY'] = 1;
		}
		elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (!empty($_REQUEST['password']) && $_REQUEST['password'] == $_REQUEST['confirm_password'])
				$password_was_changed = SJB_UserManager::changeUserPassword($user_info['sid'], $_REQUEST['password']);
			else
				$ERRORS['PASSWORD_NOT_CONFIRMED'] = 1;
		}

		if ($password_was_changed) {
			$template_processor->display('successful_password_change.tpl');
		}
		else {
			$template_processor->assign('username', $username);
			$template_processor->assign('verification_key', $verification_key);
			$template_processor->assign('errors', $ERRORS);
			$template_processor->display('change_password.tpl');
		}
	}
}
