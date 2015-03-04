<?php
class SJB_Admin_Users_Logout extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		SJB_Admin::admin_log_out();

		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL"));
	}
}

