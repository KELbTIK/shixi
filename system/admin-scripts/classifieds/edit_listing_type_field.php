<?php

class SJB_Admin_Classifieds_EditListingTypeField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listing_field_sid = SJB_Request::getVar('sid', null);

		if (!is_null($listing_field_sid)) {

			$listingFieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
			$old_listing_field_id = $listingFieldInfo['id'];
			$listingFieldInfo = array_merge($listingFieldInfo, $_REQUEST);
			$listing_field = new SJB_ListingField($listingFieldInfo, $listingFieldInfo['listing_type_sid']);
			$listing_field->setSID($listing_field_sid);
			$formSubmitted = SJB_Request::getVar('action', '');
			if (!in_array($listing_field->field_type, array('video', 'picture', 'file', 'complex'))) {
				$profileField = SJB_Request::getVar('profile_field', false);
				if ($formSubmitted) {
					if ($profileField) {
						$listingFieldInfo['default_value'] = '';
					} else {
						$listingFieldInfo['profile_field_as_dv'] = '';
					}
				}
				$default_value = array
				(
					'id' => 'default_value',
					'sid' => isset($listingFieldInfo['sid']) ? $listingFieldInfo['sid'] : '',
					'caption' => 'Default Value',
					'value' => isset($listingFieldInfo['default_value']) ? $listingFieldInfo['default_value'] : '',
					'type' => $listing_field->field_type,
					'length' => '',
					'is_required' => false,
					'is_system' => true,
				);
				$additionalParameters = array();
				switch ($listing_field->field_type) {
					case 'list':
						if (isset($listingFieldInfo['list_values']))
							$additionalParameters = array('list_values' => $listingFieldInfo['list_values']);
						break;

					case 'multilist':
						if (isset($listingFieldInfo['list_values']))
							$additionalParameters = array('list_values' => $listingFieldInfo['list_values']);
						if (!is_array($default_value['value']))
							if (strpos($default_value['value'], ','))
								$default_value['value'] = explode(',', $default_value['value']);
							else
								$default_value['value'] = array($default_value['value']);
						break;

					case 'tree':
						if (isset($listingFieldInfo['tree_values']))
							$additionalParameters = array('tree_values' => $listingFieldInfo['tree_values']);
						break;

					case 'monetary':
						if (isset($listingFieldInfo['currency_values']))
							$default_value['currency_values'] = $listingFieldInfo['currency_values'];
						break;
				}
				$default_value = array_merge($default_value, $additionalParameters);
				$listing_field->addProperty($default_value);

				$user_groups = SJB_UserGroupManager::getAllUserGroupsInfo();
				$list_values = array();
				foreach ($user_groups as $user_group) {
					$list_values = array_merge($list_values, SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group['sid']));
				}

				$profile_field_as_dv = array
				(
					'id' => 'profile_field_as_dv',
					'caption' => 'Default Value',
					'value' => (isset($listingFieldInfo['profile_field_as_dv'])) ? $listingFieldInfo['profile_field_as_dv'] : '',
					'type' => 'list',
					'list_values' => $list_values,
					'length' => '',
					'is_required' => false,
					'is_system' => true,
				);
				$listing_field->addProperty($profile_field_as_dv);

				if (in_array($listing_field->field_type, array('tree', 'multilist', 'list'))) {
					$sort_by_alphabet = array(
						'id' => 'sort_by_alphabet',
						'caption' => 'Sort Values By Alphabet',
						'value' => (isset($listingFieldInfo['sort_by_alphabet']) ? $listingFieldInfo['sort_by_alphabet'] : ''),
						'type' => 'boolean',
						'lenght' => '',
						'is_required' => false,
						'is_system' => true,
					);
					$listing_field->addProperty($sort_by_alphabet);
				}

				$tp->assign('profileFieldAsDV', !empty($listingFieldInfo['profile_field_as_dv']));
			}
			if (in_array($listing_field->field_type, array('multilist', 'list'))) {
				$listing_field->addDisplayAsProperty($listingFieldInfo['display_as']);
			}
			// infil instructions should be the last element in form
			if (!in_array($listing_field->getFieldType(), array('complex','tree','location')) && 'ApplicationSettings' != $listing_field->getPropertyValue('id')) {
				if ($formSubmitted) {
					$listing_field->addInfillInstructions(SJB_Request::getVar('instructions'));
				} else {
					$listing_field->addInfillInstructions((isset($listingFieldInfo['instructions']) ? $listingFieldInfo['instructions'] : ''));
				}
			}
			/**
			 * "Display as" options for TREE TYPE
			 */
			if ('tree' == $listing_field->getFieldType()) {
				$listing_field->addProperty(SJB_TreeType::getDisplayAsDetail((isset($listingFieldInfo['display_as_select_boxes']) ? $listingFieldInfo['display_as_select_boxes'] : '')));
				$treeLevelsNumber = SJB_ListingFieldTreeManager::getTreeDepthBySID($listing_field_sid);
				$tp->assign('tree_levels_number', $treeLevelsNumber);

				// treee levels captions
				for ($i = 1; $i <= $treeLevelsNumber; $i++)
				{
					$levelID = 'level_' . $i;
					$listing_field->addProperty(
						array(
							'id' => $levelID,
							'caption' => $i . ' Level Name',
							'value' => (isset($listingFieldInfo[$levelID])) ? $listingFieldInfo[$levelID] : '',
							'type' => 'string',
							'length' => '250',
							'is_required' => false,
							'is_system' => true,
						)
					);
				}
			}
			/*
			 * end of ""Display as" options for TREE TYPE"
			 */

			$edit_form = new SJB_Form($listing_field);
			$edit_form->makeDisabled("type");

			$errors = array();

			if ($formSubmitted && $edit_form->isDataValid($errors)) {
				SJB_ListingFieldManager::saveListingField($listing_field);
				SJB_ListingFieldManager::changeListingPropertyIDs($listingFieldInfo['id'], $old_listing_field_id);

				if ($formSubmitted == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/edit-listing-type/?sid=" . $listing_field->getListingTypeSID());
				}
			}

			$edit_form->registerTags($tp);
			$tp->assign("form_fields", $edit_form->getFormFieldsInfo());
			$tp->assign("errors", $errors);
			$tp->assign("listing_type_sid", $listing_field->getListingTypeSID());
			$tp->assign("field_type", $listing_field->getFieldType());
			$tp->assign("field_sid", $listing_field->getSID());
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_field->getListingTypeSID());
			$tp->assign("listing_type_info", $listing_type_info);
			$tp->assign("listing_field_info", $listingFieldInfo);
			$tp->display("edit_listing_type_field.tpl");

		}


	}
}
