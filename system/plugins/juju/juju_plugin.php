<?php

class JujuPlugin extends SJB_PluginAbstract
{
	private static $jujuListings = array();

	/**
	 * @return array
	 */
	public function pluginSettings()
	{
		$numberOfListings = array();
		for ($i = 1; $i <= 20; $i++) {
			$numberOfListings[] = array(
				'id'      => "{$i}",
				'caption' => "{$i}",
			);
		}
		
		return array(
			array (
				'id'          => 'JujuID',
				'caption'     => 'Publisher ID',
				'type'        => 'string',
				'comment'     => 'To get Publisher ID please register a <a href="http://www.job-search-engine.com/publisher/signup" target="_blank">publisher\'s profile at Juju</a>.',
				'length'      => '50',
				'is_required' => true,
				'order'       => null,
			),
			array (
				'id'          => 'JujuKeywords',
				'caption'     => 'Keywords',
				'type'        => 'string',
				'length'      => '50',
				'order'       => null,
			),
			array (
				'id'          => 'JujuLocation',
				'caption'     => 'Location',
				'type'        => 'string',
				'comment'     => 'State, County, City or Zip Code',
				'length'      => '50',
				'is_required' => false,
				'order'       => null,
			),
			array (
				'id'          => 'JujuRadius',
				'caption'     => 'Radius',
				'type'        => 'list',
				'list_values' => self::getRadiusValues(),
				'length'      => '50',
				'order'       => null,
			),
			array (
				'id'			=> 'countJujuListings',
				'caption'		=> 'Number of listings',
				'type'			=> 'list',
				'list_values'   => $numberOfListings,
				'comment'		=> 'The Number of listings imported from Juju to be displayed per page in search results.<br />*Juju has a limit of 20 jobs to be backfilled per one request.',
				'length'		=> '50',
				'order'			=> null,
			),
		);
	}

	/**
	 * @param array $arrayOfProviders
	 * @return array
	 */
	public static function registerAsListingsProvider($arrayOfProviders = array())
	{
		$arrayOfProviders[] = 'juju';
		return $arrayOfProviders;
	}

	/**
	 * @param array $listingsStructure
	 * @return array
	 */
	public static function addJujuListingsToListingStructure(array $listingsStructure)
	{
		foreach (self::$jujuListings as $key => $value) {
			$listingsStructure['juju_' . $key] = $value;
		}
		return $listingsStructure;
	}

	/**
	 * @param $params
	 * @return mixed
	 */
	public static function getListingsFromJuju($params)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($params->listing_type_sid);
		if ($listingTypeID == 'Job' && $GLOBALS['uri'] == '/search-results-jobs/' || $GLOBALS['uri'] == '/ajax/') {
			$criteria = $params->criteria_saver->criteria;
			$keywords = self::getKeywords(isset($criteria['keywords']) ? $criteria['keywords'] : array());
			$category = self::getCategory(isset($criteria['JobCategory']['multi_like']) ? $criteria['JobCategory']['multi_like'] : array());
			if (!empty($category)) {
				$keywords .= '+' . $category;
			}
			$location = self::getLocation($criteria, 'JujuLocation');
			$radius   = self::getRadius(isset($criteria['Location']['location']['radius']) ? $criteria['Location']['location']['radius'] : null);
			
			$countListings = self::getCountListings();
			$page          = self::getPage();
			
			$publisherID = SJB_Settings::getSettingByName('JujuID');
			$ip          = SJB_Request::getVar('REMOTE_ADDR', '', 'SERVER');
			$userAgent   = urlencode(SJB_Request::getUserAgent());
			
			$url = "http://api.juju.com/jobs?partnerid={$publisherID}&k={$keywords}&l={$location}&r={$radius}&useragent={$userAgent}&ipaddress={$ip}&jpp={$countListings}&page={$page}&highlight=0";
			self::setListingsUsingUrl($url);
		}
		return $params;
	}

	/**
	 * @param array $keywords
	 * @return string
	 */
	private static function getKeywords(array $keywords)
	{
		$result = '';
		if (!empty($keywords)) {
			foreach ($keywords as $key => $value) {
				if (in_array($key, array('exact_phrase', 'any_words', 'all_words'))) {
					$result .= empty($result) ? urlencode(trim($value)) : '+' . urlencode(trim($value));
				}
			}
		}
		
		if (empty($result)) {
			$result = SJB_Settings::getSettingByName('JujuKeywords');
			$result = urlencode(trim($result));
		}
		
		return empty($result) ? '' : $result;
	}

	/**
	 * @param int $radius
	 * @return string
	 */
	private static function getRadius($radius)
	{
		$result = $radius == null ? SJB_Settings::getSettingByName('JujuRadius') : $radius;
		return empty($result) ? '' : $result;
	}

	/**
	 * @param array $category
	 * @return string
	 */
	private static function getCategory(array $category)
	{
		$result = '';
		if (!empty($category)) {
			$values = SJB_ListingFieldDBManager::getMultilistValuesBySids($category);
			if ($values) {
				foreach ($values as $value) {
					$result .= empty($result) ? urlencode(trim($value['value'])) : '+' . urlencode(trim($value['value']));
				}
			}
		}
		
		return $result;
	}

	/**
	 * @return int
	 */
	private static function getCountListings()
	{
		$result = SJB_Settings::getSettingByName('countJujuListings');
		return empty($result) ? 10 : $result;
	}

	/**
	 * @return string|int
	 */
	private static function getPage()
	{
		return SJB_Request::getVar('page', 1, 'GET');
	}

	/**
	 * @param string $url
	 */
	private static function setListingsUsingUrl($url)
	{
		$listings  = array();
		$xmlString = SJB_HelperFunctions::getUrlContentByCurl($url);
		if ($xmlString !== false) {
			$doc = new DOMDocument();
			try {
				$doc->loadXML($xmlString, LIBXML_NOERROR);
				$results = $doc->getElementsByTagName('item');
				if ($results instanceof DOMNodeList) {
					foreach ($doc->getElementsByTagName('item') as $result) {
						$result = simplexml_import_dom($result);
						$listings[] = array(
							'Title'          => htmlspecialchars_decode((string) $result->title),
							'CompanyName'    => (string) isset($result->company) ? $result->company : '',
							'JobDescription' => (string) $result->description,
							'Location'       => array(
								'Country'       => isset($result->county) ? (string) $result->county : '',
								'State_Code'    => isset($result->state) ? (string) $result->state : '',
								'City'          => (string) $result->city,
							),
							'activation_date' => (string) $result->postdate,
							'url'             => $result->link,
							'target'          => 'target="_blank" ',
							'api'             => 'juju',
							'code'            => '<span id="juju_at"><a href="http://www.juju.com/">jobs</a> by <a href="http://www.juju.com/" title="Job Search"><img src="http://www.job-search-engine.com/assets/images/juju_logo.png" style="width: 54px; height: auto; border: 0; vertical-align: middle;"></a></span>'
						);
					}
				}
			} catch (ErrorException $e) {
				SJB_Logger::error($e->getMessage());
			}
		}
		
		self::$jujuListings = $listings;
	}

	private static function getRadiusValues()
	{
		return array(
			array(
				'id'      => '0',
				'caption' => '0 miles',
			),
			array(
				'id'      => '5',
				'caption' => '5 miles',
			),
			array(
				'id'      => '10',
				'caption' => '10 miles',
			),
			array(
				'id'      => '20',
				'caption' => '20 miles',
			),
			array(
				'id'      => '50',
				'caption' => '50 miles',
			),
			array(
				'id'      => '100',
				'caption' => '100 miles',
			)
		);
	}
}
