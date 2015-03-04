<?php
require_once 'phpbb_bridge_plugin.php';
$userSession = PhpBBBridgePlugin::getUserSessionBySessionId(SJB_Session::getSessionId());
if (!empty($userSession)) {
	if (SJB_UserManager::isUserLoggedIn()) {
		if ($userSession['user_sid'] !== SJB_UserManager::getCurrentUserSID()) {
			if (!$userSession['user_sid']) {
				SJB_Authorization::logout();
			} else {
				SJB_Session::setValue('current_user', PhpBBBridgePlugin::sessionDecode($userSession['data']));
			}
		}
	} else {
		SJB_Session::setValue('current_user', PhpBBBridgePlugin::sessionDecode($userSession['data']));
	}
}

SJB_Event::handle('Login', array('PhpBBBridgePlugin', 'login'));
SJB_Event::handle('Logout', array('PhpBBBridgePlugin', 'logout'));
