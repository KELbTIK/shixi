<?php

class SJB_Admin_Users_InstructionUserProfileField extends SJB_Function
{

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user_field_sid = SJB_Request::getVar('user_field_sid', null);
		$errors = array();
		if (!is_null($user_field_sid)) {
			$profile_field = SJB_UserProfileFieldManager::getFieldInfoBySID($user_field_sid);
			$profile_field['user_group'] = SJB_UserGroupManager::getUserGroupIDBySID($profile_field['user_group_sid']);
			$tp->assign('fieldInfo', $profile_field);
		} else {
			$errors[] = 'The system cannot proceed as some required parameters are not set';
		}
		$tp->assign('errors', $errors);
		$tp->display('instruction_user_profile_field.tpl');
	}
}
