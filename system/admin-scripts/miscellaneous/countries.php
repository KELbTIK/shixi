<?php
class SJB_Admin_Miscellaneous_Countries extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_countries');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'list');
		$errors = array();
		$template = 'countries.tpl';
		$paginator = new SJB_CountriesPagination();

		switch ($action) {
			case 'move_country':
			case 'save_order':
				$template = 'move_country.tpl';
				$itemSIDs = SJB_Request::getVar('item_order', array());
				try {
					SJB_CountriesManager::saveItemsOrder($paginator->currentPage, $paginator->itemsPerPage, $itemSIDs);
					$tp->assign('action', $action);
				} catch (Exception $e) {
					$errors['SAVING_ORDER'] = $e->getMessage();
				}
				$countries = SJB_CountriesManager::getAllCountries();
				$tp->assign('countries', $countries);
				break;
			case 'activate':
				$countriesSIDs = array_keys(SJB_Request::getVar('countries', array()));
				foreach ($countriesSIDs as $countrySID) 
					SJB_CountriesManager::activateCountryBySID($countrySID);
				$action = 'list';
				break;
			case 'deactivate':
				$countriesSIDs = array_keys(SJB_Request::getVar('countries', array()));
				$defaultCountry = SJB_Settings::getSettingByName('default_country');
				foreach ($countriesSIDs as $countrySID) {
					if ($defaultCountry == $countrySID) {
						$errors['DEFAULT_DEACTIVATE'] = SJB_CountriesManager::getCountryInfoBySID($countrySID);
					}
					else {
						SJB_CountriesManager::deactivateCountryBySID($countrySID);
					}
				}
				$action = 'list';
				break;
			case 'delete':
				$countriesSIDs = array_keys(SJB_Request::getVar('countries', array()));
				$defaultCountry = SJB_Settings::getSettingByName('default_country');
				foreach ($countriesSIDs as $countrySID) {
					if ($defaultCountry == $countrySID) {
						$errors['DEFAULT_DELETE'] = SJB_CountriesManager::getCountryInfoBySID($countrySID);
					}
					else {
						SJB_CountriesManager::deleteCountryBySID($countrySID);
					}
				}
				$action = 'list';
				break;
			case 'add_country':
				$template = 'add_country.tpl';
				$formSubmitted = SJB_Request::getVar('action_add', false);
				$country = new SJB_Country($_REQUEST);
				$addCountryForm = new SJB_Form($country);
				$addCountryForm->registerTags($tp);
				if ($formSubmitted &&  $addCountryForm->isDataValid($errors)) {
					SJB_CountriesManager::saveCountry($country);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/countries/");
				}
				else {
					$formFields = $addCountryForm->getFormFieldsInfo();
					$tp->assign('form_fields', $formFields);
				}
				break;
			case 'edit_country':
				$template = 'edit_country.tpl';
				$countrySID = SJB_Request::getVar('country_id', false);
				$formSubmitted = SJB_Request::getVar('action_add', false);
				$countryInfo = SJB_CountriesManager::getCountryInfoBySID($countrySID);
				if ($countryInfo) {
					$countryInfo = array_merge($countryInfo, $_REQUEST);
					$country = new SJB_Country($countryInfo);
					$addCountryForm = new SJB_Form($country);
					$addCountryForm->registerTags($tp);
					$country->setSID($countrySID);
					if ($formSubmitted &&  $addCountryForm->isDataValid($errors)) {
						SJB_CountriesManager::saveCountry($country);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/countries/");
					}
					else {
						$formFields = $addCountryForm->getFormFieldsInfo();
						$tp->assign('form_fields', $formFields);
						$tp->assign('country_id', $countrySID);
					}
				}
				else {
					$tp->assign('action', 'edit');
					$errors['WRONG_COUNTRY_ID_SPECIFIED'] = 'WRONG_COUNTRY_ID_SPECIFIED'; 
					$template = 'country_errors.tpl';
				}
				break;
			case 'import_countries':
				$template = 'import_countries.tpl';
				$fileInfo = isset($_FILES['import_file']) ? $_FILES['import_file'] : null;
				$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				
				if ($fileInfo['error']) {
					$errors[] = SJB_UploadFileManager::getErrorId($fileInfo['error']);
				}
				elseif ($fileInfo) {
					$fileFormats  = array('csv', 'xls', 'xlsx');
					$pathInfo = pathinfo($fileInfo['name']);
					$fileExtension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : '';
					if (!in_array(strtolower($fileExtension), $fileFormats)) 
						$errors[] = 'Please choose Excel or csv file';
					else {
						$importFile = new SJB_ImportFileXLS($fileInfo);
						$importFile->parse();
						$importedData = $importFile->getData();
						$country = new SJB_Country();
						$count = 0;
						foreach ($importedData as $key => $importedColumn) {
							if ($key == 1) {
								$data = array_merge(array(array('country_code', 'country_name')), array($importedColumn));
								$importedProcessor = new SJB_ImportedCountryProcessor($data, $country);
							}
							if (!$importedColumn) {
								continue;
							}
							$countryInfo = $importedProcessor->getData($importedColumn);
							if (!empty($countryInfo['country_code']) && !empty($countryInfo['country_name'])) {
								$country = new SJB_Country($countryInfo);
								$country->setPropertyValue('active', 1);
								$countrySID = SJB_CountriesManager::getCountrySIDByCountryCode($countryInfo['country_code']);
								if ($countrySID) {
									$country->setSID($countrySID);
								}
								SJB_CountriesManager::saveCountry($country);
								if (!$countrySID) {
									$count++;
								}
							}
						}
						$tp->assign('imported_countries_count', $count);
						$template = 'import_countries_result.tpl';
					}
				}
				break;
				
			case 'change_setting':
				$action = 'list';
				$countrySID = SJB_Request::getVar('default_country');
				SJB_Settings::updateSettings(array('default_country'=>$countrySID));
				break;
		}
		
		if ($action == 'list') {
			$countries = SJB_CountriesManager::getAllCountries(($paginator->currentPage - 1) * $paginator->itemsPerPage, $paginator->itemsPerPage);
			$countriesForDefault = SJB_CountriesManager::getAllActiveCountries();
			$paginator->setItemsCount(SJB_CountriesManager::countCountries());

			$tp->assign('paginationInfo', $paginator->getPaginationInfo());
			$tp->assign('countries', $countries);
			$tp->assign('countriesForDefault', $countriesForDefault);
			$tp->assign("settings", SJB_Settings::getSettings());
		}
		$tp->assign("errors", $errors);
		$tp->display($template);
	}
}
