<?php


class SJB_Admin_Classifieds_ListingTypes extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(
			array(
				'manage_listing_types_and_specific_listing_fields',
				'set_posting_pages',
			)
		);
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$listing_types_structure = SJB_ListingTypeManager::createTemplateStructureForListingTypes();
		$template_processor->assign("listing_types", $listing_types_structure);
		$template_processor->display("listing_types.tpl");
	}
}

