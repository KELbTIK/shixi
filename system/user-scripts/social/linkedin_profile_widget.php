<?php

class SJB_Social_ProfileWidget extends SJB_Function
{
	public function execute()
	{
		if (class_exists('SJB_SocialPlugin') && in_array('linkedin', SJB_SocialPlugin::getAvailablePlugins())
				&& SJB_Settings::getSettingByName('li_companyProfileWidget')) {

			$companyName = SJB_Request::getVar('companyName');
			if ($companyName) {
				$tp = SJB_System::getTemplateProcessor();
				$tp->assign('companyName', $companyName);
				$tp->display('linkedin_profile_widget.tpl');
			}
		}
	}
}