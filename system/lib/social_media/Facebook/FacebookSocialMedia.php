<?php

define('FB_APP_ID', SJB_Settings::getSettingByName('fb_appID'));
define('FB_APP_SECRET', SJB_Settings::getSettingByName('fb_appSecret'));

class SJB_FacebookSocialMedia
{

	const NETWORK_ID = 'facebook';

	/**
	 * @var Facebook $object
	 */
	private static $object = null;

	/**
	 * Constructor
	 */
	public function SJB_FacebookSocialMedia()
	{
		// Create our Application instance.
		self::$object = new Facebook(array(
			'appId'  => FB_APP_ID,
			'secret' => FB_APP_SECRET,
		));
	}

	/**
	 * @return Facebook|null
	 */
	public function getObject()
	{
		return self::$object;
	}

	/**
	 * @param string $redirectUrl
	 * @return string
	 */
	public function getLoginUrl($redirectUrl = '')
	{
		$params = array(
			'scope' => 'publish_stream,publish_actions,offline_access',
			'redirect_uri' => $redirectUrl
		);

		return self::$object->getLoginUrl($params);
	}

	/**
	 * @param array $params
	 * @return string
	 */
	public function getLogoutUrl($params=array())
	{
		return self::$object->getLogoutUrl($params);
	}

	/**
	 * @return string
	 */
	public function getUser()
	{
		return self::$object->getUser();
	}

	/**
	 * @return string
	 */
	public function getAccessToken()
	{
		return self::$object->getAccessToken();
	}

	/**
	 * @param string $logoutUrl
	 */
	public function logout($logoutUrl = '')
	{
		if (empty($logoutUrl)) {
			$logoutUrl = self::$object->getLogoutUrl(array('next' => self::getRedirectUrl() . '#postJobs'));
		}

		self::$object->setAccessToken('');
		self::$object->destroySession();
		SJB_HelperFunctions::redirect($logoutUrl);
	}

	/**
	 * @param int $feedSID
	 * @param string $accountId
	 * @param string $accountName
	 */
	public function updateAccessToken($feedSID, $accountId = '', $accountName = '')
	{
		$accessToken    = $this->getAccessToken();
		$expirationDate = date('Y-m-d', time() + 60 * 24 * 60 * 60);
		if (!empty($accountId) && !empty($accountName)) {
			SJB_DB::query("UPDATE `facebook_feeds` SET `access_token` = ?s, expiration_date = ?s, `account_id` = ?s, `account_name` = ?s
						WHERE `sid` = ?n", $accessToken, $expirationDate, $accountId, $accountName, $feedSID);
		} else {
			SJB_DB::query("UPDATE `facebook_feeds` SET `access_token` = ?s, expiration_date = ?s
						WHERE `sid` = ?n", $accessToken, $expirationDate, $feedSID);
		}
	}

	/**
	 * @param null $feedSID
	 * @param null $grantPermission
	 * @return array
	 */
	public function getAccountInfo($feedSID = null, $grantPermission = null)
	{
		$accountInfo = array();
		$accountName = $this->getUser();
		$accessToken = $this->getAccessToken();
		if (empty($accessToken)) {
			$accessToken = self::getSavedAccessToken($feedSID);
		}
		
		if ($feedSID && $grantPermission == 'grant' && !SJB_Request::getVar('grant_proceed')) {
			// After clicking the 'Grant Permission' button we are redirected to Facebook first, in order to log out. 'grant_proceed' means we have already done it. 
			$redirectUrl = $this->getLogoutUrl(
				array(
					'next' => self::getRedirectUrl() . "?action=edit_feed&sub_action=grant&grant_proceed=1&soc_network=facebook&sid={$feedSID}",
					'access_token' => $accessToken,
				)
			);
			
			$this->logout($redirectUrl);
		}
		else if ($grantPermission == 'change_account' && !SJB_Request::getVar('grant_proceed')) {
			if ($feedSID) {
				$redirectUrl = $this->getLogoutUrl(
					array(
						'next' => self::getRedirectUrl() . "?action=edit_feed&sub_action=change_account&grant_proceed=1&sid={$feedSID}",
						'access_token' => $accessToken,
					)
				);
			} else {
				$redirectUrl = $this->getLogoutUrl(
					array(
						'next' => self::getRedirectUrl() . "?{$_SERVER['QUERY_STRING']}&grant_proceed=1",
						'access_token' => $accessToken,
					)
				);
			}
			
			$this->logout($redirectUrl);
		}
		else if (!empty($accountName)) {
			$accountID   = $this->getAccountID($accountName);
			$accountInfo = array(
								'account_id'   => (string)$accountID,
								'account_name' => (string)$accountName,
								'access_token' => (string)$accessToken,
								'isAuthorized' => true
							);
		}
		else if (SJB_Request::getVar('sub_action', null, 'GET') || SJB_Request::getVar('action', null, 'GET') == 'authorize') {
			if ($feedSID && $grantPermission) {
				if ($grantPermission == 'change_account') {
					$redirectUrl = self::getRedirectUrl() . "?action=edit_feed&sub_action=changed&sid={$feedSID}";
				} else {
					// After clicking the 'Grant Permission' button and logout from Facebook we are redirected to the Facebook login form.
					$redirectUrl = self::getRedirectUrl() . "?action=grant&sub_action=grant_proceed&soc_network=facebook&sid={$feedSID}";
				}
			} else {
				$redirectUrl = self::getRedirectUrl() . '?action=authorize';
			}
			
			$loginUrl = $this->getLoginUrl($redirectUrl);
			SJB_HelperFunctions::redirect($loginUrl);
		}
		
		return $accountInfo;
	}

	/**
	 * @return bool
	 */
	public function approveAccount()
	{
		return !SJB_Request::getVar('code', false, 'GET') ? true : false;
	}

	/**
	 * @param string $accountName
	 * @return string
	 */
	private function getAccountID($accountName)
	{
		$userObject  = self::$object->api("/$accountName");
		if (!empty($userObject)) {
			if (isset($userObject['email'])) {
				$accountID = $userObject['email'];
			}
			else if (isset($userObject['name'])) {
				$accountID = $userObject['name'];
			}
			else if (isset($userObject['username'])) {
				$accountID = $userObject['username'];
			} else {
				$accountID = '';
			}
		} else {
			$accountID = '';
		}
		return $accountID;
	}

	/**
	 * @param $feedSID
	 * @return bool|int|mixed
	 */
	public function getSavedAccessToken($feedSID)
	{
		return SJB_DB::queryValue('SELECT `access_token` FROM `facebook_feeds` WHERE `sid` = ?s', $feedSID);
	}

	/**
	 * @return string
	 */
	private static function getRedirectUrl()
	{
		return SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . self::NETWORK_ID;
	}


}