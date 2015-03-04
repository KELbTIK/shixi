<?php

class SJB_Classifieds_LatestListings extends SJB_Function
{
	public function execute()
	{
		$template = SJB_Request::getVar('template', 'latest_listings.tpl');
		if (isset($this->params['mime_type'])) {
			header("Content-type: " . $this->params['mime_type']);
		}

		$listing_type = SJB_Request::getVar('listing_type', 'Job');
		$items_count = SJB_Request::getVar('items_count', 1);

		$searches['data']['listing_type']['equal'] = $listing_type;
		$searches['data']['default_sorting_field'] = 'activation_date';
		$searches['data']['default_sorting_order'] = 'DESC';
		$searches['data']['default_listings_per_page'] = $items_count;

		$searchResultsTP = new SJB_SearchResultsTP($searches['data'], $listing_type);
		$searchResultsTP->setLimit($items_count);

		$tp = $searchResultsTP->getChargedTemplateProcessor();
		$tp->assign('lastBuildDate', date('D, d M Y H:i:s'));
		$tp->assign('number_of_cols', SJB_Request::getVar('number_of_cols', 1));
		$tp->display($template);
	}
}