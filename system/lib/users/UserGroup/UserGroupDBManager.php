<?php

class SJB_UserGroupDBManager extends SJB_ObjectDBManager
{
	public static function deleteUserGroupInfo($user_group_sid)
	{
		return parent::deleteObjectInfoFromDB('user_groups', $user_group_sid);
	}
	
	public static function getAllUserGroupsInfo()
	{
		return parent::getObjectsInfoByType("user_groups");
	}
	
	public static function getUserGroupInfoBySID($user_group_sid)
	{
		return parent::getObjectInfo("user_groups", $user_group_sid);
	}

	public static function saveUserGroup($user_group)
	{
		parent::saveObject("user_groups", $user_group);
	}
	
	public static function getUserGroupSIDByID($user_group_id)
	{
		return SJB_DB::queryValue("SELECT sid FROM user_groups WHERE id = ?s", $user_group_id);
	}
	
	public static function getUserGroupIDBySID($user_group_sid)
	{
		return SJB_DB::queryValue("SELECT id FROM user_groups WHERE sid = ?s", $user_group_sid);
	}
	
	public static function getUserGroupIDByUserSID($userSid)
	{
		return SJB_DB::queryValue("SELECT ug.`id` FROM `user_groups` ug, `users` u WHERE u.`sid` = ?n AND ug.`sid` = u.`user_group_sid` LIMIT 1", $userSid);
	}

	public static function getUserGroupIDByUserName($userName)
	{
		return SJB_DB::queryValue("SELECT ug.`id` FROM `user_groups` ug, `users` u WHERE u.`username` = ?s AND ug.`sid` = u.`user_group_sid` LIMIT 1", $userName);
	}

	public static function getUserGroupNameBySID($user_group_sid)
	{
		$user_group_info = parent::getObjectInfo("user_groups", $user_group_sid);
		return $user_group_info['name'];
	}

	public static function getUserGroupSIDByName($user_group_name)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM `user_groups` WHERE `name`=?s", $user_group_name);
	}
}
