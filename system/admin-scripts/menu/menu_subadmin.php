<?php

class SJB_Admin_Menu_ShowSubadminMenu extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		if (SJB_SubAdmin::getSubAdminSID()) {
			$tp = SJB_System::getTemplateProcessor();
			$tp->assign('subadmin', SJB_SubAdmin::getSubAdminInfo());
			$tp->display('subadmin_left_menu.tpl');
		}
	}
}







