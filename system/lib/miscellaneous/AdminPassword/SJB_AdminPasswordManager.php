<?php

class SJB_AdminPasswordManager extends SJB_ObjectManager
{

	public static function saveAdmin($admin)
	{
		SJB_AdminPasswordDBManager::saveAdminDetails($admin);
	}

	public static function getCurrentAdminDetails()
	{
		$username = !empty($_SESSION['username']) ? $_SESSION['username'] : '';
		$adminDetails = SJB_AdminPasswordDBManager::getAdminDetailsByUsername($username);
		if (!empty($adminDetails)) {
			return array_pop($adminDetails);
		}
	}

	public static function getNewPasswordValue($adminDetails)
	{
		if (!empty($adminDetails['new_password']['original'])) {
			return $adminDetails['new_password']['original'];
		}
		return false;
	}

}
