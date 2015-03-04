<?php

class SJB_TwitterSocialMedia
{
	const NETWORK_ID = 'twitter';

	public function getAccessToken($feedSID = null, $action = null, &$errors)
	{
		SJB_Session::setValue('twitterFeed', serialize($_REQUEST));

		if ($feedSID != null) {
			$feedInfo = SJB_SocialMedia::getFeedInfoByNetworkIdAndSID(self::NETWORK_ID, $feedSID);
		}
		if ($action != 'grant') {
			$feedInfo['consumerKey'] = SJB_Request::getVar('consumerKey');
			$feedInfo['consumerSecret'] = SJB_Request::getVar('consumerSecret');
			$feedInfo['account_id'] = SJB_Request::getVar('account_id');
		}

		$config = array(
			'callbackUrl'           => self::getCallBackUrl($feedSID, $action, SJB_Request::getVar('submit')),
			'siteUrl'               => 'http://twitter.com/oauth',
			'consumerKey'           => $feedInfo['consumerKey'],
			'consumerSecret'        => $feedInfo['consumerSecret'],
			'requestTokenUrl'       => 'https://api.twitter.com/oauth/request_token',
			'userAuthorizationUrl'  => 'https://api.twitter.com/oauth/authorize',
			'accessTokenUrl'        => 'https://api.twitter.com/oauth/access_token',
		);

		$consumer = new Zend_Oauth_Consumer($config);
		$client = new Zend_Http_Client();
		$client->setConfig(array('sslcert' => 'cacert.pem'));
		$consumer->setHttpClient($client);

		$sessionTwitterRequestToken = SJB_Session::getValue('TWITTER_REQUEST_TOKEN');
		if (SJB_Request::getVar('process_token', false) && !is_null($sessionTwitterRequestToken)) {
			$accessToken = $consumer->getAccessToken($_GET, unserialize($sessionTwitterRequestToken));
			$feedInfo['access_token'] = $accessToken;
			$twitter = self::getZendServiceTwitter($feedInfo, $accessToken);

			$response = $twitter->account->accountVerifyCredentials()->toValue();
			if (!empty($response->screen_name) && strtolower($response->screen_name) == strtolower($feedInfo['account_id'])) {
				return $accessToken;
			} else {
				$errors[] = 'Twitter account verification failed';
				return false;
			}
		} else {
			if ($requestToken = $consumer->getRequestToken()) {
				SJB_Session::setValue('TWITTER_REQUEST_TOKEN', serialize($requestToken));
				$consumer->redirect();
			} else {
				$errors[] = 'Could not retrieve a valid Token. Please check "Consumer Key" and "Consumer secret"';
				return false;
			}
		}
	}

	private function getCallBackUrl($feedSID, $action, $formSubmitted)
	{
		$url = SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI() . '?soc_network=twitter&process_token=1';
		if ($formSubmitted) {
			$url .= '&submit=' . $formSubmitted;
		}
		if (!empty($feedSID)) {
			$url .= '&sid=' . $feedSID;
			if ($action == 'grant') {
				$url .= '&action=authorize&sub_action=' . $action;
			} else {
				$url .= '&action=' . $action;
			}
		} else {
			$url .= '&action=' . $action;
		}
		return $url;
	}

	/**
	 * @param $feedSID
	 * @return mixed|null
	 */
	public function getSavedAccessToken($feedSID)
	{
		if ($feedSID) {
			$accessToken = SJB_DB::queryValue('SELECT `access_token` FROM `twitter_feeds` WHERE `sid` = ?s', $feedSID);

			if (!empty($accessToken)) {
				return unserialize($accessToken);
			}
		}
		return null;
	}

	public static function updateFeedToken($sid, $token)
	{
		SJB_DB::queryExec('UPDATE `twitter_feeds` SET `access_token` = ?s WHERE `sid` = ?n', serialize($token), $sid);
	}

	/**
	 * @return bool
	 */
	public function approveAccount()
	{
		return false;
	}

	/**
	 * @param $feedInfo
	 * @param Zend_Oauth_Token_Access $accessToken
	 * @return Zend_Service_Twitter
	 */
	public static function getZendServiceTwitter($feedInfo, Zend_Oauth_Token_Access $accessToken = null)
	{
		if (!$accessToken) {
			$accessToken = !empty($feedInfo['access_token']) ? unserialize($feedInfo['access_token']) : '';
		}

		return new Zend_Service_Twitter(
			array(
				'username'      => $feedInfo['account_id'],
				'accessToken'   => $accessToken,
				'oauthOptions'  => array(
					'consumerKey'       => $feedInfo['consumerKey'],
					'consumerSecret'    => $feedInfo['consumerSecret'],
				)
			)
		);
	}
}
