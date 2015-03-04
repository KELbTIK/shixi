<?php

class SJB_Admin_Users_LoginAsUser extends SJB_Function
{
	public function isAccessible()
	{
		$userName = SJB_Request::getVar('username', null);
		$userGroupID = SJB_UserGroupManager::getUserGroupIDByUserName($userName);
		$this->setPermissionLabel('manage_' . strtolower($userGroupID));
		return parent::isAccessible();
	}

	public function execute()
	{
		$username = SJB_Request::getVar('username', '');
		$password = SJB_Request::getVar('password', '');

		$user_exists_by_username = SJB_DB::queryValue('SELECT count(*) FROM `users` WHERE `username` = ?s', $username);
		if ($user_exists_by_username) {
			$user_exists_by_password = SJB_DB::queryValue('SELECT count(*) FROM `users` WHERE `username` = ?s AND `password` = ?s', $username, $password);
			if ($user_exists_by_password) {
				$user_info = SJB_UserManager::getUserInfoByUserName($username);
				if (!$user_info['active'])
					echo '<br>' . $username . '<br><br><font color="red">Your account is not active</font>';
			} else {
				echo '<br><font color="red">Incorrect username or/and password</font>';
			}
		} else {
			echo '<br><font color="red">Incorrect username or/and password</font>';
		}
		exit();
	}
}
