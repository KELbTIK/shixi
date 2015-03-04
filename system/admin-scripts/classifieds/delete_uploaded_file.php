<?php

class SJB_Admin_Classifieds_DeleteUploadedFile extends SJB_Function
{
	public function execute()
	{
		$listing_id = SJB_Request::getVar('listing_id', null);
		$listing_info = SJB_ListingManager::getListingInfoBySID($listing_id);
		$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_info['listing_type_sid']);
		$field_id = SJB_Request::getVar('field_id', null);

		if (is_null($listing_id) || is_null($field_id)) {
			$errors['PARAMETERS_MISSED'] = 1;
		} elseif (is_null($listing_info) || !isset($listing_info[$field_id])) {
			$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
		} else {
			$uploaded_file_id = $listing_info[$field_id];
			SJB_UploadFileManager::deleteUploadedFileByID($uploaded_file_id);
			$listing_info[$field_id] = '';
			$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
			$listing->setSID($listing_id);
			SJB_ListingManager::saveListing($listing);
		}

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('errors', isset($errors) ? $errors : null);
		$tp->assign('listing_id', $listing_id);
		$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
		$tp->display('delete_uploaded_file.tpl');
	}
}
