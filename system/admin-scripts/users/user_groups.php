<?php


class SJB_Admin_Users_UserGroups extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_user_groups', 'manage_user_groups_permissions'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$user_groups_structure = SJB_UserGroupManager::createTemplateStructureForUserGroups();
		$template_processor->assign("user_groups", $user_groups_structure);
		$template_processor->display("user_groups.tpl");
	}
}