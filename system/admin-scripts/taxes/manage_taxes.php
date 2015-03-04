<?php

class SJB_Admin_Taxes_ManageTaxes extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_tax_rules');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$template = 'manage_taxes.tpl';
		$formSubmitted = SJB_Request::getVar('event', false);
		$field_errors = array();
		switch ($action) {
			case 'add':
				$taxInfo = $_REQUEST;
				$template = 'add_tax.tpl';
				$country = SJB_Request::getVar('Country', false);
				$state = SJB_Request::getVar('State', false);
				if (!isset($taxInfo['active']))
					$taxInfo['active'] = 1;
				$tax = new SJB_Taxes($taxInfo);
				$addTaxForm = new SJB_Form($tax);
				$addTaxForm->registerTags($tp);
				if ($formSubmitted){
					$addTaxForm->isDataValid($field_errors);
					if (SJB_TaxesManager::isTaxExistByCountryAndState($country, $state)) {
						$field_errors[] = 'NOT_UNIQUE_COUNTRY_AND_STATE';
					}
					if (!$field_errors) {
						SJB_TaxesManager::saveTax($tax);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-taxes/");
					}

				}
				$formFields = $addTaxForm->getFormFieldsInfo();
				$tp->assign("state_sid", intval($state));
				$tp->assign("form_fields", $formFields);

				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$tp->assign(
					"METADATA",
					array (
						"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
					)
				);
				break;
			case 'edit':
				$template = 'edit_tax.tpl';
				$taxSID = SJB_Request::getVar('sid', false);
				$taxInfo = SJB_TaxesManager::getTaxInfoBySID($taxSID);
				if ($taxInfo) {
					$taxInfo = array_merge($taxInfo, $_REQUEST);
					$country = $taxInfo['Country'];
					$state = $taxInfo['State'];
					$tax = new SJB_Taxes($taxInfo);
					$editTaxForm = new SJB_Form($tax);
					$editTaxForm->registerTags($tp);
					$tax->setSID($taxSID);
					if ($formSubmitted) {
						$editTaxForm->isDataValid($field_errors);
						if (SJB_TaxesManager::isTaxExistByCountryAndState($country, $state, $taxSID)) {
							$field_errors[] = 'NOT_UNIQUE_COUNTRY_AND_STATE';
						}
						if (!$field_errors) {
							SJB_TaxesManager::saveTax($tax);
							if ($formSubmitted == 'save') {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-taxes/");
							} else {
								$tax->setFloatNumbersIntoValidFormat();
							}
						}
					}

					$formFields = $editTaxForm->getFormFieldsInfo();
					$tp->assign("form_fields", $formFields);
					$tp->assign("sid", $taxSID);
					$tp->assign("state_sid", intval($state));

					$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
					$tp->assign(
						"METADATA",
						array (
							"form_fields" => $metaDataProvider->getFormFieldsMetadata($formFields),
						)
					);
				}
				else {
					$tp->assign('action', 'edit');
					$field_errors[] = 'WRONG_TAX_ID_SPECIFIED';
					$template = 'errors.tpl';
				}
				break;
			case 'delete':
				$taxSID = SJB_Request::getVar('sid', 0);
				SJB_TaxesManager::deleteTaxBySID($taxSID);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-taxes/");
				break;
			case 'deactivate':
				$taxSID = SJB_Request::getVar('sid', 0);
				SJB_TaxesManager::deactivateTaxBySID($taxSID);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-taxes/");
				break;
			case 'setting':
				$enableTaxes = SJB_Request::getVar('enable_taxes', 0);
				SJB_Settings::updateSetting('enable_taxes', $enableTaxes);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-taxes/");
				break;
			case 'activate':
				$taxSID = SJB_Request::getVar('sid', 0);
				SJB_TaxesManager::activateTaxBySID($taxSID);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-taxes/");
				break;
			default:
				$taxes_structure = array();
				$taxes = SJB_TaxesManager::getAllTaxesInfo();
				foreach ($taxes as $tax_info)
					$taxes_structure[$tax_info['sid']] = SJB_TaxesManager::createTemplateStructureForTax($tax_info);
				$tp->assign('taxes', $taxes_structure);
				break;
		}
		$tp->assign('errors', $field_errors);
		$tp->display($template);
	}
}
