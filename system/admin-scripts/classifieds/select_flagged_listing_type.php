<?php

class SJB_Admin_Classifieds_SelectFlaggedListingType extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listingTypeSID = SJB_Request::getVar('listing_type');

		if (!empty($listingTypeSID)) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/flagged-listings/?listing_type_sid=" . $listingTypeSID);
		}

		$allListingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();

		$tp->assign('listing_types', $allListingTypes);

		$tp->display('select_flagged_listing_type.tpl');
	}
}