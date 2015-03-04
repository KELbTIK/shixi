<?php

class SJB_Users_UserNotifications extends SJB_Function
{
	public function isAccessible()
	{
		$cu = SJB_UserManager::getCurrentUser();
		if (!empty($cu) && $cu->isSubuser()) {
			return false;
		}
		else {
			return parent::isAccessible();
		}
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user = SJB_UserManager::getCurrentUser();

		if ($user) {
			$userNotificationsManager = new SJB_UserNotificationsManager($user);

			$userNotificationsInfo = $userNotificationsManager->getUserNotificationsInfo();
			$userNotificationsInfo = array_merge($userNotificationsInfo, $_REQUEST);
			$userNotifications = new SJB_UserNotifications($userNotificationsInfo);

			$userNotificationsForm = new SJB_Form($userNotifications);
			$userNotificationsForm->registerTags($tp);

			$userNotificationsFields = $userNotificationsForm->getFormFieldsInfo();
			$tp->assign('form_fields', $userNotificationsFields);

			if (SJB_Request::getVar('action') === 'save') {
				$errors = array();
				if ($userNotificationsForm->isDataValid($errors)) {
					$userNotifications->update();
					$tp->assign('isSaved', true);
				}
				$tp->assign('errors', $errors);
			}

			$tp->assign('userNotificationGroups', $userNotificationsManager->getNotificationGroups()->getGroups());
			$tp->assign('userNotifications', $userNotificationsManager->getEnabledForGroupUserNotifications());

			$listingTypes = SJB_ListingTypeManager::getListingTypeByUserSID($user->getSID());
			$approveSetting = SJB_ListingTypeManager::getWaitApproveSettingByListingType($listingTypes);
			$tp->assign('approve_setting', $approveSetting);

			$tp->display('user_notifications.tpl');
		}
		else {
			$tp->display('login.tpl');
		}
	}
}
