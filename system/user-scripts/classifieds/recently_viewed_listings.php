<?php

class SJB_Classifieds_RecentlyViewedListings extends SJB_Function
{
	public function execute()
	{
		$count_listing = SJB_Request::getVar('count_listing', 10);
		$listings_structure = array();
		$listing_structure_meta_data = array();
		$tp = SJB_System::getTemplateProcessor();

		if (SJB_UserManager::isUserLoggedIn()) {
			$user_sid = SJB_UserManager::getCurrentUserSID();
			$viewed_listings = SJB_UserManager::getRecentlyViewedListingsByUserSid($user_sid, $count_listing);
			if (count($viewed_listings)) {
				foreach ($viewed_listings as $viewed_listing) {
					$listing = SJB_ListingManager::getObjectBySID($viewed_listing['listing_sid']);
					if (empty($listing)) {
						continue;
					}
					$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
					$listings_structure[] = $listing_structure;
					if (isset($listing_structure['METADATA'])) {
						$listing_structure_meta_data = array_merge($listing_structure_meta_data, $listing_structure['METADATA']);
					}
				}
				$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
				$tp->assign("METADATA", array("listing" => $metaDataProvider->getMetaData($listing_structure_meta_data)));
				$tp->assign("listings", $listings_structure);
			}
			$tp->display('recently_viewed_listings.tpl');
		}
	}
}
