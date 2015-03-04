<?php

class SJB_Users_UserBanned extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('adminEmail', SJB_System::getSettingByName('notification_email'));
		$tp->display('user_banned.tpl');
	}
}
