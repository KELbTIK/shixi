<?php
/**
* Login function
*/

$ds = DIRECTORY_SEPARATOR;
define( 'SJB_PATH_BASE', dirname(__FILE__ )."{$ds}..{$ds}..{$ds}..");
define ('SJB_BASE_DIR', SJB_PATH_BASE."{$ds}");

function login_sjb(&$username, &$user_data)
{
	global $phpbb_root_path, $db, $user, $config, $cache, $phpEx;
	define('LOGIN_PHPBB', true); //set define to allow to check for recursivity
	
	$password = is_array($user_data) ? $user_data['password'] : $user_data;
	$status = null;
	if (!$password) {
		return array(
			'status'	=> LOGIN_ERROR_PASSWORD,
			'error_msg'	=> 'NO_PASSWORD_SUPPLIED',
			'user_row'	=> array('user_id' => ANONYMOUS),
		);
	}

	if (!$username) {
		return array(
			'status'	=> LOGIN_ERROR_USERNAME,
			'error_msg'	=> 'LOGIN_ERROR_USERNAME',
			'user_row'	=> array('user_id' => ANONYMOUS),
		);
	}
	$sql = 'DESCRIBE '.USERS_TABLE.' login_name';
	$result = $db->sql_query($sql);
	$has_login_name = $db->sql_fetchrow();
	$db->sql_freeresult($result);

	if (!empty($has_login_name)) {
		$sql = 'SELECT user_id, username, user_password, user_passchg, user_email, user_type, login_name
			FROM ' . USERS_TABLE . "
			WHERE login_name = '" . $db->sql_escape($username) . "'";
	}
	else {
		$sql = 'SELECT user_id, username, user_password, user_passchg, user_email, user_type
			FROM ' . USERS_TABLE . "
			WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'";
	}
			
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	if ($row) {
		// User inactive...
		if ($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE) {
			return array(
				'status'		=> LOGIN_ERROR_ACTIVE,
				'error_msg'		=> 'ACTIVE_ERROR',
				'user_row'		=> $row,
			);
		} 
		$status = LOGIN_SUCCESS;
	};
	$dir = getcwd();
	loadSJB();
	//get the sjb user
	$errors = array();
	$logged_in = SJB_Authorization::login($username, $password, false, $errors, false);
	// user not in phpbb3 db, but is in sjb
	$userInfo = SJB_UserDBManager::getUserInfoByUserName($username); 
	chdir($dir);
	if ($row && $userInfo) {
	
		if (!$logged_in && phpbb_check_hash($password, $row['user_password'])) {
			if (SJB_UserManager::changeUserPassword($userInfo['sid'], $password))
				$errors = array();
				$logged_in = SJB_Authorization::login($username, $password, false, $errors, false);
		}
		elseif ($logged_in && !phpbb_check_hash($password, $row['user_password'])) {
			$sql_ary = array(
				'user_actkey'		=> '',
				'user_password'		=> phpbb_hash($password),
				'user_newpasswd'	=> '',
				'user_pass_convert'	=> 0,
				'user_login_attempts'	=> 0,
			);

			$sql = 'UPDATE ' . USERS_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE user_id = ' . $row['user_id'];
			$db->sql_query($sql);
			
		}
	}
	elseif (!$row && $userInfo) {
		// retrieve default group id
		$sql = 'SELECT group_id
				FROM ' . GROUPS_TABLE . "
				WHERE group_name = '" . $db->sql_escape('REGISTERED') . "'
				AND group_type = " . GROUP_SPECIAL;
		$result = $db->sql_query($sql);
		$group = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!$group) {
			trigger_error('NO_GROUP');
		}

		// generate user account data
		$row = array(
			'username'		=> $username,
			'user_password'	=> phpbb_hash($password),
			'user_email'	=> $userInfo['email'],
			'group_id'		=> $group['group_id'],
			'user_type'		=> (string)USER_NORMAL,
		);
		
		if (!empty($has_login_name)) {
			$row['username']   = $userInfo['username'];
			$row['login_name'] = $username;
		}
		
		$status = LOGIN_SUCCESS_CREATE_PROFILE;
	}
	elseif ($row && !$userInfo && isset($errors['NO_SUCH_USER'])) {
		if (phpbb_check_hash($password, $row['user_password'])) {
			$errors = array();
		}
	}
	if (isset($errors['INVALID_PASSWORD'])) {
		return array(
				'status'	=> LOGIN_ERROR_PASSWORD,
				'error_msg'	=> 'LOGIN_ERROR_PASSWORD',
				'user_row'	=> array('user_id' => ANONYMOUS),
			);
	}
	elseif (isset($errors['USER_NOT_ACTIVE'])){
		return array(
				'status'		=> LOGIN_ERROR_ACTIVE,
				'error_msg'		=> 'ACTIVE_ERROR',
				'user_row'		=> $row,
			);
	}
	elseif (isset($errors['BANNED_USER'])) {
		define('IN_CHECK_BAN', 1);
		return array(
			'status'		=> BAN_TRIGGERED_BY_IP,
			'error_msg'		=> 'BAN_TRIGGERED_BY_IP',
			'user_row'		=> $row,
		);
	}
	elseif ($errors) {
		return array(
			'status'		=> $errors,
			'error_msg'		=> 'ACTIVE_ERROR',
			'user_row'		=> $row,
		);
	}
	
	// Successful login... set user_login_attempts to zero...

	return array(
		'status'		=> $status,
		'error_msg'		=> false,
		'user_row'		=> $row,
	);
}

function logout_sjb()
{
	$dir = getcwd();
	loadSJB();
	SJB_Authorization::logout();
	chdir($dir);
}

function loadSJB()
{
	require_once(SJB_PATH_BASE.'/system/core/System.php');
	SJB_System::loadSystemSettings(SJB_PATH_BASE.'/config.php');
	SJB_System::loadSystemSettings(SJB_PATH_BASE.'/system/user-config/DefaultSettings.php');
	SJB_System::boot();
	SJB_DB::init(SJB_System::getSystemSettings('DBHOST'), SJB_System::getSystemSettings('DBUSER'), SJB_System::getSystemSettings('DBPASSWORD'), SJB_System::getSystemSettings('DBNAME'));
	PHPBB_Session::init(SJB_System::getSystemSettings('SITE_URL'));
}

require_once(SJB_PATH_BASE.'/system/core/Session.php');
class PHPBB_Session extends SJB_Session
{
	public static function init($url) {
		$sessionStorage = new PHPBBSessionStorage();
		session_set_save_handler(
			array($sessionStorage, 'open'),
			array($sessionStorage, 'close'),
			array($sessionStorage, 'read'),
			array($sessionStorage, 'write'),
			array($sessionStorage, 'destroy'),
			array($sessionStorage, 'gc')
		);
		
		$path = SJB_Session::getSessionCookiePath($url);
		SJB_WrappedFunctions::ini_set("session.cookie_path", $path);
		SJB_WrappedFunctions::session_start();
	}
}

class PHPBBSessionStorage extends SessionStorage
{
	public static function write($id, $session_data)
	{
		$dbPath = SJB_PATH_BASE.'/system/core/DB.php';
		if (strstr(getcwd(), 'system\lib'))
			$dbPath = '../core/DB.php';
		require_once($dbPath);
		SJB_DB :: init(SJB_System::getSystemSettings('DBHOST'), SJB_System::getSystemSettings('DBUSER'), SJB_System::getSystemSettings('DBPASSWORD'), SJB_System::getSystemSettings('DBNAME'));
		$user_sid = 0;
		if (isset($_SESSION['current_user']))
			$user_sid = $_SESSION['current_user']['sid'];
			
		if (count(SJB_DB::query("select * from session where `session_id` = ?s", $id)) > 0)
			SJB_DB::query("update session set `data` = ?s, `time` = ?s, `user_sid` = ?n where `session_id` = ?s", $session_data, time(), $user_sid, $id);
		else
			SJB_DB::query("insert into session (`session_id`, `data`, `time`, `user_sid`) values (?s, ?s, ?s, ?n)", $id, $session_data, time(), $user_sid);
		return true;
	}
}

class LoginLogoutFromSJB
{
	
	function loginFromSJB()
	{
		global $db, $user, $template, $auth, $phpEx, $phpbb_root_path, $config;
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup('ucp');
		return $this->login();
	}
	
	function logoutFromSJB()
	{
		global $db, $user, $template, $auth, $phpEx, $phpbb_root_path, $config;
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup('ucp');
		$this->logout();
	}
	
	function login()
	{
		global $db, $user, $template, $auth, $phpEx, $phpbb_root_path, $config;
	
		$err = '';
		$result = false;
	
		// Make sure user->setup() has been called
		if (empty($user->lang))
			$user->setup();

		if (isset($_POST['login']))
		{
			$password	= request_var('password', '', true);
			$username	= request_var('username', '', true);
			$autologin	= (!empty($_POST['autologin'])) ? true : false;
			$viewonline = (!empty($_POST['viewonline'])) ? 0 : 1;
			$admin 		= ($admin) ? 1 : 0;
			$viewonline = ($admin) ? $user->data['session_viewonline'] : $viewonline;
	
			// If authentication is successful we redirect user to previous page
			$result = $auth->login($username, $password, $autologin, $viewonline, $admin);
		}
		return $result;
	}
	
	function logout()
	{
		global $db, $user, $template, $auth, $phpEx, $phpbb_root_path, $config;
		$user->session_kill();
		$user->session_begin();
		return true;
	}
}

