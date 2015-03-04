<?php

class SJB_Admin_Main_AdminLogin extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == 'admin') {
			if (!SJB_SubAdmin::admin_authed() && !SJB_Admin::admin_authed()) {
				if (SJB_Admin::NeedShowSplashScreen()) {
					SJB_Admin::ShowSplashScreen();
					exit;
				}
				if (!SJB_Admin::admin_auth())
					exit;
			}
		}
	}
}