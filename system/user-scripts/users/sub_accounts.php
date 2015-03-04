<?php

class SJB_Users_SubAccounts extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('create_sub_accounts');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$template = 'sub_accounts.tpl';
		$currentUserInfo = SJB_UserManager::getCurrentUserInfo();
		$listSubusers = false;

		if (!empty($currentUserInfo['subuser']) && SJB_Request::getVar('action_name') != 'edit' && SJB_Request::getVar('user_id', 0) != $currentUserInfo['subuser']['sid']) {
			$errors['ACCESS_DENIED'] = 'ACCESS_DENIED';
		}

		switch (SJB_Request::getVar('action_name')) {
			case 'new':
				$form_submitted = SJB_Request::getMethod() === SJB_Request::METHOD_POST;

				$user_group_sid = $currentUserInfo['user_group_sid'];
				$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
				$_REQUEST['user_group_id'] = $user_group_info['id'];

				$user = SJB_ObjectMother::createUser($_REQUEST, $user_group_sid);
				$props = $user->getProperties();
				$allowedProperties = array('username', 'email', 'password');
				foreach ($props as $prop) {
					if (!in_array($prop->getID(), $allowedProperties))
						$user->deleteProperty($prop->getID());
				}

				$registration_form = SJB_ObjectMother::createForm($user);
				$registration_form->registerTags($tp);
				if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid) && $form_submitted) {
					$email = $user->getPropertyValue('email');
					if (is_array($email))
						$email = $email['original'];
					$user->setPropertyValue('username', $email);
				}

				$registration_form = SJB_ObjectMother::createForm($user);
				if ($form_submitted && $registration_form->isDataValid($errors)) {
					$user->addParentProperty($currentUserInfo['sid']);
					$subuserPermissions = array(
						'subuser_add_listings' => array ('title' => 'Add new listings', 'value' => 'deny'),
						'subuser_manage_listings' => array ('title' => 'Manage listings and applications of other sub users', 'value' => 'deny'),
						'subuser_manage_subscription' => array ('title' => 'View and update subscription', 'value' => 'deny'),
						'subuser_use_screening_questionnaires' => array ('title' => 'Manage Questionnaries', 'value' => 'deny'),
					);

					SJB_UserManager::saveUser($user);
					SJB_Statistics::addStatistics('addSubAccount', $user->getUserGroupSID(), $user->getSID());
					SJB_Acl::clearPermissions('user', $user->getSID());
					foreach ($subuserPermissions as $permissionID => $permission) {
						$allowDeny = SJB_Request::getVar($permissionID, 'deny');
						$subuserPermissions[$permissionID]['value'] = $allowDeny;
						SJB_Acl::allow($permissionID, 'user', $user->getSID(), $allowDeny);
					}
					SJB_UserManager::activateUserByUserName($user->getUserName());
					SJB_Notifications::sendSubuserRegistrationLetter($user, SJB_Request::get(), $subuserPermissions);
					$tp->assign('isSubuserRegistered', true);
					$listSubusers = true;
				}
				else {
					if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid))
						$user->deleteProperty("username");
					$registration_form = SJB_ObjectMother::createForm($user);
					if ($form_submitted)
						$registration_form->isDataValid($errors);

					$registration_form->registerTags($tp);

					$form_fields = $registration_form->getFormFieldsInfo();
					$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);

					$tp->assign("user_group_info", $user_group_info);
					$tp->assign("errors", $errors);
					$tp->assign("form_fields", $form_fields);

					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign
					(
						"METADATA",
						array
						(
							"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields),
						)
					);

					$tp->display('subuser_registration_form.tpl');
				}
				break;

			case 'edit':
				$userInfo = SJB_UserManager::getUserInfoBySID(SJB_Request::getVar('user_id', 0));
				if (!empty($userInfo) && $userInfo['parent_sid'] === $currentUserInfo['sid']) {
					$userInfo = array_merge($userInfo, $_REQUEST);
					$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($currentUserInfo['user_group_sid']);

					$user = new SJB_User($userInfo, $userInfo['user_group_sid']);
					$user->setSID($userInfo['sid']);
					$user->addParentProperty($currentUserInfo['sid']);

					$props = $user->getProperties();
					$allowedProperties = array('username', 'email', 'password');
					foreach ($props as $prop) {
						if (!in_array($prop->getID(), $allowedProperties))
							$user->deleteProperty($prop->getID());
					}

					$user->makePropertyNotRequired("password");

					$edit_profile_form = SJB_ObjectMother::createForm($user);
					$edit_profile_form->registerTags($tp);
					$edit_profile_form->makeDisabled("username");

					$form_submitted = SJB_Request::getMethod() == SJB_Request::METHOD_POST;

					if (empty($errors) && $form_submitted && $edit_profile_form->isDataValid($errors)) {
						$password_value = $user->getPropertyValue('password');

						if (empty($password_value['original'])) {
							$user->deleteProperty('password');
						}
						$currentUser = SJB_UserManager::getCurrentUser();
						if (!$currentUser->isSubuser()) {
							$subuserPermissions = array('subuser_add_listings', 'subuser_manage_listings', 'subuser_manage_subscription', 'subuser_use_screening_questionnaires');
							SJB_Acl::clearPermissions('user', $user->getSID());
							foreach ($subuserPermissions as $permission) {
								SJB_Acl::allow($permission, 'user', $user->getSID(), SJB_Request::getVar($permission, 'deny'));
							}
						}

						SJB_UserManager::saveUser($user);

						$tp->assign("form_is_submitted", true);
					}
					else {
						$tp->assign("errors", $errors);
					}

					$form_fields = $edit_profile_form->getFormFieldsInfo();

					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign(
						"METADATA",
						array
						(
							"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields),
						)
					);

					$tp->assign("form_fields", $form_fields);
					$tp->assign('user_info', $userInfo);
					$tp->display('edit_subuser_profile.tpl');
				}
				break;

			case 'delete':
				$users = SJB_Request::getVar('user_id', array());
				foreach ($users as $user) {
					SJB_UserManager::deleteUserById($user);
				}
				$listSubusers = true;
				break;

			default:
				$listSubusers = true;
				break;
		}
		if ($listSubusers) {
			$tp->assign('errors', $errors);
			$tp->assign('subusers', SJB_UserManager::getSubusers($currentUserInfo['sid']));
			$tp->assign('isEmailAsUsername', SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($currentUserInfo['user_group_sid']));
			$tp->display($template);
		}
	}
}
