<?php
class SJB_FacebookPostingsPublisher extends SJB_SocialMediaPostingsPublisher
{

	/**
	 * @param $feedInfo
	 */
	function __construct($feedInfo)
	{
		$this->networkID = 'facebook';
		$this->feedInfo = $feedInfo;
		$this->tp = SJB_System::getTemplateProcessor();
	}


	/**
	 * @param $listingSID
	 * @return mixed
	 * @throws Exception
	 */
	public function postListing($listingSID)
	{
		$listing  = SJB_ListingManager::getObjectBySID($listingSID);
		if (!$listing instanceof SJB_Listing) {
			$params = array($listingSID);
			$message = 'Listing #$param_0 does not exist in system';
			throw new SJB_FeedException($message, $params);
		}
		$listingInfo = SJB_ListingManager::createTemplateStructureForListing($listing);
		$listingUrl = SJB_ListingManager::getListingUrlBySID($listingSID);

		$link = " {$listingUrl} {$this->feedInfo['hash_tags']}";
		$userInfo = SJB_Array::get($listingInfo, 'user');
		$this->tp->assign('post_template', $this->feedInfo['post_template']);
		$this->tp->assign('listing', $listingInfo);
		$this->tp->assign('user', $userInfo);
		$text = $this->tp->fetch($this->template);
		$picture = $userInfo['Logo']['file_url'] == null ? SJB_System::getSystemSettings('SITE_URL') . '/' . SJB_TemplatePathManager::getAbsoluteImagePath(SJB_TemplateSupplier::getUserCurrentTheme(), 'main', 'logo.png' ) : $userInfo['Logo']['file_url'];

		return $this->postToWall($listingSID, $text . $link, $listingUrl, $picture);
	}

	/**
	 * @param $listingSID
	 * @param $message
	 * @param $listingUrl
	 * @param $pictureUrl
	 * @return mixed
	 * @throws Exception
	 */
	private function postToWall($listingSID, $message, $listingUrl, $pictureUrl)
	{
		$facebook = new SJB_FacebookSocialMedia();
		$facebookObj = $facebook->getObject();
		$accountName = $this->feedInfo['account_name'];

		$args = array(
			'access_token' => $this->feedInfo['access_token'],
			'message'      => $message,
			'link'         => $listingUrl,
			'picture'      => $pictureUrl,
		);

		$response = $facebookObj->api("/$accountName/feed", "post", $args);

		if (!empty($response->error)) {
			$params = array($listingSID, $response->error);
			$message = 'Error with listing #$param_0 : $param_1';
			throw new Exception($message, $params);
		}
		return $response;
	}
}