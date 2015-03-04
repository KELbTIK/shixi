<?php

class SJB_Admin_Miscellaneous_AddLocation extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_listing_types_and_specific_listing_fields', 'edit_zipcode_database'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$location = new SJB_Location($_REQUEST);
		if (SJB_Request::getVar('state', false)) {
			$location->state_code = SJB_StatesManager::getStateCodeByStateName(SJB_Request::getVar('state', ''));
		} else {
			$location->state_code = '';
		}

		$formSubmitted = 'add' == SJB_Request::getVar('action', false);
		$locationAdded = false;

		if ($formSubmitted) {
			if ($location->isDataValid($errors)) {
				if (SJB_LocationManager::saveLocation($location)) {
					$location = new SJB_Location();
					$locationAdded = true;
				} else {
					$errors['Name'] = 'NOT_UNIQUE_VALUE';
				}
			}
		}

		$countries = SJB_CountriesManager::getAllCountriesCodesAndNames();
		$locationInfo = $location->getInfo();

		$tp->assign('locationAdded', $locationAdded);
		$tp->assign('countries', $countries);
		$tp->assign('errors', $errors);
		$tp->assign('location_info', $locationInfo);
		$tp->display('add_location.tpl');
	}
}
