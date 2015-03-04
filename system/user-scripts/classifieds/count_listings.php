<?php

class SJB_Classifieds_CountListings extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listing_types = SJB_ListingTypeManager::getAllListingTypesInfo();

		$countListings = array();
		foreach ($listing_types as $type) {
			$requested_data = array();
			$requested_data['action'] = 'search';
			$requested_data['active']['equal'] = '1';
			$requested_data['listing_type']['equal'] = $type['id'];
			$requireApprove = SJB_ListingTypeManager::getWaitApproveSettingByListingType($type['sid']);
			if ($requireApprove)
				$requested_data['status']['equal'] = 'approved';

			$listing = new SJB_Listing(array(), $type['sid']);
			$id_alias_info = $listing->addIDProperty();
			$listing->addActivationDateProperty();
			$username_alias_info = $listing->addUsernameProperty();
			$listing_type_id_info = $listing->addListingTypeIDProperty();
			$listing->addCompanyNameProperty();

			if ($type['id'] == 'Resume')
				$requested_data['access_type'] = array('accessible' => SJB_UserManager::getCurrentUserSID());

			$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($requested_data, $listing);

			$aliases = new SJB_PropertyAliases();
			$aliases->addAlias($id_alias_info);
			$aliases->addAlias($username_alias_info);
			$aliases->addAlias($listing_type_id_info);

			$searcher = new SJB_ListingSearcher();
			$countListings[$type['id']] = $searcher->countRowsByCriteria($criteria, $aliases);
		}
		$tp->assign('listings_types', $countListings);
		$tp->display('count_listings.tpl');
	}
}