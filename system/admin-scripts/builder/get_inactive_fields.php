<?php

class SJB_Admin_Builder_GetInactiveFields extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_form_builder');
		return parent::isAccessible();
	}

	public function execute()
	{
		$formBuilder = SJB_FormBuilderManager::getFormBuilder();
		if ($formBuilder instanceof SJB_FormBuilder) {
			$tp = $formBuilder->getChargedTemplateProcessor();
			$tp->assign('fields_inactive', $formBuilder->getInactiveFields());
			$tp->assign('defaultCountry', SJB_Settings::getSettingByName('default_country'));
			$tp->assign('listingTypeID', $formBuilder->getListingTypeID());
			$tp->assign('mode', $formBuilder->getBuilderType());

			$tp->display('form_builder_in.tpl');
		}
	}
}
