<?php

class SJB_Admin_Users_AddUserGroup extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_user_groups'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$user_group = new SJB_UserGroup($_REQUEST);
		$add_user_group_form = new SJB_Form($user_group);
		$form_is_submitted = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add');
		$errors = null;
		$tp = SJB_System::getTemplateProcessor();

		if ($form_is_submitted && $add_user_group_form->isDataValid($errors)) {
			SJB_UserGroupManager::saveUserGroup($user_group);
			$page = array(
				'uri' => '/'.mb_strtolower($user_group->getPropertyValue('id'), 'UTF-8').'-products/',
				'module' => 'payment',
				'function' => 'user_products',
				'access_type' => 'user',
				'parameters' => 'userGroupID='.$user_group->getID(),
			);
			$userPage = new SJB_UserPage();
			$page_data = SJB_UserPage::extractPageData($page);
			$userPage->setPageData($page_data);
			$userPage->save();
			$this->addLocationField($user_group->getSID());
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/user-groups/");
		} else {
			$add_user_group_form->registerTags($tp);
			$tp->assign('notifications', $user_group->getNotifications());
			$tp->assign('notificationGroups', $user_group->getNotificationsGroups());
			$tp->assign('form_fields', $add_user_group_form->getFormFieldsInfo());
			$tp->assign('errors', $errors);
			$tp->display('add_user_group.tpl');
		}
	}

	private function addLocationField($userGroupSid)
	{
		$locationFieldDetails = array(
			'id'          => 'Location',
			'caption'     => 'Location',
			'type'        => 'location',
			'is_required' => '0',
		);
		$locationField = new SJB_UserProfileField($locationFieldDetails);
		$locationField->setUserGroupSID($userGroupSid);
		SJB_UserProfileFieldManager::saveUserProfileField($locationField);
	}
}
