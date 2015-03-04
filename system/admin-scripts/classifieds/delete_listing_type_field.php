<?php

class SJB_Admin_Classifieds_DeleteListingTypeField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_field_sid = SJB_Request::getVar('sid', null);

		if (!is_null($listing_field_sid)) {
			$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
			SJB_ListingFieldManager::deleteListingFieldBySID($listing_field_sid);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-type/?sid=" . $listing_field_info['listing_type_sid']);
		}
		echo 'The system  cannot proceed as Listing Field SID is not set';
	}
}
