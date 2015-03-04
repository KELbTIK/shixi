<?php

require_once 'GooglePlus/Google_Client.php';
require_once 'GooglePlus/contrib/Google_PlusService.php';
require_once 'GooglePlus/contrib/Google_Oauth2Service.php';

class GooglePlusSocialPlugin extends SJB_SocialPlugin
{

	/**
	 * @var string
	 */
	protected static $network = 'google_plus';

	/**
	 * @var string
	 */
	protected static $networkCaption = 'Google+';

	/**
	 * @var array
	 */
	protected static $aUserFields = array('email', 'LastName');

	/**
	 * @var null|GooglePlus
	 */
	private static $object = null;

	/*
	 * Set Scopes
	 */
	public $scopes = array(
					'https://www.googleapis.com/auth/userinfo.email',
					'https://www.googleapis.com/auth/plus.login'
					);

	public function initialize()
	{
		self::$object = new Google_Client();
		self::$object->setApplicationName('Google+ PHP SJB Application');
		self::$object->setClientId(SJB_System::getSettingByName('oauth2_client_id'));
		self::$object->setClientSecret(SJB_System::getSettingByName('client_secret'));
		self::$object->setRedirectUri(SJB_System::getSystemSettings('SITE_URL') . '/social/?network=google_plus');
		self::$object->setDeveloperKey(SJB_System::getSettingByName('developer_key'));
		self::$object->setScopes($this->scopes);

		return $this->getProfileInformation();
	}

	public function __construct()
	{
		$_SESSION['sn']['authorized'] = (isset($_SESSION['sn']['authorized'])) ? $_SESSION['sn']['authorized'] : false;

		if ($_SESSION['sn']['authorized'] === true && self::$network === $_SESSION['sn']['network']) {
			$this->initialize();
		}
	}

	public function init()
	{
		$this->cleanSessionData(self::$network);
		$error = SJB_Request::getVar('error', false);
		if ($error && $error == 'access_denied') {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL'));
		}
		if (isset($_GET['code'])) {
			$this->initialize();
			$googlePlusService = new Google_PlusService(self::$object);
			$oauth2 = new Google_Oauth2Service(self::$object);
			self::$object->authenticate($_GET['code']);
			$_SESSION['access_token'] = self::$object->getAccessToken();
			if (!empty($_SESSION['access_token'])) {
				self::$oProfile = self::getProfileInformation($googlePlusService, $oauth2);
				if (self::$oProfile) {
					$this->flagSocialPluginInSession(self::$network);
					self::$oSocialPlugin = $this;
					$this->redirectToRegistrationSocialPage();
				}
			}
		}
		if (is_null(self::$object) && empty($_SESSION['sn']['authorized'])) {
			$this->initialize();
			$authUrl = self::$object->createAuthUrl();
			SJB_HelperFunctions::redirect($authUrl);
		}
		elseif (self::$oProfile && !parent::ifUserIsRegistered(self::$network)) {
			$this->redirectToRegistrationSocialPage();
		}
	}

	/**
	 * @param SJB_Object $object
	 * @return SJB_Object
	 */
	public static function fillRegistrationDataWithUser(SJB_Object $object)
	{
		if (self::$oSocialPlugin instanceof GooglePlusSocialPlugin && $oProfile = self::getProfileObject()) {
			/** @var $oProperty SJB_ObjectProperty */
			foreach ($object->getProperties() as $oProperty) {
				$value = '';
				switch ($oProperty->getID()) {

					case 'email':
						if (!empty($oProfile['email']) && empty($_POST['email'])) {
							$value = array('original' => $oProfile['email'], 'confirmed' => $oProfile['email']);
						}
						break;

					case 'FirstName':
						if (!empty($oProfile['name']['givenName'])) {
							$value = $oProfile['name']['givenName'];
						}
						break;

					case 'LastName':
						if (!empty($oProfile['name']['familyName'])) {
							$value = $oProfile['name']['familyName'];
						}
						break;

					case 'ContactName':
						if (!empty($oProfile['name']['formatted'])) {
							$value = $oProfile['name']['formatted'];
						}
						break;

					case 'CompanyName':
						if (!empty($oProfile['organizations']) && !empty($oProfile['organizations'][0]['name'])) {
							$value = $oProfile['organizations'][0]['name'];
						} elseif (!empty($oProfile['nickname'])) {
							$value = $oProfile['nickname'];
						} elseif (!empty($oProfile['displayName'])) {
							$value = $oProfile['displayName'];
						}
						break;


					case 'sendmail':
						$value = false;
						break;

					case 'username':
					case 'password':
						continue(2);
						break;

					default:
						$propertyCanBeDeleted = !in_array($oProperty->getID(), self::$aUserFields)
							&& !in_array($oProperty->getID(), self::$aListingFields)
							&& !$oProperty->isRequired();
						if ($propertyCanBeDeleted) {
							$object->deleteProperty($oProperty->getID());
						}
						break;
				}
				if (!empty($value)) {
					$reqVal = SJB_Request::getVar($oProperty->getID(), false);
					if (empty($reqVal)) {
						$object->setPropertyValue($oProperty->getID(), $value);
					}
				}
			}
		}

		return $object;
	}

	public function getProfileInformation($googlePlusService = false, $oauth2 = false)
	{
		if (!empty($_SESSION['google_plus']['profile_info']) && $_SESSION['sn']['authorized'] === true) {
			self::$oProfile = unserialize($_SESSION['google_plus']['profile_info']);
		} elseif ($googlePlusService && $oauth2) {
			if (self::$object) {
				if (isset($_SESSION['access_token'])) {
					self::$object->setAccessToken($_SESSION['access_token']);
				}
				if (self::$object->getAccessToken()) {
					self::$oProfile = $googlePlusService->people->get('me');
					$userInfo = $oauth2->userinfo->get();
					self::$oProfile['email'] = $userInfo['email'];
					$_SESSION['google_plus']['profile_info'] = serialize(self::$oProfile);
				}
			}
		}
		if (self::$oProfile) {
			self::$oSocialPlugin = $this;
		} else {
			unset($_SESSION['google_plus']);
		}
		return self::$oProfile;
	}

	/**
	 * @return string
	 */
	public static function getNetwork()
	{
		return self::$network;
	}

	/**
	 * @return string
	 */
	public static function getNetworkCaption()
	{
		return self::$networkCaption;
	}

	/**
	 * save social information
	 * access token,
	 */
	public function saveProfileSystemInfo()
	{
		if ($oProfile = self::getProfileObject()) {
			$googlePlusID = $oProfile['id'];
			$profileInfo = serialize($oProfile);

			if ($googlePlusID && $profileInfo) {
				return SJB_DB::query('INSERT INTO `google` SET `google_id` = ?s, `profile_info` = ?s
					ON DUPLICATE KEY UPDATE `profile_info`=?s', $googlePlusID, $profileInfo, $profileInfo);
			}

			return false;
		}

		return null;
	}

	/**
	 * @param SJB_User $user
	 * @return SJB_User
	 */
	public static function addReferenceDetails(SJB_User $user)
	{
		if (self::$oProfile && self::$oSocialPlugin) {
			self::definePasswordAndUsernameByEmail($user);

			$user->addProperty(array(
				'id' => 'reference_uid',
				'type' => 'string',
				'value' => self::getNetwork() . '_' . self::$oProfile['id'],
				'is_system' => true));
			$user->addProperty(array(
				'id' => 'active',
				'type' => 'boolean',
				'value' => true,
				'is_system' => true));
		}
		return $user;
	}

	public static function logout()
	{
		if (self::$object && self::$oProfile) {
			unset($_SESSION['google_plus']);
			unset($_SESSION['sn']);
		}
	}

	public static function ifUserIsRegisteredByReferenceUid($referenceUid)
	{
		$result = SJB_DB::query('SELECT `sid` FROM `users` WHERE `reference_uid` = ?s', $referenceUid);
		if (!empty($result)) {
			$result = array_shift($result);
			return $result['sid'];
		} else {
			$userInfo = SJB_DB::query("SELECT `sid`,`reference_uid` FROM `users` WHERE `username` = `email` AND `email` = ?s", self::$oProfile['email']);
			$userInfo = array_pop($userInfo);
			if (!empty($userInfo)) {
				$oldGoogleID = str_replace('google_', '', $userInfo['reference_uid']);
				SJB_DB::queryExec("UPDATE `users` SET `reference_uid` = ?s WHERE `sid` = ?n", $referenceUid, $userInfo['sid']);
				SJB_DB::queryExec('UPDATE `google` SET `google_id` = ?s, `profile_info` = ?s WHERE `google_id` = ?s', self::$oProfile['id'], serialize(self::$oProfile), $oldGoogleID);
				return $userInfo['sid'];
			}
		}
		return false;
	}
}