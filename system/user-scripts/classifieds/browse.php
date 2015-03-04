<?php

class SJB_Classifieds_Browse extends SJB_Function
{
	var $parameters = array();
	
	public function isAccessible()
	{
		$browseUrl = SJB_Request::getVar('browseUrl', false);
		if ($browseUrl) {
			$parameters = SJB_BrowseDBManager::getBrowseParametersByUri($browseUrl);
			$this->parameters = array_merge($_REQUEST, unserialize($parameters));
		} else {
			$this->parameters = $_REQUEST;
		}
		
		$listingTypeId = SJB_Request::getVar('listing_type_id', '');
		$browseManager = SJB_ObjectMother::createBrowseManager($listingTypeId, $this->parameters);
		$params = $browseManager->getParams();
		if (empty($params))
			return true;
		$this->setPermissionLabel('view_' . strtolower($listingTypeId) . '_search_results');
		return parent::isAccessible();
	}

	public function execute()
	{
		$uri = parse_url($_SERVER['REQUEST_URI']);
		if (!preg_match("/\/$/", $uri['path'])) {
			$uri = parse_url($_SERVER['REQUEST_URI']);
			$query = isset($uri['query']) ? '?' . $uri['query'] : '';
			SJB_HelperFunctions::redirect($uri['path'] . '/' . $query);
		} else {
			$uri = SJB_Request::getVar('browseUrl', $this->getUri());
		}
		
		$listingTypeId = SJB_Request::getVar('listing_type_id', '');
		$browseManager = SJB_ObjectMother::createBrowseManager($listingTypeId, $this->parameters);
		
		$browseItems = array();
		if ($browseManager->canBrowse()) {
			if (SJB_Settings::getValue('enableBrowseByCounter')) {
				$browseItems = $browseManager->getItemsFromDB($uri, true);
			} else {
				$browseItems = $browseManager->getItems($this->parameters, true);
			}
		}
		
		$tp = $this->getTemplateProcessor($browseManager, $listingTypeId);
		$tp->assign('browseItems', $browseItems);
		$tp->assign('recordsNumToDisplay', SJB_Request::getVar('recordsNumToDisplay', 20));
		$tp->assign('user_page_uri', $uri);
		$tp->assign('sitePageUri', SJB_HelperFunctions::getSiteUrl() . $this->getUri());
		$tp->assign('browse_level', $browseManager->getLevel() + 1);
		$tp->assign('browse_navigation_elements', $browseManager->getNavigationElements($uri));
		$tp->display(SJB_Request::getVar('browse_template', 'browse_items_and_results.tpl'));
	}

	protected function getUri()
	{
		$globalTemplateVariables = SJB_System::getGlobalTemplateVariables();
		$uri = $globalTemplateVariables['GLOBALS']['user_page_uri'];
		return preg_match("/\/$/", $uri) ? $uri : $uri . '/';
	}

	/**
	 * @param SJB_BrowseManager $browseManager
	 * @param $listingTypeId
	 * @return SJB_TemplateProcessor
	 */
	protected function getTemplateProcessor($browseManager, $listingTypeId)
	{
		if ($browseManager->canBrowse()) {
			$browsing_meta_data = $browseManager->getBrowsingMetaData();
			$tp = SJB_System::getTemplateProcessor();
			$tp->assign('METADATA', $browsing_meta_data);
		}
		else {
			$requestData = $browseManager->getRequestDataForSearchResults();

			$requestData['default_listings_per_page'] = 10;
			$requestData['default_sorting_field'] = "activation_date";
			$requestData['default_sorting_order'] = "DESC";
			if (isset($_REQUEST['restore']))
				$requestData['restore'] = 1;
			else
				$requestData['action'] = 'search';
			if (isset($_REQUEST['searchId']))
				$requestData['searchId'] = SJB_Request::getVar('searchId');
			if (isset($_REQUEST['sorting_field']))
				$requestData['sorting_field'] = $_REQUEST['sorting_field'];
			if (isset($_REQUEST['sorting_order']))
				$requestData['sorting_order'] = $_REQUEST['sorting_order'];
			if (isset($_REQUEST['listings_per_page']))
				$requestData['listings_per_page'] = SJB_Request::getVar('listings_per_page', null);
			$requestData['page'] = SJB_Request::getVar('page', null);

			// fix for mapView in search results
			$requestData['view'] = SJB_Request::getVar('view', null);
			$useRefine = false;
			if ($requestData['view'] == 'map') {
				$useRefine = true;
			}

			$searchResultsTP = new SJB_SearchResultsTP($requestData, $listingTypeId, false, $useRefine);
			$searchResultsTP->usePriority(true);
			$tp = $searchResultsTP->getChargedTemplateProcessor();
			SJB_Statistics::addSearchStatistics($searchResultsTP->getListingSidCollectionForCurrentPage(), $listingTypeId);
			$tp->assign('errors', $searchResultsTP->pluginErrors);
			$tp->assign('listing_type', $listingTypeId);
		}
		$tp->assign('columns', SJB_Request::getVar('columns', 1));

		return $tp;
	}

}
