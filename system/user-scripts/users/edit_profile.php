<?php

class SJB_Users_EditProfile extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user_info = SJB_Authorization::getCurrentUserInfo();

		if (!empty($user_info['subuser'])) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/sub-accounts/edit/?user_id=' . $user_info['subuser']['sid']);
		}

		if (!empty($user_info)) {
			$user_info = array_merge($user_info, $_REQUEST);

			$username = $user_info['username'];
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_info['user_group_sid']);
			$delete_profile = SJB_Request::getVar('command', '', 'post') == 'unregister-user';

			$errors = array();
			if ($delete_profile && SJB_Acl::getInstance()->isAllowed('delete_user_profile')) {
				try {
					$user = SJB_UserManager::getObjectBySID($user_info['sid']);
					SJB_UserManager::deleteUserById($user_info['sid']);
					SJB_AdminNotifications::sendAdminDeletingUserProfile($user, SJB_Request::getVar('reason', '', 'post'));
					SJB_Authorization::logout();
					$user_info = array();
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/edit-profile/?profile_deleted=true');
				} catch (Exception $e) {
					$errors[] = $e->getMessage();
				}
			}

			$user = new SJB_User($user_info, $user_info['user_group_sid']);
			$user->setSID($user_info['sid']);

			$user->deleteProperty("active");
			$user->deleteProperty("featured");
			$user->makePropertyNotRequired("password");

			$user->getProperty('email')->type->disableEmailConfirmation();

			$edit_profile_form = new SJB_Form($user);
			$edit_profile_form->registerTags($tp);

			$edit_profile_form->makeDisabled("username");

			$form_submitted = SJB_Request::getVar('action', false) == 'save_info';

			if ($form_submitted && $edit_profile_form->isDataValid($errors)) {
				$password_value = $user->getPropertyValue('password');

				if (empty($password_value['original'])) {
					$user->deleteProperty('password');
				}

				SJB_UserManager::saveUser($user);
				SJB_Authorization::updateCurrentUserSession();




				// >>> SJB-1197
				// needs to check session for ajax-uploaded files, and set it to user profile
				$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
				$formToken         = SJB_Request::getVar('form_token');
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

						// and save user with new fields data
						SJB_UserManager::saveUser($user);
						SJB_Authorization::updateCurrentUserSession();

						// clean temporary storage
						$tmpUploadsStorage = SJB_Array::unsetValueByPath($tmpUploadsStorage, "{$formToken}");

						// CLEAR TEMPORARY SESSION STORAGE
						SJB_Session::setValue('tmp_uploads_storage', $tmpUploadsStorage);
					}
				}
				// <<< SJB-1197





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

			$tp->assign("show_mailing_flag", $user_group_info['show_mailing_flag']);
			$tp->assign("form_fields", $form_fields);
			$tp->assign('userTree', true);
			$tp->display('edit_profile.tpl');
		}
		elseif (empty($user_info) && SJB_Request::getVar('profile_deleted', '') == true) {
			$user = new SJB_User(array());

			$edit_profile_form = new SJB_Form($user);
			$edit_profile_form->registerTags($tp);

			$edit_profile_form->makeDisabled("username");
			$form_fields = $edit_profile_form->getFormFieldsInfo();

			$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
			$tp->assign
			(
				"METADATA",
				array
				(
					"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields),
				)
			);

			$tp->assign("form_fields", $form_fields);
			$tp->assign('action', 'delete_profile');
			$tp->assign('tree_link_users', 'users');
			$tp->assign('userTree', true);
			$tp->display('edit_profile.tpl');
		}
		else {
			$tp->assign("ERROR", "NOT_LOGIN");
			$tp->display("../miscellaneous/error.tpl");
			return;
		}
	}
}
