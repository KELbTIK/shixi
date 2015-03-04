<?php
class SJB_Admin_SubAdmins_ManageSubadmins extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign("subadmins", SJB_SubAdminManager::getAllSubAdminsInfo());
		$tp->display("manage_subadmins.tpl");
	}
}
