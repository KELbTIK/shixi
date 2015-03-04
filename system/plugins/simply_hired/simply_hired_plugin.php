<?php

require_once 'simpleXML/simplexml.class.php';

class SimplyHiredPlugin extends SJB_PluginAbstract 
{
	public static $simplyhiredListings = array();
	
	function pluginSettings()
	{
		return array(
			array (
				'id'            => 'simplyHiredSiteUrl',
				'caption'       => 'Country',
				'type'          => 'list',
				'list_values'   => $this->getDomains(),
				'length'        => '50',
				'is_required'   => true,
				'order'         => null,
			),
			array (
				'id'			=> 'jobAMaticDomain',
				'caption'		=> 'Job-a-matic domain',
				'type'			=> 'string',
				'length'		=> '50',
				'is_required'	=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'countSimplyHiredListings',
				'caption'		=> 'Number of listings',
				'type'			=> 'integer',
				'comment'		=> 'The Number of listings imported from Indeed to be displayed per page in search results',
				'length'		=> '50',
				'is_required'	=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'simplyHiredPublisherID',
				'caption'		=> 'Publisher ID',
				'type'			=> 'string',
				'comment'		=> 'To get your Publisher ID, go to http://simplyhired.com',
				'length'		=> '50',
				'is_required'	=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'simplyHiredKeyword',
				'caption'		=> 'Keywords',
				'type'			=> 'string',
				'comment'		=> 'Specifying this parameter you can limit jobs from SimplyHired to jobs containing these phrases',
				'length'		=> '50',
				'is_required'	=> true,
				'order'			=> null,
			)
			,
			array (
				'id'			=> 'simplyHiredLocation',
				'caption'		=> 'Location',
				'type'			=> 'string',
				'comment'		=> 'Use a postal code or a "city, state/province/region" combination',
				'length'		=> '50',
				'is_required'	=> false,
				'order'			=> null,
			)
			,
			array (
				'id'			=> 'simplyHiredMiles',
				'caption'		=> 'Miles',
				'type'			=> 'string',
				'comment'		=> 'Distance from search location in miles. Default is 25',
				'length'		=> '50',
				'is_required'	=> false,
				'order'			=> null,
			)
			,
			array (
				'id'			=> 'simplyHiredSortBy',
				'caption'		=> 'Sort By',
				'type'			=> 'string',
				'comment'		=> 'A parameter indicating the sort order of organic jobs.<br>
				Valid values include:<br>
				<ul>
			    <li>rd = relevance descending (default)</li>
			    <li>ra = relevance ascending</li>
			    <li>dd = last seen date descending</li>
			    <li>da = last seen date ascending</li>
			    <li>td = title descending</li>
			    <li>ta = title ascending</li>
			    <li>cd = company descending</li>
			    <li>ca = company ascending</li>
			    <li>ld = location descending</li>
			    <li>la = location ascending</li>
			    </ul>
				',
				'length'		=> '50',
				'is_required'	=> false,
				'order'			=> null,
			)
		);
	}

	/**
	 * @return array
	 */
	private function getDomains()
	{
		return array(
			0  => array('id' => 'simplyhired.com', 'caption'    => 'United States'),
			1  => array('id' => 'simplyhired.com.ar', 'caption' => 'Argentina'),
			2  => array('id' => 'simplyhired.com.au', 'caption' => 'Australia'),
			3  => array('id' => 'simplyhired.at', 'caption'     => 'Austria'),
			4  => array('id' => 'simplyhired.be', 'caption'     => 'Belgium'),
			5  => array('id' => 'simplyhired.com.br', 'caption' => 'Brazil'),
			6  => array('id' => 'simplyhired.ca', 'caption'     => 'Canada'),
			7  => array('id' => 'simplyhired.cn', 'caption'     => 'China'),
			8  => array('id' => 'simplyhired.fr', 'caption'     => 'France'),
			9  => array('id' => 'simplyhired.ge', 'caption'     => 'Germany'),
			10 => array('id' => 'simplyhired.co.in', 'caption'  => 'India'),
			11 => array('id' => 'simplyhired.ie', 'caption'     => 'Ireland'),
			12 => array('id' => 'simplyhired.it', 'caption'     => 'Italy'),
			13 => array('id' => 'simplyhired.jp', 'caption'     => 'Japan'),
			14 => array('id' => 'simplyhired.kr', 'caption'     => 'Korea'),
			15 => array('id' => 'simplyhired.mx', 'caption'     => 'Mexico'),
			16 => array('id' => 'simplyhired.nl', 'caption'     => 'Netherlands'),
			17 => array('id' => 'simplyhired.pt', 'caption'     => 'Portugal'),
			18 => array('id' => 'simplyhired.ru', 'caption'     => 'Russia'),
			19 => array('id' => 'za.simplyhired.com', 'caption' => 'South Africa'),
			20 => array('id' => 'simplyhired.es', 'caption'     => 'Spain'),
			21 => array('id' => 'simplyhired.se', 'caption'     => 'Sweden'),
			22 => array('id' => 'simplyhired.ch', 'caption'     => 'Switzerland'),
			23 => array('id' => 'simplyhired.co.uk', 'caption'  => 'United Kingdom'),
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
		$arrayOfProviders[] = 'simplyhired';
		return $arrayOfProviders;
	}

	
	public static function getListingsFromSimplyHired($params)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($params->listing_type_sid);
		if ($listingTypeID == 'Job' && $GLOBALS['uri'] == '/search-results-jobs/' || $GLOBALS['uri'] == '/ajax/') {
			$publisherID 	= SJB_Settings::getSettingByName('simplyHiredPublisherID');
			$limit = SJB_Settings::getSettingByName('countSimplyHiredListings');
			$ip          	= $_SERVER['REMOTE_ADDR'];
			$userAgent  	= urlencode(SJB_Request::getUserAgent());
			$start = $limit*($params->listing_search_structure['current_page']-1)+1;
	
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
				'Australia'     => 'au',
				'Austria'       => 'at',
				'Belgium'       => 'be',
				'Brazil'        => 'br',
				'Canada'        => 'ca',
				'France'        => 'fr',
				'Germany'       => 'de',
				'India'         => 'in',
				'Ireland'       => 'ie',
				'Italy'         => 'it',
				'Mexico'        => 'mx',
				'Netherlands'   => 'nl',
				'Spain'         => 'es',
				'Switzerland'   => 'ch',
				'United Kingdom' => 'gb',
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
				foreach ($categoryCriteria as $category) {
					if(!empty($fieldList[$category]))
						$keywords .= empty($keywords) ? $fieldList[$category] : ' OR ' . $fieldList[$category];
				}
				if (!empty($keywords))
					$keywords = "({$keywords})";
			}

			foreach ($criteria as $field) {
				if (is_array($field)) {
					foreach ($field as $fieldType => $values) {
						if ($fieldType == 'multi_like_and') {
							foreach ($values as $val) {
								$keywords .= empty($keywords) ? $val : ' ' . $val;
							}
						}
					}
				}
			}

			if (isset($criteria['keywords']) && !empty($criteria['keywords'])) {
				foreach ($criteria['keywords'] as $key => $item) {
					if (in_array($key, array('exact_phrase', 'any_words', 'all_words', 'like'))) {
						$keywords .= $item;
					}
				}
			}
			$systemKeywords = trim(SJB_Settings::getSettingByName('simplyHiredKeyword'));
			$keywords = $systemKeywords ? '(' . $systemKeywords . ')' . ($keywords ? ' OR (' . $keywords . ')' : '') : $keywords;
			$keywords = preg_replace('/\s+/', ' ', $keywords);
			$keywords = str_replace(',', '', $keywords);
			$keywords = urlencode(trim($keywords));
			
			$location = self::getLocation($criteria, 'simplyHiredLocation');
			
			$radius = SJB_Settings::getSettingByName('simplyHiredMiles');
			if (isset($criteria['Location_ZipCode']['geo']['radius']) && !empty($criteria['Location_ZipCode']['geo']['radius'])) {
				$radius = $criteria['Location_ZipCode']['geo']['radius'];
				if ($radius == 'any') {
					$radius = '';
				}
			}
			
			$sortBy  = SJB_Settings::getSettingByName('simplyHiredSortBy');
			$siteUrl = SJB_Settings::getSettingByName('simplyHiredSiteUrl');
			$jobAMaticDomain = SJB_Settings::getSettingByName('jobAMaticDomain', false);
			if (!empty($jobAMaticDomain) && ($siteUrl == 'simplyhired.com')) {
				$jobAMaticDomain = str_replace('http://', '', $jobAMaticDomain);
				$jobAMaticDomain = str_replace('/', '', $jobAMaticDomain);
				$jobAMaticDomain = "&jbd={$jobAMaticDomain}";
			} else {
				$jobAMaticDomain = '';
			}
			if ($siteUrl == 'simplyhired.com') {
				$ssty = 2;
			} else {
				$ssty = 3;
			}

			$url = "http://api.{$siteUrl}/a/jobs-api/xml-v2/q-{$keywords}/l-{$location}/mi-$radius/ws-$limit/pn-{$params->listing_search_structure['current_page']}/sb-{$sortBy}?pshid={$publisherID}&ssty={$ssty}&cflg=r{$jobAMaticDomain}&clip={$ip}";
			$sxml = new simplexml();

            $xmlString = SJB_HelperFunctions::getUrlContentByCurl($url);
			$simplyhiredListings = array();

			if ($xmlString === false) {
//				throw new Exception("simplyHiredPlugin: Failed to read XML from url - {$url}");
				SJB_Logger::error("simplyHiredPlugin: Failed to read XML from url - {$url}");
			}
			else {
				//$tree = $sxml->xml_load_file($url, 'array');
				$tree = $sxml->xml_load_file($xmlString, 'array');

				$totalResults = 0;

				if ($tree !== false) {

					$results = isset($tree['rs'])?$tree['rs']:array();
					$outputCountry = array_flip($countryCodes);

					foreach ($results as $node) {
						if ($tree['rq']['rpd'] == 1) {
							$node = array($node);
						}
						foreach ($node as $key => $result) {
							$state     = (string) $result['loc']['@attributes']['st'];
							$country   = (string) $result['loc']['@attributes']['country'];

							$simplyhiredListings [$key] = array(
								'Title'          => (string) $result['jt'],
								'CompanyName'    => (string) isset($result['cn']['@content']) ? $result['cn']['@content'] : '',
								'JobDescription' => (string) $result['e'],
								'Location'       => array(
									'Country'        => empty($country) ? '' : (isset($outputCountry [ strtolower($country) ]) ? $outputCountry [ strtolower($country) ] : '' ),
									'State'          => empty($location[1]) ? '' : (isset($stateIndexes [ strtoupper($state) ]) ? $stateIndexes [ strtoupper($state) ] : ''),
									'State_Code'     => empty($state) ? '' : strtoupper($state),
									'City'           => (string) $result['loc']['@attributes']['cty'],
								),
								'activation_date'=> (string) $result['dp'],
								'url'            => (string) $result['src']['@attributes']['url'],
								'api'			 => 'simplyHired',
								'onmousedown'    => ' onMouseDown="xml_sclk(this);" ',
								'target'         => ' target="_blank"',
								'onclick'        => 'onclick="addStatisticsForSimplyHired();" ',
								'code'			 => '<span style="font-size:10px; position:relative; top:-5px; font-family:Arial,sans-serif;color: #333;"><a style="color: #333; text-decoration:none" href="' . SJB_Request::getProtocol() . '://www.simplyhired.com/">Jobs</a> by</span> <a STYLE="text-decoration:none" href="' . SJB_Request::getProtocol() . '://www.simplyhired.com/"><img src="' . SJB_Request::getProtocol() . '://www.jobamatic.com/c/jbb/images/simplyhired.png" alt="Simply Hired"></a>'
							);
						}
					}
				}
			}

			self::$simplyhiredListings = $simplyhiredListings;
		}
		return $params;
	}
	
	public static function addSimplyHiredListingsToListingStructure($listings_structure)
	{
		foreach (self::$simplyhiredListings as $key => $simplyhiredListing) {
			$listings_structure['simplyhired_'.$key] = $simplyhiredListing;
		}
		return $listings_structure;
	}
}