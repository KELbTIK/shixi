<?php


class SJB_Admin_Users_DeleteUserGroup extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_user_groups'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$user_group_sid = SJB_Request::getVar('sid', null);
		if (!is_null($user_group_sid)) {
			SJB_UserGroupManager::deleteUserGroupBySID($user_group_sid);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/user-groups/");
		} else {
			echo 'The system  cannot proceed as User Group SID is not set';
		}
	}
}
