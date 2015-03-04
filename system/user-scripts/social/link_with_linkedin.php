<?php

class SJB_Social_LinkWithLinkedin extends SJB_Function
{
	public function execute()
	{
		if (SJB_Authorization::isUserLoggedIn() && class_exists('SJB_SocialPlugin') && !SJB_SocialPlugin::getProfileObject() && $socPlugins = SJB_SocialPlugin::getAvailablePlugins()) {
			$tp = SJB_System::getTemplateProcessor();

			$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID(SJB_UserManager::getCurrentUser()->user_group_sid);

			/**
			 * delete from plugins array plugins that are not allowed
			 * for this userGroup registration
			 */
			SJB_SocialPlugin::preparePluginsThatAreAvailableForRegistration($socPlugins, $userGroupInfo['id']);

			if (empty($socPlugins)) {
				return null;
			}
			$socialNetworks = SJB_SocialPlugin::getSocialNetworks($socPlugins);
			$tp->assign('label', 'link');
			$tp->assign('social_plugins', $socialNetworks);
			$tp->display('social_plugins.tpl');

		}
	}
}