<?php

class SJB_Miscellaneous_SitemapGenerator extends SJB_Function
{
	public function execute()
	{
		$list_of_pages = SJB_PageManager::get_pages('user');
		$scriptPath = explode(SJB_System::getSystemSettings("SYSTEM_URL_BASE"), __FILE__);
		$scriptPath = array_shift($scriptPath);

		$handle = fopen($scriptPath . "sitemap.xml", "w");
		$text = '<?xml version="1.0" encoding="UTF-8"?>
					<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		fwrite($handle, $text);
		foreach ($list_of_pages as $page) {
			if ($page['uri'] == '/display-job/') {
				$request['action'] = 'search';
				$request['listing_type']['equal'] = 'Job';
				$found_listings_sids = $this->searchListings($request, 'Job');
				foreach ($found_listings_sids as $sid) {
					$listing_info = SJB_ListingManager::getListingInfoBySID($sid);
					$title = htmlspecialchars($listing_info['Title']);
					$title = $sid . "/" . preg_replace("/[\\/\\\:*?\"<>|%#$\s]/", "_", $title) . ".html";
					$text = '
					<url>
					<loc>' . SJB_System::getSystemSettings("SITE_URL") . $page['uri'] . $title . '</loc>
					<lastmod>' . date('Y-m-d') . '</lastmod>
					<changefreq>daily</changefreq>
					<priority>1</priority>
					</url>';

					fwrite($handle, $text);
				}
			} elseif ($page['uri'] == '/display-resume/') {
				$request['action'] = 'search';
				$request['listing_type']['equal'] = 'Resume';
				$found_listings_sids = $this->searchListings($request, 'Resume');
				foreach ($found_listings_sids as $sid) {
					$listing_info = SJB_ListingManager::getListingInfoBySID($sid);
					$title = htmlspecialchars($listing_info['Title']);
					$title = $sid . "/" . preg_replace("/[\\/\\\:*?\"<>|%#$\s]/", "_", $title) . ".html";
					$text = '
						<url>
						<loc>' . SJB_System::getSystemSettings("SITE_URL") . $page['uri'] . $title . '</loc>
						<lastmod>' . date('Y-m-d') . '</lastmod>
						<changefreq>daily</changefreq>
						<priority>1</priority>
						</url>';

					fwrite($handle, $text);
				}
			} elseif ($page['uri'] != '/callback/') {
				$text = '
					<url>
					<loc>' . SJB_System::getSystemSettings("SITE_URL") . $page['uri'] . '</loc>
					<lastmod>' . date('Y-m-d') . '</lastmod>
					<changefreq>daily</changefreq>
					<priority>1</priority>
					</url>';

				fwrite($handle, $text);
			}
		}
		$text = '
			</urlset>';
		fwrite($handle, $text);
		fclose($handle);
	}

	private function searchListings($requested_data, $listing_type_id)
	{
		$criteria_saver = new SJB_ListingCriteriaSaver();
		$found_listings_sids = array();
		$listing_type_sid = !empty($listing_type_id) ? SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id) : 0;
		$requireApprove = SJB_ListingTypeManager::getWaitApproveSettingByListingType($listing_type_sid);
		if ($requireApprove) {
			$requested_data['status']['equal'] = 'approved';
		}
		$requested_data['active']['equal'] = '1';
		$criteria_saver->setSessionForCriteria(array_merge($criteria_saver->getCriteria(), $requested_data));
		$found_listings_sids = $this->getListingSidCollectionFromRequest($requested_data, $listing_type_sid, $criteria_saver);
		return $found_listings_sids;
	}

	private function getListingSidCollectionFromRequest($requested_data, $listing_type_sid, $criteria_saver)
	{
		$listing = new SJB_Listing(array(), $listing_type_sid);
		$id_alias_info = $listing->addIDProperty();
		$listing->addActivationDateProperty();
		$username_alias_info = $listing->addUsernameProperty();
		$listing_type_id_info = $listing->addListingTypeIDProperty();
		$listing->addCompanyNameProperty();

		// select only accessible listings by user sid
		// see SearchCriterion.php, AccessibleCriterion class
		$requested_data['access_type'] = array('accessible' => SJB_UserManager::getCurrentUserSID());

		$criteria = $criteria_saver->getCriteria();
		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData(array_merge($criteria, $requested_data), $listing);

		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($id_alias_info);
		$aliases->addAlias($username_alias_info);
		$aliases->addAlias($listing_type_id_info);

		$sortingFields = array();
		$orderInfo = $criteria_saver->getOrderInfo();
		$property = $listing->getProperty($orderInfo['sorting_field']);
		if (!empty($property) && $property->isSystem()) {
			$sortingFields = array(
				'priority' => 'desc',
				$orderInfo['sorting_field'] => $orderInfo['sorting_order']
			);
		}
		$searcher = new SJB_ListingSearcher();
		return $searcher->getObjectsSIDsByCriteria($criteria, $aliases, $sortingFields);
	}
}
