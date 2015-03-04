<?php

class SJB_Users_Registration extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$registration_form_template = 'registration_form.tpl';
		if (SJB_Authorization::isUserLoggedIn()) {
			$tp->display('already_logged_in.tpl');
			return;
		}

		$tp->assign('terms_of_use_check', SJB_System::getSettingByName('terms_of_use_check'));

		$user_group_id = SJB_Request::getVar('user_group_id', null);
		if (!is_null($user_group_id)) {
			$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);
			if (empty($user_group_sid)) {
				$errors['NO_SUCH_USER_GROUP_IN_THE_SYSTEM'] = 1;
			}
		}

		$this->setSessionValueForRedirectAfterRegister();
		if (!is_null($user_group_id) && empty($errors)) {
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);

			$user = SJB_ObjectMother::createUser($_REQUEST, $user_group_sid);

			if (SJB_Request::isAjax() || 'true' == SJB_Request::getVar('isajaxrequest')) {
				$field = SJB_Request::getVar('type');
				if ('email' == $field)
					$user->getProperty($field)->type->disableEmailConfirmation();
				echo $user->getProperty($field)->isValid();
				exit;
			}

			$user->deleteProperty('active');
			$user->deleteProperty('featured');

			$form_submitted = SJB_Request::getVar('action', false) == 'register';

			if (class_exists('MobilePlugin') && MobilePlugin::isMobileThemeOn()) {
				$user->prepareRegistrationFields();
			}
			$registration_form = SJB_ObjectMother::createForm($user);
			$registration_form->registerTags($tp);

			if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid) && $form_submitted) {
				$email = $user->getPropertyValue('email');
				if (is_array($email))
					$email = $email['original'];
				$user->setPropertyValue('username', $email);
			}

			if ($form_submitted && $registration_form->isDataValid($errors)) {
				$user->deleteProperty('captcha');
				$defaultProduct = SJB_UserGroupManager::getDefaultProduct($user_group_sid);
				SJB_UserManager::saveUser($user);
				SJB_Statistics::addStatistics('addUser', $user->getUserGroupSID(), $user->getSID());

				$availableProductIDs = SJB_ProductsManager::getProductsIDsByUserGroupSID($user_group_sid);
				if ($defaultProduct && in_array($defaultProduct, $availableProductIDs)) {
					$contract = new SJB_Contract(array('product_sid' => $defaultProduct));
					$contract->setUserSID($user->getSID());
					$contract->saveInDB();
				}


				// >>> SJB-1197
				// needs to check session for ajax-uploaded files, and set it to user profile
				$formToken         = SJB_Request::getVar('form_token');
				$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');

				if (!empty($formToken)) {
					$tmpUploadedFields = SJB_Array::getPath($tmpUploadsStorage, $formToken);

					if (!is_null($tmpUploadsStorage) && is_array($tmpUploadedFields)) {
						// prepare user profile fields array
						$userProfileFieldsInfo = SJB_UserProfileFieldManager::getAllFieldsInfo();
						$userProfileFields     = array();
						foreach ($userProfileFieldsInfo as $field) {
							$userProfileFields[$field['id']] = $field;
						}


						// look for temporary values
						foreach ($tmpUploadedFields as $fieldId => $fieldInfo) {
							// check field ID for valid ID in user profile fields
							if (!array_key_exists($fieldId, $userProfileFields) || empty($fieldInfo)) {
								continue;
							}

							$fieldType         = $userProfileFields[$fieldId]['type'];
							$profilePropertyId = $fieldId . '_' . $user->getSID();

							switch ( strtolower($fieldType)) {
								case 'video':
								case 'file':
									// change temporary file ID
									SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $profilePropertyId, $fieldInfo['file_id']);

									// set value of user property to new uploaded file
									$user->setPropertyValue($fieldId, $profilePropertyId);
									break;

								case 'logo':
									// change temporary file ID and thumb ID
									SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $profilePropertyId, $fieldInfo['file_id']);
									SJB_DB::query("UPDATE `uploaded_files` SET `id` = ?s WHERE `id` = ?s", $profilePropertyId . '_thumb', $fieldInfo['file_id'] . '_thumb');

									// set value of user property to new uploaded file
									$user->setPropertyValue($fieldId, $profilePropertyId);
									break;

								default:
									break;
							}
							$tmpUploadsStorage = SJB_Array::unsetValueByPath($tmpUploadsStorage, "{$formToken}/{$fieldId}");
						}

						// save user with new values
						SJB_UserManager::saveUser($user);

						// clean temporary storage
						$tmpUploadsStorage = SJB_Array::unsetValueByPath($tmpUploadsStorage, "{$formToken}");

						// CLEAR TEMPORARY SESSION STORAGE
						SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);
					}
				}
				// <<< SJB-1197


				// notifying administrator
				SJB_AdminNotifications::sendAdminUserRegistrationLetter($user);

				// Activation
				$isSendActivationEmail = SJB_UserGroupManager::isSendActivationEmail($user_group_sid);
				$isApproveByAdmin = SJB_UserGroupManager::isApproveByAdmin($user_group_sid);
                if($isApproveByAdmin)
                    SJB_UserManager::setApprovalStatusByUserName($user->getUserName(), 'Pending');

				if ($isSendActivationEmail) {
					$fromAnonymousShoppingCart = SJB_Session::getValue('fromAnonymousShoppingCart');
					SJB_Session::unsetValue('fromAnonymousShoppingCart');
					$isSent = SJB_Notifications::sendUserActivationLetter($user->getSID(), $fromAnonymousShoppingCart ? true : false);
					if ($isSent) {
						$registration_form_template = 'registration_confirm.tpl';
					} else {
						SJB_FlashMessages::getInstance()->addWarning('ERROR_SEND_ACTIVATION_EMAIL');
						$registration_form_template = 'registration_failed_to_send_activation_email.tpl';
					}
				}
				else if ((!$isSendActivationEmail) && $isApproveByAdmin) {
					SJB_UserManager::setApprovalStatusByUserName($user->getUserName(), 'Pending');
					$registration_form_template = 'registration_pending.tpl';
				}
				else {
					SJB_UserManager::activateUserByUserName($user->getUserName());
					if (!SJB_SocialPlugin::getProfileSocialID($user->getSID()))
						SJB_Notifications::sendUserWelcomeLetter($user->getSID());
					SJB_Authorization::login($user->getUserName(), $_REQUEST['password']['original'], false, $errors);
					$proceedToPosting = SJB_Session::getValue('proceed_to_posting');
					if ($proceedToPosting) {
						$redirectUrl = SJB_HelperFunctions::getSiteUrl() . '/add-listing/?listing_type_id=' . SJB_Session::getValue('listing_type_id') . '&proceed_to_posting=' . $proceedToPosting . '&productSID=' . SJB_Session::getValue('productSID');
					} else {
						$pageId = !empty($user_group_info['after_registration_redirect_to']) ? $user_group_info['after_registration_redirect_to'] : '';
						$redirectUrl = SJB_UserGroupManager::getRedirectUrlByPageID($pageId);
					}
					SJB_HelperFunctions::redirect($redirectUrl);
				}
			}
			else {
				if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid)) {
					$user->deleteProperty('username');
				}
				
				$registration_form = SJB_ObjectMother::createForm($user);
				$registration_form->registerTags($tp);
				
				$registration_form_template = 'registration_form.tpl';

				if (isset($_REQUEST['reg_form_template']))
					$registration_form_template = $_REQUEST['reg_form_template'];
				elseif (!empty($user_group_info['reg_form_template']))
					$registration_form_template = $user_group_info['reg_form_template'];

				$form_fields = $registration_form->getFormFieldsInfo();

				// define default template with ajax checking
				$registration_form->setDefaultTemplateByFieldName('email', 'email_ajaxchecking.tpl');
				$registration_form->setDefaultTemplateByFieldName('username', 'unique_string.tpl');
				// use specific template for user profile video
				$registration_form->setDefaultTemplateByFieldName('video', 'video_profile.tpl');

				$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);

				$tp->assign('user_group_info', $user_group_info);
				$tp->assign('errors', $errors);
				$tp->assign('form_fields', $form_fields);

				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$tp->assign('METADATA',
					array(
						'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields),
					)
				);
			}
		} else {
			$registration_form_template = 'registration_choose_user_group.tpl';
			$user_groups_info = SJB_UserGroupManager::getAllUserGroupsInfo();
			$tp->assign('user_groups_info', $user_groups_info);
		}
		$tp->assign('userTree', true);
		$tp->assign('errors', $errors);
		$tp->display($registration_form_template);
	}

	private function setSessionValueForRedirectAfterRegister()
	{
		$refererUri = SJB_Request::getVar('HTTP_REFERER', null, 'SERVER');
		if ($refererUri) {
			$refererUri = parse_url($refererUri);
			if (basename($refererUri['path']) != 'registration') {
				if (basename($refererUri['path']) != 'add-listing') {
					SJB_Session::unsetValue('proceed_to_posting');
					SJB_Session::unsetValue('productSID');
					SJB_Session::unsetValue('listing_type_id');
				}
				if (basename($refererUri['path']) != 'shopping-cart') {
					SJB_Session::unsetValue('fromShoppingCart');
				} else {
					if (SJB_Request::getVar('fromShoppingCart', false)) {
						SJB_Session::setValue('fromAnonymousShoppingCart', true);
					}
				}
			}
		}
	}
}
