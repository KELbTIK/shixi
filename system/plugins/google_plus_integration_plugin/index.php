<?php

require_once 'google_plus_integration_plugin.php';
$includePath = explode(PATH_SEPARATOR, get_include_path());
unset($includePath[0]);
set_include_path(implode(PATH_SEPARATOR, $includePath));
SJB_SocialPlugin::loadPlugin('google_plus', new GooglePlusSocialPlugin());

if (GooglePlusSocialPlugin::getNetwork() === SJB_SocialPlugin::getNetwork()) {
	SJB_Event::handle('Login_Plugin', array('SJB_SocialPlugin', 'login'));
	SJB_Event::handle('Logout', array('GooglePlusSocialPlugin', 'logout'), 1000);
	SJB_Event::handle('FillRegistrationData_Plugin', array('SJB_SocialPlugin', 'fillRegistrationDataWithUser'));
	SJB_Event::handle('PrepareRegistrationFields_SocialPlugin', array('SJB_SocialPlugin', 'prepareRegistrationFields'));
	SJB_Event::handle('MakeRegistrationFieldsNotRequired_SocialPlugin', array('SJB_SocialPlugin', 'makeRegistrationFieldsNotRequired'));
	SJB_Event::handle('AddReferencePluginDetails', array('GooglePlusSocialPlugin', 'addReferenceDetails'));
}
