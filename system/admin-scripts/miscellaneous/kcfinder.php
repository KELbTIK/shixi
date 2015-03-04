<?php

class SJB_Admin_Miscellaneous_Kcfinder extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$_SESSION['KCFINDER'] = array();
		$_SESSION['KCFINDER']['disabled'] = false;
		$_SESSION['KCFINDER']['uploadURL'] = SJB_System::getSystemSettings('USER_SITE_URL') . '/files/userfiles';
		$_SESSION['KCFINDER']['uploadDir'] = SJB_BASE_DIR . 'files/userfiles';
		error_reporting(E_ERROR);
		chdir(SJB_BASE_DIR . 'system/ext/kcfinder');
		if (strpos($_SERVER['REQUEST_URI'], 'upload.php') !== false)
			include 'kcfinder/upload.php';
		else
			include 'kcfinder/browse.php';
		exit();
	}
}