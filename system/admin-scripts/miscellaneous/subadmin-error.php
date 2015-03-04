<?php

class SJB_Admin_Miscellaneous_FunctionIsNotAccessibleForSubadmin extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$type = 'subadmin';
		$role = SJB_SubAdmin::getSubAdminSID();

		// get new defined permissions for notification letter
		$acl = SJB_SubAdminAcl::getInstance();
		$permissions = SJB_SubAdminAcl::getAllPermissions($type, $role);
		$resources = $acl->getResources();
		SJB_SubAdminAcl::mergePermissionsWithResources($resources, $permissions);

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('permissions', $resources);
		$tp->assign('admin_email', SJB_Settings::getSettingByName('notification_email'));

		$tp->display('../miscellaneous/subadmin-error.tpl');

	}
}

