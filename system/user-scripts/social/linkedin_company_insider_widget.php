<?php

class SJB_Social_CompanyInsiderWidget extends SJB_Function
{
	public function execute()
	{
		if (class_exists('SJB_SocialPlugin') && in_array('linkedin', SJB_SocialPlugin::getAvailablePlugins())
				&& SJB_Settings::getSettingByName('li_companyInsiderWidget')) {

			$companyName = str_replace(" ", "-", SJB_Request::getVar('companyName', ''));

			if (!empty($companyName)) {
				$tp = SJB_System::getTemplateProcessor();
				$tp->assign('companyName', $companyName);
				$tp->display('company_insider_widget.tpl');
			}
		}
	}
}