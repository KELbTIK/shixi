<?php

class SJB_SubAdmin extends SJB_Admin
{
	static private $subAdminInfo = null;

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
		return isset($_REQUEST['action']) && $_REQUEST['action'] == 'login' && self::admin_login(SJB_Request::getVar('username'), SJB_Request::getVar('password'));
	}

	public static function isSubAdminExist()
	{
		$username = SJB_DB::quote(SJB_Request::getVar('username'));
		$password = md5(SJB_DB::quote(SJB_Request::getVar('password')));
		$value = SJB_DB::queryValue("SELECT * FROM `subadmins` WHERE `username` = ?s AND `password` = '?w'", $username, $password);
		if (empty($value)) {
			return false;
		}
		return true;
	}

	/**
	 * checking for existing authorized administrator
	 * Function checks if administrator has authorized
	 * @return 'true' if administrator has authorized or 'false' otherwise
	 */
	public static function admin_authed()
	{
		if ( (!is_null(SJB_Session::getValue('username')) && !is_null(SJB_Session::getValue('usertype'))) && SJB_Session::getValue('usertype') == "subadmin" ) {
			return self::setAdminInfo(SJB_Session::getValue('username'));
		}
		return false;
	}
	
	/**
	 * logging into system as administrator
	 * Function logs administrator into system.
	 * If operation succeded it registers session variables 'username' and 'usertype'
	 * @param string $username user's name
	 * @param string $password user's password
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	public static function admin_login($username)
	{
		$username = SJB_DB::quote($username);
		SJB_SubAdmin::setAdminInfo($username);

		SJB_Session::setValue('adminLoginCounter', 1);
		SJB_Session::setValue('username', $username);
		SJB_Session::setValue('usertype', "subadmin");
		setcookie("admin_mode", 'on', null, '/' );

		return true;
	}

	public static function checkCurrentPassword($sPassword)
	{
		return (strcmp(self::$subAdminInfo['password'], md5($sPassword)) === 0 );
	}

	public static function setAdminInfo($username)
	{
		$result = SJB_DB::query('SELECT * FROM `subadmins` WHERE `username` = ?s ', $username);
		if (!empty($result)) {
			self::$subAdminInfo = $result[0];
			return true;
		}
		return false;
	}

	public static function getSubAdminSID()
	{
		if (isset(self::$subAdminInfo['sid']))
			return self::$subAdminInfo['sid'];
		return null;
	}

	public static function getSubAdminInfo()
	{
		return self::$subAdminInfo;
	}

	/**
	 * logging administrator out of system
	 * Function logs administrator out of system
	 */
	public static function admin_log_out()
	{
		SJB_Session::unsetValue('username');
		SJB_Session::unsetValue('usertype');
		SJB_Session::unsetValue('admintype');
		SJB_Session::unsetValue('adminLoginCounter');
		setcookie("admin_mode", '', time()-3600, '/');
	}
	
}
