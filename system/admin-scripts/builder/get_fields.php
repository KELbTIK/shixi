<?php

class SJB_Admin_Builder_GetFields extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_form_builder');
		return parent::isAccessible();
	}

	public function execute()
	{
		$fieldsHolderID = SJB_Request::getVar('fieldsHolderID', null);

		if (!$fieldsHolderID)
			throw new Exception('FieldsHolderID is not specified');

		$formBuilder = SJB_FormBuilderManager::getFormBuilder();
		$formBuilder->prepareFieldsHolder($fieldsHolderID);

		$tp = $formBuilder->getChargedTemplateProcessor();

		$tp->assign('fields_active', $formBuilder->getActiveFields());
		$tp->assign('fieldsHolderID', $fieldsHolderID);
		$tp->assign('holderType', SJB_Request::getVar('holderType', 'wide'));
		$tp->assign('holderTitle', SJB_Request::getVar('holderTitle', ''));
		$tp->assign('listingTypeID', $formBuilder->getListingTypeID());

		$template = $formBuilder->getFormFieldSetTemplate();

		$tp->display($template);
	}
}