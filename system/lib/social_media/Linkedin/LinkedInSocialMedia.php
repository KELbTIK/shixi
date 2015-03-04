<?php
class SJB_LinkedInSocialMedia
{
	const NETWORK_ID = 'linkedin';

	/**
	 * @var SJB_LinkedIn
	 */
	private static $object;


	/**
	 * @param $callbackUrl
	 * @param null $accessToken
	 * @return SimpleXMLElement
	 * @throws Exception
	 */
	public function authorize($callbackUrl, $accessToken = null) {
		self::$object = new SJB_LinkedIn($callbackUrl);

		if (empty($accessToken)) {
			// check for response from LinkedIn
			if (!$this->isTokenRequested()) {
				self::$object->_getRequestToken();
			} else {
				if (SJB_Request::getVar(SJB_LinkedIn::OAUTH_PROBLEM)) {
					throw new Exception('oAuth Problem: ' . SJB_Request::getVar(SJB_LinkedIn::OAUTH_PROBLEM));
				}
			}
		}
		self::$object->_getAccessToken($accessToken);
		$response = self::$object->getProfileInfo(array('id', 'email-address'));
		return new SimpleXMLElement($response);
	}

	/**
	 * @param null $feedSID
	 * @param null $grantPermission
	 * @return array
	 */
	public function getAccountInfo($feedSID = null, $grantPermission = null) {
		$accessToken = null;
		$callBackUrl = $this->createCallbackUrl($feedSID, $grantPermission);

		if (!$grantPermission && !SJB_Request::getVar('oauth_token', false)) {
			$accessToken = self::getSavedAccessToken($feedSID);
		}
		$oProfile = self::authorize($callBackUrl, $accessToken);
		$accountId = empty($oProfile->{'email-address'}) ? '' : (string) $oProfile->{'email-address'};
		if ($grantPermission && $feedSID) {
			$this->saveAccountInfo($feedSID, $accountId);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . self::NETWORK_ID . '?message=' . urldecode('Account is successfully updated') . '#postJobs');
		}
		$accountInfo = array();
		if ($oProfile->{'error-code'}) {
			throw new Exception($oProfile->{'message'});
		} else {
			$groups = self::getGroups();
			if (!empty($accountId)) {
				$accountInfo = array(
					'account_id'   => $accountId,
					'groups'       => $groups,
					'isAuthorized' => true
				);
			}
		}

		return $accountInfo;
	}

	/**
	 * @param $feedSID
	 * @param string $accountId
	 * @return array|null
	 */
	public function saveAccountInfo($feedSID, $accountId)
	{
		self::$object   = new SJB_LinkedIn($this->createCallbackUrl());
		$accessToken    = self::$object->_getAccessToken();
		$liveTime       = isset($accessToken->_params['oauth_expires_in']) ? $accessToken->_params['oauth_expires_in'] : 60 * 24 * 60 * 60;
		$expirationDate = date('Y-m-d', time() + $liveTime);
		
		$accessToken = serialize($accessToken);
		if (!empty($accountId)) {
			$result = SJB_DB::query('UPDATE `linkedin_feeds` SET `access_token` = ?s, expiration_date = ?s, `account_id` = ?s WHERE `sid` = ?n OR `access_token` = ?s', $accessToken, $expirationDate, $accountId, $feedSID, $accessToken);
		} else {
			$result = SJB_DB::query('UPDATE `linkedin_feeds` SET `access_token` = ?s, expiration_date = ?s WHERE `sid` = ?n OR `access_token` = ?s', $accessToken, $expirationDate, $feedSID, $accessToken);
		}
		
		if ($result) {
			self::$object->setAccessToken(null);
			SJB_Session::unsetValue(self::NETWORK_ID);
		}
		return $result;
	}

	/**
	 * @param null|int $feedSID
	 * @param null|string $grantPermission
	 * @return string
	 */
	public function createCallbackUrl($feedSID = null, $grantPermission = null)
	{
		$callBackUrl = SJB_System::getSystemSettings('SITE_URL') . '/social-media/?soc_network=' . self::NETWORK_ID;
		if ($feedSID && ($grantPermission && $grantPermission == 'change_account')) {
			$callBackUrl .= "&action=edit_feed&sid={$feedSID}&" . SJB_LinkedIn::_GET_RESPONSE . '=1';
		} elseif ($feedSID && ($grantPermission && $grantPermission == 'grant')) {
			$callBackUrl .= "&action=authorize&sub_action=grant&sid={$feedSID}&" . SJB_LinkedIn::_GET_RESPONSE . '=1';
		} else {
			$callBackUrl .= '&action=authorize&' . SJB_LinkedIn::_GET_RESPONSE . '=1';
		}
		return $callBackUrl;
	}

	/**
	 * @return bool
	 */
	private function isTokenRequested()
	{
		return isset($_GET[SJB_LinkedIn::_GET_RESPONSE]);
	}

	/**
	 * @return array|null
	 */
	public function getGroups()
	{
		if (!is_object(self::$object)) {
			self::$object = new SJB_LinkedIn();
		}
		if (self::$object->_getAccessToken()) {
			$groups = self::$object->getGroups();
			if ($groups) {
				$groupValues = array();
				foreach($groups->{'group-membership'} as $groupMembership) {
					$group = $groupMembership->group;
					$groupsList[(string)$group->id] = (string)$group->name;
				}
				if (!empty($groupsList)) {
					asort($groupsList, SORT_STRING);
					foreach ($groupsList as $groupID => $groupName) {
						$groupValues[] = array(
							'id'      => $groupID,
							'caption' => $groupName
						);
					}
					return $groupValues;
				}
			}
		}
		return null;
	}

	/**
	 * @return bool
	 */
	public function approveAccount()
	{
		return false;
	}

	/**
	 * @param $feedSID
	 * @return mixed|null
	 */
	public function getSavedAccessToken($feedSID)
	{
		if ($feedSID) {
			$accessToken = SJB_DB::queryValue('SELECT `access_token` FROM `linkedin_feeds` WHERE `sid` = ?s', $feedSID);

			if (!empty($accessToken)) {
				return unserialize($accessToken);
			}
		}
		return null;
	}
}
