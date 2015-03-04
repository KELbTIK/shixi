<?php

class IndeedPlugin extends SJB_PluginAbstract
{
	public static $indeedListings = array();
	
	function pluginSettings()
	{
		$numberOfListings = array();
		for ($i = 1; $i <=25; $i++) {
			$numberOfListings[] = array(
				'id'      => "{$i}",
				'caption' => "{$i}",
			);
		}
		
		return array( 
			array (
				'id'			=> 'countIndeedListings',
				'caption'		=> 'Number of listings',
				'type'			=> 'list',
				'list_values'   => $numberOfListings,
				'comment'		=> 'The Number of listings imported from Indeed to be displayed per page in search results.<br />*Indeed has a limit of 25 jobs to be backfilled per one request.',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'IndeedPublisherID',
				'caption'		=> 'Publisher ID',
				'type'			=> 'string',
				'comment'		=> 'To get the Publisher ID, go to https://indeed.com, sign in/register, then go to Publishers menu (https://ads.indeed.com/jobroll/) and Create an Account.<br/>Once you created an account, go to XML Feed tab (https://ads.indeed.com/jobroll/xmlfeed) and find your Publisher ID in the table below. ',
				'length'		=> '50',
				'order'			=> null,
			),			
			array (
				'id'			=> 'IndeedSiteType',
				'caption'		=> 'Site Type',
				'type'			=> 'list',
				'list_values'   => array(
					array(
						'id'      => 'jobsite',
						'caption' => 'jobsite',
					),
					array(
						'id'      => 'employer',
						'caption' => 'employer'
					),
				),
				'comment'		=> "To show only jobs from job boards use 'jobsite'. For jobs from direct employer websites use 'employer'",
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'IndeedJobType',
				'caption'		=> 'Job Type',
				'type'			=> 'list',
				'list_values'   => array(
					array(
						'id'      => 'fulltime',
						'caption' => 'fulltime',
					),
					array(
						'id'      => 'parttime',
						'caption' => 'parttime',
					),
					array(
						'id'      => 'contract',
						'caption' => 'contract',
					),
					array(
						'id'      => 'internship',
						'caption' => 'internship',
					),
					array(
						'id'      => 'temporary',
						'caption' => 'temporary',
					),
				),
				'comment'		=> 'Allowed values: "fulltime", "parttime", "contract", "internship", "temporary"',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'IndeedCountry',
				'caption'		=> 'Country',
				'type'			=> 'list',
				'list_values'   => array(
					array(
					'id'      => 'us',
					'caption' => 'United States',
					),
					array(
					'id'      => 'ar',
					'caption' => 'Argentina',
					),
					array(
					'id'      => 'au',
					'caption' => 'Australia',
					),
					array(
					'id'      => 'at',
					'caption' => 'Austria',
					),
					array(
					'id'      => 'bh',
					'caption' => 'Bahrain',
					),
					array(
					'id'      => 'be',
					'caption' => 'Belgium',
					),
					array(
					'id'      => 'br',
					'caption' => 'Brazil',
					),
					array(
					'id'      => 'ca',
					'caption' => 'Canada',
					),
					array(
					'id'      => 'cl',
					'caption' => 'Chile',
					),
					array(
					'id'      => 'cn',
					'caption' => 'China',
					),
					array(
					'id'      => 'co',
					'caption' => 'Colombia',
					),
					array(
					'id'      => 'cz',
					'caption' => 'Czech Republic',
					),
					array(
					'id'      => 'dk',
					'caption' => 'Denmark',
					),
					array(
					'id'      => 'fi',
					'caption' => 'Finland',
					),
					array(
					'id'      => 'fr',
					'caption' => 'France',
					),
					array(
					'id'      => 'de',
					'caption' => 'Germany',
					),
					array(
					'id'      => 'gr',
					'caption' => 'Greece',
					),
					array(
					'id'      => 'hk',
					'caption' => 'Hong Kong',
					),
					array(
					'id'      => 'hu',
					'caption' => 'Hungary',
					),
					array(
					'id'      => 'in',
					'caption' => 'India',
					),
					array(
					'id'      => 'id',
					'caption' => 'Indonesia',
					),
					array(
					'id'      => 'ie',
					'caption' => 'Ireland',
					),
					array(
					'id'      => 'il',
					'caption' => 'Israel',
					),
					array(
					'id'      => 'it',
					'caption' => 'Italy',
					),
					array(
					'id'      => 'jp',
					'caption' => 'Japan',
					),
					array(
					'id'      => 'kr',
					'caption' => 'Korea',
					),
					array(
					'id'      => 'kw',
					'caption' => 'Kuwait',
					),
					array(
					'id'      => 'lu',
					'caption' => 'Luxembourg',
					),
					array(
					'id'      => 'my',
					'caption' => 'Malaysia',
					),
					array(
					'id'      => 'mx',
					'caption' => 'Mexico',
					),
					array(
					'id'      => 'nl',
					'caption' => 'Netherlands',
					),
					array(
					'id'      => 'nz',
					'caption' => 'New Zealand',
					),
					array(
					'id'      => 'no',
					'caption' => 'Norway',
					),
					array(
					'id'      => 'om',
					'caption' => 'Oman',
					),
					array(
					'id'      => 'pk',
					'caption' => 'Pakistan',
					),
					array(
					'id'      => 'pe',
					'caption' => 'Peru',
					),
					array(
					'id'      => 'ph',
					'caption' => 'Philippines',
					),
					array(
					'id'      => 'pl',
					'caption' => 'Poland',
					),
					array(
					'id'      => 'pt',
					'caption' => 'Portugal',
					),
					array(
					'id'      => 'qa',
					'caption' => 'Qatar',
					),
					array(
					'id'      => 'ro',
					'caption' => 'Romania',
					),
					array(
					'id'      => 'ru',
					'caption' => 'Russia',
					),
					array(
					'id'      => 'sa',
					'caption' => 'Saudi Arabia',
					),
					array(
					'id'      => 'sg',
					'caption' => 'Singapore',
					),
					array(
					'id'      => 'za',
					'caption' => 'South Africa',
					),
					array(
					'id'      => 'es',
					'caption' => 'Spain',
					),
					array(
					'id'      => 'se',
					'caption' => 'Sweden',
					),
					array(
					'id'      => 'ch',
					'caption' => 'Switzerland',
					),
					array(
					'id'      => 'tw',
					'caption' => 'Taiwan',
					),
					array(
					'id'      => 'tr',
					'caption' => 'Turkey',
					),
					array(
					'id'      => 'ae',
					'caption' => 'United Arab Emirates',
					),
					array(
					'id'      => 'gb',
					'caption' => 'United Kingdom',
					),
					array(
					'id'      => 've',
					'caption' => 'Venezuela',
					),					
				),
				'comment'		=> 'Search within country specified. Default is us',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'IndeedSort',
				'caption'		=> 'Sort',
				'type'			=> 'list',
				'list_values'   => array(
					array(
						'id'      => 'relevance',
						'caption' => 'relevance',
					),
					array(
						'id'      => 'date',
						'caption' => 'date',
					),
				),
				'comment'		=> 'Sort by relevance or date. Default is relevance',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'IndeedHighlightKeywords',
				'caption'		=> 'Highlight keywords',
				'type'			=> 'boolean',
				'comment'       => 'Highlight keywords in search results from indeed',
				'order'			=> null,
			),
		);
	}


	/**
	 * Register this plugin as listings provider for ajax requests
	 *
	 * @static
	 * @param array $arrayOfProviders
	 * @return array
	 */
	public static function registerAsListingsProvider($arrayOfProviders = array())
	{
		$arrayOfProviders[] = 'indeed';
		return $arrayOfProviders;
	}


	public static function getListingsFromIndeed($params)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($params->listing_type_sid);
		if ($listingTypeID == 'Job' && $GLOBALS['uri'] == '/search-results-jobs/' || $GLOBALS['uri'] == '/ajax/') {
			$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : $params->listing_search_structure['current_page'];
			
			$publisherID = SJB_Settings::getSettingByName('IndeedPublisherID');
			$limit       = SJB_Settings::getSettingByName('countIndeedListings');
			$ip          = $_SERVER['REMOTE_ADDR'];
			$userAgent   = urlencode(SJB_Request::getUserAgent());
			$start       = $limit * ($page - 1);
			
			$stateIndexes = array(
				'AL' => 'Alabama',
				'AK' => 'Alaska',
				'AZ' => 'Arizona',
				'AR' => 'Arkansas',
				'CA' => 'California',
				'CO' => 'Colorado',
				'CT' => 'Connecticut',
				'DE' => 'Delaware',
				'FL' => 'Florida',
				'GA' => 'Georgia',
				'HI' => 'Hawaii',
				'ID' => 'Idaho',
				'IL' => 'Illinois',
				'IN' => 'Indiana',
				'IA' => 'Iowa',
				'KS' => 'Kansas',
				'KY' => 'Kentucky',
				'LA' => 'Louisiana',
				'ME' => 'Maine',
				'MD' => 'Maryland',
				'MA' => 'Massachusetts',
				'MI' => 'Michigan',
				'MN' => 'Minnesota',
				'MS' => 'Mississippi',
				'MO' => 'Missouri',
				'MT' => 'Montana',
				'NE' => 'Nebraska',
				'NV' => 'Nevada',
				'NH' => 'New Hampshire',
				'NJ' => 'New Jersey',
				'NM' => 'New Mexico',
				'NY' => 'New York',
				'NC' => 'North Carolina',
				'ND' => 'North Dakota',
				'OH' => 'Ohio',
				'OK' => 'Oklahoma',
				'OR' => 'Oregon',
				'PA' => 'Pennsylvania',
				'RI' => 'Rhode Island', 
				'SC' => 'South Carolina',
				'SD' => 'South Dakota',
				'TN' => 'Tennessee',
				'TX' => 'Texas',
				'UT' => 'Utah',
				'VT' => 'Vermont',
				'VA' => 'Virginia',
				'WA' => 'Washington',
				'WV' => 'West Virginia',
				'WI' => 'Wisconsin',
				'WY' => 'Wyoming',
				'DC' => 'District of Columbia',
				'AS' => 'American Samoa',
				'GU' => 'Guam',
				'MP' => 'Northern Mariana Islands',
				'PR' => 'Puerto Rico',
				'UM' => "United's Minor Outlying Islands",
				'VI' => 'Virgin Islands'
			);
			
			$countryCodes = array(
				'United States' => 'us',
				'Argentina' => 'ar',
				'Australia' => 'au',
				'Austria' => 'at',
				'Bahrain' => 'bh',
				'Belgium' => 'be',
				'Brazil' => 'br',
				'Canada' => 'ca',
				'Chile' => 'cl',
				'China' => 'cn',
				'Colombia' => 'co',
				'Czech Republic' => 'cz',
				'Denmark' => 'dk',
				'Finland' => 'fi',
				'France' => 'fr',
				'Germany' => 'de',
				'Greece' => 'gr',
				'Hong Kong' => 'hk',
				'Hungary' => 'hu',
				'India' => 'in',
				'Indonesia' => 'id',
				'Ireland' => 'ie',
				'Israel' => 'il',
				'Italy' => 'it',
				'Japan' => 'jp',
				'Korea' => 'kr',
				'Kuwait' => 'kw',
				'Luxembourg' => 'lu',
				'Malaysia' => 'my',
				'Mexico' => 'mx',
				'Netherlands' => 'nl',
				'New Zealand' => 'nz',
				'Norway' => 'no',
				'Oman' => 'om',
				'Pakistan' => 'pk',
				'Peru' => 'pe',
				'Philippines' => 'ph',
				'Poland' => 'pl',
				'Portugal' => 'pt',
				'Qatar' => 'qa',
				'Romania' => 'ro',
				'Russia' => 'ru',
				'Russian Federation' => 'ru',
				'Saudi Arabia' => 'sa',
				'Singapore' => 'sg',
				'South Africa' => 'za',
				'Spain' => 'es',
				'Sweden' => 'se',
				'Switzerland' => 'ch',
				'Taiwan' => 'tw',
				'Turkey' => 'tr',
				'United Arab Emirates' => 'ae',
				'United Kingdom' => 'gb',
				'Venezuela' => 've',
			);

			$countryDomains = array(
				'us' => 'indeed.com',
				'ar' => 'ar.indeed.com',
				'au' => 'au.indeed.com',
				'at' => 'at.indeed.com',
				'bh' => 'bh.indeed.com',
				'be' => 'be.indeed.com',
				'br' => 'indeed.com.br',
				'ca' => 'ca.indeed.com',
				'cl' => 'indeed.cl',
				'cn' => 'cn.indeed.com',
				'co' => 'co.indeed.com',
				'cz' => 'cz.indeed.com',
				'dk' => 'dk.indeed.com',
				'fi' => 'indeed.fi',
				'fr' => 'indeed.fr',
				'de' => 'de.indeed.com',
				'gr' => 'gr.indeed.com',
				'hk' => 'indeed.hk',
				'hu' => 'hu.indeed.com',
				'in' => 'indeed.co.in',
				'id' => 'id.indeed.com',
				'ie' => 'ie.indeed.com',
				'il' => 'il.indeed.com',
				'it' => 'it.indeed.com',
				'jp' => 'jp.indeed.com',
				'kr' => 'kr.indeed.com',
				'kw' => 'kw.indeed.com',
				'lu' => 'indeed.lu',
				'my' => 'indeed.com.my',
				'mx' => 'indeed.com.mx',
				'nl' => 'indeed.nl',
				'nz' => 'nz.indeed.com',
				'no' => 'no.indeed.com',
				'om' => 'om.indeed.com',
				'pk' => 'indeed.com.pk',
				'pe' => 'indeed.com.pe',
				'ph' => 'indeed.com.ph',
				'pl' => 'pl.indeed.com',
				'pt' => 'indeed.pt',
				'qa' => 'qa.indeed.com',
				'ro' => 'ro.indeed.com',
				'ru' => 'ru.indeed.com',
				'sa' => 'sa.indeed.com',
				'sg' => 'indeed.com.sg',
				'za' => 'indeed.co.za',
				'es' => 'indeed.es',
				'se' => 'se.indeed.com',
				'ch' => 'indeed.ch',
				'tw' => 'tw.indeed.com',
				'tr' => 'tr.indeed.com',
				'ae' => 'indeed.ae',
				'gb' => 'indeed.co.uk',
				've' => 've.indeed.com',
			);
		// SET PARAMS FOR REQUEST
			$keywords = '';
			$criteria = $params->criteria_saver->criteria;
			$fieldSID = SJB_ListingFieldManager::getListingFieldSIDByID('JobCategory');
			$fieldInfo = SJB_ListingFieldDBManager::getListValuesBySID($fieldSID);
			$fieldList = array();
			foreach ($fieldInfo as $val) 
				$fieldList[$val['id']] = $val['caption'];
			
			$categoryCriteria = isset($criteria['JobCategory']['multi_like']) ? $criteria['JobCategory']['multi_like'] : '';
			
			if (!empty($categoryCriteria)) {
				if (!empty($keywords))
					$keywords .= ' or ';
				foreach ($categoryCriteria as $category) {
					if (!empty($category) && !empty($fieldList[$category]))
						$keywords .= $fieldList[$category] . ' or ';
				}
				$keywords = substr($keywords, 0, strlen($keywords) - 4);
			}
			foreach ($criteria as $fieldName => $field) {
				if (is_array($field)) {
					foreach ($field as $fieldType => $values) {
						if ($fieldType === 'multi_like_and') {
							foreach ($values as $val) {
								if ($keywords != '')
									$keywords .= " and ";
								$keywords .= $val;
							}
						}
					}
				}
			}
			if (isset($criteria['keywords']) && !empty($criteria['keywords'])) {
				foreach ($criteria['keywords'] as $key => $item) {
					if (in_array($key, array('exact_phrase', 'any_words', 'all_words'))) {
						if (!empty($keywords))
							$keywords .= ' or ';
						$keywords .= $item;
					}
				}
			}
			if (substr($keywords, -4) == ' or ') {
				$keywords = substr($keywords, 0, strlen($keywords) - 4);
			}
			$keywords = trim($keywords);
			$keywords = urlencode($keywords);
			
			$location = self::getLocation($criteria);

			if (isset($criteria['Location']['location']['radius']) && !empty($criteria['Location']['location']['radius'])) {
				if ($criteria['Location']['location']['radius'] == 'any') {
					$radius = '';
				} else {
					$radius = $criteria['Location']['location']['radius'];
				}
			} else {
				$radius = 0;
			}

			$indeedCountry = SJB_Settings::getSettingByName('IndeedCountry');
			$country = !empty($criteria['Location_Country']['multi_like'][0]) ? $criteria['Location_Country']['multi_like'][0] : $indeedCountry;
			
			$codes = array_values($countryCodes);
			if (!in_array($country, $codes)) {
				// ok. Country value - not correct Indeed value. Lets try convert it.
				if (is_numeric($country)) {
					$countryInfo = SJB_CountriesManager::getCountryInfoBySID($country);
					$country = !empty($countryInfo['country_code'])?$countryInfo['country_code']:'';
				}
			}
			$jobType   = SJB_Settings::getSettingByName('IndeedJobType');
			$siteType  = SJB_Settings::getSettingByName('IndeedSiteType');
			$sort      = SJB_Settings::getSettingByName('IndeedSort');
			$highlight = SJB_Settings::getSettingByName('IndeedHighlightKeywords');
			$url       = "http://api.indeed.com/ads/apisearch?publisher={$publisherID}&q={$keywords}&l={$location}&sort={$sort}&radius={$radius}&st={$siteType}&jt={$jobType}&start={$start}&limit={$limit}&fromage=&filter=&latlong=1&co={$country}&highlight={$highlight}&chnl=&userip={$ip}&useragent={$userAgent}&v=2";

			$ch = curl_init();
			
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			// grab URL and pass it to the browser
			$xml = curl_exec($ch);
			curl_close($ch);
			
			$indeedListings = array();

			if ($xml !== false) {
				$doc = new DOMDocument();
				try {
					$doc->loadXML($xml, LIBXML_NOERROR);
					$results = $doc->getElementsByTagName('results');
					if ($results instanceof DOMNodeList) {
						$outputCountry = array_flip($countryCodes);
						
						$totalResults = $doc->getElementsByTagName('totalresults')->item(0)->nodeValue;
						$totalPages   = ceil(((integer) $totalResults) / $limit);
						$pageNumber   = $doc->getElementsByTagName('pageNumber')->item(0)->nodeValue;
						$indeedDomain = !empty($indeedCountry) && isset($countryDomains[$indeedCountry]) ? $countryDomains[$indeedCountry] : $countryDomains['us'];
						if (strpos($indeedDomain, '.') !== 2) {
							$indeedDomain = 'www.' . $indeedDomain;
						}
						
						foreach ($results as $node) {
							foreach ($node->getElementsByTagName('result') as $result) {
								$resultXML = simplexml_import_dom($result);
								$jobKey    = (string) $resultXML->jobkey;
								$state     = (string) $resultXML->state;
								$country   = (string) $resultXML->country;
								
								$indeedListings [$jobKey] = array(
									'Title'          => (string) $resultXML->jobtitle,
									'CompanyName'    => (string) $resultXML->company,
									'JobDescription' => (string) $resultXML->snippet,
									'Location'       => array(
										'Country'        => empty($country) ? '' : $outputCountry [ strtolower($country) ],
										'State'          => empty($state) ? '' : isset($stateIndexes [ strtoupper($state) ]) ? $stateIndexes [ strtoupper($state) ] : $state,
										'State_Code'     => empty($state) ? '' : strtoupper($state),
										'City'           => (string) $resultXML->city,
									),
									'url'            => (string) $resultXML->url,
									'onmousedown'    => ' onMouseDown="' . (string) $resultXML->onmousedown . '" ',
									'target'         => ' target="_blank" ',
									'jobkey'         => $jobKey,
									'activation_date'=> (string) $resultXML->date,
									'api'            => 'indeed',
									'code'           => '<span id="indeed_at"><a href="' . SJB_Request::getProtocol() . '://' . $indeedDomain .'/">jobs</a> by <a href="' . SJB_Request::getProtocol() . '://' . $indeedDomain . '/" title="Job Search"><img src="' . SJB_Request::getProtocol() . '://www.indeed.com/p/jobsearch.gif" style="border: 0; vertical-align: middle;" alt="Indeed job search"></a></span>',
									'pageNumber'     => $pageNumber,
									'totalPages'     => $totalPages,
								);
							}
						}
					}
					else {
						SJB_Logger::error('CANT GET INDEED XML RESULTS');
					}
				}
				catch (ErrorException $e) {
					SJB_Logger::error($e->getMessage());
				}
			}
			else {
				SJB_Logger::error('NOT VALID RESPONSE FROM INDEED');
			}
			self::$indeedListings = $indeedListings;
		}
		return $params;
	}
	
	public static function addIndeedListingsToListingStructure($listings_structure)
	{
		foreach (self::$indeedListings as $indeedListing)
			$listings_structure[$indeedListing['jobkey']] = $indeedListing;
		return $listings_structure;
	}
}
