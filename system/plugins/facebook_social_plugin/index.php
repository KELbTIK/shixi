<?php

if (!function_exists('curl_init')) {
	$GLOBALS[SJB_SocialPlugin::SOCIAL_ACCESS_ERROR]['SOCIAL_ACCESS_ERROR'] = 'facebook';
	SJB_Error::writeToLog('Facebook needs the CURL PHP extension.');
	return null;
}

if (!function_exists('json_decode')) {
	$GLOBALS[SJB_SocialPlugin::SOCIAL_ACCESS_ERROR]['SOCIAL_ACCESS_ERROR'] = 'facebook';
	SJB_Error::writeToLog('Facebook needs the JSON PHP extension.');
	return null;
}

require_once 'facebook_social_plugin.php';

SJB_SocialPlugin::loadPlugin('facebook', $socPlugin = new FacebookSocialPlugin());

if ($socPlugin->getNetwork() === SJB_SocialPlugin::getNetwork()) {
	/*
	 * login/logout
	 */
	SJB_Event::handle('Login_Plugin', array('SJB_SocialPlugin', 'login'));
	SJB_Event::handle('Logout', array('SJB_SocialPlugin', 'logout'), 1000);

	/*
	 * registration
	 */
	SJB_Event::handle('FillRegistrationDataRequest_Plugin', array('FacebookSocialPlugin', 'fillRegistrationDataWithRequest'));
	SJB_Event::handle('FillRegistrationData_Plugin', array('SJB_SocialPlugin', 'fillRegistrationDataWithUser'));
	SJB_Event::handle('PrepareRegistrationFields_SocialPlugin', array('FacebookSocialPlugin', 'prepareRegistrationFields'));
	SJB_Event::handle('MakeRegistrationFieldsNotRequired_SocialPlugin', array('FacebookSocialPlugin', 'makeRegistrationFieldsNotRequired'));
	SJB_Event::handle('AddReferencePluginDetails', array('SJB_SocialPlugin', 'addReferenceDetails'));
	SJB_Event::handle('SocialPlugin_PostRegistrationActions', array('SJB_SocialPlugin', 'postRegistrationActions'));
	SJB_Event::handle('SocialPlugin_AddListingFieldsIntoRegistration', array('FacebookSocialPlugin', 'addListingFieldsIntoRegistration'));

	/*
	 * LISTING AUTOFILL SYNCHRONIZATION
	 */
	SJB_Event::handle('SocialSynchronization', array('SJB_SocialPlugin', 'autofillListing'));
	SJB_Event::handle('SocialSynchronizationForm', array('SJB_SocialPlugin', 'autofillListingForm'));
	SJB_Event::handle('SocialSynchronizationFields', array('SJB_SocialPlugin', 'autofillListingFields'));
	SJB_Event::handle('SocialSynchronizationFieldsOnPostingPages', array('SJB_SocialPlugin', 'autofillListingFieldsOnPostingPages'));
}
