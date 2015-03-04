<?php

class SJB_Admin_Classifieds_DeleteListingField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_common_listing_fields');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$listing_field_sid = SJB_Request::getVar('sid', null);

		if (!is_null($listing_field_sid)) {
			$listingFieldID = SJB_ListingFieldManager::getListingFieldIDBySID($listing_field_sid);
			if ($listingFieldID != 'Location') 
				SJB_ListingFieldManager::deleteListingFieldBySID($listing_field_sid);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/listing-fields/");
		}

		echo 'The system  cannot proceed as Listing Field SID is not set';
	}
}

