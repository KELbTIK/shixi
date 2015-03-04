<?php
class SJB_Admin_Miscellaneous_States extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_states_or_regions');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'list');
		$countrySID = SJB_Request::getVar('country_sid', false);
		$errors = array();
		$template = 'states.tpl';
		$countries = SJB_CountriesManager::getAllCountries();
		$paginator = new SJB_StatesPagination();

		switch ($action) {
			case 'move_state':
			case 'save_order':
				$template = 'move_state.tpl';
				$itemSIDs = SJB_Request::getVar('item_order', array());
				try {
					SJB_StatesManager::saveItemsOrder($paginator->currentPage, $paginator->itemsPerPage, $itemSIDs);
					$tp->assign('action', $action);
				} catch (Exception $e) {
					$errors['SAVING_ORDER'] = $e->getMessage();
				}
				$states = SJB_StatesManager::getAllStates($countrySID);
				$tp->assign('states', $states);
				break;
			case 'activate':
				$statesSIDs = array_keys(SJB_Request::getVar('states', array()));
				foreach ($statesSIDs as $stateSID) 
					SJB_StatesManager::activateStateBySID($stateSID);
				$action = 'list';
				break;
			case 'deactivate':
				$statesSIDs = array_keys(SJB_Request::getVar('states', array()));
				foreach ($statesSIDs as $stateSID) 
					SJB_StatesManager::deactivateStateBySID($stateSID);
				$action = 'list';
				break;
			case 'delete':
				$statesSIDs = array_keys(SJB_Request::getVar('states', array()));
				foreach ($statesSIDs as $stateSID) 
					SJB_StatesManager::deleteStateBySID($stateSID);
				$action = 'list';
				break;
			case 'add_state':
				$template = 'add_state.tpl';
				$formSubmitted = SJB_Request::getVar('action_add', false);
				$state = new SJB_State($_REQUEST);
				$addStateForm = new SJB_Form($state);
				$addStateForm->registerTags($tp);
				$addValidParam = array('field' => 'country_sid', 'value' => $countrySID);
				if ($formSubmitted &&  $addStateForm->isDataValid($errors, $addValidParam)) {
					$state->addProperty(array (
			 			'id'			=> 'country_sid',
						'type'			=> 'list',
						'value' 		=> $countrySID,
						'is_required'	=> true,
						'is_system'		=> true,
			 		));
					SJB_StatesManager::saveState($state);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/states/?country_sid=".$countrySID);
				}
				else {
					$formFields = $addStateForm->getFormFieldsInfo();
					$tp->assign('form_fields', $formFields);
				}
				break;
			case 'edit_state':
				$template = 'edit_state.tpl';
				$stateSID = SJB_Request::getVar('state_id', false);
				$formSubmitted = SJB_Request::getVar('action_add', false);
				$stateInfo = SJB_StatesManager::getStateInfoBySID($stateSID);
				if ($stateInfo) {
					$stateInfo = array_merge($stateInfo, $_REQUEST);
					$state = new SJB_State($stateInfo);
					$addStateForm = new SJB_Form($state);
					$addStateForm->registerTags($tp);
					$state->setSID($stateSID);
					$addValidParam = array('field' => 'country_sid', 'value' => $stateInfo['country_sid']);
					if ($formSubmitted &&  $addStateForm->isDataValid($errors, $addValidParam)) {
						SJB_StatesManager::saveState($state);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/states/?country_sid=".$stateInfo['country_sid']);
					}
					else {
						$formFields = $addStateForm->getFormFieldsInfo();
						$tp->assign('form_fields', $formFields);
						$tp->assign('state_id', $stateSID);
					}
				}
				else {
					$tp->assign('action', 'edit');
					$errors['WRONG_STATE_ID_SPECIFIED'] = 'WRONG_STATE_ID_SPECIFIED'; 
					$template = 'state_errors.tpl';
				}
				break;
			case 'import_states':
				$template = 'import_states.tpl';
				$fileInfo = isset($_FILES['import_file']) ? $_FILES['import_file'] : null;
				$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				
				if ($fileInfo['error']) {
					$errors[] = SJB_UploadFileManager::getErrorId($fileInfo['error']);
				}
				elseif ($fileInfo) {
					$fileFormats  = array('csv', 'xls', 'xlsx');
					$pathInfo = pathinfo($fileInfo['name']);
					$fileExtension = isset($pathInfo['extension']) ? strtolower($pathInfo['extension']) : '';
					if (!in_array(strtolower($fileExtension), $fileFormats)) {
						$errors[] = 'Please choose Excel or csv file';
					} else {
						$importFile = new SJB_ImportFileXLS($fileInfo);
						$importFile->parse();
						$importedData = $importFile->getData();
						$state = new SJB_State();
						$count = 0;
						foreach ($importedData as $key => $importedColumn) {
							if ($key == 1) {
								$data = array_merge(array(array('state_code', 'state_name')), array($importedColumn));
								$importedProcessor = new SJB_ImportedStateProcessor($data, $state);
							}
							if (!$importedColumn) {
								continue;
							}
							$stateInfo = $importedProcessor->getData($importedColumn);
							if (!empty($stateInfo['state_code']) && !empty($stateInfo['state_name'])) {
								$state = new SJB_State($stateInfo);
								$state->addProperty(array (
										'id'			=> 'country_sid',
										'type'			=> 'list',
										'value'			=> $countrySID,
										'is_required'	=> true,
										'is_system'		=> true,
								));
								$state->setPropertyValue('active', 1);
								$stateSID = SJB_StatesManager::getStateSIDByStateCode($stateInfo['state_code'], $countrySID);
								if ($stateSID) {
									$state->setSID($stateSID);
								} else {
									$count++;
								}
								SJB_StatesManager::saveState($state);
							}
						}
						$tp->assign('imported_states_count', $count);
						$template = 'import_states_result.tpl';
					}
				}
				break;
		}

		if ($action == 'list') {
			$countryCode = SJB_Settings::getSettingByName('default_country_code');
			if (!$countrySID)
				$countrySID = SJB_CountriesManager::getCountrySIDByCountryCode($countryCode);
			if (!$countrySID) {
				$allCountries = SJB_CountriesManager::getAllCountries();
				foreach ($allCountries as $country)	{
					$countrySID = $country['sid'];
					break;
				}
			}
			$countryInfo = SJB_CountriesManager::getCountryInfoBySID($countrySID);
			if ($countryInfo && $countryInfo['country_code'] != $countryCode)
				SJB_Settings::updateSetting('default_country_code', $countryInfo['country_code']);
			$states = SJB_StatesManager::getAllStates($countrySID, ($paginator->currentPage - 1) * $paginator->itemsPerPage, $paginator->itemsPerPage);
			$paginator->setItemsCount(SJB_StatesManager::countStates($countrySID));

			$tp->assign('states', $states);
			$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		}

		$tp->assign("countries", $countries);
		$tp->assign("country_sid", $countrySID);
		$tp->assign("errors", $errors);
		$tp->display($template);
	}
}
