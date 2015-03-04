<?php

class SJB_UserDBManager extends SJB_ObjectDBManager
{
	/**
	 * @param SJB_User $user
	 * @return array|bool|int
	 */
	public static function saveUser($user)
	{
		$user_group_sid = $user->getuserGroupSID();
		$user_exists = !is_null($user->getSID());
				
		if (!is_null($user_group_sid)) {
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
			$userName = $user->details->getProperty('username')->getValue();
			if ( isset($user_group_info['user_email_as_username']) && (($user_group_info['user_email_as_username']) == true) && strpos($userName, 'jobg8') === false)
			{
				$useremail = $user->details->getProperty('email')->getValue();
				if ( !is_array($useremail) || (!array_key_exists('original', $useremail)) )
					$user->details->getProperty('username')->setValue( $useremail );
				else
					$user->details->getProperty('username')->setValue( $useremail['original'] );
			}
			
			parent::saveObject("users", $user);

			if (!$user_exists) {
				SJB_DB::query("UPDATE ?w
						   SET `registration_date` = NOW(), `activation_key` = ?s, `verification_key` = ?s
						   WHERE `sid` = ?n",
						   "users", $user->getActivationKey(), $user->getVerificationKey(), $user->getSID());
			}
			
			return SJB_DB::query("UPDATE ?w SET `user_group_sid` = ?n WHERE `sid` = ?n", "users", $user_group_sid, $user->getSID());
		}
		
		return false;
	}

	public static function getAllUsersInfo()
	{
		return parent::getObjectsInfoByType("users");
	}

	public static function deleteUserById($id)
	{
		SJB_DB::query('UPDATE `users` SET `parent_sid` = 0 WHERE `parent_sid` = ?n', $id);
		SJB_DB::query('UPDATE `listings` SET `subuser_sid` = 0 WHERE `subuser_sid` = ?n', $id);
		$user = SJB_UserManager::getObjectBySID($id);
		SJB_Statistics::addStatistics('deleteUser', $user->getUserGroupSID(), $user->getSID());
		return parent::deleteObjectInfoFromDB('users', $id);
	}

	public static function deleteEmptyUsers()
	{
		SJB_DB::query("DELETE FROM `users` WHERE `username` = ?s OR `username` IS NULL", "");
	}

	public static function activateUserByUserName($username)
	{
		return SJB_DB::query("UPDATE `users` SET `active` = 1 WHERE `username` = ?s", $username);
	}

	public static function deactivateUserByUserName($username)
	{
		SJB_DB::query("UPDATE `users` SET `active` = 0 WHERE `username` = ?s", $username);
	}
	
	public static function getUserInfoByUserName($username)
	{
		if (empty($username))
			return null;
		$user_sid = SJB_DB::queryValue("SELECT `sid` FROM `users` WHERE `username` = ?s", $username);
		if (empty($user_sid))
			return null;
		return parent::getObjectInfo("users", $user_sid);
	}
	
	public static function getUserInfoByExtUserID($extUserID, $listingTypeID)
	{
		if (empty($extUserID)) {
			return null;
		}
		$userInfo = SJB_DB::query("SELECT u.`sid`, ug.`id` as user_group FROM `users` u INNER JOIN `user_groups` ug ON ug.`sid` = u.`user_group_sid` WHERE u.`extUserID` = ?s", $extUserID);
		foreach ($userInfo as $key => $user) {
			unset($userInfo[$key]);
			$userInfo[$user['user_group']] = $user['sid'];
		}
		$userSID = null;
		if (!$userInfo) {
			return null;
		}
		elseif (count($userInfo) > 1) {
			if ($listingTypeID == 'Job' && array_key_exists('Employer', $userInfo)) {
				$userSID = $userInfo['Employer'];
			}
			elseif ($listingTypeID == 'Resume' && array_key_exists('JobSeeker', $userInfo)) {
				$userSID = $userInfo['JobSeeker'];
			}
			else {
				$userSID = array_pop($userInfo);
			}
		}
		else {
			$userSID = array_pop($userInfo);
		}
		
		return parent::getObjectInfo("users", $userSID);
	}

	public static function getUserInfoByUserEmail($email)
	{
		$user_sid = SJB_DB::queryValue("SELECT `sid` FROM `users` WHERE `email` = ?s", $email);
		if (empty($user_sid))
			return null;
		return parent::getObjectInfo("users", $user_sid);
	}
	
	public static function getUserInfoBySID($user_sid)
	{
		return parent::getObjectInfo("users", $user_sid);
	}

	public static function getUserNameByUserSID($user_sid)
	{
		return SJB_DB::queryValue("SELECT `username` FROM `users` WHERE `sid` = ?n", $user_sid);
	}

	public static function getExtUserIDByUserSID($user_sid)
	{
		return SJB_DB::queryValue("SELECT `extUserID` FROM `users` WHERE `sid` = ?n", $user_sid);
	}
	
	public static function getUserSIDsLikeUsername($username)
	{
		if (empty($username))
			return null;
		
		$users_info = SJB_DB::query("SELECT `sid` FROM `users` WHERE `username` LIKE '%?w%'", $username);
		if (!empty($users_info)) {
			foreach ($users_info as $user_info)
				$users_sids[$user_info['sid']] = $user_info['sid'];
			return $users_sids;
		}
		return null;
	}

	public static function getUserSIDsLikeCompanyName($companyName)
	{
		if (empty($companyName)) {
			return null;
		}

		$usersInfo = SJB_DB::query("SELECT `sid` FROM `users` WHERE `CompanyName` LIKE '%?w%'", SJB_DB::quote($companyName));
		if (!empty($usersInfo)) {
			foreach ($usersInfo as $userInfo) {
				$usersSids[$userInfo['sid']] = $userInfo['sid'];
			}
			return $usersSids;
		}
		return null;
	}

	public static function getUserSidsLikeFirstNameOrLastName($name)
	{
		if (empty($name)) {
			return null;
		}

		$users_info = SJB_DB::query("SELECT `sid` FROM `users` WHERE `FirstName` LIKE '%?w%' OR `LastName` LIKE '%?w%'", $name, $name);
		if (!empty($users_info)) {
			foreach ($users_info as $user_info) {
				$users_sids[$user_info['sid']] = $user_info['sid'];
			}
			return $users_sids;
		}
		return null;
	}

	public static function getUserSIDsLikeSearchString($search_string)
	{
		if (empty($search_string))
			return null;
		$users_info = SJB_DB::query("SELECT `sid` FROM `users` WHERE `FirstName` LIKE '%?w%' OR `LastName` LIKE '%?w%' OR `ContactName` LIKE '%?w%'
									OR `CompanyName` LIKE '%?w%' OR `email` LIKE '%?w%'", $search_string, $search_string, $search_string, $search_string, $search_string);
		if (!empty($users_info)) {
			foreach ($users_info as $user_info)
				$users_sids[$user_info['sid']] = $user_info['sid'];
			return $users_sids;
		}
		return null;
	}

	public static function deleteActivationKeyByUsername($username)
	{
		return SJB_DB::query("UPDATE `users` SET `activation_key`='' WHERE `username` = ?s ", $username);
	}
}

