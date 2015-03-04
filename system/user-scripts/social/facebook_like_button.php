<?php

class SJB_Social_FacebookLikeButton extends SJB_Function
{
	public function execute()
	{
		$listingTypeID = SJB_Request::getVar('type');

		if (class_exists('SJB_SocialPlugin') && in_array('facebook', SJB_SocialPlugin::getAvailablePlugins()) && SJB_Settings::getSettingByName('fb_like' . $listingTypeID)) {

			$listing = SJB_Request::getVar('listing', array());
			$listingID = isset($listing['id']) ? $listing['id'] : 0;
			$tp = SJB_System::getTemplateProcessor();

			switch ($listingTypeID)
			{
				case 'Job':
					$tp->assign('url', SJB_System::getSystemSettings('SITE_URL') . '/display-job/' . $listingID . '/');
					break;
				case 'Resume':
					$tp->assign('url', SJB_System::getSystemSettings('SITE_URL') . '/display-resume/' . $listingID . '/');
					break;
				default:
					$tp->assign('url', SJB_System::getSystemSettings('SITE_URL') . '/display-listing/' . $listingID . '/');
					break;
			}

			$tp->display('facebook_like_button.tpl');
		}
	}
}
