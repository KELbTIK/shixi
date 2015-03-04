<?php

class BeyondPlugin extends SJB_PluginAbstract
{
	public static $beyondListings = array();
	
	function pluginSettings()
	{
		return array( 
			array (
				'id'			=> 'countBeyondListings',
				'caption'		=> 'Number of listings',
				'type'			=> 'integer',
				'comment'		=> 'The Number of listings imported from Beyond to be displayed per page in search results',
				'length'		=> '50',
				'is_required'	=> true,
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyondAffiliateID',
				'caption'		=> 'Affiliate ID',
				'type'			=> 'string',
				'comment'		=> '',
				'length'		=> '50',
				'is_required'	=> true,
				'order'			=> null,
			),	
			array (
				'id'			=> 'MobileBeyondAffiliateID',
				'caption'		=> 'Mobile Affiliate ID',
				'type'			=> 'string',
				'comment'		=> '',
				'length'		=> '50',
				'order'			=> null,
			),		
			array (
				'id'			=> 'BeyondKeywords',
				'caption'		=> 'Keywords',
				'type'			=> 'string',
				'comment'		=> 'Use "" to search on multiple keywords. For Boolean searching use AND/OR and group with "()", ie admin+AND(finance+OR+banking), is admin AND (finance OR banking)',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyondIndustryList',
				'caption'		=> 'Industry List',
				'type'			=> 'string',
				'comment'		=> 'Separate mulitple values with the pipe "|" character. <a href="http://www.beyond.com/common/services/documentation/default.asp?p=il" target="_blank">View Possible Inputs</a>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyondCountry',
				'caption'		=> 'Country',
				'type'			=> 'string',
				'comment'		=> '<a href="http://www.beyond.com/common/services/documentation/default.asp?p=ct" target="_blank">View Possible Inputs</a>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyondState',
				'caption'		=> 'State',
				'type'			=> 'string',
				'comment'		=> '<a href="http://www.beyond.com/common/services/documentation/default.asp?p=st" target="_blank">View Possible Inputs</a>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyondZipcode',
				'caption'		=> 'Zipcode',
				'type'			=> 'string',
				'comment'		=> '',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyonEducationLevel',
				'caption'		=> 'Education Level',
				'type'			=> 'string',
				'comment'		=> '<a href="http://www.beyond.com/common/services/documentation/default.asp?p=el" target="_blank">View Possible Inputs</a>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyonEmploymentType',
				'caption'		=> 'Employment Type',
				'type'			=> 'string',
				'comment'		=> '<a href="http://www.beyond.com/common/services/documentation/default.asp?p=et" target="_blank">View Possible Inputs</a>',
				'length'		=> '50',
				'order'			=> null,
			),
			array (
				'id'			=> 'BeyonExperienceLevel',
				'caption'		=> 'Experience Level',
				'type'			=> 'string',
				'comment'		=> '<a href="http://www.beyond.com/common/services/documentation/default.asp?p=ex" target="_blank">View Possible Inputs</a>',
				'length'		=> '50',
				'order'			=> null,
			)
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
		$arrayOfProviders[] = 'beyond';
		return $arrayOfProviders;
	}

	
	public static function getListingsFromBeyond($params)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($params->listing_type_sid);
		if ($listingTypeID == 'Job' && $GLOBALS['uri'] == '/search-results-jobs/' || $GLOBALS['uri'] == '/ajax/') {
			$limit = SJB_Settings::getSettingByName('countBeyondListings');
			$start = $limit*($params->listing_search_structure['current_page']-1)+1;
			if ($limit) {
			// SET PARAMS FOR REQUEST
				$criteria = $params->criteria_saver->criteria;
				$categoryCriteria = isset($criteria['JobCategory']['multi_like']) ? $criteria['JobCategory']['multi_like'] : '';
				$category = SJB_Settings::getSettingByName('BeyondIndustryList');
				if (!empty($categoryCriteria)) {
					$categoryFromCriteria = self::getCategory($categoryCriteria);
					if ($category) {
						$category = explode('|', $category);
						$category = array_unique(array_merge($category, $categoryFromCriteria));
					}
					else
						$category = $categoryFromCriteria;
					$category = implode('|', $category);
				}
				$keywords = SJB_Settings::getSettingByName('BeyondKeywords');
				foreach ($criteria as $field) {
					if (is_array($field)) {
						foreach ($field as $fieldType => $values) {
							if ($fieldType === 'multi_like_and') {
								foreach ($values as $val) {
									if ($keywords != '') {
										$keywords .= ' AND ';
									}
									$keywords .= $val;
								}
							}
							if ($fieldType === 'location') {
								if (isset($values['value']) && !empty($values['value'])) {
									if ($keywords != '') {
										$keywords .= ' AND ';
									}
									$keywords .= $values['value'];
								}
							}
							if ($fieldType === 'like') {
								if ($keywords != '') {
									$keywords .= ' AND ';
								}
								$keywords .= $values;
							}
						}
					}
				}
				if (isset($criteria['keywords']) && !empty($criteria['keywords'])) {
					foreach ($criteria['keywords'] as $key => $item) {
						if (in_array($key, array('exact_phrase', 'any_words', 'all_words', 'like'))) {
							if (!empty($keywords))
								$keywords .= ' OR ';
							$keywords .= $item;
						}
					}
				}
				if (substr($keywords, -4) == ' OR ')
					$keywords = substr($keywords, 0, strlen($keywords) - 4);
				$keywords = trim($keywords);
				$keywords = urlencode($keywords);
				$keywords = !empty($keywords)?"({$keywords})":'';
				$city = '';
				if (!empty($criteria['Location_City']['like'])) {
					$city = urlencode($criteria['Location_City']['like']);
				}
				else if (!empty($criteria['Location_City']['multi_like_and'][0])) {
					$city = urlencode($criteria['Location_City']['multi_like_and'][0]);
				}
				else if (!empty($criteria['Location']['location']['value'])) {
					$city = urlencode($criteria['Location']['location']['value']);
				}
				$state = SJB_Settings::getSettingByName('BeyondState');
				if (isset($criteria['Location_State']['multi_like'])) {
					foreach ($criteria['Location_State']['multi_like'] as $stateSID) {
						if (!empty($stateSID)) {
							$stateInfo = SJB_StatesManager::getStateInfoBySID($stateSID);
							$state = !empty($stateInfo['state_code']) ? $stateInfo['state_code'] : '';
						}
					}
				}

				$countryCriteria = isset($criteria['Location_Country']['multi_like'][0]) ? $criteria['Location_Country']['multi_like'][0] : SJB_Settings::getSettingByName('BeyondCountry');
				$country = SJB_Settings::getSettingByName('BeyondCountry');
				if ($countryCriteria && is_numeric($countryCriteria)) {
					$countryInfo = SJB_CountriesManager::getCountryInfoBySID($countryCriteria);
					$country = !empty($countryInfo['country_code'])?$countryInfo['country_code']:'';
				}

				$employmentTypeCriteria = isset($criteria['EmploymentType']['multi_like']) ? $criteria['EmploymentType']['multi_like'] : '';
				$employmentType = SJB_Settings::getSettingByName('BeyonEmploymentType');
				if ($employmentTypeCriteria)
					$employmentType = self::getEmploymentType($employmentTypeCriteria);
	
				$zipCode = SJB_Settings::getSettingByName('BeyondZipcode');
				if (isset($criteria['Location_ZipCode']['geo']['location']) && !empty($criteria['Location_ZipCode']['geo']['location']))
					$zipCode = $criteria['Location_ZipCode']['geo']['location'];
				
				$educationLevel = SJB_Settings::getSettingByName('BeyonEducationLevel');
				$experienceLevel = SJB_Settings::getSettingByName('BeyonExperienceLevel');
				
				$affID = SJB_Settings::getSettingByName('BeyondAffiliateID');
				$isIPhone = false;
				if (class_exists('MobilePlugin')) {
					$isIPhone = MobilePlugin::isPhone();
				}
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) === SJB_Settings::getValue('mobile_url')
					|| (SJB_Settings::getValue('detect_iphone') && $isIPhone)) {
						$mobileAffID = SJB_Settings::getSettingByName('MobileBeyondAffiliateID');
						$affID = $mobileAffID?$mobileAffID:$affID;
					}

				$url = "http://www.beyond.com/common/services/job/search/default.asp?aff={$affID}&kw={$keywords}&kt=3&il={$category}&ct={$country}&st={$state}&zc={$zipCode}&el={$educationLevel}&et={$employmentType}&ex={$experienceLevel}&nw=e&pn={$start}&mx={$limit}&fwhere={$city}";

				$ch = curl_init();
					
				// set URL and other appropriate options
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				
				// grab URL and pass it to the browser
				$xml = curl_exec($ch);
			
				// close cURL resource, and free up system resources
				curl_close($ch);
				
				$beyondListings = array();
	
				if ($xml !== false) {
					$doc = new DOMDocument();
					$doc->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING);

					$results = $doc->getElementsByTagName('Item');
					if ($results instanceof DOMNodeList) {
						// if we need just total results number
						foreach ($results as $node) {
							$resultXML = simplexml_import_dom($node);
							$jobKey    = (string) $resultXML->SourceInformationID;
							$location = explode(',', (string) $resultXML->Location);
							$state = '';
							$stateCode = '';
							$country = '';
							$city = !empty($location[0]) ? $location[0] : '';
							if (isset($location[1])) {
								$location = explode(' ', trim($location[1]));
								if (!empty($location[0])) {
									$stateCode = trim($location[0]);
									$state = self::getState($stateCode);
								}
								if (!empty($location[1]))
									$country = self::getCountry(trim($location[1]));
							}

							$beyondListings [$jobKey] = array(
								'Title'          => (string) $resultXML->Title,
								'CompanyName'    => (string) $resultXML->CompanyName,
								'JobDescription' => (string) $resultXML->ShortDescription,
								'JobCategory'    => (string) $resultXML->CareerFocus,
								'State'          => $state,
								'Country'        => $country,
								'City'			 => $city,
								'Location'       => array(
										'Country'        => $country,
										'State'          => $state,
										'State_Code'     => $stateCode,
										'City'			 => $city
									),
								'url'            => SJB_System::getSystemSettings('SITE_URL').'/partnersite/?url='.base64_encode((string) $resultXML->ApplyURL),
								'jobkey'         => $jobKey,
								'activation_date'=> (string) $resultXML->Modified,
								'api'			 => 'beyond',
								'code'			 => '<span id=beyond_at><a href="http://www.beyond.com/">jobs</a> by <a href="http://www.beyond.com/" title="Job Search"><img src="'.SJB_System::getSystemSettings('SITE_URL').'/system/plugins/beyond_integration_plugin/logo.png" style="border: 0; vertical-align: middle;" alt="Beyond job search"></a></span>'
							);
						}
					}
					else {
						SJB_Logger::error('CANT GET BEYOND XML RESULTS');
					}

				}
				else {
					SJB_Logger::error('NOT VALID RESPONSE FROM BEYOND');
				}
				self::$beyondListings = $beyondListings;
			}
		}
		return $params;
	}
	
	public static function addBeyondListingsToListingStructure($listings_structure)
	{
		foreach (self::$beyondListings as $beyondListing)
			$listings_structure[$beyondListing['jobkey']] = $beyondListing;
		return $listings_structure;
	}
	
	private static function getCountry($country)
	{
		$countryCodes = array(
				'United States'=>'US', 
				'Canada'=>'CA', 
				'Aland Islands'=>'AX', 
				'Albania'=>'AL', 
				'Algeria'=>'DZ', 
				'American Samoa'=>'AS', 
				'Andorra'=>'AD', 
				'Angola'=>'AO', 
				'Anguilla'=>'AI', 
				'Antarctica'=>'AQ', 
				'Antigua and Barbuda'=>'AG', 
				'Argentina'=>'AR', 
				'Armenia'=>'AM', 
				'Aruba'=>'AW', 
				'Australia'=>'AU', 
				'Austria'=>'AT', 
				'Azerbaijan'=>'AZ', 
				'Bahamas, The'=>'BS', 
				'Bahrain'=>'BH', 
				'Bangladesh'=>'BD', 
				'Barbados'=>'BB', 
				'Belarus'=>'BY', 
				'Belgium'=>'BE', 
				'Belize'=>'BZ', 
				'Benin'=>'BJ', 
				'Bermuda'=>'BM', 
				'Bhutan'=>'BT', 
				'Bolivia'=>'BO', 
				'Bosnia and Herzegovina'=>'BA', 
				'Botswana'=>'BW', 
				'Bouvet Island'=>'BV', 
				'Brazil'=>'BR', 
				'British Indian Ocean Territory'=>'IO', 
				'British Virgin Islands'=>'VG', 
				'Brunei'=>'BN', 
				'Bulgaria'=>'BG', 
				'Burkina Faso'=>'BF', 
				'Burma'=>'MM', 
				'Burundi'=>'BI', 
				'Cambodia'=>'KH', 
				'Cameroon'=>'CM', 
				'Cape Verde'=>'CV', 
				'Cayman Islands'=>'KY', 
				'Central African Republic'=>'CF', 
				'Chad'=>'TD', 
				'Chile'=>'CL', 
				'China'=>'CN', 
				'Christmas Island'=>'CX', 
				'Cocos (Keeling) Islands'=>'CC', 
				'Colombia'=>'CO', 
				'Comoros'=>'KM', 
				'Congo, Democratic Republic of the'=>'CD', 
				'Congo, Republic of the'=>'CG', 
				'Cook Islands'=>'CK', 
				'Costa Rica'=>'CR', 
				'Cote d\'Ivoire'=>'CI', 
				'Croatia'=>'HR', 
				'Cuba'=>'CU', 
				'Cyprus'=>'CY', 
				'Czech Republic'=>'CZ', 
				'Denmark'=>'DK', 
				'Djibouti'=>'DJ', 
				'Dominica'=>'DM', 
				'Dominican Republic'=>'DO', 
				'Ecuador'=>'EC', 
				'Egypt'=>'EG', 
				'El Salvador'=>'SV', 
				'Equatorial Guinea'=>'GQ', 
				'Eritrea'=>'ER', 
				'Estonia'=>'EE', 
				'Ethiopia'=>'ET', 
				'Falkland Islands (Islas Malvinas)'=>'FK', 
				'Faroe Islands'=>'FO', 
				'Fiji'=>'FJ', 
				'Finland'=>'FI', 
				'France'=>'FR', 
				'France, Metropolitan'=>'FX', 
				'French Guiana'=>'GF', 
				'French Polynesia'=>'PF', 
				'French Southern and Antarctic Lands'=>'TF', 
				'Gabon'=>'GA', 
				'Gambia, The'=>'GM', 
				'Georgia'=>'GE', 
				'Germany'=>'DE', 
				'Ghana'=>'GH', 
				'Gibraltar'=>'GI', 
				'Greece'=>'GR', 
				'Greenland'=>'GL', 
				'Grenada'=>'GD', 
				'Guadeloupe'=>'GP', 
				'Guam'=>'GU', 
				'Guatemala'=>'GT', 
				'Guernsey'=>'GG', 
				'Guinea'=>'GN', 
				'Guinea-Bissau'=>'GW', 
				'Guyana'=>'GY', 
				'Haiti'=>'HT', 
				'Heard Island and McDonald Islands'=>'HM', 
				'Holy See (Vatican City)'=>'VA', 
				'Honduras'=>'HN', 
				'Hong Kong'=>'HK', 
				'Hungary'=>'HU', 
				'Iceland'=>'IS', 
				'India'=>'IN', 
				'Indonesia'=>'ID', 
				'Iran'=>'IR', 
				'Iraq'=>'IQ', 
				'Ireland'=>'IE', 
				'Isle of Man'=>'IM', 
				'Israel'=>'IL', 
				'Italy'=>'IT', 
				'Jamaica'=>'JM', 
				'Japan'=>'JP', 
				'Jersey'=>'JE', 
				'Jordan'=>'JO', 
				'Kazakhstan'=>'KZ', 
				'Kenya'=>'KE', 
				'Kiribati'=>'KI', 
				'Korea, North'=>'KP', 
				'Korea, South'=>'KR', 
				'Kuwait'=>'KW', 
				'Kyrgyzstan'=>'KG', 
				'Laos'=>'LA', 
				'Latvia'=>'LV', 
				'Lebanon'=>'LB', 
				'Lesotho'=>'LS', 
				'Liberia'=>'LR', 
				'Libya'=>'LY', 
				'Liechtenstein'=>'LI', 
				'Lithuania'=>'LT', 
				'Luxembourg'=>'LU', 
				'Macau'=>'MO', 
				'Macedonia'=>'MK', 
				'Madagascar'=>'MG', 
				'Malawi'=>'MW', 
				'Malaysia'=>'MY', 
				'Maldives'=>'MV', 
				'Mali'=>'ML', 
				'Malta'=>'MT', 
				'Marshall Islands'=>'MH', 
				'Martinique'=>'MQ', 
				'Mauritania'=>'MR', 
				'Mauritius'=>'MU', 
				'Mayotte'=>'YT', 
				'Mexico'=>'MX', 
				'Micronesia, Federated States of'=>'FM', 
				'Moldova'=>'MD', 
				'Monaco'=>'MC', 
				'Mongolia'=>'MN', 
				'Montenegro'=>'ME', 
				'Montserrat'=>'MS', 
				'Morocco'=>'MA', 
				'Mozambique'=>'MZ', 
				'Namibia'=>'NA', 
				'Nauru'=>'NR', 
				'Nepal'=>'NP', 
				'Netherlands'=>'NL', 
				'Netherlands Antilles'=>'AN', 
				'New Caledonia'=>'NC', 
				'New Zealand'=>'NZ', 
				'Nicaragua'=>'NI', 
				'Niger'=>'NE', 
				'Nigeria'=>'NG', 
				'Niue'=>'NU', 
				'Norfolk Island'=>'NF', 
				'Northern Mariana Islands'=>'MP', 
				'Norway'=>'NO', 
				'Oman'=>'OM', 
				'Pakistan'=>'PK', 
				'Palau'=>'PW', 
				'Palestinian Territory, Occupied'=>'PS', 
				'Panama'=>'PA', 
				'Papua New Guinea'=>'PG', 
				'Paraguay'=>'PY', 
				'Peru'=>'PE', 
				'Philippines'=>'PH', 
				'Pitcairn Islands'=>'PN', 
				'Poland'=>'PL', 
				'Portugal'=>'PT', 
				'Puerto Rico'=>'PR', 
				'Qatar'=>'QA', 
				'Reunion'=>'RE', 
				'Romania'=>'RO', 
				'Russia'=>'RU', 
				'Rwanda'=>'RW', 
				'Saint Barthelemy'=>'BL', 
				'Saint Helena'=>'SH', 
				'Saint Kitts and Nevis'=>'KN', 
				'Saint Lucia'=>'LC', 
				'Saint Martin'=>'MF', 
				'Saint Pierre and Miquelon'=>'PM', 
				'Saint Vincent and the Grenadines'=>'VC', 
				'Samoa'=>'WS', 
				'San Marino'=>'SM', 
				'Sao Tome and Principe'=>'ST', 
				'Saudi Arabia'=>'SA', 
				'Senegal'=>'SN', 
				'Serbia'=>'RS', 
				'Seychelles'=>'SC', 
				'Sierra Leone'=>'SL', 
				'Singapore'=>'SG', 
				'Slovakia'=>'SK', 
				'Slovenia'=>'SI', 
				'Solomon Islands'=>'SB', 
				'Somalia'=>'SO', 
				'South Africa'=>'ZA', 
				'South Georgia and the Islands'=>'GS', 
				'Spain'=>'ES', 
				'Sri Lanka'=>'LK', 
				'Sudan'=>'SD', 
				'Suriname'=>'SR', 
				'Svalbard'=>'SJ', 
				'Swaziland'=>'SZ', 
				'Sweden'=>'SE', 
				'Switzerland'=>'CH', 
				'Syria'=>'SY', 
				'Taiwan'=>'TW', 
				'Tajikistan'=>'TJ', 
				'Tanzania'=>'TZ', 
				'Thailand'=>'TH', 
				'Timor-Leste'=>'TL', 
				'Togo'=>'TG', 
				'Tokelau'=>'TK', 
				'Tonga'=>'TO', 
				'Trinidad and Tobago'=>'TT', 
				'Tunisia'=>'TN', 
				'Turkey'=>'TR', 
				'Turkmenistan'=>'TM', 
				'Turks and Caicos Islands'=>'TC', 
				'Tuvalu'=>'TV', 
				'Uganda'=>'UG', 
				'Ukraine'=>'UA', 
				'United Arab Emirates'=>'AE', 
				'United Kingdom'=>'GB', 
				'United States Minor Outlying Islands'=>'UM', 
				'Uruguay'=>'UY', 
				'Uzbekistan'=>'UZ', 
				'Vanuatu'=>'VU', 
				'Venezuela'=>'VE', 
				'Vietnam'=>'VN', 
				'Virgin Islands'=>'VI', 
				'Wallis and Futuna'=>'WF', 
				'Western Sahara'=>'EH', 
				'Yemen'=>'YE', 
				'Zambia'=>'ZM'
			);
			if (isset($countryCodes[$country])) 
				$country = $countryCodes[$country];
			else {
				foreach ($countryCodes as $countryName => $countryCode) {
					if ($countryCode == $country) {
						$country = $countryName;
						break;
					}
				}
			}
			
			return $country;
	}
	
	private static function getState($state)
	{
		$stateIndexes = array(
				'Alabama'=>'AL',
				'Alaska'=>'AK',
				'Arizona'=>'AZ',
				'Arkansas'=>'AR',
				'California'=>'CA',
				'Colorado'=>'CO',
				'Connecticut'=>'CT',
				'Delaware'=>'DE',
				'Florida'=>'FL',
				'Georgia'=>'GA',
				'Hawaii'=>'HI',
				'Idaho'=>'ID',
				'Illinois'=>'IL',
				'Indiana'=>'IN',
				'Iowa'=>'IA',
				'Kansas'=>'KS',
				'Kentucky'=>'KY',
				'Louisiana'=>'LA',
				'Maine'=>'ME',
				'Maryland'=>'MD',
				'Massachusetts'=>'MA',
				'Michigan'=>'MI',
				'Minnesota'=>'MN',
				'Mississippi'=>'MS',
				'Missouri'=>'MO',
				'Montana'=>'MT',
				'Nebraska'=>'NE',
				'Nevada'=>'NV',
				'New Hampshire'=>'NH',
				'New Jersey'=>'NJ',
				'New Mexico'=>'NM',
				'New York'=>'NY',
				'North Carolina'=>'NC',
				'North Dakota'=>'ND',
				'Ohio'=>'OH',
				'Oklahoma'=>'OK',
				'Oregon'=>'OR',
				'Pennsylvania'=>'PA',
				'Rhode Island'=>'RI',
				'South Carolina'=>'SC',
				'South Dakota'=>'SD',
				'Tennessee'=>'TN',
				'Texas'=>'TX',
				'Utah'=>'UT',
				'Vermont'=>'VT',
				'Virginia'=>'VA',
				'Washington'=>'WA',
				'West Virginia'=>'WV',
				'Wisconsin'=>'WI',
				'Wyoming'=>'WY',
				'District of Columbia'=>'DC',
				'American Samoa'=>'AS',
				'Guam'=>'GU',
				'Northern Mariana Islands'=>'MP',
				'Puerto Rico'=>'PR',
				'United\'s Minor Outlying Islands'=>'UM',
				'Virgin Islands'=>'VI'
			);
			if (isset($stateIndexes[$state])) 
				$state = $stateIndexes[$state];
			else {
				foreach ($stateIndexes as $stateName => $stateIndex) {
					if ($stateIndex == $state) {
						$state = $stateName;
						break;
					}
				}
			}
			
			return $state;
	}
	
	private static function getEmploymentType($employmentType)
	{
		$employmentTypeList = array(
			'Full time' => '139',
			'Part time' => '141',
			'Contractor' => '142|143',
			'Intern' => '2106',
			'Seasonal' => '140',
			);
		$result = array();
		foreach ($employmentType as $employment) {
			if (isset($employmentTypeList[$employment]))
				$result[] = $employmentTypeList[$employment];
		}
		$result = array_unique($result);
		return count($result)>1?173:array_pop($result);
	}

	private static function getCategory($categories)
	{
		$industryList = require_once 'industry_config.php';
		$result = array();
		$fieldSID = SJB_ListingFieldManager::getListingFieldSIDByID('JobCategory');
		$fieldInfo = SJB_ListingFieldDBManager::getListValuesBySID($fieldSID);
		$fieldList = array();
		foreach ($fieldInfo as $val) 
			$fieldList[$val['id']] = $val['caption'];
			
		foreach ($categories as $category) {
			if ( !empty($fieldList[$category]) && isset($industryList[$fieldList[$category]]))
				$result[] = $industryList[$fieldList[$category]];
		}
		
		return array_unique($result);
	}
}
