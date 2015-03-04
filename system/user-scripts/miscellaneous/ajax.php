<?php

class SJB_Miscellaneous_Ajax extends SJB_Function
{
	public function execute()
	{
		$action = SJB_Request::getVar('action', null);

		// set for debug call via GET request
		if (empty($action)) {
			$action = SJB_Request::getVar('action', '');
		}

		switch ($action) {
			case 'rate' :
				$this->jsRate();
				break;

			case 'comment' :
				$this->add_comment();
				break;

			case 'cookiePreferences' :
				$this->changeCookiePreferences();
				break;

			case 'request_for_listings':
				SJB_AjaxRequests::requestToListingsProviders();
				break;

			case 'get_blog_content':
				SJB_AjaxRequests::getBlogContent();
				break;

			case 'get_refine_search_block':
				SJB_AjaxRequests::getRefineSearchBlock();
				break;

			default :
				exit();
		}
		exit();
	}

	private function add_comment()
	{
		if (!SJB_UserManager::isUserLoggedIn())
			exit();

		$last = 1;
		$message =  SJB_Request::getVar('message', '', SJB_Request::METHOD_POST);
		$listing_id = SJB_Request::getInt('listing', 0, SJB_Request::METHOD_POST);

		$template_processor = SJB_System::getTemplateProcessor();
		$user_info = SJB_UserManager::getCurrentUserInfo();

		$comment = new SJB_Comment(array_merge(
			array('message' => $message),
			array('user_id' => $user_info['sid'])), $listing_id);

		/** @var SJB_ObjectProperty $property */
		foreach($comment->getProperties() as $property) {
			$validation = $property->isValid();
			if (true !== $validation) {
				$validation = 'COMMENT_' . $validation;
				$template_processor->assign('ERRORS', array($validation => true));
				$template_processor->display('../classifieds/error.tpl');
				exit;
			}
		}
		
		SJB_CommentManager::saveComment($comment);

		$comment_array = array(
			'id' => $comment->getSID(),
			'message' => htmlentities($message, ENT_QUOTES, "UTF-8"),
			'user' => array(
				'email' => $user_info['email'],
				'username' => $user_info['username']),
				'added' => date('d.m.Y H:M'));

		$template_processor->assign('iteration_last', $last);
		$template_processor->assign('comment', $comment_array);
		$template_processor->display('../classifieds/listing_comments_item.tpl');
	}

	private function changeCookiePreferences()
	{
		$cookiePreferencesValue = SJB_Request::getVar('cookiePreferencesValue', null);
		if ($cookiePreferencesValue && in_array($cookiePreferencesValue, array('System', 'Functional', 'Advertising'))) {
			$_COOKIE['cookiePreferences'] = $cookiePreferencesValue;
			setcookie('cookiePreferences', $cookiePreferencesValue, time() + 30 * 24 * 3600, '/');
			echo true;
		}
	}

	private function jsRate()
	{
		if (!SJB_UserManager::isUserLoggedIn())
			exit();

		$listing_sid = SJB_Request::getInt('listing', 0, SJB_Request::METHOD_POST);
		$rate = SJB_Request::getInt('rate', 0, SJB_Request::METHOD_POST);
		$new_rating = SJB_Rating::setRaiting($rate, $listing_sid, SJB_UserManager::getCurrentUserSID());
		if (isset ($new_rating ['rating'])) {
			echo $new_rating ['rating'];
		}
	}
}




class SJB_AjaxRequests
{
	private static $i18n = null;


	/**
	 * Method gets request for listings to listings providers and dispatch individual events for providers plugins.
	 * After dispatch will be created usual listings structure and than will be returned as JSON, marked to jQuery callback.
	 *
	 * @static
	 * @return mixed
	 */
	public static function requestToListingsProviders()
	{
		// get list of listing providers
		$listingProviders = array();
		SJB_Event::dispatch('registerListingProviders', $listingProviders, true);

		$listing_type_id = isset($_REQUEST['listing_type']['equal'])? $_REQUEST['listing_type']['equal']: SJB_Session::getValue('listing_type_id');
		if ($listing_type_id) {
			$_REQUEST['listing_type']['equal'] = $listing_type_id;
		}

		$searchResultsTP = new SJB_SearchResultsTP($_REQUEST, $listing_type_id);
		// manually create listing_search_structure (in main search for listings this called in getChargedTemplateProcessor)
		// This need to properly work of listings providers per page search
		$searchResultsTP->listing_search_structure = $searchResultsTP->criteria_saver->createTemplateStructureForSearch();

		// check, if requested for specified providerName
		$specifiedProviderName = isset($_REQUEST['provider']) ? $_REQUEST['provider'] : '';
		
		// dispatch event to given listings providerName
		$listingsStructure = array();
		foreach ($listingProviders as $providerName) {
			if (!empty($specifiedProviderName)) {
				if ($providerName != $specifiedProviderName) {
					continue;
				}
			}
			try {
				SJB_Event::dispatch($providerName . 'BeforeGenerateListingStructure', $searchResultsTP, true);
			} catch(Exception $e){
				if (strpos($e->getMessage(), 'simplyHiredPlugin: Failed to read XML from url -') !== false) {
					$a = explode('- ', $e->getMessage());
					$searchResultsTP->pluginErrors['SIMPLY_HIRED_XML_READ_FAILED'] = $a[1];
				}
			}
			// fill listings structure with provider listings
			SJB_Event::dispatch($providerName . 'AfterGenerateListingStructure', $listingsStructure, true);
		}

		$tp = $searchResultsTP->getChargedTemplateProcessorForListingStructure($listingsStructure);

		$tp->display('../classifieds/search_results_jobs_listings.tpl');
	}


	/**
	 * Method dispatch event to get blog content and display it
	 * @static
	 */
	public static function getBlogContent()
	{
		$tp = SJB_System::getTemplateProcessor();
		$content = '';
		SJB_Event::dispatch('DisplayBlogContent', $content, true);
		$tp->assign('content', $content);
		$tp->display('blog_page.tpl');
	}

	public static function getRefineSearchBlock()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listingTypeId = SJB_Request::getVar('listing_type');
		if (!isset($listingTypeId['equal'])) {
			$_REQUEST['listing_type']['equal'] = SJB_Session::getValue('listing_type_id');
		}

		$searchResultsTP = new SJB_SearchResultsTP($_REQUEST, $listingTypeId['equal']);
		$searchCriteria = $searchResultsTP->getCriteriaSaver()->getCriteria();
		if (SJB_Request::getVar('showRefineFields', false)) {
			$refineFields = SJB_RefineSearch::getRefineFieldsByCriteria($searchResultsTP, $searchCriteria);
			$tp->assign('refineFields', $refineFields);
		}
		$currentSearch = SJB_RefineSearch::getCurrentSearchByCriteria($searchCriteria);
		$tp->assign('currentSearch', $currentSearch);
		$tp->assign('searchId', SJB_Request::getVar('searchId'));
		$tp->assign('view', SJB_Request::getVar('view'));
		$tp->display('../classifieds/search_results_refine_block.tpl');
	}

}