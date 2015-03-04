<?php

class SJB_SubAdminManager extends SJB_ObjectManager
{
	public static function getCurrentUserInfo()
	{
		if (SJB_Admin::admin_authed())
			return SJB_Authorization::getCurrentUserInfo();
		return null;
	}

	/**
	 *
	 * @param int $subAdminSID
	 * @param string $name
	 * @param string $value
	 */
	public static function SaveSubAdminNotifications($subAdminSID, $name, $value)
	{
		if (SJB_Request::isAjax() && $name && $value) {
			$perm = ( 'true' === $value ) ? 'allow' : 'deny';
			if ( SJB_SubAdminAcl::setSubAdminNotificationByPermName($subAdminSID, $name, $perm) )
				exit('saved');
			exit('failed');
		}
		return false;
	}	// 	public static function SaveSubAdminNotifications($subAdminSID, $name, $value)


	/**
	 * Get current user
	 *
	 * @return SJB_User|null
	 */
	public static function getCurrentSubAdmin()
	{
		$user_info = SJB_SubAdminManager::getCurrentUserInfo();
		$user = null;
		if (!is_null($user_info)) {
			$user = new SJB_User($user_info);
			$user->setSID($user_info['sid']);
		}
		return $user;
	}
	
	/**
	 * Gets user object by sid
	 *
	 * @param int $user_sid
	 * @return User
	 */
	public static function getObjectBySID($user_sid)
	{
		$user_info = SJB_SubAdminManager::getUserInfoBySID($user_sid);
		if (!is_null($user_info)) {
			$user = new SJB_User($user_info, $user_info['user_group_sid']);
			$user->setSID($user_info['sid']);
			return $user;
		}
		return null;
	}
	
	public static function getSubAdminInfoBySID($user_sid)
	{
		return SJB_SubAdminDBManager::getSubAdminInfoBySID($user_sid);
	}
	
	public static function isUserActiveBySID($user_sid)
	{
		return SJB_DB::queryValue("SELECT active FROM users WHERE sid = ?n", $user_sid);
	}
	
	public static function saveSubAdmin($user)
	{
		return SJB_SubAdminDBManager::saveSubAdmin($user);
	}
	
	public static function getAllSubAdminsInfo()
	{
		return SJB_SubAdminDBManager::getAllSubAdminsInfo();
	}
	
	public static function deleteSubAdminByUserName($username)
	{
		if ($username === "") {
			SJB_SubAdminDBManager::deleteEmptySubAdmins();
		}
		
        $user_info = SJB_SubAdminManager::getSubAdminInfoByUserName($username);

        if ($user_info !== null) {
	        self::deleteSubAdminById($user_info['sid']);
        }
	}
	
	public static function deleteSubAdminById($id)
	{
		SJB_SubAdminDBManager::deleteSubAdminById($id);
		SJB_SubAdminAcl::clearPermissions('subadmin', $id);
	}
	
	public static function getSubAdminInfoByUserName($username)
	{
		return SJB_SubAdminDBManager::getSubAdminInfoByUserName($username);
	}
	
	public static function getCurrentUserSID()
	{
		$user_info = SJB_SubAdminManager::getCurrentUserInfo();
		if (!is_null($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserNameBySubAdminSID($user_sid)
	{
		return SJB_SubAdminDBManager::getUsernameBySubAdminSID($user_sid);
	}

	public static function getUserSIDbyUsername($username)
	{
		$user_info = SJB_SubAdminManager::getUserInfoByUserName($username);
		if (!empty($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserSIDbyEmail($email)
	{
		$user_info = SJB_SubAdminDBManager::getSubAdminInfoByEmail($email);
		if (!empty($user_info))
			return $user_info['sid'];
		return null;
	}

	/**
	 *
	 * @param string $notification
	 * @return array | boolean
	 */
	public static function getIfSubAdminsNotifiedOn($notification)
	{
		$aSubAdmins = self::getAllSubAdminsInfo();
		if ( is_array($aSubAdmins)) {
			$aSubAdminEmail = array();
			$acl = SJB_SubAdminAcl::getInstance();
			foreach($aSubAdmins as $subAdmin) {
				if ($acl->isAllowed($notification, $subAdmin['sid'], true))
					$aSubAdminEmail[] = $subAdmin['email'];
			}
			return $aSubAdminEmail;
		}
		return false;
	}
	
}
