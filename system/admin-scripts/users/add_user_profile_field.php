<?php


class SJB_Admin_Users_AddUserProfileField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_user_groups_profile_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$user_group_sid = isset($_REQUEST['user_group_sid']) ? $_REQUEST['user_group_sid'] : null;
		$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
		$user_profile_field = new SJB_UserProfileField($_REQUEST);

		$user_profile_field->setUserGroupSID($user_group_sid);
		//infill instructions field
		//$user_profile_field->addInfillInstructions(SJB_Request::getVar('instructions'));
		$add_user_profile_field_form = new SJB_Form($user_profile_field);
		$form_is_submitted = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add');
		$errors = null;

		if ($form_is_submitted && $add_user_profile_field_form->isDataValid($errors)) {

			SJB_UserProfileFieldManager::saveUserProfileField($user_profile_field);
			if (SJB_Request::getVar('type', '') == 'youtube') {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/instruction_user_profile_field/?user_group_sid=" . $user_group_sid . "&user_field_sid=". $user_profile_field->sid);
			} else {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/edit-user-profile/?user_group_sid=" . $user_group_sid);
			}
		}
		else {

			$template_processor = SJB_System::getTemplateProcessor();
			$add_user_profile_field_form->registerTags($template_processor);

			$template_processor->assign("form_fields", $add_user_profile_field_form->getFormFieldsInfo());
			$template_processor->assign("user_group_sid", $user_group_sid);
			$template_processor->assign("errors", $errors);
			$template_processor->assign("user_group_info", $user_group_info);

			$template_processor->display("add_user_profile_field.tpl");
		}
	}
}
