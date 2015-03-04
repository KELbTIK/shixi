<?php


class SJB_Admin_Users_DeleteUserProfileField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_user_groups_profile_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$user_profile_field_sid = SJB_Request::getVar('sid', null);
		if (!is_null($user_profile_field_sid)) {
			$user_profile_field_info = SJB_UserProfileFieldManager::getFieldInfoBySID($user_profile_field_sid);
			SJB_UserProfileFieldManager::deleteUserProfileFieldBySID($user_profile_field_sid);
			$user_group_sid = $user_profile_field_info['user_group_sid'];
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-user-profile/?user_group_sid=' . $user_group_sid);
		} else {
			echo 'The system  cannot proceed as User Group SID is not set';
		}
	}
}
