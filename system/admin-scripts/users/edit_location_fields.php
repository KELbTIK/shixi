<?php

class SJB_Admin_Users_EditLocationFields extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_user_groups_profile_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'list');
		$fieldSID = SJB_Request::getVar('field_sid', false);
		$parentSID = SJB_Request::getVar('sid', false);
		$userGroupSID = SJB_Request::getVar('user_group_sid', false);
		$errors = null;

		if ($fieldSID) {
			$tp->assign('field_sid', $fieldSID);
			$fieldInfo = SJB_UserProfileFieldManager::getFieldInfoBySID($fieldSID);
			$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($fieldInfo['user_group_sid']);
			$userGroupSID = isset($userGroupInfo['sid']) ? $userGroupInfo['sid'] : 0;
			$tp->assign("field_info", $fieldInfo);
			$tp->assign("group_info", $userGroupInfo);
			$tp->assign("group_sid", $userGroupSID);
			switch ($action) {
				case 'edit':
					$formSubmitted = SJB_Request::getVar('submit_form', false);
					$sid = SJB_Request::getVar('sid', false);
					$userFieldSid = SJB_Request::getVar('sid', 0);
					$userFieldInfo = SJB_UserProfileFieldDBManager::getUserProfileFieldInfoBySID($userFieldSid);
					$userFieldInfo = array_merge($userFieldInfo, $_REQUEST);
					if (!empty($userFieldInfo['default_value_setting']) && $userFieldInfo['default_value_setting'] == 'default_country')
						$userFieldInfo['default_value'] = $userFieldInfo['default_value_setting'];
					$userField = new SJB_UserProfileField($userFieldInfo);
					$userField->deleteProperty('type');
					$userField->addProperty(array('id' => 'hidden',
												'caption' => 'Hidden',
												'type' => 'boolean',
												'value' => (isset($userFieldInfo['hidden'])) ? $userFieldInfo['hidden'] : '',
												'is_system' => true));
					
					$additionalParameters = array();
					if ($userFieldInfo['id'] == 'Country') {
						$additionalParameters = array('list_values' =>  SJB_CountriesManager::getAllCountriesCodesAndNames());
						$userField->addProperty(array('id' => 'display_as',
												'caption' => 'Display Country as',
												'type' => 'list',
												'value' => (isset($userFieldInfo['display_as'])) ? $userFieldInfo['display_as'] : '',
												'list_values' => array(
													array('id' => 'country_name', 'caption' => 'Country Name'),
													array('id' => 'country_code', 'caption' => 'Country Code'),
												),
												'is_system' => true, 
												'is_required' => true));
					}
					elseif($userFieldInfo['id'] == 'State') {
						$defaultCountry = SJB_UserProfileFieldManager::getDefaultCountryByParentSID($fieldSID);
						$additionalParameters['list_values'] = SJB_StatesManager::getStatesNamesByCountry($defaultCountry);
						$additionalParameters['comment'] = !$defaultCountry?'Please select default country first to select default State':'';
						$userField->addProperty(array('id' => 'display_as',
												'caption' => 'Display State as',
												'type' => 'list',
												'value' => (isset($userFieldInfo['display_as'])) ? $userFieldInfo['display_as'] : '',
												'list_values' => array(
													array('id' => 'state_name', 'caption' => 'State Name'),
													array('id' => 'state_code', 'caption' => 'State Code'),
												),
												'is_system' => true, 
												'is_required' => true));
					}
					
					$defaultValue = array(
						'id' => 'default_value',
						'sid' => isset($userFieldInfo['sid']) ? $userFieldInfo['sid'] : '',
						'caption' => 'Default Value',
						'value' => isset($userFieldInfo['default_value']) ? $userFieldInfo['default_value'] : '',
						'type' => $userField->field_type,
						'length' => '',
						'is_required' => false,
						'is_system' => true);
					$defaultValue = array_merge($defaultValue, $additionalParameters);
					$userField->addProperty($defaultValue);
					
					if ($formSubmitted)
						$userField->addInfillInstructions(SJB_Request::getVar('instructions'));
					else
						$userField->addInfillInstructions((isset($userFieldInfo['instructions']) ? $userFieldInfo['instructions'] : ''));
		
					$UserFieldForm = new SJB_Form($userField);
					$UserFieldForm->registerTags($tp);
					$userField->setSID($sid);
					$userField->setUserGroupSID($userGroupSID);
					$addValidParam = array('field' => 'parent_sid', 'value' => $parentSID);
					if ($formSubmitted && $UserFieldForm->isDataValid($errors, $addValidParam)) {
						SJB_UserProfileFieldManager::saveUserProfileField($userField);
						if (SJB_Request::getVar('apply') == 'no' && empty($errors)) 
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/edit-user-profile-field/edit-location-fields/?field_sid=" . $fieldSID);
					}
					$UserFieldForm->makeDisabled('id');
					$tp->assign('profileFieldAsDV', !empty($userFieldInfo['profile_field_as_dv']));

					$tp->assign('userFieldInfo', $userFieldInfo);
					$tp->assign("field_type", $userField->getFieldType());
					$tp->assign("sid", $userFieldSid);
					$tp->assign("form_fields", $UserFieldForm->getFormFieldsInfo());
					$tp->assign("errors", $errors);
					$tp->display("edit_location_field.tpl");
					break;
				case 'move_up':
					SJB_UserProfileFieldManager::moveUpFieldBySID($fieldSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-user-profile-field/edit-location-fields/?field_sid=" . $parentSID);
					break;
				case 'move_down':
					SJB_UserProfileFieldManager::moveDownFieldBySID($fieldSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-user-profile-field/edit-location-fields/?field_sid=" . $parentSID);
					break;
			}

			if ($action == 'list') {
				$userFieldsInfo = SJB_UserProfileFieldManager::getUserProfileFieldsInfoByParentSID($fieldSID);
				$userFields = array();
				$userFieldSids = array();

				foreach ($userFieldsInfo as $userFieldInfo)
				{
					$userField = new SJB_UserProfileField($userFieldInfo);
					$userField->addProperty(array('id' => 'hidden',
							'caption' => 'Hidden',
							'type' => 'boolean',
							'value' => (isset($userFieldInfo['hidden'])) ? $userFieldInfo['hidden'] : '',
							'is_system' => true));
					$userField->setSID($userFieldInfo['sid']);

					$userFields[] = $userField;
					$userFieldSids[] = $userFieldInfo['sid'];
				}

				$form_collection = new SJB_FormCollection($userFields);
				$form_collection->registerTags($tp);

				$tp->assign("user_field_sids", $userFieldSids);
				$tp->assign("user_group_sid", $userGroupSID);
				$tp->display("user_location_fields.tpl");
			}
		}
	}
}