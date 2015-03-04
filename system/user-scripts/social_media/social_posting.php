<?php

class SJB_SocialMedia_SocialPosting extends SJB_Function
{
	public function execute()
	{
		$tp         = SJB_System::getTemplateProcessor();
		$listingSID = SJB_Request::getVar('listing_id', false);
		$errors     = array();
		$buttons    = SJB_SocialMediaSharer::getButtons($listingSID);
		$template   = 'social_posting.tpl';

		if (empty($listingSID)) {
			$errors['PARAMETERS_MISSED'] = 'Listing ID is not defined';
		}

		$tp->assign('listingSID', $listingSID);
		$tp->assign('errors', $errors);
		$tp->assign('buttons', $buttons);
		$tp->display($template);
	}
}