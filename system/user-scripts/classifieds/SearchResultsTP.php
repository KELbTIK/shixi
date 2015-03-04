<?php

class SJB_SearchResultsTP
{
	var $requested_data;
	var $listing_type_sid;
	var $listing_type_id;
	private $listingsLimit = 0;
	
	/**
	 * SJB_ListingCriteriaSaver
	 *
	 * @var SJB_ListingCriteriaSaver
	 */
	var $criteria_saver;
	var $found_listings_sids;
	var $listing_search_structure;
	var $searchId;
	var $relevance;
	var $useRefine = false;
	private $usePriority = false;

    public $pluginErrors = array();

	function _filter_data(&$array, $key, $pattern)
	{
		if (isset($array[$key])) {
			if (is_array($array[$key])) {
				foreach ($array[$key] as $itemNumber => $filter) {
					if (!preg_match($pattern, $filter)) {
						unset($array[$key][$itemNumber]);
					}
				}
				if (!$array[$key]) {
					unset($array[$key]);
				}
				
			} elseif (!preg_match($pattern, $array[$key])) {
				unset($array[$key]);
			}
		}
	}

	public function usePriority($usePriority)
	{
		$this->usePriority = $usePriority;
	}

	public function __construct($requested_data, $listing_type_id, $relevance = false, $useRefine = false)
	{
 		$this->_filter_data($requested_data, 'sorting_field', '/^[_\w\d]+$/');
 		$this->_filter_data($requested_data, 'sorting_order', '/(^DESC$)|(^ASC$)/i');
 		$this->_filter_data($requested_data, 'default_sorting_field', '/^[_\w\d]+$/');
 		$this->_filter_data($requested_data, 'default_sorting_order', '/(^DESC$)|(^ASC$)/i');

		$this->requested_data = $requested_data;
		$this->listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$this->listing_type_id = $listing_type_id;
		$this->relevance = $relevance;
		$this->useRefine = $useRefine;
		$this->searchId = microtime(true);
		if (isset($requested_data['searchId'])) {
			$this->searchId = strip_tags($requested_data['searchId']);
		}
		$this->criteria_saver = new SJB_ListingCriteriaSaver($this->searchId);
		$this->found_listings_sids = array();
	}

	function getChargedTemplateProcessor()
	{
		$order_info = $this->criteria_saver->getOrderInfo();

		if (isset($this->requested_data['sorting_field'], $this->requested_data['sorting_order'])) {
			$order_info = array(	'sorting_field'	=> $this->requested_data['sorting_field'],
									'sorting_order'	=> $this->requested_data['sorting_order']);
		}
		if (!isset($order_info['sorting_field']) && !isset($order_info['sorting_order']) && SJB_Request::getVar('searchId', false)) {
			$this->requested_data['sorting_field'] = $order_info['sorting_field'] = !empty($this->requested_data['default_sorting_field']) ? $this->requested_data['default_sorting_field'] : null;
			$this->requested_data['sorting_order'] = $order_info['sorting_order'] = !empty($this->requested_data['default_sorting_order']) ? $this->requested_data['default_sorting_order'] : null;
		}
		
		$this->criteria_saver->setSessionForOrderInfo($order_info);

		if ( isset($_REQUEST['show_brief_or_detailed']) ) {
			$show_brief_or_detailed = $_REQUEST['show_brief_or_detailed'];
		} elseif ($this->criteria_saver->getBriefOrDetailedSearch()) {
			$show_brief_or_detailed = $this->criteria_saver->getBriefOrDetailedSearch();			
		} else {
			$show_brief_or_detailed = 'detailed';
		}
		$this->criteria_saver->setSessionForBriefOrDetailedSearch($show_brief_or_detailed);

		$requireApprove = SJB_ListingTypeManager::getWaitApproveSettingByListingType($this->listing_type_sid);
		if ( $requireApprove ) {
		    $this->requested_data['status']['equal'] = 'approved';
    	}

    	$this->requested_data['active']['equal'] = '1';
		$this->criteria_saver->setSessionForCriteria(array_merge(
			array('active' => array('equal' => '1')), // Duplicate for sql query optimization
			$this->criteria_saver->getCriteria(),
			$this->requested_data));
		$this->found_listings_sids = $this->_getListingSidCollectionFromRequest();

		$lpp = $this->criteria_saver->getListingsPerPage();
		if (!empty($this->requested_data['default_listings_per_page']) && empty($lpp))
			$this->criteria_saver->setSessionForListingsPerPage(intval($this->requested_data['default_listings_per_page']));
		if (isset($this->requested_data['listings_per_page']))
			$this->criteria_saver->setSessionForListingsPerPage(intval($this->requested_data['listings_per_page']));

		$this->criteria_saver->setSessionForCurrentPage(1);
		if (isset($this->requested_data['page']))
			$this->criteria_saver->setSessionForCurrentPage($this->requested_data['page']);

		$this->criteria_saver->setSessionForObjectSIDs($this->found_listings_sids);
		$this->listing_search_structure = $this->criteria_saver->createTemplateStructureForSearch();

		if (empty($this->listing_search_structure['sorting_field'])) {
	 		$this->listing_search_structure['sorting_field'] = 'activation_date';
	 	}

        try {
	 	    SJB_Event::dispatch('beforeGenerateListingStructure', $this, true);
        } catch(Exception $e){
            if (strpos($e->getMessage(), 'simplyHiredPlugin: Failed to read XML from url -') !== false) {
                $a = explode('- ', $e->getMessage());
                $this->pluginErrors['SIMPLY_HIRED_XML_READ_FAILED'] = $a[1];
            }
        }
		$listings_structure = array();
		if ($this->listing_search_structure['listings_number'] > 0) {
			$currentUserSID = SJB_UserManager::getCurrentUserSID();
			$isUserLoggedIn = SJB_UserManager::isUserLoggedIn();
			$listings_structure = new SJB_Iterator;
			if (!empty($this->requested_data['view']) && $this->requested_data['view'] == 'map') {
				$listings_structure->setView('map');
			}
			$listings_structure->setListingsSids($this->getListingSidCollectionForCurrentPage());
	 		$listings_structure->setListingTypeSID($this->listing_type_sid);
	 		$listings_structure->setCriteria($this->criteria_saver->criteria);
	 		$listings_structure->setUserLoggedIn($isUserLoggedIn);
	 		$listings_structure->setCurrentUserSID($currentUserSID);
		}

		SJB_Event::dispatch('afterGenerateListingStructure', $listings_structure, true);
		return $this->_getChargedTemplateProcessor($listings_structure);
	}

	/**
	 * use this function to create listings structure for plugin listings (Indeed, Beyond etc.)
	 * This will use for ajax request from search results page
	 *
	 * @param array $listingsStructure
	 * @return SJB_TemplateProcessor
	 */
	public function getChargedTemplateProcessorForListingStructure($listingsStructure = array())
	{
		return $this->_getChargedTemplateProcessor($listingsStructure);
	}

	function getListingCollectionStructure($sorted_found_listings_sids_for_current_page)
	{
	    $listings_structure = array();
		$currentUserSID = SJB_UserManager::getCurrentUserSID();
		$isUserLoggedIn = SJB_UserManager::isUserLoggedIn();
		foreach ($sorted_found_listings_sids_for_current_page as $sid) {
			$listing = SJB_ListingManager::getObjectBySID($sid);
			$listing->addPicturesProperty();
			$listings_structure[$listing->getID()] = SJB_ListingManager::createTemplateStructureForListing($listing);
			$listings_structure[$listing->getID()] = SJB_ListingManager::newValueFromSearchCriteria($listings_structure[$listing->getID()], $this->criteria_saver->criteria);
			if ($isUserLoggedIn) {
				$listings_structure[$listing->getID()]['saved_listing'] = SJB_SavedListings::getSavedListingsByUserAndListingSid($currentUserSID, $listing->getID());
			}
		}

		return $listings_structure;
	}
	
	function getListingSidCollectionForCurrentPage()
	{
		if (empty($this->listing_search_structure['listings_per_page']))
			return $this->found_listings_sids;
		
		$this->_normalizeCurrentPage();
		$listing_sids_by_page =  array_chunk($this->found_listings_sids, $this->listing_search_structure['listings_per_page'], true);
		
		// check array for listings
		if (empty($listing_sids_by_page)) {
			return array();
		}
		return $listing_sids_by_page[$this->listing_search_structure['current_page'] - 1];
	}
	
	function _normalizeCurrentPage()
	{
		if ($this->listing_search_structure['current_page'] > $this->listing_search_structure['pages_number'])
			$this->listing_search_structure['current_page'] = $this->listing_search_structure['pages_number'];
		if ($this->listing_search_structure['current_page'] < 1)
			$this->listing_search_structure['current_page'] = 1;
	}

	public function getLocationProperty($fieldName, $listing, $sortingField)
	{
		$property = $listing->getProperty($fieldName);
		if ($property && $fields = @$property->type->child) {
			$sortingFieldName = str_replace($fieldName."_", '', $sortingField);
			$property = $fields->getProperty($sortingFieldName);
			$property->setID($sortingField);
		}
		return $property;
	}
	
	function _getChargedTemplateProcessor(&$listings_structure)
	{
		$tp = SJB_System::getTemplateProcessor();
		$searchCriteria = $this->criteria_saver->getCriteria();
		$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($this->listing_type_sid);
		if (!empty($listing_type_info['show_brief_or_detailed'])) {
			$is_show_brief_or_detailed = $listing_type_info['show_brief_or_detailed'];
			$show_brief_or_detailed = $this->criteria_saver->getBriefOrDetailedSearch();
			$tp->assign("is_show_brief_or_detailed", $is_show_brief_or_detailed);
			$tp->assign("show_brief_or_detailed", $show_brief_or_detailed);
		}
		$keywordsHighlight = '';
		if (isset($searchCriteria['keywords']) && SJB_System::getSettingByName('use_highlight_for_keywords')) {
			foreach ($searchCriteria['keywords'] as $type => $keywords) {
				$keywordsTrim = trim($keywords);
				if (!empty($keywordsTrim)) {
					switch ($type) {
						case 'like':
						case 'exact_phrase':
							$keywordsHighlight = json_encode($keywords);
							break;
						case 'all_words':
						case 'any_words':
							$keywordsHighlight = json_encode(explode(' ', $keywords));
							break;
						case 'boolean':
							$keywordsHighlight = json_encode(SJB_BooleanEvaluator::parse($keywords, true));
							break;
					}
				}
			}
		}
		$view = !empty($this->requested_data['view']) ? $this->requested_data['view'] : 'list';
		
		$tp->assign("keywordsHighlight", $keywordsHighlight);
		$tp->assign("sorting_field", $this->listing_search_structure['sorting_field']);
		$tp->assign("sorting_order", $this->listing_search_structure['sorting_order']);
		$tp->assign("listing_search", $this->listing_search_structure);
		$tp->assign("listings", $listings_structure);
		$tp->assign("searchId", $this->searchId);
		$tp->assign("view_on_map", SJB_System::getSettingByName('view_on_map'));
		$tp->assign("view", $view);

		$listing = new SJB_Listing(array(), $this->listing_type_sid);
		$user = new SJB_User(array());
		$listing_structure_meta_data = SJB_ListingManager::createMetadataForListing($listing, $user);
				
		if (isset($searchCriteria['username']['equal'])) {
			$userSID = SJB_UserManager::getUserSIDbyUsername($searchCriteria['username']['equal']);

			$user 		= SJB_UserManager::getObjectBySID($userSID);
			$userInfo	= !empty($user) ? SJB_UserManager::createTemplateStructureForUser($user) : null;
			$tp->assign("userInfo", $userInfo);
		}

		if (isset($searchCriteria['listing_type']['equal']) && SJB_System::getSettingByName('turn_on_refine_search_'.$searchCriteria['listing_type']['equal']) && $this->useRefine) {
			$tp->assign("refineSearch", true);
		}
		$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
		$metadata = array("listing" => $metaDataProvider->getMetaData($listing_structure_meta_data));
		$tp->assign("METADATA", $metadata);
		
		return $tp;	
	}

	function _getListingSidCollectionFromRequest()
	{
		$listing = new SJB_Listing(array(), $this->listing_type_sid);
		$id_alias_info = $listing->addIDProperty();
		$listing->addActivationDateProperty();
		$listing->addFeaturedProperty();
		$listing->addFeaturedLastShowedProperty();
		$username_alias_info = $listing->addUsernameProperty();
		$listing_type_id_info = $listing->addListingTypeIDProperty();
		$listing->addCompanyNameProperty();

		// select only accessible listings by user sid
		// see SearchCriterion.php, AccessibleCriterion class
		if ($this->listing_type_id == 'Resume')
			$this->requested_data['access_type'] = array('accessible' => SJB_UserManager::getCurrentUserSID());

		$criteria = $this->criteria_saver->getCriteria();
		if (isset($this->requested_data['PostedWithin']['multi_like'][0]) || isset($criteria['PostedWithin']['multi_like'][0])) {
			$within_period = '';
			if (isset($this->requested_data['PostedWithin']['multi_like'][0])) {
				$within_period = $this->requested_data['PostedWithin']['multi_like'][0];
				unset ($this->requested_data['PostedWithin']['multi_like']);
			}
			if (isset($criteria['PostedWithin']['multi_like'][0])) {
				$within_period = $criteria['PostedWithin']['multi_like'][0];
				unset($criteria['PostedWithin']);
			}
			$i18n = SJB_I18N::getInstance();
			$this->requested_data['activation_date']['not_less'] = $i18n->getDate(date('Y-m-d', strtotime("- {$within_period} days")));
		}

		if (isset($this->requested_data['CompanyName']['multi_like_and'][0]) || isset($criteria['CompanyName']['multi_like_and'][0])) {
			if (isset($this->requested_data['CompanyName']['multi_like_and'][0])) {
				$companyName = $this->requested_data['CompanyName']['multi_like_and'][0];
				unset($this->requested_data['CompanyName']);
			}
			if (isset($criteria['CompanyName']['multi_like_and'][0])) {
				$companyName = $criteria['CompanyName']['multi_like_and'][0];
				unset($criteria['CompanyName']);
			}
			$userName = SJB_UserManager::getUserNameByCompanyName($companyName);

			if ($userName) {
				$this->requested_data['username']['equal'] = $userName;
			}
         }

		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData(array_merge($criteria, $this->requested_data), $listing);

		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($id_alias_info);
		$aliases->addAlias($username_alias_info);
		$aliases->addAlias($listing_type_id_info);

		$sortingFields = array();
		if ($this->usePriority) {
			$sortingFields['priority'] = 'DESC';
		}
		$innerJoin = array();
		$orderInfo = $this->criteria_saver->getOrderInfo();
		if (is_array($orderInfo['sorting_field'])) {
			$requestedSortingField  = array();
			foreach ($orderInfo['sorting_field'] as $orderInfoProperty) {
				$fieldName = strstr($orderInfoProperty, '_', true);
				$id = $this->getLocationProperty($fieldName, $listing, $orderInfoProperty);
				if (!empty($id)) {
					switch ($orderInfoProperty) {
						case 'Location_State':
							$innerJoin['states'] = array(
								'stateName'   => '`states`.`state_code`',
								'join'        => 'LEFT JOIN',
								'join_field'  => 'sid',
								'join_field2' => $orderInfoProperty,
								'main_table'  => 'listings',
							);
							break;
						case 'Location_Country':
							$innerJoin['countries'] = array(
								'countryName' => '`countries`.`country_name`',
								'join'        => 'LEFT JOIN',
								'join_field'  => 'sid',
								'join_field2' => $orderInfoProperty,
								'main_table'  => 'listings',
							);
							break;
						default:
							break;
					}
					$sortingFields[$orderInfoProperty] = $orderInfo['sorting_order'];
					$requestedSortingField[] = $orderInfoProperty;
				}
				$this->requested_data['sorting_field'] = $requestedSortingField;
				$this->requested_data['sorting_order'] = $orderInfo['sorting_order'];
			}
		} else {
			$property = $listing->getProperty($orderInfo['sorting_field']);
			if (!empty($property) && $property->isSystem()) {
				$sortingFields[$orderInfo['sorting_field']] = $orderInfo['sorting_order'];
				if ($property->getID() == 'CompanyName') {
					$innerJoin['users'] = array(
						'join'        => 'INNER JOIN',
						'join_field'  => 'sid',
						'join_field2' => 'user_sid',
						'main_table'  => 'listings'
					);
				}
				$this->requested_data['sorting_field'] = $orderInfo['sorting_field'];
				$this->requested_data['sorting_order'] = $orderInfo['sorting_order'];
			} else {
				$sortingFields['activation_date'] = 'DESC';
				$this->requested_data['sorting_field'] = 'activation_date';
				$this->requested_data['sorting_order'] = 'DESC';
			}
		}
		$searcher = new SJB_ListingSearcher();

		if ($this->listingsLimit) {
			$searcher->setLimit($this->listingsLimit);
		}

		$this->listing_search_structure['sorting_field'] = $this->requested_data['sorting_field'];
		$this->listing_search_structure['sorting_order'] = $this->requested_data['sorting_order'];
		return $searcher->getObjectsSIDsByCriteria($criteria, $aliases, $sortingFields, $innerJoin, $this->relevance);
	}

	function _getEmptyListing()
	{
		$listing = new SJB_Listing(array(), $this->listing_type_sid);
		$listing->addPicturesProperty();
		$listing->addIDProperty();
		$listing->addListingTypeIDProperty();
		$listing->addActivationDateProperty();
		$listing->addUsernameProperty();
		$listing->addCompanyNameProperty();
		$listing->addFeaturedProperty();
		$listing->addFeaturedLastShowedProperty();
		return $listing;
	}

	public function setLimit($listingsLimit)
	{
		$this->listingsLimit = $listingsLimit;
	}

	public function getListingCollectionStructureForMap(&$listings_structure)
	{
		$remove_keys = array();
		foreach ($listings_structure as $key => $listing_structure) {
			if (empty($listing_structure['Location']['ZipCode']) && empty($listing_structure['latitude']) && empty($listing_structure['longitude'])) {
				$remove_keys[] = $key;
			}
		}
		foreach ($remove_keys as $remove_key) {
			$listings_structure->offsetUnset($remove_key);
		}
		$listings_number = $listings_structure->count();
		$first = ($this->listing_search_structure['current_page'] - 1) * $this->listing_search_structure['listings_per_page'];
		$last = $this->listing_search_structure['listings_per_page'] * $this->listing_search_structure['current_page'] - 1;
		$remove_keys = array();
		$index = 0;
		foreach ($listings_structure as $key => $listing_structure) {
			if ($index < $first || $index > $last) {
				$remove_keys[] = $key;
			}
			$index++;
		}
		foreach ($remove_keys as $remove_key) {
			$listings_structure->offsetUnset($remove_key);
		}
		$this->listing_search_structure['listings_number'] = $listings_number;
		$this->listing_search_structure['pages_number'] = ceil($listings_number / $this->listing_search_structure['listings_per_page']);
	}

	/**
	 * @return \SJB_ListingCriteriaSaver
	 */
	public function getCriteriaSaver()
	{
		return $this->criteria_saver;
	}
}
