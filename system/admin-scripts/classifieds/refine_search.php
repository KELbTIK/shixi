<?php

class SJB_Admin_Classifieds_RefineSearch extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_refine_search_parameters');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action');
		$field_id = SJB_Request::getVar('field_id', false);

		if ($field_id || $action == 'save_setting') {
			switch ($action) {
				case 'save':
					$listing_type_sid = SJB_Request::getVar('listing_type_sid', false);
					$userField = 0;
					if ($listing_type_sid) {
						if (strstr($field_id, 'user_')) {
							$field_id = str_replace('user_', '', $field_id);
							$userField = 1;
						}
						if (!SJB_RefineSearch::getFieldByFieldSIDListingTypeSID($field_id, $listing_type_sid, $userField)) {
							SJB_RefineSearch::addField($field_id, $listing_type_sid, $userField);
						}
					}
					break;
				case 'save_setting':
					$listing_type_id = SJB_Request::getVar('listing_type_id', false);
					$refine_search_items_limit = SJB_Request::getVar('refine_search_items_limit', false);
					if ($listing_type_id) {
						$settingValue = SJB_Request::getVar('turn_on_refine_search_' . $listing_type_id, 0);
						if (SJB_Settings::getSettingByName('turn_on_refine_search_' . $listing_type_id) === false) {
							SJB_Settings::addSetting('turn_on_refine_search_' . $listing_type_id, $settingValue);
						} else {
							SJB_Settings::updateSetting('turn_on_refine_search_' . $listing_type_id, $settingValue);
						}
					}
					elseif ($refine_search_items_limit) {
						if (SJB_Settings::getSettingByName('refine_search_items_limit') === false) {
							SJB_Settings::addSetting('refine_search_items_limit', $refine_search_items_limit);
						} else {
							SJB_Settings::updateSetting('refine_search_items_limit', $refine_search_items_limit);
						}
					}
					break;
				case 'delete':
					SJB_RefineSearch::removeField($field_id);
					break;
				case 'move_up':
					$listing_type_sid = SJB_Request::getVar('listing_type_sid', false);
					if ($listing_type_sid) {
						SJB_RefineSearch::moveUpFieldBySID($field_id, $listing_type_sid);
					}
					break;
				case 'move_down':
					$listing_type_sid = SJB_Request::getVar('listing_type_sid', false);
					if ($listing_type_sid) {
						SJB_RefineSearch::moveDownFieldBySID($field_id, $listing_type_sid);
					}
					break;
			}
		}

		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as $key => $listingType) {
			$fields = array_merge(SJB_ListingFieldManager::getCommonListingFieldsInfo(), SJB_ListingFieldManager::getListingFieldsInfoByListingType($listingType['sid']));
			foreach ($fields as $field_key => $field) {
				if ($field['type'] == 'location') {
					if(is_array($field['fields'])) {
						$fields = array_merge($fields, $field['fields']);
					}
				}
				if (!in_array($field['type'], array('list', 'multilist', 'string', 'boolean', 'tree')) 
														|| in_array($field['id'], array('ApplicationSettings', 'access_type', 'anonymous', 'screening_questionnaire'))) {
					foreach ($fields as $fieldKey => $searchField) {
						if ($searchField['id'] == $field['id']) {
							unset($fields[$fieldKey]);
						}
					}
				}
			}
			$listingTypes[$key]['fields'] = $fields;
			if ($key == 'Job') {
				$userFieldSID = SJB_DB::queryValue("SELECT `sid` FROM `user_profile_fields` WHERE `id` = 'CompanyName'");
				if (!empty($userFieldSID)) {
					$listingTypes[$key]['user_fields'] = SJB_UserProfileFieldManager::getFieldInfoBySID($userFieldSID);
				}
			}
			$listingTypes[$key]['saved_fields'] = SJB_RefineSearch::getFieldsByListingTypeSID($listingType['sid']);
			$listingTypes[$key]['setting'] = SJB_Settings::getSettingByName('turn_on_refine_search_' . $listingType['id']);
		}

		$tp->assign('refine_search_items_limit', SJB_Settings::getSettingByName('refine_search_items_limit'));
		$tp->assign('listingTypes', $listingTypes);
		$tp->display('refine_search.tpl');
	}
}