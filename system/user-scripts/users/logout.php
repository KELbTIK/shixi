<?php

class SJB_Users_Logout extends SJB_Function
{
	public function execute()
	{
		SJB_Authorization::logout();
		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL'));
	}
}
