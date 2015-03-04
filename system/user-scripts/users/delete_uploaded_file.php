<?php

class SJB_Users_DeleteUploadedFile extends SJB_Function
{
	public function execute()
	{
		$user_info = SJB_UserManager::getCurrentUserInfo();

		$field_id = isset($_REQUEST['field_id']) ? $_REQUEST['field_id'] : null;

		if (is_null($field_id)) {

			$errors['PARAMETERS_MISSED'] = 1;

		} elseif (!isset($user_info[$field_id])) {

			$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;

		} else {

			$uploaded_file_id = $user_info[$field_id];

			SJB_UploadFileManager::deleteUploadedFileByID($uploaded_file_id);

			$user_info[$field_id] = "";
			$user_info['email'] = array('original' => $user_info['email']);
			$user = new SJB_User($user_info, $user_info['user_group_sid']);

			$user->deleteProperty("active");
			$user->deleteProperty('password');
			$user->setSID(SJB_UserManager::getCurrentUserSID());

			SJB_UserManager::saveUser($user);

		}


		$template_processor = SJB_System::getTemplateProcessor();

		$template_processor->assign("errors", isset($errors) ? $errors : null);

		$template_processor->display("delete_uploaded_file.tpl");

	}
}
