<?php

class SJB_Social_SocialLogin extends SJB_Function
{
	public function execute()
	{
		if (!SJB_Authorization::isUserLoggedIn()
				&& class_exists('SJB_SocialPlugin')
				&& $socPlugins = SJB_SocialPlugin::getAvailablePlugins()) {
			
			SJB_SocialPlugin::preparePluginsThatAreAvailableForRegistration($socPlugins);
			if (empty($socPlugins)) {
				return null;
			}
			
			$socNetworks = SJB_SocialPlugin::getSocialNetworks($socPlugins);
			
			$tp = SJB_System::getTemplateProcessor();
			$tp->assign('aSocPlugins', $socNetworks);
			$tp->display('login_buttons.tpl');
		}
		else if (SJB_Authorization::isUserLoggedIn()) {
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-account/');
		}
	}
}

