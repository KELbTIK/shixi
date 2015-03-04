<?php

class SJB_Admin_Users_EditUserProfile extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_user_groups_profile_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user_group_sid = SJB_Request::getVar('user_group_sid', null);
		$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
		$errors = null;

		if (!is_null($user_group_sid)) {
			if (isset($_REQUEST['action'], $_REQUEST['field_sid'])) {
				if ($_REQUEST['action'] == 'move_up') {
					SJB_UserProfileFieldManager::moveUpFieldBySID($_REQUEST['field_sid']);
				} elseif ($_REQUEST['action'] == 'move_down') {
					SJB_UserProfileFieldManager::moveDownFieldBySID($_REQUEST['field_sid']);
				}
			}
			$user_profile_fields = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group_sid);
		} else {
			$errors['USER_GROUP_SID_NOT_SET'] = 1;
			$user_profile_fields = null;
		}

		$tp->assign("errors", $errors);
		$tp->assign("user_profile_fields", $user_profile_fields);
		$tp->assign("user_group_sid", $user_group_sid);
		$tp->assign("user_group_info", $user_group_info);
		$tp->display("edit_user_profile_fields.tpl");
	}
}
