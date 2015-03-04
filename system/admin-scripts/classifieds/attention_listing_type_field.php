<?php

class SJB_Admin_Classifieds_AttentionListingTypeField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_common_listing_fields', 'manage_listing_types_and_specific_listing_fields'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listing_field_sid = SJB_Request::getVar('listing_sid', null);
		$errors = array();
		$listingTypes = array();
		if (!is_null($listing_field_sid)) {
			$listing_field = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
			$listing_type_id = 'Job/Resume';
			if ($listing_field['listing_type_sid'] != 0) {
				$listing_type_id = SJB_ListingTypeManager::getListingTypeIDBySID($listing_field['listing_type_sid']);
				array_push($listingTypes, SJB_ListingTypeManager::getListingTypeInfoBySID(SJB_Array::get($listing_field, 'listing_type_sid')));
			}
			else {
				$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
			}

			$tp->assign('listingTypesInfo', $listingTypes);
			$tp->assign('listing_type_id', $listing_type_id);
			$tp->assign('listing_sid', $listing_field_sid);
			$tp->assign('listing_field_info', $listing_field);
			$tp->assign('listing_type_sid', $listing_field['listing_type_sid']);
		}
		else {
			$errors[] = 'The system cannot proceed as Listing SID is not set';
		}

		$tp->assign('errors', $errors);
		$tp->display('attention_listing_type_field.tpl');
	}
}
