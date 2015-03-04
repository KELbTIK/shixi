<?php


class SJB_Admin_Classifieds_MoveListingTypeField extends SJB_Function
{
	public function execute()
	{
		$listing_field_sid = SJB_Request::getVar('sid', null);
		if (!is_null($listing_field_sid)) {
			$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
			if ($_REQUEST['action'] == 'move_up') {
				SJB_ListingFieldManager::moveUpFieldBySID($listing_field_sid);
			} elseif ($_REQUEST['action'] == 'move_down') {
				SJB_ListingFieldManager::moveDownFieldBySID($listing_field_sid);
			}
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-type/?sid=" . $listing_field_info['listing_type_sid']);
		} else {
			echo 'The system  cannot proceed as Listing Field SID is not set';
		}
	}
}
