<?php

class SJB_Admin_SocialMedia_SocialMedia extends SJB_Function
{
	public function isAccessible()
	{
		$network = SJB_Request::getVar('passed_parameters_via_uri');
		if (empty($network)) {
			$network = SJB_Request::getVar('soc_network');
		}
		switch ($network) {
			case 'facebook':
				$this->setPermissionLabel('set_linkedin_plug-in');
				break;
			case 'linkedin':
				$this->setPermissionLabel('set_facebook_plug-in');
				break;
			case 'twitter':
				$this->setPermissionLabel('set_twitter_plug-in');
				break;
			case 'googleplus':
				$this->setPermissionLabel('set_googleplusplugin');
				break;
			case 'bitly':
				$this->setPermissionLabel('social_media_bitly');
				break;
			default:
				$this->setPermissionLabel(
					array(
						'set_linkedin_plug-in',
						'set_facebook_plug-in',
						'set_twitter_plug-in',
						'set_googleplusplugin',
						'social_media_bitly'
					)
				);
				break;
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$networkFeeds = array();
		$template = 'social_media.tpl';
		$formSubmitted = SJB_Request::getVar('submit');
		$action = SJB_Request::getVar('action');
		$subAction = SJB_Request::getVar('sub_action');
		$sid = SJB_Request::getVar('sid');
		$groups = array();
		$accountInfo = null;
		$messages = array();
		$savedSettings = array();
		
		if (SJB_Request::getVar('error', false)) {
			$errors[] = SJB_Request::getVar('error', false);
		}
		if (SJB_Request::getVar('message', false)) {
			$messages[] = SJB_Request::getVar('message', false);
		}
		$socNetworks = array (
			'facebook'      => array ('name' => 'Facebook'),
			'linkedin'      => array ('name' => 'Linkedin'),
			'twitter'       => array ('name' => 'Twitter'),
			'googleplus'    => array ('name' => 'Google+'),
			'bitly'         => array ('name' => 'Bitly')
		);

		$network = SJB_Request::getVar('passed_parameters_via_uri');
		if (empty($network)) {
			$network = SJB_Request::getVar('soc_network');
		}
		switch ($network) {
			case 'facebook':
				$template = 'social_media_settings.tpl';
				$objectName = 'SJB_FacebookSocial';
				break;
			case 'linkedin':
				$template = 'social_media_settings.tpl';
				$objectName = 'SJB_LinkedInSocial';
				break;
			case 'twitter':
				$template = 'social_media_settings.tpl';
				$objectName = 'SJB_TwitterSocial';
				break;
			case 'googleplus':
				$template = 'social_media_settings.tpl';
				$objectName = 'SJB_GooglePlusSocial';
				break;
			default:
				$network    = '';
				$action     = '';
				$objectName = '';
				break;
		}
		
		switch ($action) {
			case 'add_feed':
				SJB_Session::unsetValue($network);
				SJB_Session::unsetValue($network . 'Feed');
				$template = 'feed_input_form.tpl';
				$accountID  = SJB_Request::getVar('account_id', false);
				$isAuthorized = SJB_Request::getVar('authorized', false);

				if ($accountID) {
					$tp->assign('accountID', $accountID);
				}

				$feed = new $objectName;
				$addForm = new SJB_Form($feed);
				$addForm->registerTags($tp);

				$searchFormBuilder = new SJB_SearchFormBuilder($feed);
				$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST);
				$searchFormBuilder->setCriteria($criteria);
				$searchFormBuilder->registerTags($tp);

				$systemFields = $feed->details->systemFields;
				$postingFields = $feed->details->postingFields;
				$listingFields = $feed->details->commonFields;

				$tp->assign('authorized', $isAuthorized);
				$tp->assign('listingFields', $listingFields);
				$tp->assign('postingFields', $postingFields);
				$tp->assign('systemFields', $systemFields);
				$tp->assign('action', $action);
				break;

			case 'save_feed':
				$template = 'feed_input_form.tpl';
				$fieldErrors = array();
				$mediaObject = $objectName . 'Media';
				$networkSocialMedia = new $mediaObject();
				$isAuthorized = SJB_Request::getVar('authorized', false);
				$actionFeed = SJB_Request::getVar('action_feed');
				if (($actionFeed != 'add_feed') && ($network != 'twitter')) {
					try {
						$accountInfo = $networkSocialMedia->getAccountInfo($sid);
					} catch (Exception $e) {
						$isAuthorized = false;
						$errors[] = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
					}
				}
				$isGroupsExist = !empty($accountInfo['groups']);
				if (SJB_Request::getVar('process_token', false)) {
					$_REQUEST = unserialize(SJB_Session::getValue($network . 'Feed'));
					$_REQUEST['process_token'] = 1;
				}

				$feed = new $objectName($_REQUEST, $isGroupsExist, $isAuthorized);
				if ($isGroupsExist) {
					$groups = $accountInfo['groups'];
				}
				if ($sid) {
					$feed->setSID($sid);
					$tp->assign('feed_sid', $sid);
				}
				$criteriaSaver = new SJB_ListingCriteriaSaver();
				$criteriaSaver->setSessionForCriteria($_REQUEST);
				$requestedData = $criteriaSaver->getCriteria();

				$searchFormBuilder = new SJB_SearchFormBuilder($feed);
				$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST);
				$searchFormBuilder->setCriteria($criteria);
				$searchFormBuilder->registerTags($tp);

				$properties = $feed->getProperties();

				foreach ($properties as $key => $property) {
					if (!$property->isSystem()) {
						$feed->deleteProperty($key);
					}
				}
				
				$this->checkToken($tp, $networkSocialMedia, $errors, array(), $network, $sid);
				
				$addForm = new SJB_Form($feed);
				$addForm->registerTags($tp);
				if ($addForm->isDataValid($fieldErrors)) {
					if ($network == 'twitter') {
						try {
							$accessToken = $networkSocialMedia->getAccessToken($sid, $action, $errors);
						} catch (Exception $e) {
							$accessToken = false;
							$errors[] = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
						}
						if (empty($errors) && $accessToken != false) {
							$feed->addProperty(
											array(
												'id'        => 'access_token',
												'type'      => 'text',
												'value'     => serialize($accessToken),
												'is_system' => true)
							);
						}
					}
					else if ($network == 'facebook' && !empty($accountInfo)) {
						$feed->addProperty(
										array(
											'id'        => 'access_token',
											'type'      => 'text',
											'value'     => serialize($accountInfo['access_token']),
											'is_system' => true)
						);
						$feed->addProperty(
										array(
											'id'        => 'account_name',
											'type'      => 'text',
											'value'     => serialize($accountInfo['account_name']),
											'is_system' => true)
						);
					}
					if (empty($errors)) {
						unset($requestedData['groups']);
						$feed->addProperty(
										array(
											'id'        => 'search_data',
											'type'      => 'text',
											'value'     => serialize($requestedData),
											'is_system' => true)
						);
						$feed->saveFeed($feed, $action);
						if ($formSubmitted == 'save') {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . $network . '#postJobs');
						}
						$tp->assign('feed_sid', $feed->getSID());
					}
				}

				$systemFields = $feed->details->systemFields;
				$postingFields = $feed->details->postingFields;
				$listingFields = $feed->details->commonFields;
				$changeAccountUrl = "action=authorize&sid={$sid}&sub_action=change_account&soc_network={$network}";

				$tp->assign('field_errors', $fieldErrors);
				$tp->assign('change_url', $changeAccountUrl);
				$tp->assign('listingFields', $listingFields);
				$tp->assign('postingFields', $postingFields);
				$tp->assign('systemFields', $systemFields);
				$tp->assign('action', $actionFeed);
				$tp->assign('authorized', $isAuthorized);
				$tp->assign('allGroups', $groups);
				break;

			case 'grant':
				$mediaObject = $objectName . 'Media';
				$networkSocialMedia = new $mediaObject();
				$error = '';
				try {
					$accountInfo = $networkSocialMedia->getAccountInfo($sid, $subAction);
				} catch (Exception $e) {
					$tp->assign('authorized', false);
					$error = $e->getMessage();
				}
				
				if (empty($error)) {
					$networkSocialMedia->updateAccessToken($sid, $accountInfo['account_id'], $accountInfo['account_name']);
					$logoutUrl = SJB_System::getSystemSettings('SITE_URL') . "/social-media/{$network}?message=ACCOUNT_UPDATED#postJobs";
				} else {
					$logoutUrl = SJB_System::getSystemSettings('SITE_URL') . "/social-media/{$network}?error={$error}#postJobs";
				}
				
				// After clicking the 'Grant Permission' button and login to Facebook we are redirected to the feeds list.
				SJB_HelperFunctions::redirect($logoutUrl);
				break;
			case 'edit_feed':
				if (!SJB_Request::getVar('oauth_token', false)) {
					SJB_Session::unsetValue($network);
				}
				if (!SJB_Request::getVar('process_token', false)) {
					SJB_Session::unsetValue($network . 'Feed');
				}
				if ($sid) {
					$changeAccountUrl = "action=authorize&sid={$sid}&sub_action=change_account&soc_network={$network}";
					$feedInfo = $objectName::getFeedInfoByNetworkIdAndSID($network, $sid);
					$feedInfo = array_merge($feedInfo, $_REQUEST);
					$criteriaInfo = $feedInfo['search_data'] ? unserialize($feedInfo['search_data']) : '';
					$mediaObject = $objectName . 'Media';
					$networkSocialMedia = new $mediaObject();
					$isAuthorized = true;
					if ($network != 'twitter') {
						try {
							$accountInfo = $networkSocialMedia->getAccountInfo($sid, $subAction);
						} catch (Exception $e) {
							$isAuthorized = false;
							$errors[] = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
						}
					}
					$isGroupsExist = !empty($accountInfo['groups']);
					if ($isGroupsExist) {
						$groups = $accountInfo['groups'];
					}
					if ($accountInfo && $subAction == 'changed') {
						$feedInfo = array_merge($feedInfo, $accountInfo);
					}
					$feed = new $objectName($feedInfo, $isGroupsExist, $isAuthorized);
					$editForm = new SJB_Form($feed);
					$editForm->registerTags($tp);

					$searchFormBuilder = new SJB_SearchFormBuilder($feed);
					$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($criteriaInfo);
					$searchFormBuilder->setCriteria($criteria);
					$searchFormBuilder->registerTags($tp);

					$systemFields = $feed->details->systemFields;
					$postingFields = $feed->details->postingFields;
					$listingFields = $feed->details->commonFields;

					$this->checkToken($tp, $networkSocialMedia, $errors, $feedInfo, $network, $sid);
					
					$tp->assign('listingFields', $listingFields);
					$tp->assign('postingFields', $postingFields);
					$tp->assign('systemFields', $systemFields);
					$tp->assign('feed_sid', $sid);
					$tp->assign('authorized', $isAuthorized);
					$tp->assign('allGroups', $groups);
					$tp->assign('action', $action);
					$tp->assign('change_url', $changeAccountUrl);
					$template = 'feed_input_form.tpl';
				}
				break;

			case 'authorize':
				if (!SJB_Request::getVar('oauth_token', false)) {
					SJB_Session::unsetValue($network);
				}
				$mediaObject = $objectName . 'Media';
				$networkSocialMedia = new $mediaObject();
				try {
					if ($network == 'twitter') {
						$accessToken = $networkSocialMedia->getAccessToken($sid, $subAction, $errors);
						$networkSocialMedia->updateFeedToken($sid, $accessToken);
						if (SJB_Request::getVar('sub_action', null, 'GET') == 'grant') {
							if (empty($errors)) {
								$messages[] = 'Account is successfully updated.';
							}
							break;
						}
					} else {
						$accountInfo = $networkSocialMedia->getAccountInfo($sid, $subAction);
					}
				} catch (Exception $e) {
					$errors[] = SJB_I18N::getInstance()->gettext('Backend', $e->getMessage());
				}
				
				$changeAccountUrl = "action=authorize&sub_action=change_account&soc_network={$network}";
				$template = 'feed_input_form.tpl';
				
				$isAuthorized = isset($accountInfo['account_id']);
				$isGroupsExist = !empty($accountInfo['groups']);

				$feed = new $objectName($accountInfo, $isGroupsExist, $isAuthorized);
				if ($isGroupsExist) {
					$groups = $accountInfo['groups'];
				}

				$addForm = new SJB_Form($feed);
				$addForm->registerTags($tp);

				$searchFormBuilder = new SJB_SearchFormBuilder($feed);
				$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST);
				$searchFormBuilder->setCriteria($criteria);
				$searchFormBuilder->registerTags($tp);

				$systemFields = $feed->details->systemFields;
				$postingFields = $feed->details->postingFields;
				$listingFields = $feed->details->commonFields;

				$tp->assign('listingFields', $listingFields);
				$tp->assign('postingFields', $postingFields);
				$tp->assign('systemFields', $systemFields);
				$tp->assign('action', $action);
				$tp->assign('authorized', $isAuthorized);
				$tp->assign('change_url', $changeAccountUrl);
				$tp->assign('allGroups', $groups);
				break;

			case 'delete_feed':
				$sid = SJB_Request::getVar('sid');
				if ($sid) {
					$feed = new $objectName;
					$feed->deleteFeed($sid);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . $network . '#postJobs');
				}
				break;

			case 'status':
				$sid = SJB_Request::getVar('sid');
				$active = SJB_Request::getVar('active');
				$feedInfo = $objectName::getFeedInfoByNetworkIdAndSID($network, $sid);
				if ($feedInfo != null && ($active == '1' || $active == '0')) {
					$objectName::updateFeedStatus($network . '_feeds', $active, $sid);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . $network . '#postJobs');
				} else {
					$errors[] = 'Feed does not exist';
				}
				break;

			case 'save_settings':
				$request = $_REQUEST;
				$error = $this->checkFields($request, $objectName);
				if (!$error) {
					SJB_Settings::updateSettings($request);
					if ($formSubmitted == 'save') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/');
					}
					else if ($formSubmitted == 'apply') {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/social-media/' . $network);
					}
				}
				
				$savedSettings = $request;
				break;
		}
		
		if ($network) {
			if (empty($savedSettings)) {
				$savedSettings = SJB_Settings::getSettings();
			}
			
			SJB_Event::dispatch('RedefineSavedSetting', $savedSettings, true);
			if ($network != 'googleplus' && $action != 'edit_feed' && $action != 'add_feed' && $action != 'save_feed') {
				$networkFeeds = SJB_SocialMedia::getFeedsInfoByNetworkID($network);
				$currentDate = date('Y-m-d', time());
				foreach ($networkFeeds as $key => $feed) {
					if (SJB_SocialMedia::isFeedExpired($feed, $currentDate)) {
						$networkFeeds[$key]['expired'] = true;
					}
				}
			}
			$tp->assign('network', $network);
			$tp->assign('savedSettings', $savedSettings);
			$tp->assign('networkFeeds', $networkFeeds);
			$tp->assign('networkName', $socNetworks[$network]['name']);
			if ($network != 'twitter') {
				$networkObject = new $objectName;
				$settings = $networkObject->getConnectSettings();
				$tp->assign('settings', $settings);
			}
		} else {
			$tp->assign('socNetworks', $socNetworks);
		}
		
		$tp->assign('networkFeeds', $networkFeeds);
		$tp->assign('socNetworks', $socNetworks);
		$tp->assign('errors', $errors);
		$tp->assign('messages', $messages);
		$tp->display($template);
	}

	/**
	 * @param SJB_TemplateProcessor $tp
	 * @param $networkSocialMedia
	 * @param array $errors
	 * @param array $feedInfo
	 * @param string $network
	 * @param int $sid
	 */
	private function checkToken(SJB_TemplateProcessor $tp, $networkSocialMedia, array &$errors, array $feedInfo, $network, $sid)
	{
		if ($networkSocialMedia->approveAccount()) {
			if (empty($feedInfo)) {
				$feedInfo = SJB_SocialMedia::getFeedInfoByNetworkIdAndSID($network, $sid);
			}
			
			if (!empty($feedInfo)) {
				if (SJB_SocialMedia::isFeedExpired($feedInfo)) {
					$errors[] = 'TOKEN_EXPIRED';
					$tp->assign('expired', true);
				} else {
					$errors[] = 'APPROVE_ACCOUNT';
					$tp->assign('approveAccount', true);
				}
			}
		}
	}

	/**
	 * @param  array  $settings
	 * @param  string $socialPlugin
	 * @return bool
	 */
	private function checkFields(array $settings, $socialPlugin)
	{
		$pluginObj      = new $socialPlugin;
		$settingsFields = $pluginObj->getConnectSettings();
		$error          = false;
		foreach ($settingsFields as $settingsField) {
			if (!empty($settingsField['is_required']) && $settingsField['is_required'] === true && empty($settings[$settingsField['id']])) {
				SJB_FlashMessages::getInstance()->addWarning('EMPTY_VALUE', array('fieldCaption' => $settingsField['caption']));
				$error = true;
			}
			else if (!empty($settingsField['validators'])) {
				foreach ($settingsField['validators'] as $validator) {
					$isValid = $validator::isValid($settings[$settingsField['id']]);
					if ($isValid !== true) {
						SJB_FlashMessages::getInstance()->addWarning('EMPTY_VALUE', array('fieldCaption' => $settingsField['caption']));
						$error = true;
					}
				}
			}
		}
		
		return $error;
	}
}