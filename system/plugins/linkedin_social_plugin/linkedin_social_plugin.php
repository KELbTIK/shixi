<?php

class LinkedinSocialPlugin extends SJB_SocialPlugin
{
	private $requestedProfileFields = array(
		'id',
        'email-address',
		'first-name',
		'main-address',
		'last-name',
		'headline',
		'date-of-birth',
		'industry',
		'summary',
		'positions',
		'educations',
		'specialties',
		'picture-url',
		'phone-numbers',
		'twitter-accounts',
		'public-profile-url',
		'location'
	);

	const NETWORK_ID = 'linkedin';
	const NETWORK_CAPTION = 'LinkedIn';

	/**
	 * @var SJB_LinkedIn
	 */
	private static $object;

	public static function getNetwork()
	{
		return self::NETWORK_ID;
	}

	public static function getNetworkCaption()
	{
		return self::NETWORK_CAPTION;
	}

	public function init()
	{
		$this->cleanSessionData(self::NETWORK_ID);
		$GLOBALS[self::SOCIAL_ACCESS_ERROR] = array();

		if (is_null(self::$object) && empty($_SESSION['sn']['authorized'])) {
			try {
				/**
				 * initialize user by profile social id
				 * if not initialized trying to authorized by default
				 */
				if (!empty($_SESSION[self::NETWORK_ID]['id']) && !isset($_GET['setid'])) {
					if ($this->initializeByProfileSocialID($_SESSION[self::NETWORK_ID]['id'])) {
						$this->flagSocialPluginInSession(self::NETWORK_ID);
						$_SESSION[self::NETWORK_ID]['id'] = (string)self::$oProfile->id;
						return true;
					}
				}

				$this->saveUserGroupIDIfPossible();

				self::$object = new SJB_LinkedIn($this->createCallbackUrl());

				// check for response from LinkedIn
				if (!$this->isTokenRequested()) {
					self::$object->_getRequestToken();
				}
				else {
					if (SJB_Request::getVar(SJB_LinkedIn::OAUTH_PROBLEM)) {
						throw new Exception('oAuth Problem: ' . SJB_Request::getVar(SJB_LinkedIn::OAUTH_PROBLEM));
					}

					self::$object->_getAccessToken();
					$this->flagSocialPluginInSession(self::NETWORK_ID);
					$this->takeDataFromServer = true;
					$this->getProfileInformation();
					$this->saveProfileSystemInfo();

					// if user already registered we should link his profile with linkedIn id
					if ($oCurrentUser = SJB_UserManager::getCurrentUser()) {
						$this->setUserSocialIDByUserSID($oCurrentUser->getSID(), self::$oProfile->id);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-account/');
					}
					elseif (!parent::ifUserIsRegistered(self::NETWORK_ID, null)) {
						$this->redirectToRegistrationSocialPage();
					}
				}
			}
			catch (Exception $e) {
				$this->registerError($e);
				SJB_Error::writeToLog($e->getMessage());
			}
		}
		elseif (self::$oProfile && !parent::ifUserIsRegistered(self::NETWORK_ID)) {
			/**
			 * if user already logged in using social plugin but not registered
			 * redirect him to registration social page
			 */
			$this->redirectToRegistrationSocialPage();
		}
	}

	public function createCallbackUrl()
	{
		return SJB_System::getSystemSettings('SITE_URL') . SJB_Navigator::getURI() . '?network=linkedin&' . SJB_LinkedIn::_GET_RESPONSE . '=1';
	}

	/**
	 *
	 * @param string $profileSocialID
	 * @return boolean
	 */
	public function initializeByProfileSocialID($profileSocialID)
	{
		if (!parent::ifUserIsRegistered(self::NETWORK_ID, $profileSocialID)) {
			return false;
		}

		if ($accessToken = $this->getProfileSavedAccessToken($profileSocialID)) {
			return $this->initialize($accessToken);
		}

		return false;
	}

	public function getProfileSavedAccessToken($socialID)
	{
		$socInfo = SJB_DB::query('SELECT `access` FROM `linkedin` WHERE `linkedin_id` = ?s', $socialID);

		if (!empty($socInfo)) {
			$socInfo = array_shift($socInfo);

			if (!empty($socInfo['access'])) {
				return unserialize($socInfo['access']);
			}
		}
		return null;
	}

	public function getProfileSocialSavedInfoBySocialID($socialID)
	{
		$socInfo = SJB_DB::query('SELECT * FROM `linkedin` WHERE `linkedin_id` = ?s', $socialID);

		if (!empty($socInfo)) {
			$socInfo = array_shift($socInfo);

			if (!empty($socInfo['access'])) {
				$socInfo['access'] = unserialize($socInfo['access']);
				$socInfo['profile_info'] = new SimpleXMLElement(unserialize($socInfo['profile_info']));
				return $socInfo;
			}
		}
		return null;

	}

	public static function getProfilePublicUrlByProfileLinkedinID($socialID)
	{
		$socInfo = SJB_DB::query('SELECT `profile_info` FROM `linkedin` WHERE `linkedin_id` = ?s', $socialID);

		if (!empty($socInfo)) {
			$socInfo = array_shift($socInfo);

			if (!empty($socInfo['profile_info'])) {
				$nProf = unserialize($socInfo['profile_info']);
				$nProf = new SimpleXMLElement($nProf);
				return !empty($nProf->{'public-profile-url'}) ? (string)$nProf->{'public-profile-url'} : false;
			}
		}

		return null;
	}

	/**
	 *
	 * @param array|object $access_token
	 */
	public function initialize($access_token)
	{
		self::$object = new SJB_LinkedIn();

		if (self::$object->_getAccessToken($access_token)) {
			return $this->getProfileInformation();
		}

		return null;
	}

	public function __construct($takeDataFromServer = false)
	{
		$_SESSION['sn']['authorized'] = (isset($_SESSION['sn']['authorized'])) ? $_SESSION['sn']['authorized'] : FALSE;

		if (isset($_GET['autofill'])) {
			$this->takeDataFromServer = true;
		}
		else {
			$this->takeDataFromServer = $takeDataFromServer;
		}

		if ($_SESSION['sn']['authorized'] === TRUE && self::NETWORK_ID === $_SESSION['sn']['network'] && !empty($_SESSION[self::NETWORK_ID]['accessToken'])) {
			$this->initialize(unserialize($_SESSION[self::NETWORK_ID]['accessToken']));
		}
	}

	public function getSocialIDByReferenceUID($referenceUID)
	{
		return substr($referenceUID, (strlen(self::NETWORK_ID) + 1));
	}

	/**
	 * save social information
	 * access token,
	 */
	public function saveProfileSystemInfo()
	{
		if ($oProfile = self::getProfileObject()) {
			$linkedinID = (string)$oProfile->id;
			$access = $_SESSION[self::NETWORK_ID]['accessToken'];
			$profileInfo = serialize($oProfile->asXML());

			if ($linkedinID && $access && $profileInfo) {
				return SJB_DB::query('INSERT INTO `linkedin` SET `linkedin_id` = ?s, `access` = ?s, `profile_info` = ?s
					ON DUPLICATE KEY UPDATE `access` = ?s, `profile_info`=?s', $linkedinID, $access, $profileInfo, $access, $profileInfo);
			}
			return false;
		}
		return null;
	}

	private function getProfileInformation()
	{
		if (!$this->takeDataFromServer && $oCurUser = SJB_UserManager::getCurrentUser()) {
			$curUserSID = $oCurUser->getSID();
			$profileSocialID = self::getProfileSocialID($curUserSID);

			if ($profileSocialID) {
				$aProfExpl = explode($this->getNetwork() . '_', $profileSocialID);
				$linkedinID = $aProfExpl[1];

				$profileSocialInfo = $this->getProfileSocialSavedInfoBySocialID($linkedinID);

				if ($profileSocialInfo) {
					self::$oProfile = $profileSocialInfo['profile_info'];
					self::$oSocialPlugin = $this;

					if (SJB_HelperFunctions::debugModeIsTurnedOn()) {
						SJB_HelperFunctions::debugInfoPush(self::$oProfile, 'SOCIAL_PLUGIN');
					}
					return true;
				}
			}
		}

		if (self::$object) {
			try {
				$response = self::$object->getProfileInfo($this->requestedProfileFields);
				if ($response) {
					self::$oProfile = new SimpleXMLElement($response);
					self::$oSocialPlugin = $this;

					if (SJB_HelperFunctions::debugModeIsTurnedOn()) {
						SJB_HelperFunctions::debugInfoPush(self::$oProfile, 'SOCIAL_PLUGIN');
					}
					return true;
				}
			} catch (Exception $ex) {
				// revocation successful, clear session
				unset($_SESSION['oauth'][self::NETWORK_ID]);
				$this->cleanSessionData(self::NETWORK_ID);

				if (SJB_HelperFunctions::debugModeIsTurnedOn()) {
					$debug = "Error retrieving profile information:\n\nRESPONSE:\n\n<pre>" . print_r($ex->getMessage()) . "</pre>";
					SJB_HelperFunctions::debugInfoPush($debug, 'SOCIAL_PLUGINS');
				}
			}
		}

		return null;
	}

	public static function logout()
	{
		if (self::$object && self::$oProfile) {
			// если нужно отозвать token, использовать эту ф-цию
//			$this->revokeToken();
			// у нас такой цели нет, поэтому просто чистим тоукен
			self::$object->setAccessToken(null);
			SJB_Session::unsetValue('sn');
			SJB_Session::unsetValue(self::NETWORK_ID);
			SJB_Session::unsetValue('oauth');
		}
	}

	public function revokeToken()
	{
		$response = self::$object->revoke();

		if ($response === TRUE) {
			unset($_SESSION['sn']['authorized']);
			unset($_SESSION[self::NETWORK_ID]);
			// revocation successful, clear session
			unset($_SESSION['oauth'][self::NETWORK_ID]);

			if (empty($_SESSION['oauth'][self::NETWORK_ID])) {
				// session destroyed
			} else {
				SJB_Error::writeToLog('Error clearing user\'s session');
			}
		} else {
			SJB_Error::writeToLog("Error revoking user's token:\n\nRESPONSE:\n\n" . print_r($response, TRUE) . "\n\nLINKEDIN OBJ:\n\n" . print_r(self::$object, TRUE));
		}
	}

	public function fillRequestOutSocialData(&$request)
	{
		if ($oProfile = self::getProfileObject()) {
			$oLF = new SJB_LinkedinFields($oProfile);
			$aFieldAssoc = require_once __DIR__ . '/../../lib/social_media/LinkedIn/LinkedinSettings.php';

			$oLF->fillOutListingData_Request($request, $aFieldAssoc);
		}

		return $request;
	}

	/**
	 * @param SJB_Object $obj
	 * @return SJB_Listing|SJB_Object|SJB_User
	 */
	public function fillObjectOutSocialData(SJB_Object $obj)
	{
		if ($oProfile = self::getProfileObject()) {
			$oLF = new SJB_LinkedinFields($oProfile);
			$aFieldAssoc = require __DIR__ . '/../../lib/social_media/LinkedIn/LinkedinSettings.php';

			$oLF->fillOutListingData_Object($obj, $aFieldAssoc);
		}

		return $obj;
	}

	/**
	 * @param SJB_Object $object
	 * @return SJB_Object
	 */
	public static function fillRegistrationDataWithUser(SJB_Object $object)
	{
		if (self::$oSocialPlugin instanceof LinkedinSocialPlugin && $oProfile = self::getProfileObject()) {
			/** @var $oProperty SJB_ObjectProperty */
			foreach ($object->getProperties() as $oProperty) {
				$value = '';

				switch ($oProperty->getID()) {
					case 'Country':
						if (!empty($oProfile->location->country->code)) {
							$value = SJB_Countries::getCountryNameByISO2((string)$oProfile->location->country->code);
						}
						break;
					case 'Location':
						$country = '';
						$city = '';
						$address = '';
						if (!empty($oProfile->location->country->code)) {
							$country = SJB_Countries::getCountryNameByISO2((string)$oProfile->location->country->code);
							$country = $country ? SJB_CountriesManager::getCountrySIDByCountryName($country) : '';
						}
						if (!empty($oProfile->location->name)) {
							$city = $oProfile->location->name;
						}
						if (!empty($oProfile->{'main-address'})) {
							$address = $oProfile->{'main-address'};
						}
						
						$location = $object->getChild('Location');
						
						$propertyInfo = $location->getPropertyInfo('Country');
						if ($propertyInfo && (!$propertyInfo['hidden'] || isset($propertyInfo['madeHidden']))) {
							$location->setPropertyValue('Country', $country);
						}
						
						$propertyInfo = $location->getPropertyInfo('City');
						if ($propertyInfo && (!$propertyInfo['hidden'] || isset($propertyInfo['madeHidden']))) {
							$location->setPropertyValue('City', $city);
						}
						
						$propertyInfo = $location->getPropertyInfo('Address');
						if ($propertyInfo && (!$propertyInfo['hidden'] || isset($propertyInfo['madeHidden']))) {
							$location->setPropertyValue('Address', $address);
						}
						break;
					case 'DateOfBirth':
						if (!empty($oProfile->{'date-of-birth'})) {
							$year = !empty($oProfile->{'date-of-birth'}->year) ? (string)$oProfile->{'date-of-birth'}->year : '0000';
							$month = !empty($oProfile->{'date-of-birth'}->month) ? (string)$oProfile->{'date-of-birth'}->month : '00';
							$day = !empty($oProfile->{'date-of-birth'}->day) ? (string)$oProfile->{'date-of-birth'}->day : '00';
							$value = SJB_I18N::getInstance()->getDate($day . '-' . $month . '-' . $year);
						}
						break;
					case 'FirstName':
						if (!empty($oProfile->{'first-name'})) {
							$value = $oProfile->{'first-name'};
						}
						break;
					case 'LastName':
						if (!empty($oProfile->{'last-name'})) {
							$value = $oProfile->{'last-name'};
						}
						break;
					case 'ContactName':
						if (!empty($oProfile->{'last-name'})) {
							$value = $oProfile->{'first-name'} . ' ' . $oProfile->{'last-name'};
						}
						break;
					case 'WebSite':
						// WebSite
						if (!empty($oProfile->website)) {
							$value = $oProfile->website;
						}
						break;
					case 'Title':
					case 'TITLE':
						if (!empty($oProfile->positions->position->title)) {
							$value = $oProfile->positions->position->title;
						}
						break;
					case 'CompanyName':
						if (!empty($oProfile->positions->position->company->name)) {
							$value = $oProfile->positions->position->company->name;
						}
						break;
					case 'CompanyDescription':
						if (!empty($oProfile->summary)) {
							$value = $oProfile->summary;
						}
						break;
					case 'City':
						if (!empty($oProfile->location->name)) {
							$value = $oProfile->location->name;
						}
						break;
					case 'PhoneNumber':
						if (!empty($oProfile->{'phone-numbers'})) {
							$aPhoneNumbers = array();
							foreach (self::$oProfile->{'phone-numbers'}->{'phone-number'} as $phone) {
								array_push($aPhoneNumbers, $phone->{'phone-number'} . ' (' . $phone->{'phone-type'} . ')');
							}
							$value = implode(', ', $aPhoneNumbers);
						}
						break;
					case 'jsTwitter':
						if (!empty($oProfile->{'twitter-accounts'})) {
							$aTwitters = array();
							foreach (self::$oProfile->{'twitter-accounts'}->{'twitter-account'} as $twitter) {
								array_push($aTwitters, $twitter->{'provider-account-name'});
							}
							$value = implode(', ', $aTwitters);
						}
						break;
					case 'Address':
						if (!empty($oProfile->{'main-address'})) {
							$value = $oProfile->{'main-address'};
						}
						break;

					case 'sendmail':
						$value = false;
						break;

                    case 'email':
                        if (!empty($oProfile->{'email-address'}) && !SJB_Request::getVar('email', null))
                            $value = array('original' => $oProfile->{'email-address'}, 'confirmed' => $oProfile->{'email-address'});
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
					if (empty($reqVal)) // if user did not modified his data in form
						$object->setPropertyValue($oProperty->getID(), $value);
				}
			}
		}

		return $object;
	}

	public function peopleSearch($aFields = array())
	{
		$aPrepared = array();

		if (self::$object && self::$oProfile && is_array($aPrepared)) {
			foreach ($aFields as $fieldName => $fieldValue) {
				if (!empty($fieldValue)) {
					if ('facet' == $fieldName)
						$aPrepared[$fieldName] = $fieldValue;
					else
						$aPrepared[$fieldName] = urlencode($fieldValue);
				}
			}

			$response = self::$object->peopleSearch($aPrepared);


			if ($response) {
				$oPersons = new SimpleXMLElement($response);
				if ((int)$oPersons->{'num-results'} >= 0)
					return $oPersons;
			}
		}

		return null;
	}

	public function preparePeopleStructure($oPersons)
	{
		$result = array();

		if (!empty($oPersons->people->person)) {
			foreach ($oPersons->people->person as $oPerson) {
				$structure = array(
					'firstName' => empty($oPerson->{'first-name'}) ? 'Undefined' : (string)$oPerson->{'first-name'},
					'lastName' => empty($oPerson->{'last-name'}) ? '' : (string)$oPerson->{'last-name'},
					'industry' => empty($oPerson->industry) ? '' : (string)$oPerson->industry,
					'id' => empty($oPerson->id) ? '' : (string)$oPerson->id,
					'headline' => empty($oPerson->headline) ? '' : (string)$oPerson->headline,
					'url' => empty($oPerson->{'site-standard-profile-request'}->url) ? '' : (string)$oPerson->{'site-standard-profile-request'}->url,
				);
				array_push($result, $structure);
			}
		}

		return $result;
   	}

	private function isTokenRequested()
	{
		return isset($_GET[SJB_LinkedIn::_GET_RESPONSE]);
	}
}
