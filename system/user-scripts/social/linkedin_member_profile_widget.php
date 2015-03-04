<?php

class SJB_Social_MemberProfileWidget extends SJB_Function
{
	public function execute()
	{
		if (class_exists('SJB_SocialPlugin') && in_array('linkedin', SJB_SocialPlugin::getAvailablePlugins())
			&& SJB_Settings::getSettingByName('li_memberProfileWidget')) {

			$userSID = SJB_Request::getInt('profileSID', '');

			if ($userSID && $profilePublicUrl = SJB_SocialPlugin::getProfilePublicUrlByProfileID($userSID)) {
				$tp = SJB_System::getTemplateProcessor();
				$tp->assign('inPublicUrl', $profilePublicUrl);
				$tp->display('linkedin_member_profile_widget.tpl');
			}
		}
	}
}