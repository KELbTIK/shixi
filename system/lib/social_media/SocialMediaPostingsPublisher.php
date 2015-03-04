<?php

class SJB_SocialMediaPostingsPublisher
{
	/**
	 * @var array
	 */
	public $feedInfo;

	/**
	 * @var string
	 */
	public $listingTypeID = 'Job';

	/**
	 * @var null
	 */
	public $networkID = null;
	/**
	 * @var string
	 */
	protected $template = 'post_to_social_network.tpl';

	/**
	 * @var SJB_TemplateProcessor
	 */
	protected $tp;

	/**
	 * @var int
	 */
	public $postedListingsNum = 0;

	/**
	 * @param $feedInfo
	 * @param $networkID
	 * @return SJB_FacebookPostingsPublisher|SJB_LinkedInPostingsPublisher
	 */
	public static function getPublisher($feedInfo, $networkID)
	{
		switch ($networkID) {
			case 'facebook':
				return new SJB_FacebookPostingsPublisher($feedInfo);
			case 'linkedin':
				return new SJB_LinkedInPostingsPublisher($feedInfo);
			case 'twitter':
				return new SJB_TwitterPostingsPublisher($feedInfo);
			default:
				break;
		}

	}

	/**
	 * @param $listingSID
	 * @return bool
	 */
	public function postListing($listingSID)
	{
		return true;
	}

	/**
	 * @return int|mixed
	 */
	public function getPostingLimitForFeed()
	{
		$feedPostingLimit = SJB_Array::get($this->feedInfo, 'posting_limit');
		return ((int) $feedPostingLimit) > 0 ? $feedPostingLimit : 1000;
	}

	/**
	 * @return array
	 */
	public function getListingsSIDsToPostByFeed()
	{
		$foundListingsSIDsForFeed = $this->getListingsSIDsByFeedCriteria();
		if (!empty($foundListingsSIDsForFeed)) {
			$postedListingsSIDs = $this->getPostedListingsSIDsForFeed($foundListingsSIDsForFeed);
			foreach ($foundListingsSIDsForFeed as $key => $listingSID) {
				if (in_array($listingSID, $postedListingsSIDs)) {
					unset($foundListingsSIDsForFeed[$key]);
				}
			}
		}

		return $foundListingsSIDsForFeed;
	}

	/**
	 * @return array
	 */
	public function getListingsSIDsByFeedCriteria()
	{
		$searchData = $this->prepareSearchData($this->feedInfo['search_data']);
		$searchResultsTP = new SJB_SearchResultsTP($searchData, $this->listingTypeID);
		$searchResultsTP->getChargedTemplateProcessor();
		return $searchResultsTP->found_listings_sids;
	}

	/**
	 * @param $searchData
	 * @return mixed
	 */
	public function prepareSearchData($searchData)
	{
		$request = unserialize($searchData);
		$request['action'] = 'search';
		$request['listing_type']['equal'] = $this->listingTypeID;
		$request['default_listings_per_page'] = 100;
		$request['default_sorting_field'] = 'activation_date';
		$request['default_sorting_order'] = 'DESC';

		return $request;
	}

	/**
	 * @param array $listings
	 * @return array
	 */
	public function getPostedListingsSIDsForFeed(array $listings)
	{
		$postedListingsSIDs = array();
		if (!empty($listings)) {
			$query = 'SELECT `listing_sid` FROM `social_network_postings` WHERE `feed_sid` = ?n AND `listing_sid` IN (?l) AND `network_id` = ?s';
			$results = SJB_DB::query($query, $this->feedInfo['sid'], $listings, $this->networkID);
			foreach ($results as $result) {
				array_push($postedListingsSIDs, $result['listing_sid']);
			}
		}

		return $postedListingsSIDs;
	}

	/**
	 * @return int
	 */
	public function getPostedTodayByFeed()
	{
		$query = "SELECT count(`listing_sid`) as `posted` FROM `social_network_postings`
					WHERE `feed_sid` = ?n AND (INTERVAL 1 DAY + `date`) > NOW() AND `network_id` = ?s";
		$result = SJB_DB::queryValue($query, $this->feedInfo["sid"], $this->networkID);
		return (int)$result;
	}

	/**
	 * @param $url
	 * @return null
	 */
	public function getBitLyLinkForUrl($url)
	{
		return null;
	}

	/**
	 * @param $listingSID
	 * @param null $postID
	 */
	public function saveListingAsPostedForCurrentFeed($listingSID, $postID = null)
	{
		SJB_DB::query("INSERT INTO `social_network_postings` (`feed_sid`, `network_id`, `listing_sid`, `date`, `post_id`) VALUES(?n, ?s, ?n, NOW(), ?s)", $this->feedInfo['sid'], $this->networkID, $listingSID, $postID);
	}

}

/**
 * Class SJB_FeedException
 */
class SJB_FeedException extends Exception
{
	/**
	 * @param string $message
	 * @param array $params
	 */
	public function __construct($message, array $params)
	{
		$this->errorMessage = $message;
		$this->params = $params;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage()
	{
		return $this->errorMessage;
	}
}