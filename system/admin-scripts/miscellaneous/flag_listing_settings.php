<?php

class SJB_Admin_Miscellaneous_FlagListingSettings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_flag_listing_settings');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();

		$template = 'flag_listing_settings.tpl';

		$form_submitted = SJB_Request::getVar('submit');
		$action = SJB_Request::getVar('action');
		$itemSID = SJB_Request::getVar('item_sid');

		switch ($action) {
			case 'save':
				$saveValue = trim(SJB_Request::getVar('new_value'));
				$listingTypesArray = SJB_Request::getVar('flag_listing_types');

				$typesForSave = '';
				// make string to save
				if (!empty($listingTypesArray)) {
					$typesForSave = implode(',', $listingTypesArray);
				}
				if (empty($saveValue)) {
					$errors['PLEASE_ENTER_FLAG_REASON'] = 'Please enter flag reason';
				}
				if (empty($errors) && empty($typesForSave)) {
					$errors['PLEASE_SELECT_LISTING_TYPE'] = 'Please select listing type';
				}

				if (empty($errors)) {
					if (!$itemSID) { // ADD NEW ITEM
						$result = SJB_DB::query('SELECT `sid` FROM `flag_listing_settings` WHERE `listing_type_sid` = ?s AND `value` = ?s LIMIT 1', $typesForSave, $saveValue);
						if (!empty($result)) {
							$errors['THIS_FLAG_REASON_ALREADY_EXISTS_IN_THE_SYSTEM'] = 'This flag reason already exists in the system';
						} else {
							SJB_DB::queryExec('INSERT INTO `flag_listing_settings` SET `listing_type_sid` = ?s, `value` = ?s', $typesForSave, $saveValue);
						}
					} else { // UPDATE ITEM
						SJB_DB::queryExec('UPDATE `flag_listing_settings` SET `value` = ?s, `listing_type_sid` = ?s WHERE `sid` = ?n', $saveValue, $typesForSave, $itemSID);
					}

					if ($form_submitted == 'save') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flag-listing-settings/');
					} elseif ($form_submitted == 'apply') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flag-listing-settings/?item_sid=' . $itemSID . '&action=edit');
					}
				}

				break;

			case 'delete':
				SJB_DB::query('DELETE FROM `flag_listing_settings` WHERE `sid` = ?n', $itemSID);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/flag-listing-settings/');
				break;

			case 'edit':
				$currentItem = SJB_DB::query('SELECT * FROM `flag_listing_settings` WHERE `sid` = ?n LIMIT 1', $itemSID);
				$template = 'flag_listing_settings_edit.tpl';

				if ($currentItem) {
					$currentItem = array_pop($currentItem);
					$typesArray = explode(',', $currentItem['listing_type_sid']);
					$currentItem['listing_type_sid'] = $typesArray;
				}
				$tp->assign('current_setting', $currentItem);
				break;
		}

		// Need to select listing type
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		$types = array();
		foreach ($listingTypes as $elem) {
			$types[$elem['sid']] = $elem;
		}
		$listingTypes = $types;
		$tp->assign('listing_types', $listingTypes);

		$settings = SJB_DB::query('SELECT * FROM `flag_listing_settings`');
		foreach ($settings as $key => $elem) {
			$settings[$key]['listing_type_sid'] = explode(',', $elem['listing_type_sid']);
		}

		$tp->assign('settings', $settings);
		$tp->assign('errors', $errors);

		$tp->display($template);
	}
}
