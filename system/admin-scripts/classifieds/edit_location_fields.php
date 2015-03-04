<?php

class SJB_Admin_Classifieds_EditLocationFields extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$action = SJB_Request::getVar('action', 'list');
		$fieldSID = SJB_Request::getVar('field_sid', false);
		$parentSID = SJB_Request::getVar('sid', false);
		$errors = null;

		if ($fieldSID) {

			$tp->assign('field_sid', $fieldSID);
			$field_info = SJB_ListingFieldManager::getFieldInfoBySID($fieldSID);
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($field_info['listing_type_sid']);
			$tp->assign("field_info", $field_info);
			$tp->assign("type_info", $listing_type_info);
			$tp->assign("type_sid", isset($listing_type_info['sid']) ? $listing_type_info['sid'] : false);
			switch ($action) {
				case 'edit':
					$form_submitted = SJB_Request::getVar('submit_form', false);
					$sid = SJB_Request::getVar('sid', 0);
					$listingFieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoBySID($sid);
					$listingFieldInfo = array_merge($listingFieldInfo, $_REQUEST);
					$listingField = new SJB_ListingField($listingFieldInfo);
					$listingField->deleteProperty('type');
					$listingField->addProperty(array('id' => 'hidden',
												'caption' => 'Hidden',
												'type' => 'boolean',
												'value' => (isset($listingFieldInfo['hidden'])) ? $listingFieldInfo['hidden'] : '',
												'is_system' => true));

					$profileField = SJB_Request::getVar('profile_field', false);
					if ($form_submitted)
						if ($profileField) {
							$listingFieldInfo['default_value'] = '';
							$listingFieldInfo['profile_field_as_dv'] = $listingFieldInfo['id'];
						}
						else
							$listingFieldInfo['profile_field_as_dv'] = '';
					
					if (!empty($listingFieldInfo['default_value_setting'])) {
						if ($listingFieldInfo['default_value_setting'] == 'default_country') {
							$listingFieldInfo['default_value'] = $listingFieldInfo['default_value_setting'];
							$listingFieldInfo['profile_field_as_dv'] = '';
						}
						elseif ($listingFieldInfo['default_value_setting'] == 'profile_field') {
							$listingFieldInfo['default_value'] = '';
							$listingFieldInfo['profile_field_as_dv'] = $listingFieldInfo['id'];
						}
					}
					
					$additionalParameters = array();
					if ($listingFieldInfo['id'] == 'Country') {
						$additionalParameters = array('list_values' =>  SJB_CountriesManager::getAllCountriesCodesAndNames());
						$display_as = array('id' => 'display_as',
												'caption' => 'Display Country as',
												'type' => 'list',
												'value' => (isset($listingFieldInfo['display_as'])) ? $listingFieldInfo['display_as'] : '',
												'list_values' => array(
													array('id' => 'country_name', 'caption' => 'Country Name'),
													array('id' => 'country_code', 'caption' => 'Country Code'),
												),
												'is_system' => true, 
												'is_required' => true);
					}
					elseif($listingFieldInfo['id'] == 'State') {
						$defaultCountry = SJB_ListingFieldManager::getDefaultCountryByParentSID($fieldSID);
						$additionalParameters['list_values'] = array();
						if (is_numeric($defaultCountry))
							$additionalParameters['list_values'] = SJB_StatesManager::getStatesNamesByCountry($defaultCountry);
						elseif(!empty($defaultCountry)) {
							$listingFieldInfo['profile_field_as_dv'] = $listingFieldInfo['id'];
							$tp->assign('disableField', 1);
						}
						else
							$additionalParameters['comment'] = 'Please select default country first to select default State';
							
						$display_as = array('id' => 'display_as',
												'caption' => 'Display State as',
												'type' => 'list',
												'value' => (isset($listingFieldInfo['display_as'])) ? $listingFieldInfo['display_as'] : '',
												'list_values' => array(
													array('id' => 'state_name', 'caption' => 'State Name'),
													array('id' => 'state_code', 'caption' => 'State Code'),
												),
												'is_system' => true, 
												'is_required' => true);
					}
					$default_value = array(
						'id' => 'default_value',
						'sid' => isset($listingFieldInfo['sid']) ? $listingFieldInfo['sid'] : '',
						'caption' => 'Default Value',
						'value' => (isset($listingFieldInfo['default_value'])) ? $listingFieldInfo['default_value'] : '',
						'type' => $listingField->field_type,
						'length' => '',
						'is_required' => false,
						'is_system' => true,
						'add_parameter' => (isset($listingFieldInfo['add_parameter'])) ? $listingFieldInfo['add_parameter'] : '',
					);

					$default_value = array_merge($default_value, $additionalParameters);
					$listingField->addProperty($default_value);
					$profile_field_as_dv = array(
						'id' => 'profile_field_as_dv',
						'caption' => 'Default Value',
						'value' => (isset($listingFieldInfo['profile_field_as_dv'])) ? $listingFieldInfo['profile_field_as_dv'] : '',
						'type' => 'list',
						'length' => '',
						'is_required' => false,
						'is_system' => true,
					);
					
					$listingField->addProperty($profile_field_as_dv);
					if (isset($display_as))
						$listingField->addProperty($display_as);
					
					if ($form_submitted)
						$listingField->addInfillInstructions(SJB_Request::getVar('instructions'));
					else
						$listingField->addInfillInstructions((isset($listingFieldInfo['instructions']) ? $listingFieldInfo['instructions'] : ''));

					$ListingFieldForm = new SJB_Form($listingField);
					$ListingFieldForm->registerTags($tp);
					$listingField->setSID($sid);
					$addValidParam = array('field' => 'parent_sid', 'value' => $parentSID);
					
					if ($form_submitted && $ListingFieldForm->isDataValid($errors, $addValidParam)) {
						SJB_ListingFieldManager::saveListingField($listingField);
						if ($listingFieldInfo['id'] == 'Country') {
							$profileFieldAsDv = $listingField->getPropertyValue('profile_field_as_dv');
							if ($profileFieldAsDv) {
								$listingFieldsInfo = SJB_ListingFieldManager::getListingFieldsInfoByParentSID($fieldSID);
								foreach ($listingFieldsInfo as $fieldInfo) {
									if ($fieldInfo['id'] == 'State') {
										$listingField = new SJB_ListingField($fieldInfo);
										$listingField->setSID($fieldInfo['sid']);
										$default_value = array(
											'id' => 'default_value',
											'sid' => isset($fieldInfo['sid']) ? $fieldInfo['sid'] : '',
											'caption' => 'Default Value',
											'value' => '',
											'type' => $listingField->field_type,
											'length' => '',
											'is_required' => false,
											'is_system' => true,
											'add_parameter' => (isset($fieldInfo['add_parameter'])) ? $fieldInfo['add_parameter'] : '',
										);
										$listingField->addProperty($default_value);
										$profile_field_as_dv = array(
											'id' => 'profile_field_as_dv',
											'caption' => 'Default Value',
											'value' => 'State',
											'type' => 'list',
											'length' => '',
											'is_required' => false,
											'is_system' => true,
										);
										$listingField->addProperty($profile_field_as_dv);
										SJB_ListingFieldManager::saveListingField($listingField);
									}
								}
							}
						}
						if (SJB_Request::getVar('apply') == 'no' && empty($errors)) 
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/edit-listing-field/edit-location-fields/?field_sid=" . $fieldSID);
					}

					$ListingFieldForm->makeDisabled('id');
					$tp->assign('profileFieldAsDV', !empty($listingFieldInfo['profile_field_as_dv']));
					$tp->assign('listingFieldInfo', $listingFieldInfo);
					$tp->assign("field_type", $listingField->getFieldType());
					$tp->assign("sid", $sid);
					$tp->assign("form_fields", $ListingFieldForm->getFormFieldsInfo());
					$tp->assign("errors", $errors);
					$tp->display("edit_location_field.tpl");
					break;
				case 'move_up':
					SJB_ListingFieldManager::moveUpFieldBySID($fieldSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-field/edit-location-fields/?field_sid=" . $parentSID);
					break;
				case 'move_down':
					SJB_ListingFieldManager::moveDownFieldBySID($fieldSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-field/edit-location-fields/?field_sid=" . $parentSID);
					break;
			}

			if ($action == 'list') {
				$listingFieldsInfo = SJB_ListingFieldManager::getListingFieldsInfoByParentSID($fieldSID);
				$listingFields = array();
				$listingFieldSids = array();

				foreach ($listingFieldsInfo as $listingFieldInfo) {
					$listingField = new SJB_ListingField($listingFieldInfo);
					$listingField->addProperty(array('id' => 'hidden',
							'caption' => 'Hidden',
							'type' => 'boolean',
							'value' => (isset($listingFieldInfo['hidden'])) ? $listingFieldInfo['hidden'] : '',
							'is_system' => true));
					$listingField->setSID($listingFieldInfo['sid']);

					$listingFields[] = $listingField;
					$listingFieldSids[] = $listingFieldInfo['sid'];
				}

				$form_collection = new SJB_FormCollection($listingFields);
				$form_collection->registerTags($tp);

				$tp->assign("listing_field_sids", $listingFieldSids);
				$tp->display("listing_location_fields.tpl");
			}
		}

	}
}
