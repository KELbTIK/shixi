<?php
class SJB_TwitterPostingsPublisher extends SJB_SocialMediaPostingsPublisher
{

	/**
	 * @param $feedInfo
	 */
	function __construct($feedInfo)
	{
		$this->networkID = 'twitter';
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
		$zendServiceTwitter = SJB_TwitterSocialMedia::getZendServiceTwitter($this->feedInfo);
		$post = $this->getMessageByListingSIDToPost($listingSID);
		$response = $zendServiceTwitter->statusesUpdate($post);

		if (!empty($response->error)) {
			$params = array($listingSID, $response->error);
			$message = 'Error with listing #$param_0 : $param_1';
			throw new SJB_FeedException($message, $params);
		}

		sleep(5);

		return true;
	}

	public function getMessageByListingSIDToPost($listingSID)
	{
		$listing = SJB_ListingManager::getObjectBySID($listingSID);
		if (!$listing instanceof SJB_Listing) {
			$params = array($listingSID);
			$message = 'Listing #$param_0 does not exist in system';
			throw new SJB_FeedException($message, $params);
		}

		$listingInfo = SJB_ListingManager::createTemplateStructureForListing($listing);

		$link = SJB_BitlyManager::getBitlyShortenUrlByListingSid($listingSID);
		$link = " {$link} {$this->feedInfo['hash_tags']}";

		$userInfo = SJB_Array::get($listingInfo, 'user');

		$this->tp->assign('post_template', $this->feedInfo['post_template']);
		$this->tp->assign('listing', $listingInfo);
		$this->tp->assign('user', $userInfo);
		$text = $this->tp->fetch($this->template);

		$post = $text . $link;
		if (mb_strlen($post) > 138) {
			$countStrCut = 138 - mb_strlen($post) - 3;
			$text = mb_substr($text, 0, $countStrCut) . '...';
			$post = $text . $link;
		}

		return $post;
	}
}
