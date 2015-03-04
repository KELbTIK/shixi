<?php

class SJB_Social_RegistrationSocial extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('terms_of_use_check', SJB_System::getSettingByName('terms_of_use_check'));
		$user_group_id = SJB_Request::getVar('user_group_id', null);
		$form_submitted = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'register');

		if (!is_null($user_group_id)) {
			$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);

			/**
			 * check if registration is allowed for this UserGroup
			 */
			if (!SJB_SocialPlugin::ifRegistrationIsAllowedByUserGroupSID($user_group_sid)) {
				return null;
			}

			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);

			$user = SJB_ObjectMother::createUser($_REQUEST, $user_group_sid);
			$user->deleteProperty('active');
			$user->deleteProperty('featured');

			$errors = array();

			// social plugin
			if ($form_submitted) {
				SJB_Event::dispatch('SocialPlugin_AddListingFieldsIntoRegistration', $user, true);
				SJB_Event::dispatch('MakeRegistrationFieldsNotRequired_SocialPlugin', $user, true);
			}
			else {
				SJB_Event::dispatch('PrepareRegistrationFields_SocialPlugin', $user, true);
				SJB_Event::dispatch('SocialPlugin_AddListingFieldsIntoRegistration', $user, true);
				SJB_Event::dispatch('FillRegistrationData_Plugin', $user, true);
			}

			$registration_form = SJB_ObjectMother::createForm($user);
			$registration_form->registerTags($tp);

			if ($form_submitted && $registration_form->isDataValid($errors)) {
				SJB_Event::dispatch('FillRegistrationData_Plugin', $user, true);
				SJB_Event::dispatch('AddReferencePluginDetails', $user, true);

				$user->deleteProperty('captcha');
				$user->deleteProperty('active');
				$user->deleteProperty('featured');

				SJB_UserManager::saveUser($user);
				SJB_Statistics::addStatistics('addUser', $user->getUserGroupSID(), $user->getSID(), false, 0, 0, false, 0, SJB_SocialPlugin::getNetwork());
				SJB_Statistics::addStatistics('addUser' . SJB_SocialPlugin::getNetwork(), $user->getUserGroupSID(), $user->getSID(), false, 0, 0, false, 0, SJB_SocialPlugin::getNetwork());

				// subscribe user on default product
				$defaultProduct = SJB_UserGroupManager::getDefaultProduct($user_group_sid);
				$availableProductIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($user_group_sid);

				if ($defaultProduct && in_array($defaultProduct, $availableProductIDs)) {
					$contract = new SJB_Contract(array('product_sid' => $defaultProduct));
					$contract->setUserSID($user->getSID());
					$contract->saveInDB();
				}

				SJB_SocialPlugin::sendUserSocialRegistrationLetter($user);

				// notify administrator
				SJB_AdminNotifications::sendAdminUserRegistrationLetter($user);

				// Activation
				$isSendActivationEmail = SJB_UserGroupManager::isSendActivationEmail($user_group_sid);
				$isApproveByAdmin = SJB_UserGroupManager::isApproveByAdmin($user_group_sid);
                if($isApproveByAdmin)
                    SJB_UserManager::setApprovalStatusByUserName($user->getUserName(), 'Pending');
				if ($isSendActivationEmail) {
					$isSent = SJB_Notifications::sendUserActivationLetter($user->getSID());
					if ($isSent) {
						$tp->display('../users/registration_confirm.tpl');
					} else {
						$tp->display('../users/registration_failed_to_send_activation_email.tpl');
					}
				}
				else if ((!$isSendActivationEmail) && $isApproveByAdmin) {
					SJB_UserManager::setApprovalStatusByUserName($user->getUserName(), 'Pending');
					$tp->display('../users/registration_pending.tpl');
				}
				else {
					SJB_UserManager::activateUserByUserName($user->getUserName());

					$errors = array();
					SJB_Authorization::login($user->getUserName(), $user->getPropertyValue('password'), false, $errors, false);

					// save access token, profile info for synchronization
					SJB_SocialPlugin::postRegistration();

					$tp->assign('socialNetwork', SJB_SocialPlugin::getNetwork());
					$pageId = !empty($user_group_info['after_registration_redirect_to']) ? $user_group_info['after_registration_redirect_to'] : '';
					$redirectUrl = SJB_UserGroupManager::getRedirectUrlByPageID($pageId);
					SJB_HelperFunctions::redirect($redirectUrl);
				}
			}
			else
			{
				// social plugin
				SJB_Event::dispatch('PrepareRegistrationFields_SocialPlugin', $user, true);

				if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid))
					$user->deleteProperty('username');
				$registration_form = SJB_ObjectMother::createForm($user);
				if ($form_submitted)
					$registration_form->isDataValid($errors);
				$registration_form->registerTags($tp);
				$registration_form_template = '../users/registration_form.tpl';

				if (isset($_REQUEST['reg_form_template'])) {
					$registration_form_template = $_REQUEST['reg_form_template'];
				}
				elseif (!empty($user_group_info['reg_form_template'])) {
					$registration_form_template = $user_group_info['reg_form_template'];
				}

				$form_fields = $registration_form->getFormFieldsInfo();
				$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);

				$tp->assign('user_group_info', $user_group_info);
				$tp->assign('errors', $errors);
				$tp->assign('user_group_id', $user_group_id);
				$tp->assign('form_fields', $form_fields);

				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$tp->assign('METADATA', array(
						'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
					)
				);
				$tp->assign('socialRegistration', true);
				$tp->assign('userTree', true);
				$tp->display($registration_form_template);
			}
		}
		else {
			$userGroupsSIDs = SJB_SocialPlugin::getResolvedUserGroupsByNetwork();
			$user_groups_info = array();

			foreach ($userGroupsSIDs as $groupSID) {
				array_push($user_groups_info, SJB_UserGroupManager::getUserGroupInfoBySID($groupSID));
			}

			/*
			 * if there is only one group available for registration
			 * redirect user directly on Registration Fields page
			 */
			if (count($user_groups_info) === 1 && !empty($user_groups_info[0]['id'])) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/registration-social/?user_group_id=' . $user_groups_info[0]['id']);
			}

			$tp->assign('user_groups_info', $user_groups_info);
			$tp->display('registration_choose_user_group_social.tpl');
		}

	}
}
