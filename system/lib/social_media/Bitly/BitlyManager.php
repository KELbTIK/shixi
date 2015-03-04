<?php

class SJB_BitlyManager
{
	private static $_bitlyOauthApi = 'https://api-ssl.bit.ly/v3/';
	private static $_shortenUrl;

	/**
	 * @return bool|string
	 */
	public static function getBitlyTokenId()
	{
		return SJB_Settings::getValue('bitlyTokenId');
	}

	/**
	 * @param $url
	 * @param $curl
	 * @return mixed
	 * @throws Exception
	 */
	public static function bitlyCurl($url, $curl = false)
	{
		try {
			$curl = $curl ? $curl : curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_TIMEOUT, 4);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			$output = curl_exec($curl);
		} catch (Exception $e) {
			throw new Exception(curl_error($curl));
		}
		if (empty($output)) {
			return self::bitlyCurl($url, $curl);
		}
		curl_close($curl);
		return $output;
	}

	/**
	 * @param $listingSid
	 * @return string
	 */
	public static function getBitlyShortenUrlByListingSid($listingSid)
	{
		$listingInfo       = SJB_ListingManager::getListingInfoBySID($listingSid);
		$listingType       = strtolower(SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo["listing_type_sid"]));
		$listingLongUrl    = SJB_System::getSystemSettings("USER_SITE_URL") . "/display-$listingType/$listingSid/";
		self::$_shortenUrl = self::getListingBitlyShortedUrlBySid($listingSid);

		if (empty(self::$_shortenUrl)) {
			$oAuthUrl = self::$_bitlyOauthApi . "shorten?access_token=" . self::getBitlyTokenId() . "&longUrl=" . urlencode($listingLongUrl);
			$output   = json_decode(self::bitlyCurl($oAuthUrl));
			if (isset($output->{"data"}->{"hash"})) {
				self::$_shortenUrl = $output->{"data"}->{"url"};
				self::saveBitlyShortenUrlByListingSid($listingSid);
			} else {
				return $listingLongUrl;
			}
		}
		return self::$_shortenUrl;
	}

	/**
	 * @param $listingSid
	 * @return bool|int
	 */
	private static function getListingBitlyShortedUrlBySid($listingSid)
	{
		return SJB_DB::queryValue("SELECT `value` FROM `bitly` WHERE `listingSid` = ?n", $listingSid);
	}

	/**
	 * @param $listingSid
	 */
	public static function saveBitlyShortenUrlByListingSid($listingSid)
	{
		SJB_DB::query('INSERT INTO `bitly` SET `value` = ?s, `listingSid` = ?n', self::$_shortenUrl, $listingSid);
	}
}
