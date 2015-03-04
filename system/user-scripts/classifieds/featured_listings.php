<?php

class SJB_Classifieds_FeaturedListings extends SJB_Function
{
	public function execute()
	{
		$template = SJB_Request::getVar('template', 'featured_listings.tpl');
		$listingType = SJB_Request::getVar('listing_type', 'Job');

		$searches['data']['listing_type']['equal'] = $listingType;
		$searches['data']['featured']['equal'] = 1;
		$searches['data']['default_listings_per_page'] = SJB_Request::getVar('items_count', 1);
		$searches['data']['sorting_field'] = 'featured_last_showed';
		$searches['data']['default_sorting_field'] = 'featured_last_showed';
		$searches['data']['default_sorting_order'] = 'ASC';
		$searches['data']['sorting_order'] = 'ASC';

		// фичерные листинги кешировать не будем
		$cache = SJB_Cache::getInstance();
		$caching = $cache->getOption('caching');
		$cache->setOption('caching', false);

		$searchResultsTP = new SJB_SearchResultsTP($searches['data'], $listingType);
		$searchResultsTP->setLimit(SJB_Request::getVar('items_count', 1));
		$tp = $searchResultsTP->getChargedTemplateProcessor();

		$featuredListingSIDs = $searchResultsTP->getListingSidCollectionForCurrentPage();
		if ($featuredListingSIDs) {
			SJB_DB::query('UPDATE `listings` SET `featured_last_showed` = NOW() WHERE `sid` in (?w)', implode(',', $featuredListingSIDs));
			SJB_Statistics::addSearchStatistics($featuredListingSIDs, $listingType);
		}
		$cache->setOption('caching', $caching);

		$tp->assign('number_of_cols', SJB_Request::getVar('number_of_cols', 1));
		$tp->display($template);
	}
}