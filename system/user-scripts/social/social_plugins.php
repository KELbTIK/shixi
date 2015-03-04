<?php

class SJB_Social_SocialPlugins extends SJB_Function
{
	/** @var SJB_TemplateProcessor */
	protected $tp;

	public function execute()
	{
		$this->tp = SJB_System::getTemplateProcessor();
		
		if ( ! SJB_Authorization::isUserLoggedIn()
				&& class_exists('SJB_SocialPlugin')
				&& '/registration-social/' != SJB_Navigator::getUri()
				&& $socPlugins = SJB_SocialPlugin::getAvailablePlugins()) {
			
			$this->showErrorsIfExist();
			
			$userGroupID = SJB_Request::getVar('user_group_id', null);
			
			SJB_SocialPlugin::preparePluginsThatAreAvailableForRegistration($socPlugins, $userGroupID);
			if (empty($socPlugins)) {
				return null;
			}
			
			if ($userGroupID) {
				$this->tp->assign('user_group_id', $userGroupID);
			}
			
			$socNetworks = SJB_SocialPlugin::getSocialNetworks($socPlugins);
			
			$this->tp->assign('label', SJB_Request::getVar('label', null));
			$this->tp->assign('social_plugins', $socNetworks);
			$this->tp->assign('shoppingCart', SJB_Request::getVar('shoppingCart', null));
			$this->tp->display('social_plugins.tpl');
		} else {
			$this->showErrorsIfExist();
		}
	}

	private function showErrorsIfExist()
	{
		if (!empty($GLOBALS[SJB_SocialPlugin::SOCIAL_LOGIN_ERROR])) {
			$this->tp->assign('errors', $GLOBALS[SJB_SocialPlugin::SOCIAL_LOGIN_ERROR]);
			$this->tp->display('../users/errors.tpl');
		}
		
		if (!empty($GLOBALS[SJB_SocialPlugin::SOCIAL_ACCESS_ERROR])) {
			$this->tp->assign('errors', $GLOBALS[SJB_SocialPlugin::SOCIAL_ACCESS_ERROR]);
			$this->tp->assign('socialNetwork', SJB_SocialPlugin::getNetwork());
			$this->tp->display('../users/errors.tpl');
		}
	}
}
