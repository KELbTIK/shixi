<?php


class SJB_Admin_Miscellaneous_EditLocation extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_zipcode_database');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();

		$location_sid = SJB_Request::getVar('sid', null);
		$errors = null;
		$field_errors = null;
		$location_info = SJB_LocationManager::getLocationInfoBySID($location_sid);

		if (!is_null($location_info)) {
			$form_is_submitted = SJB_Request::getVar('action');
			$location_info = array_merge($location_info, $_REQUEST);
			$location = new SJB_Location($location_info);
			$location->setSID($location_sid);
			if (SJB_Request::getVar('state', false)) {
				$location->state_code = SJB_StatesManager::getStateCodeByStateName(SJB_Request::getVar('state', ''));
			} else {
				$location->state_code = '';
			}

			if ($form_is_submitted && $location->isDataValid($field_errors)) {
				if (SJB_LocationManager::saveLocation($location)) {
					if ($form_is_submitted == 'save_info') {
						$redirect_url = SJB_System::getSystemSettings('SITE_URL') . '/geographic-data/';
						SJB_HelperFunctions::redirect($redirect_url);
					}
				} else {
					$field_errors['Name'] = 'NOT_UNIQUE_VALUE';
				}
			}
		} elseif (is_null($location_sid)) {
			$errors['LOCATION_SID_IS_NOT_SPECIFIED'] = 1;
		} else {
			$errors['WORNG_LOCATION_SID_IS_SPECIFIED'] = 1;
		}

		$countries = SJB_CountriesManager::getAllCountriesCodesAndNames();
		$template_processor->assign("location_info", $location_info);
		$template_processor->assign("countries", $countries);
		$template_processor->assign("errors", $errors);
		$template_processor->assign("field_errors", $field_errors);
		$template_processor->assign("location_sid", $location_sid);

		$template_processor->display("edit_location.tpl");
	}
}
