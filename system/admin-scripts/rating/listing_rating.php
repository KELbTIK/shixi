<?php

class SJB_Admin_Rating_ListingRating extends SJB_Function
{
	public function isAccessible()
	{
		$listingId = SJB_Request::getVar('listing_id', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
		$listingType = !in_array($listingTypeId, array('Resume', 'Job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel('manage_' . strtolower($listingType));
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_id = isset($_REQUEST['listing_id']) ? $_REQUEST['listing_id'] : null;

		$template_processor = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['action'])) {
			$action = strtolower($_REQUEST['action']);

			$rating_id = isset($_REQUEST['rating_id']) ? $_REQUEST['rating_id'] : null;

			$comment_ids = array();
			if (isset($_REQUEST['rating']) && is_array($_REQUEST['rating']))
				$rating_ids = array_keys($_REQUEST['rating']);
			else
				$rating_ids = array($rating_id);
			$listing_id = SJB_Rating::getListingSIDByRatingSID($rating_ids[0]);
			$listing_id = $listing_id[0]['listing_id'];

			switch ($action)
			{
				case 'delete':
					foreach ($rating_ids as $rating_id)
						SJB_Rating::deleteRating($rating_id);
					break;
			}

			header('Location: ' . SJB_System::getSystemSettings('SITE_URL') . '/listing-rating/?listing_id=' . $listing_id);
			exit;
		}

		if (!is_null($listing_id) && SJB_Settings::getSettingByName('show_rates') == 1) {
			$rating = SJB_Rating::getRatingListing($listing_id);
			$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
			$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listingInfo['listing_type_sid']);

			$template_processor->assign('rating', $rating);
			$template_processor->assign('listing_id', $listing_id);
			$template_processor->assign('rating_num', count($rating));
			$template_processor->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));

			$template_processor->display('listing_rating.tpl');
		}
	}
}
