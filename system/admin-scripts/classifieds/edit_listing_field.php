<?php

class SJB_Admin_Classifieds_EditListingField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_common_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_field_sid = SJB_Request::getVar('sid', null);
		$tp = SJB_System::getTemplateProcessor();

		if (!is_null($listing_field_sid)) {
			$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
			$listing_field_info = array_merge($listing_field_info, $_REQUEST);
			$listing_field = new SJB_ListingField($listing_field_info);
			$listing_field->setSID($listing_field_sid);
			$form_submitted = SJB_Request::getVar('action', '');
			if (!in_array($listing_field->field_type, array('video', 'picture', 'file', 'complex'))) {
				$profile_field = SJB_Request::getVar('profile_field', false);
				if ($form_submitted)
					if ($profile_field)
						$listing_field_info['default_value'] = '';
					else
						$listing_field_info['profile_field_as_dv'] = '';

				$default_value = array();
				if ($listing_field->field_type != 'location') {
					$default_value = array(
						'id' => 'default_value',
						'sid' => isset($listing_field_info['sid']) ? $listing_field_info['sid'] : '',
						'caption' => 'Default Value',
						'value' => (isset($listing_field_info['default_value'])) ? $listing_field_info['default_value'] : '',
						'type' => $listing_field->field_type,
						'length' => '',
						'is_required' => false,
						'is_system' => true,
						'add_parameter' => (isset($listing_field_info['add_parameter'])) ? $listing_field_info['add_parameter'] : '',
					);
				}

				switch ($listing_field->field_type) {

					case 'list':
						if (isset($listing_field_info['list_values']))
							$default_value['list_values'] = $listing_field_info['list_values'];
						break;

					case 'multilist':
						if (isset($listing_field_info['list_values']))
							$default_value['list_values'] = $listing_field_info['list_values'];
						if (!is_array($default_value['value']))
							if (strpos($default_value['value'], ','))
								$default_value['value'] = explode(',', $default_value['value']);
							else
								$default_value['value'] = array($default_value['value']);
						break;

					case 'tree':
						if (isset($listing_field_info['tree_values'])) {
							$default_value['tree_values'] = $listing_field_info['tree_values'];
						}
						if (isset($listing_field_info['display_as_select_boxes'])) {
							$default_value['display_as_select_boxes'] = $listing_field_info['display_as_select_boxes'];
						}
						unset($listing_field_info['choiceLimit']);
						break;

					case 'monetary':
						if (isset($listing_field_info['currency_values']))
							$default_value['currency_values'] = $listing_field_info['currency_values'];
						break;

				}
				if ($listing_field->field_type != 'location') {
					$listing_field->addProperty($default_value);
	
					$profile_field_as_dv = array(
						'id' => 'profile_field_as_dv',
						'caption' => 'Default Value',
						'value' => (isset($listing_field_info['profile_field_as_dv'])) ? $listing_field_info['profile_field_as_dv'] : '',
						'type' => 'list',
						'list_values' => SJB_UserProfileFieldManager::getAllFieldsInfo(),
						'length' => '',
						'is_required' => false,
						'is_system' => true,
					);
					$listing_field->addProperty($profile_field_as_dv);
				}

				if (in_array($listing_field->field_type, array('tree', 'multilist', 'list'))) {
					$sort_by_alphabet = array(
						'id' => 'sort_by_alphabet',
						'caption' => 'Sort Values By Alphabet',
						'value' => (isset($listing_field_info['sort_by_alphabet']) ? $listing_field_info['sort_by_alphabet'] : ''),
						'type' => 'boolean',
						'lenght' => '',
						'is_required' => false,
						'is_system' => true,
					);
					$listing_field->addProperty($sort_by_alphabet);
				}

				$tp->assign('profileFieldAsDV', !empty($listing_field_info['profile_field_as_dv']));
			}

			if (in_array($listing_field->field_type, array('multilist', 'list'))) {
				$listing_field->addDisplayAsProperty($listing_field_info['display_as']);
			}
			// infil instructions should be the last element in form
			if (!in_array($listing_field->getFieldType(), array('complex','tree','location'))) {
				if ($form_submitted) {
					$listing_field->addInfillInstructions(SJB_Request::getVar('instructions'));
				} else {
					$listing_field->addInfillInstructions((isset($listing_field_info['instructions']) ? $listing_field_info['instructions'] : ''));
				}
			}

			if ('tree' == $listing_field->getFieldType()) {
				$listing_field->addProperty(SJB_TreeType::getDisplayAsDetail((isset($listing_field_info['display_as_select_boxes']) ? $listing_field_info['display_as_select_boxes'] : '')));
				$treeLevelsNumber = SJB_ListingFieldTreeManager::getTreeDepthBySID($listing_field_sid);
				$tp->assign('tree_levels_number', $treeLevelsNumber);

				// treee levels captions
				for ($i = 1; $i <= $treeLevelsNumber; $i++) {
					$levelID = 'level_' . $i;
					$listing_field->addProperty(
						array(
							'id' => $levelID,
							'caption' => $i . ' Level Name',
							'value' => (isset($listing_field_info[$levelID])) ? $listing_field_info[$levelID] : '',
							'type' => 'string',
							'length' => '250',
							'is_required' => false,
							'is_system' => true,
						)
					);
				}
			}

			$edit_form = new SJB_Form($listing_field);
			$errors = array();

			if ($form_submitted && $edit_form->isDataValid($errors)) {
				$old_listing_field_id = SJB_Request::getVar('old_listing_field_id', null);
				SJB_ListingFieldManager::saveListingField($listing_field);
				SJB_ListingFieldManager::changeListingPropertyIDs($listing_field_info['id'], $old_listing_field_id);

				if ($form_submitted == 'save_info')
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/listing-fields/');
			}

			$edit_form->registerTags($tp);
			$edit_form->makeDisabled('type');
			if (($listing_field_info['id'] == 'Location') && empty($errors['ID'])) {
				$edit_form->makeDisabled('id');
			}
			$tp->assign('object_sid', $listing_field);
			$tp->assign('form_fields', $edit_form->getFormFieldsInfo());
			$tp->assign('errors', $errors);
			$tp->assign('field_type', $listing_field->getFieldType());
			$tp->assign('listing_field_info', $listing_field_info);
			$tp->assign('field_sid', $listing_field_sid);
			$tp->display('edit_listing_field.tpl');
		}
	}
}
