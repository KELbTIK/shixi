<?php

class SJB_Classifieds_ManageListing extends SJB_Function
{
	public function execute()
	{
		$listing_id = isset($_REQUEST['listing_id']) ? $_REQUEST['listing_id'] : null;
		$listing = SJB_ListingManager::getObjectBySID($listing_id);
		$current_user = SJB_UserManager::getCurrentUser();
		$template_processor = SJB_System::getTemplateProcessor();

		if (is_null($listing_id))
			$errors['PARAMETERS_MISSED'] = 1;
		elseif (empty($current_user))
			$errors['NOT_LOGGED_IN'] = 1;
		elseif (is_null($listing))
			$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
		elseif ($listing->getUserSID() != $current_user->getSID())
			$errors['NOT_OWNER'] = 1;
		else {
			$productInfo = $listing->getProductInfo();
			$listing_info = SJB_ListingManager::getListingInfoBySID($listing_id);
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_info['listing_type_sid']);
			$waitApprove = $listing_type_info['waitApprove'];

			$listing_info['type'] = array('id' => $listing_type_info['id'], 'caption' => $listing_type_info['name']);
			$listing_info['product'] = $productInfo;

			$template_processor->assign("listing", $listing_info);

			$contract_id = $listing_info['contract_id'];
			$template_processor->assign("waitApprove", $waitApprove);
		}
		$template_processor->assign("errors", isset($errors) ? $errors : null);
		$template_processor->display("manage_listing.tpl");

	}
}
