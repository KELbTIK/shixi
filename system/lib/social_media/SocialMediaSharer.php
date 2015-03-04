<?php

class SJB_SocialMediaSharer
{

	/**
	 * @param $listingSID
	 * @return mixed
	 */
	public static function getButtons($listingSID)
	{
		$buttons = array();
		
		$socialNetworks = SJB_SocialMediaSharer::getSocialNetworks();
		if ($socialNetworks) {
			foreach ($socialNetworks as $network) {
				if (self::isNetworkAllowed($network, $listingSID)) {
					$buttons[$network] = SJB_SocialMediaSharer::getButtonDetailsByListingSID($listingSID);
				}
			}
		}
		
		return $buttons;
	}

	/**
	 * @param  string $network
	 * @param  int    $listingSID
	 * @return bool
	 */
	private static function isNetworkAllowed($network, $listingSID)
	{
		$allowed = false;
		if (SJB_Settings::getSettingByName("enable_job_sharing_for_users_{$network}")) {
			$permission = SJB_ListingDBManager::getPermissionByListingSid('post_jobs_on_social_networks', $listingSID);
			if ($permission == 'deny') {
				$allowed = false;
			}
			else if ($permission == 'allow' || SJB_Acl::getInstance()->isAllowed('post_jobs_on_social_networks')) {
				$allowed = true;
			}
		}
		
		return $allowed;
	}

	/**
	 * @return array
	 */
	public static function getSocialNetworks()
	{
		return array('facebook', 'twitter', 'linkedin', 'googleplus');
	}

	/**
	 * @param $listingSID
	 * @return array|bool
	 */
	private static function getButtonDetailsByListingSID($listingSID)
	{
		return SJB_SocialMediaSharer::getButtonDetailsStructureByListingSID($listingSID);
	}

	/**
	 * @param $listingSID
	 * @return array|bool
	 */
	private static function getButtonDetailsStructureByListingSID($listingSID)
	{
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);

		if (!empty($listingInfo)) {
			return  array(
				'link'        => SJB_ListingManager::getListingUrlBySID($listingSID),
				'shorten_url' => SJB_BitlyManager::getBitlyShortenUrlByListingSid($listingSID),
				'title'       => $listingInfo['Title'],
			);
		}

		return false;
	}

}