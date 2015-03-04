<?php

class SJB_Admin_Builder_FormBuilders extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_form_builder');
		return parent::isAccessible();
	}

	public function execute()
	{
		$aListingTypesInfo = SJB_ListingTypeManager::getAllListingTypesInfo();
		$tp = SJB_System::getTemplateProcessor();

		$tp->assign('listingTypesInfo', $aListingTypesInfo);

		$tp->display('form_builder.tpl');
	}
}
