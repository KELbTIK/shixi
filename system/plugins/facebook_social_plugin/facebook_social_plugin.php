<?php
// Set Parameters
define('FB_CONSUMER_KEY', SJB_Settings::getSettingByName('fb_appID'));
define('FB_CONSUMER_SECRET', SJB_Settings::getSettingByName('fb_appSecret'));

require_once('facebook/facebook.php');
require_once 'facebook/FacebookDetails.php';

class FacebookSocialPlugin extends SJB_SocialPlugin
{
	const NETWORK_ID = 'facebook';
	const NETWORK_CAPTION = 'Facebook';

	/**
	 * @var Facebook $object
	 */
	private static $object = null;

	/**
	 * list of facebook's permissions
	 * @url https://developers.facebook.com/docs/authentication/permissions/
	 * @var array
	 */
	private static $aPermissionFields = array(
		'user_about_me',
		'user_work_history',
		'user_education_history',
		'email',
		'user_website',
		'user_birthday',
		'user_hometown',
		'user_interests',
		'read_friendlists',
		'publish_stream',
		'user_location',
		'offline_access',
	);

	public static function getNetwork()
	{
		return self::NETWORK_ID;
	}

	public static function getNetworkCaption()
	{
		return self::NETWORK_CAPTION;
	}

	public static function createFacebookInstance()
	{
		// Create our Application instance.
		self::$object = new Facebook(array('appId' => FB_CONSUMER_KEY, 'secret' => FB_CONSUMER_SECRET, 'cookie' => true,));
		// set certificat
		self::setCAInfo(self::$object);
	}

	public static function setCAInfo()
	{
		Facebook::$CURL_OPTS[CURLOPT_CAINFO] = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::NETWORK_ID . DIRECTORY_SEPARATOR . 'fb_ca_chain_bundle.crt';
	}

	/**
	 * save social information
	 * access token,
	 */
	public function saveProfileSystemInfo()
	{
		if ($oProfile = self::getProfileObject()) {
			$sSocialID = (string)$oProfile->id;
			$session = serialize(self::$object->getAccessToken());
			$profileInfo = serialize($oProfile);

			if ($sSocialID && $session && $profileInfo) {
				return SJB_DB::query('INSERT INTO `facebook` SET `facebook_id` = ?s, `access` = ?s, `profile_info` = ?s
					ON DUPLICATE KEY UPDATE `access` = ?s, `profile_info`=?s', $sSocialID, $session, $profileInfo, $session, $profileInfo);

			}
			return false;
		}
		return null;
	}

	public function init()
	{
		$this->cleanSessionData(self::NETWORK_ID);

		if (empty($_SESSION['sn']['authorized'])) {
			if (is_null(self::$object))
				self::createFacebookInstance();

			$this->takeDataFromServer = true;

			if (!SJB_Request::getVar('state', null, 'GET')) {
				$this->saveUserGroupIDIfPossible();
				SJB_HelperFunctions::redirect($this->getFacebookLoginUrl());
				exit();
			}
			elseif ($this->getProfileInformation()) {
				$this->flagSocialPluginInSession(self::NETWORK_ID);
				$this->saveProfileSystemInfo();

				if ($oCurrentUser = SJB_UserManager::getCurrentUser()) {
					$this->setUserSocialIDByUserSID($oCurrentUser->getSID(), self::$oProfile->id);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-account/');
				}
				elseif (!(self::$oProfile && parent::ifUserIsRegisteredByReferenceUid($this->getReferenceUid()))) {
					// redirect user to registration page if he is not registered
					$this->redirectToRegistrationSocialPage();
				}
			}
		}
		elseif (self::$oProfile && !parent::ifUserIsRegisteredByReferenceUid($this->getReferenceUid())) {
			// if user already logged in using social plugin but not registered
			// redirect him to registration social page
			$this->redirectToRegistrationSocialPage();
		}
	}

	public function getReferenceUid()
	{
		return self::NETWORK_ID . '_' . self::$oProfile->id;
	}

	public static function getFaceBookLogInOutUrl()
	{
		if (self::$oProfile)
			return self::getFacebookLogoutUrl();
		return self::getFacebookLoginUrl();
	}

	public static function getFacebookLogoutUrl()
	{
		if (is_object(self::$object) && self::$object instanceof  Facebook)
			return self::$object->getLogoutUrl();
		return false;
	}

	public static function getFacebookLoginUrl()
	{
		if (is_object(self::$object))
			return self::$object->getLoginUrl(array('scope' => implode(',', self::$aPermissionFields)));
		return false;
	}

	public function __construct($takeDataFromServer = null)
	{
		if (empty($_SESSION['sn']['network']) || self::NETWORK_ID != $_SESSION['sn']['network']) {
			return null;
		}

		// Create our Application instance.
		$this->createFacebookInstance();

		if (isset($_GET['autofill']))
			$this->takeDataFromServer = true;
		else
			$this->takeDataFromServer = $takeDataFromServer;

		$this->getProfileInformation();
	}

	public function getProfileInformation()
	{
		if (!$this->takeDataFromServer && $oCurUser = SJB_UserManager::getCurrentUser()) {
			$curUserSID = $oCurUser->getSID();
			$profileSocialID = self::getProfileSocialID($curUserSID);

			if ($profileSocialID) {
				$aProfExpl = explode($this->getNetwork() . '_', $profileSocialID);
				$linkedinID = SJB_Array::get($aProfExpl, 1);

				$profileSocialInfo = $this->getProfileSocialSavedInfoBySocialID($linkedinID);

				if ($profileSocialInfo) {
					self::$oProfile = SJB_Array::get($profileSocialInfo, 'profile_info');
					self::$oSocialPlugin = $this;

					if (SJB_HelperFunctions::debugModeIsTurnedOn())
						SJB_HelperFunctions::debugInfoPush(self::$oProfile, 'SOCIAL_PLUGIN');
					return true;
				}
			}
		}

		if (self::$object) {
			try {
				$this->_getProfileInfoByAccessToken();

				if (self::$oProfile) {
					if (SJB_HelperFunctions::debugModeIsTurnedOn())
						SJB_HelperFunctions::debugInfoPush(self::$oProfile, 'SOCIAL_PLUGINS');
					return true;
				}
				else {
					SJB_Session::unsetValue('sn');
				}
			}
			catch (FacebookApiException $e) {
				SJB_Error::writeToLog($e->getMessage());
			}
		}

		return null;
	}

	public function defineWetherEmailIsNeeded()
	{
		if (!empty(self::$oProfile->email) && !strstr(self::$oProfile->email, 'proxymail.facebook.com') && !SJB_UserManager::getUserSIDbyEmail(self::$oProfile->email)) {
			$key = array_search('email', self::$aUserFields);
			if ($key !== false)
				unset(self::$aUserFields[$key]);
		}
	}

	/**
	 * get SocialNetwork Profile info from facebook server
	 * @param string $accessToken
	 */
	public function _getProfileInfoByAccessToken($accessToken = null)
	{
		if ($accessToken) {
			self::$object->setAccessToken($accessToken);
		}

		try {
			self::$oProfile = self::$object->api('/me');
			self::$oProfile = new ArrayObject(self::$oProfile);
			self::$oProfile->setFlags(ArrayObject::ARRAY_AS_PROPS);
			self::$oSocialPlugin = $this;
			return true;
		} catch (Exception $e) {
			SJB_Error::writeToLog($e->getMessage());
		}

		return false;
	}

	/**
	 *
	 * @param string $socialID
	 * @return stdClass
	 */
	public function getProfileSocialSavedInfoBySocialID($socialID)
	{
		$socInfo = SJB_DB::query('SELECT * FROM `facebook` WHERE `facebook_id` = ?s', $socialID);

		if (!empty($socInfo)) {
			$socInfo = array_shift($socInfo);

			if (!empty($socInfo['access'])) {
				$socInfo['access'] = unserialize($socInfo['access']);
				$socInfo['profile_info'] = unserialize($socInfo['profile_info']);
				return $socInfo;
			}
		}

		return null;

	}

	public static function logout()
	{
		SJB_Session::unsetValue('sn');
		if (self::$oProfile) {
			SJB_HelperFunctions::redirect(self::getFacebookLogoutUrl());
			exit();
		}
	}

	/**
	 * fill user object with values from social account
	 * also adds some reference data
	 * @param SJB_User $user
	 * @return SJB_User
	 */
	public static function addReferenceDetails(SJB_User $user)
	{
		if (self::$oProfile) {
			parent::fillRegistrationDataWithUser($user);
			parent::addReferenceDetails($user, self::NETWORK_ID);
		}
		return $user;
	}

	public function createUser()
	{
		$user_group_id = SJB_Request::getVar('user_group_id');

		if (!is_null($user_group_id)) {
			$user_group_sid = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);
			$user = SJB_ObjectMother::createUser($_REQUEST, $user_group_sid);
			$user->deleteProperty('active');
			$user->deleteProperty('featured');

			$this->fillRegistrationDataWithUser($user);

			self::addReferenceDetails($user);
			$user->deleteProperty('captcha');
			SJB_UserManager::saveUser($user);

			// subscribe user on default product
			$defaultProduct = SJB_UserGroupManager::getDefaultProduct($user_group_sid);
			$available_products_ids = SJB_ProductsManager::getProductsByUserGroupSID($user_group_sid, $user->getSID());

			if ($defaultProduct && in_array($defaultProduct, $available_products_ids)) {
				$contract = new SJB_Contract(array('product_sid' => $defaultProduct));
				$contract->setUserSID($user->getSID());
				$contract->saveInDB();
			}

			$this->sendUserSocialRegistrationLetter($user);

			// notifying administrator
			SJB_AdminNotifications::sendAdminUserRegistrationLetter($user);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-account/');
		}
	}

	/**
	 * @param SJB_Object $object
	 * @return SJB_Object
	 */
	public static function fillRegistrationDataWithUser(SJB_Object $object)
	{
		if ($oProfile = self::getProfileObject()) {
			/** @var $oProperty SJB_ObjectProperty */
			foreach ($object->getProperties() as $oProperty) {
				$value = false;

				switch ($oProperty->getID()) {
					case 'Country':
					case 'CurrentCountry':
					case 'City':
					case 'CurrentCity':
						if (!empty($oProfile->location) && !empty($oProfile->location['name'])) {
							$location = explode(', ', $oProfile->location['name']);
							if ('Country' == $oProperty->getID() || 'CurrentCountry' == $oProperty->getID()) {
								$value = (count($location) > 2) ? ((!empty($location[2]) ? $location[2] : '')) : (!empty($location[1]) ? $location[1] : '');
							}
							elseif ('City' == $oProperty->getID() || 'CurrentCity' == $oProperty->getID()) {
								$value = (!empty($location[0])) ? $location[0] : '';
							}
						}
						break;
					case 'Location':
						$country = '';
						$city = '';
						if (!empty($oProfile->location) && !empty($oProfile->location['name'])) {
							$location = explode(', ', $oProfile->location['name']);
							$country = (count($location) > 2) ? ((!empty($location[2]) ? $location[2] : '')) : (!empty($location[1]) ? $location[1] : '');
							$country = $country ? SJB_CountriesManager::getCountrySIDByCountryName($country) : '';
							$city = (!empty($location[0])) ? $location[0] : '';
						}
						$reqVal = SJB_Request::getVar($oProperty->getID(), false);
						if (empty($reqVal)) {
							$location = $object->getChild('Location');
							
							$propertyInfo = $location->getPropertyInfo('Country');
							if ($propertyInfo && (!$propertyInfo['hidden'] || isset($propertyInfo['madeHidden']))) {
								$location->setPropertyValue('Country', $country);
							}
							
							$propertyInfo = $location->getPropertyInfo('City');
							if ($propertyInfo && (!$propertyInfo['hidden'] || isset($propertyInfo['madeHidden']))) {
								$location->setPropertyValue('City', $city);
							}
						}
						break;
					case 'WorkExperience':
						if (!empty($oProfile->work)) {
							$aWork = array();
							foreach ($oProfile->work as $position) {
								$work = '';
								if (!empty($position['employer']))
									$work .= $position['employer']['name'] . "\r\n";
								if (!empty($position['location']))
									$work .= $position['location']['name'] . "\r\n";
								if (!empty($position['start_date']))
									$work .= $position['start_date'] . "\r\n";
								if (!empty($position['end_date']))
									$work .= $position['end_date'] . "\r\n";

								if (!empty($work))
									$aWork[] = $work;
							}
							$value = implode("\r\n", $aWork);
						}
						break;

					case 'Education':
						if (!empty($oProfile->education)) {
							$aEducation = array();

							foreach ($oProfile->education as $education) {
								$sEducation = '';

								if (!empty($education['school']))
									$sEducation = $education['school']['name'];
								if (!empty($education['year']))
									$sEducation .= '(' . $education['year']['name'] . '):<br/>';
								if (!empty($education['type']))
									$sEducation .= $education['type'] . "\r\n";

								if (!empty($education['concentration'])) {
									foreach ($education['concentration'] as $concentration)
										$sEducation .= '<br/>' . $concentration['name'] . "\r\n";
								}
								if (!empty($education['classes'])) {
									foreach ($education['classes'] as $classes)
										$sEducation .= '<br/>' . $classes['name'] . ' : ' . $classes['description'] . "\r\n";
								}
								if (!empty($sEducation))
									array_push($aEducation, $sEducation);
							}
							$value = implode("\r\n", $aEducation);
						}
						break;

					case 'Title':
					case 'TITLE':
						$value = 'My Resume';
						break;

					case 'FirstName':
						if (!empty($oProfile->first_name))
							$value = $oProfile->first_name;
						break;

					case 'LastName':
						if (!empty($oProfile->last_name))
							$value = $oProfile->last_name;
						break;

					case 'ContactName':
						if (!empty($oProfile->name))
							$value = $oProfile->name;
						break;

					case 'WebSite':
						if (!empty($oProfile->website))
							$value = $oProfile->website;
						break;

					case 'CompanyName':
						if (!empty($oProfile->work) && !empty($oProfile->work[0]['employer']['name']))
							$value = $oProfile->work[0]['employer']['name'];
						break;

					case 'CompanyDescription':
						if (!empty($oProfile->summary))
							$value = $oProfile->summary;
						break;

					case 'email':
						if (!empty($oProfile->email) && !SJB_Request::getVar('email', null))
							$value = array('original' => $oProfile->email, 'confirmed' => $oProfile->email);
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
							continue(2);
						}
						break;
				}

				if (!empty($value)) {
					$reqVal = SJB_Request::getVar($oProperty->getID(), false);
					if (empty($reqVal)) // if user did not modified his data in form
						$object->setPropertyValue($oProperty->getID(), $value);
				}
			}
		}
		return $object;
	}

	/**
	 *
	 * @param array $request
	 * @return array
	 */
	public function fillRequestOutSocialData(&$request)
	{
		if ($oProfile = self::getProfileObject()) {
			require_once('facebook/FacebookFields.php');
			$oFF = new SJB_FacebookFields($oProfile);

			$aFieldAssoc = array();
			require_once('facebook/FacebookSettings.php');

			$oFF->fillOutListingData_Request($request, $aFieldAssoc);
		}

		return $request;
	}

	/**
	 * @param SJB_Object $obj
	 * @return SJB_Object
	 */
	public function fillObjectOutSocialData(SJB_Object $obj)
	{
		if ($oProfile = self::getProfileObject()) {
			require_once('facebook/FacebookFields.php');
			$oFF = new SJB_FacebookFields($oProfile);
			$aFieldAssoc = array();
			require_once('facebook/FacebookSettings.php');

			$oFF->fillOutListingData_Object($obj, $aFieldAssoc);
		}

		return $obj;
	}

	public function getSocialIDByReferenceUID($referenceUID)
	{
		return substr($referenceUID, (strlen(self::getNetwork()) + 1));
	}

}
