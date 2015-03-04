<?php

class SJB_Admin_SocialMedia_Bitly extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('social_media_bitly');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp= SJB_System::getTemplateProcessor();
		$errors         = array();
		$formSubmitted  = SJB_Request::getVar('action');
		$bitlyInfo      = new SJB_Bitly($_REQUEST);
		$bitlyForm      = new SJB_Form($bitlyInfo);

		if ($formSubmitted == 'saveSettings') {
			$bitlyForm->isDataValid($errors);
			if (!$errors) {
				SJB_Settings::updateSettings($_REQUEST);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/social-media/bitly/");
			}
		}

		$tp->assign("settings", SJB_Settings::getSettings());
		$tp->assign("errors", $errors);
		$tp->display("bitly.tpl");
	}
}
