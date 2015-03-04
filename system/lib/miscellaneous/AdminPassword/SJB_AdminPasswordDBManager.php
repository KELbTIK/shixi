<?php

class SJB_AdminPasswordDBManager extends SJB_ObjectDBManager
{

	public static function saveAdminDetails($admin)
	{
		parent::saveObject('administrator', $admin);
	}

	public static function getAdminDetailsByUsername($username)
	{
		if (!empty($username)) {
			return SJB_DB::query("SELECT * FROM `administrator` WHERE `username` = ?s", $username);
		} else {
			return false;
		}
	}

}
