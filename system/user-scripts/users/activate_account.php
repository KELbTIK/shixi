<?php

class SJB_Users_ActivateAccount extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();

		$activated = SJB_Request::getVar('account_activated', '') == 'yes';
		if (SJB_Request::getVar('returnToShoppingCart', false)) {
			SJB_Session::setValue('fromAnonymousShoppingCart', 1);
		}

		if (!$activated) {
			if (!isset($_REQUEST['username'], $_REQUEST['activation_key'])) {
				$errors['PARAMETERS_MISSED'] = 1;
			} elseif (!$userInfo = SJB_UserManager::getUserInfoByUserName($_REQUEST['username'])) {
				$errors['USER_NOT_FOUND'] = 1;
			} elseif ($userInfo['activation_key'] != $_REQUEST['activation_key']) {
				$errors['INVALID_ACTIVATION_KEY'] = true;
			} elseif ($userInfo['approval'] == 'Rejected') {
				SJB_UserDBManager::deleteActivationKeyByUsername($_REQUEST['username']);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/system/users/activate_account/?account_activated=no&approval_status=Rejected');
			} else {
				if (SJB_UserManager::activateUserByUserName($_REQUEST['username'])) {
					SJB_UserDBManager::deleteActivationKeyByUsername($_REQUEST['username']);
					if (!SJB_Authorization::isUserLoggedIn()) {
						SJB_Authorization::login($_REQUEST['username'], false, false, $errors, true, true);
						if (!SJB_SocialPlugin::getProfileSocialID($userInfo['sid'])) {
							SJB_Notifications::sendUserWelcomeLetter($userInfo['sid']);
						}
						$requireApprove = SJB_UserGroupManager::isApproveByAdmin($userInfo['user_group_sid']);
						if ($requireApprove && !empty($userInfo['approval'])) {
							$approvalStatus = $userInfo['approval'];
						} else {
							$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']);
							$pageId = !empty($userGroupInfo['after_registration_redirect_to']) ? $userGroupInfo['after_registration_redirect_to'] : '';
							$redirectUrl = SJB_UserGroupManager::getRedirectUrlByPageID($pageId);
							SJB_HelperFunctions::redirect($redirectUrl . 'account_activated=yes');
						}
					}
					$activated = 1;
				}
				else {
					$errors['CANNOT_ACTIVATE'] = TRUE;
				}
			}
		}

		$tp->assign('activated', $activated);
		$tp->assign('errors', $errors);
		$tp->assign('approvalStatus', !empty($approvalStatus) ? $approvalStatus : SJB_Request::getVar('approval_status', ''));
		$tp->assign('isLoggedIn', SJB_Authorization::isUserLoggedIn());
		$tp->display('activate_account.tpl');
	}
}