<?php

class SJB_Classifieds_SuggestedJobs extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$count_listing = SJB_Request::getVar('count_listing', 10, null, 'int');
		$current_user = SJB_UserManager::getCurrentUser();

		if (SJB_UserManager::isUserLoggedIn()) {
			$lastAddedListing = SJB_ListingManager::getLastAddedListingByUserSID($current_user->getSID());
			if ($lastAddedListing) {
				$properties = $current_user->getProperties();
				$phrase['title'] = $lastAddedListing->getPropertyValue('Title');
				foreach ($properties as $property) {
					if ($property->getType() == 'location') {
						$fields = $property->type->child;
						$childProperties = $fields->getProperties();
						foreach ($childProperties as $childProperty) {
							if (in_array($childProperty->getID(), array('City', 'State', 'Country'))) {
								$value = $childProperty->getValue();
								switch ($childProperty->getType()) {
									case 'list':
										if ($childProperty->getID() == 'State') {
											$displayAS = $childProperty->display_as;
											$displayAS = $displayAS?$displayAS:'state_name';
											$listValues = SJB_StatesManager::getStatesNamesByCountry(false, true, $displayAS);
										}
										else
											$listValues = $childProperty->type->list_values;
										foreach ($listValues as $values) {
											if ($value == $values['id'])
												$phrase[strtolower($childProperty->getID())] = $values['caption'];
										}
										break;
									default:
										$phrase[strtolower($childProperty->getID())] = $value;
										break;
								}
							}
						}
					}
				}
				$phrase = array_diff($phrase, array(''));
				$phrase = implode(" ", $phrase);

				$listing_type_id = "Job";

				$request['action'] = 'search';
				$request['listing_type']['equal'] = $listing_type_id;
				$request['default_listings_per_page'] = $count_listing;
				$request['default_sorting_field'] = "activation_date";
				$request['default_sorting_order'] = "DESC";
				$request['keywords']['relevance'] = $phrase;
				$searchResultsTP = new SJB_SearchResultsTP($request, $listing_type_id, array('field'=>'keywords', 'value'=>$phrase));
				$tp = $searchResultsTP->getChargedTemplateProcessor();
			}
			$tp->display('suggested_jobs.tpl');
		}
	}
}
