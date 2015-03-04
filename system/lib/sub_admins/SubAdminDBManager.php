<?php

class SJB_SubAdminDBManager extends SJB_ObjectDBManager
{
	/**
	 * Save subadmin to database
	 * @param SJB_SubAdmin $user
	 * @return boolean
	 */
	public static function saveSubAdmin($user)
	{
		return parent::saveObject("subadmins", $user);
		
	}

	public static function getAllSubAdminsInfo()
	{
		return parent::getObjectsInfoByType("subadmins");
	}

	public static function deleteSubAdminBySubAdminName($username)
	{
		$user_sid = SJB_DB::queryValue("SELECT sid FROM subadmins WHERE username = ?s", $username);
		return parent::deleteObjectInfoFromDB('subadmins', $user_sid);
	}

	public static function deleteSubAdminById($id)
	{
		return parent::deleteObjectInfoFromDB('subadmins', $id);
	}

	public static function deleteEmptySubAdmins()
	{
		SJB_DB::query("DELETE FROM `subadmins` WHERE `username` = ?s OR `username` IS NULL", "");
	}

	public static function getSubAdminInfoByUserName($username)
	{
		$user_sid = SJB_DB::queryValue("SELECT sid FROM `subadmins` WHERE username = ?s", $username);
		if (empty($user_sid)) {
			return null;
		}
		return parent::getObjectInfo("subadmins", $user_sid);
	}
	
	public static function getSubAdminInfoByEmail($email)
	{
		$user_sid = SJB_DB::queryValue("SELECT sid FROM `subadmins` WHERE email = ?s", $email);
		if (empty($user_sid)) {
			return null;
		}
		return parent::getObjectInfo("subadmins", $user_sid);
	}
	
	public static function getSubAdminInfoBySID($user_sid)
	{
		return parent::getObjectInfo("subadmins", $user_sid);
	}

	public static function getUsernameBySubAdminSID($user_sid)
	{
		return SJB_DB::queryValue("SELECT username FROM `subadmins` WHERE sid = ?n", $user_sid);
	}

	public static function getSubAdminSIDsLikeSubAdminname($username)
	{
		if (empty($username))
			return null;
		
		$subadmins_info = SJB_DB::query("SELECT `sid` FROM `subadmins` WHERE `username` LIKE '%?w%'", $username);
		if (!empty($subadmins_info)) {
			foreach ($subadmins_info as $user_info)
				$subadmins_sids[$user_info['sid']] = $user_info['sid'];
			return $subadmins_sids;
		}
		return null;
	}
	
}

