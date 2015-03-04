<?php

class SJB_Admin
{
	/**
	 * @package Users
	 * @subpackage Administrators
	 */

	/**
	 * authorizing administrator
	 *
	 * Function checks if there's active administrator.
	 * If it is, then it return true. If it's not it outputs
	 * form for logging into system untill administrator logins system
	 *
	 * @return bool 'true' administrator has authorized or 'false' otherwise
	 */
	public static function admin_auth()
	{
		$error = array();
		$tp = SJB_System::getTemplateProcessor();
		$params = SJB_HelperFunctions::form(array('action' => 'login') + SJB_HelperFunctions::get_request_data_params());
		if (SJB_Request::getVar('action') == 'login') {
			if (!SJB_Admin::isAdminExist(SJB_Request::getVar('username', ''), SJB_Request::getVar('password')) && !SJB_SubAdmin::isSubAdminExist()) {
				if (is_null(SJB_Session::getValue('adminLoginCounter'))) {
					SJB_Session::setValue('adminLoginCounter', 1);
				} else {
					SJB_Session::setValue('adminLoginCounter', SJB_Session::getValue('adminLoginCounter') + 1);
				}
				$error['LOGIN_PASS_NOT_CORRECT'] = true;
			}
			if (SJB_Captcha::getInstance($tp, $_REQUEST)->isValid($error) && empty($error)) {
				return SJB_SubAdmin::isSubAdminExist() ? SJB_SubAdmin::admin_auth() : SJB_Admin::admin_login(SJB_Request::getVar('username', ''));
			}
		}
		header('Content-type: text/html;charset=utf-8', true);
		$tp->assign('form_hidden_params', $params);
		$tp->assign('ERROR', $error);
		$tp->display('auth.tpl');
		return false;
	}

	/**
	 * checking for existing authorized administrator
	 * Function checks if administrator has authorized
	 * @return 'true' if administrator has authorized or 'false' otherwise
	 */
	public static function admin_authed()
	{
		return !is_null(SJB_Session::getValue('username')) && !is_null(SJB_Session::getValue('usertype')) && SJB_Session::getValue('usertype') == "admin";
	}

	/**
	 * logging into system as administrator
	 *
	 * Function logs administrator into system.
	 * If operation succeded it registers session variables 'username' and 'usertype'
	 *
	 * @param string $username user's name
	 * @param string $password user's password
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	public static function admin_login($username)
	{
		SJB_Session::setValue('username', SJB_DB::quote($username));
		SJB_Session::setValue('usertype', 'admin');
		SJB_Session::setValue('adminLoginCounter', 1);
		setcookie('admin_mode', 'on', null, '/');

		return true;
	}

	public static function isAdminExist($username, $password)
	{
		$username = SJB_DB::quote($username);
		$password = md5(SJB_DB::quote($password));

		$value = SJB_DB::queryValue("SELECT * FROM `administrator` WHERE `username` = ?s AND `password` = '?w'", $username, $password);

		return !empty($value);
	}

	/**
	 * logging administrator out of system
	 *
	 * Function logs administrator out of system
	 */
	public static function admin_log_out()
	{
		SJB_Session::unsetValue('username');
		SJB_Session::unsetValue('usertype');
		SJB_Session::unsetValue('adminLoginCounter');
		setcookie("admin_mode", '', time()-3600, '/');
	}

	public static function NeedShowSplashScreen()
	{
		return SJB_Request::getVar('showsplash') === 'true';
	}

	public static function ShowSplashScreen()
	{
		include(SJB_System::getSystemSettings('ADMIN_SPLASH_SCREEN_URL'));
	}
}
