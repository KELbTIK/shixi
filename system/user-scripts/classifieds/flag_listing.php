<?php

class SJB_Classifieds_FlagListing extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$listingSID = SJB_Request::getVar('listing_id');
		$template = 'flag_listing.tpl';
		$errors = array();

		if ($listingSID) {
			// Flag listing
			$reason = SJB_Request::getVar('reason');
			$comment = SJB_Request::getVar('comment');

			$formSubmitted = SJB_Request::getVar('action');

			if ($formSubmitted) {
				SJB_Captcha::getInstance($tp, $_REQUEST)->isValid($errors);
			}

			$listing = SJB_ListingManager::getObjectBySID($listingSID);
			if (!empty($listing)) {
				$listingInfo = SJB_ListingManager::createTemplateStructureForListing($listing);
			} else {
				$errors['WRONG_LISTING_ID_SPECIFIED'] = 'Listing does not exist';
			}

			if ($formSubmitted == 'flag' && empty($errors)) {
				SJB_ListingManager::flagListingBySID($listingSID, $reason, $comment);
				// notify admin
				SJB_AdminNotifications::sendAdminListingFlaggedLetter($listing);
				$template = 'flag_listing_sended.tpl';
			} elseif (!empty($listing)) {
				// Show form to reason
				$reasons = array();
				if (is_numeric($listingSID) && is_numeric($listing->getListingTypeSID())) {
					$reasons = SJB_DB::query("SELECT * FROM `flag_listing_settings` WHERE FIND_IN_SET(?n, `listing_type_sid`)", $listing->getListingTypeSID());
				}
				$tp->assign('flag_types', $reasons);
			}

			$tp->assign('listing_id', $listingSID);
			if (!empty($listingInfo))
				$tp->assign('listing_type_id', strtolower($listingInfo['type']['id']));

			if (!empty($errors)) {
				$tp->assign('errors', $errors);
				$tp->assign('reason', $reason);
				$tp->assign('comment', $comment);
			}
		}

		$tp->display($template);
	}
}