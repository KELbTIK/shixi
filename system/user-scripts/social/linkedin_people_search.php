<?php

class SJB_Social_LinkedinPeopleSearch extends SJB_Function
{
	public function execute()
	{
		if (class_exists('SJB_SocialPlugin') && in_array('linkedin', SJB_SocialPlugin::getAvailablePlugins()) && SJB_Settings::getSettingByName('li_allowPeopleSearch')) {
			if ('Resume' == $_REQUEST['listing_type_id'] && SJB_SocialPlugin::getNetwork() == 'linkedin') {
				$tp = SJB_System::getTemplateProcessor();
				$tp->assign('linkedinSearchIsAllowed', true);
				$tp->assign('linkedinSearch', (!empty($_SESSION['linkedinPeopleSearch']) && 'no' === $_SESSION['linkedinPeopleSearch'] && !empty($_GET['searchId'])) ? 'notChecked' : 'no');
				$tp->display('linkedin_people_search_form.tpl');
			}
		}
	}
}