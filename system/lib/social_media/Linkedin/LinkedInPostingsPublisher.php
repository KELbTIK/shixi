<?php
class SJB_LinkedInPostingsPublisher extends SJB_SocialMediaPostingsPublisher
{

	/**
	 * @param $feedInfo
	 */
	function __construct($feedInfo)
	{
		$this->networkID = 'linkedin';
		$this->feedInfo = $feedInfo;
		$this->tp = SJB_System::getTemplateProcessor();
	}

	/**
	 * @param $title
	 * @param $text
	 * @param $link
	 * @param $imageUrl
	 * @return mixed
	 */
	public function getContentToPostToGroup($title, $text, $link, $imageUrl)
	{
		$siteUrl = SJB_System::getSystemSettings("USER_SITE_URL");
		$xml = "
		<post>
			<title><![CDATA[{$siteUrl}]]></title>
			<summary></summary>
			<content>
				<title><![CDATA[{$title}]]></title>
				<description><![CDATA[{$text}]]></description>
				<submitted-url><![CDATA[{$link}]]></submitted-url>
				<submitted-image-url><![CDATA[{$imageUrl}]]></submitted-image-url>
			</content>
		</post>";
		$result = new SimpleXMLElement($xml);
		return $result->asXML();
	}

	/**
	 * @param $title
	 * @param $text
	 * @param $link
	 * @param $imageUrl
	 * @return mixed
	 */
	public function getContentToPostToUpdates($title, $text, $link, $imageUrl)
	{
		$xml = "<share>
			<comment></comment>
			<content>
				<title><![CDATA[{$title}]]></title>
				<description><![CDATA[{$text}]]></description>
				<submitted-url><![CDATA[{$link}]]></submitted-url>
				<submitted-image-url><![CDATA[{$imageUrl}]]></submitted-image-url>
			</content>
			<visibility>
			<code>anyone</code>
			</visibility>
			</share>";
		$result = new SimpleXMLElement($xml);
		return $result->asXML();
	}

	/**
	 * @param int $listingSID
	 * @return bool
	 */
	public function postListing($listingSID)
	{
		$linkedIn = new SJB_LinkedIn();
		$linkedIn->_getAccessToken(unserialize($this->feedInfo['access_token']));
		$listing = SJB_ListingManager::getObjectBySID($listingSID);
		if (!$listing instanceof SJB_Listing) {
			$params = array($listingSID);
			$message = 'Listing #$param_0 does not exist in system';
			throw new SJB_FeedException($message, $params);
		}
		$listingStructure = SJB_ListingManager::createTemplateStructureForListing($listing);
		$link = SJB_ListingManager::getListingUrlBySID($listingSID);
		$userInfo = SJB_Array::get($listingStructure, 'user');

		$this->tp->assign('post_template', $this->feedInfo['post_template']);
		$this->tp->assign('listing', $listingStructure);
		$this->tp->assign('user', $userInfo);
		$title = $this->tp->fetch($this->template);

		if ($this->feedInfo['post_to_updates']) {
			$post['content'] = self::getContentToPostToUpdates($title, $listingStructure['Title'], $link, $userInfo['Logo']['file_url']);
			$linkedIn->postToUpdates($post);
		}
		if (!empty($this->feedInfo['post_to_groups']) && !empty($this->feedInfo['groups'])) {
			$post['content'] = self::getContentToPostToGroup($title, $listingStructure['Title'], $link, $userInfo['Logo']['file_url']);
			$post['groups'] = explode(',', $this->feedInfo['groups']);
			$linkedIn->postToGroups($post);
		}
		return true;
	}
}
