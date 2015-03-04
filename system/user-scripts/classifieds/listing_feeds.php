<?php

class SJB_Classifieds_ListingFeeds extends SJB_Function
{
	public function execute()
	{
		// xml-feeds table
		$feedsTable = 'listing_feeds';
		$feed_mimetype = 'text/xml';

		$feedId = SJB_Request::getVar('feedId', '');
		$searchSID = SJB_Request::getVar('searchSID', false);

		$feed = SJB_DB::query("SELECT * FROM {$feedsTable} WHERE `sid` = ?n", $feedId);
		$feed = array_pop($feed);

		if (empty($feed) && $searchSID === false) {
			$tp = SJB_System::getTemplateProcessor();
			$errors[] = 'RSS is not exists';
			$template = 'feed_error.tpl';
			$tp->assign('errors', $errors);
		} else {
			if ($searchSID) {
				$template = 'feed_saved_search.tpl';
				$count_listings = SJB_Request::getVar('count_listings', 10);

				if ($count_user_defined = SJB_Request::getVar('count_listings', false, 'GET')) {
					$count_listings = $count_user_defined;
				}

				// get saved search results for feed
				$searches = SJB_SavedSearches::getSavedJobAlertFromDBBySearchSID($searchSID);
				$searches = array_pop($searches);

				$listing_type_id = null;
				foreach ($searches['data']['listing_type'] as $val) {
					$listing_type_id = $val;
					break;
				}

				$searches['data']['default_sorting_field'] = 'activation_date';
				$searches['data']['default_sorting_order'] = 'DESC';
				$searches['data']['default_listings_per_page'] = $count_listings;

				$searchResultsTP = new SJB_SearchResultsTP($searches['data'], $listing_type_id);
				$tp = $searchResultsTP->getChargedTemplateProcessor();

				// TODO: нужно абстрагировать получение переменной из шаблонизатора
				$tp->assign("listings", $tp->getVariable('listings')->value);
				$tp->assign('listing_type_id', $listing_type_id);
				$tp->assign("search_name", $searches['name']);
				$tp->assign("feed", $feed);
				$tp->assign("query_string", htmlspecialchars($_SERVER['QUERY_STRING']));
				$tp->assign("lastBuildDate", date('D, d M Y H:i:s'));

			} else {
				$template = $feed['template'];
				$count_listing = $feed['count'];
				$feed_type = $feed['type'];
				$feed_mimetype = $feed['mime_type'];

				if ($count_listing == 0) {
					$count_listing = 1000000;
				}

				$listing_type = SJB_ListingTypeManager::getListingTypeIDBySID($feed_type);

				$searches['data']['listing_type']['equal'] = $listing_type;
				$searches['data']['default_sorting_field'] = 'activation_date';
				$searches['data']['default_sorting_order'] = 'DESC';
				$searches['data']['default_listings_per_page'] = $count_listing;
				$searchResultsTP = new SJB_SearchResultsTP($searches['data'], $listing_type);
				$tp = $searchResultsTP->getChargedTemplateProcessor();

				$tp->assign('feed', $feed);
				$tp->assign('count_listing', $count_listing);
				$tp->assign('lastBuildDate', date('D, d M Y H:i:s'));
			}
		}
		
		for ($i = 0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
		
		header('Content-Type: ' . $feed_mimetype);

		$tp->display($template);
		exit();
	}
}