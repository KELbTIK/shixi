<?php

class SJB_Admin_Classifieds_DeleteListingType extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listingTypeSID = SJB_Request::getVar('sid', null);
		if (!is_null($listingTypeSID)) {
			SJB_Breadcrumbs::deleteBreadcrumbsByListingTypeSID($listingTypeSID);
			SJB_ListingTypeManager::deleteListingTypeBySID($listingTypeSID);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/listing-types/");
		}

		echo 'The system  cannot proceed as Listing Type SID is not set';
	}
}
