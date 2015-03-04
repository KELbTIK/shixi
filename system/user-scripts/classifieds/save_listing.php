<?php

class SJB_Classifieds_SaveListing extends SJB_Function
{
	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$listingId   = SJB_Request::getVar('listing_id', null, 'default', 'int');
		$listingType = SJB_Request::getVar('listing_type', null);
		if (!$listingType) {
			if ($listingId) {
				$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
				$listingType = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
			} else {
				$listingType = 'job';
			}
		}
		$displayForm = SJB_Request::getVar('displayForm', false);
		$error = null;
		if (!SJB_Acl::getInstance()->isAllowed('save_' . trim($listingType))) {
			$error = 'DENIED_SAVE_LISTING';
		}
		if (SJB_UserManager::isUserLoggedIn()) {
			if (!$error) {
				if (!is_null($listingId)) {
					if (SJB_UserManager::isUserLoggedIn()) {
						SJB_SavedListings::saveListingOnDB($listingId, SJB_UserManager::getCurrentUserSID());
						SJB_Statistics::addStatistics('saveListing', SJB_ListingTypeManager::getListingTypeSIDByID($listingType), $listingId);
					} else {
						SJB_SavedListings::saveListingInCookie($listingId);
					}
					$template_processor->assign('saved_listing', SJB_SavedListings::getSavedListingsByUserAndListingSid(SJB_UserManager::getCurrentUserSID(), $listingId));
				}
				else
					$error = 'LISTING_ID_NOT_SPECIFIED';
			}
			$params = SJB_Request::getVar('params', false);
			$searchId = SJB_Request::getVar('searchId', false);
			$page = SJB_Request::getVar('page', false);
			$template_processor->assign("params", $params);
			$template_processor->assign("searchId", $searchId);
			$template_processor->assign("page", $page);
			$template_processor->assign("listing_type",	$listingType);
			$template_processor->assign("listing_sid", $listingId);
			$template_processor->assign("from_login", SJB_Request::getVar("from_login", false));
			$template_processor->assign("error", $error);
			$template_processor->assign("displayForm", $displayForm);
			$template_processor->assign("view", SJB_Request::getVar('view'));
			$template_processor->display("save_listing.tpl");
		}
		else {
			$template_processor->assign("return_url", base64_encode(SJB_Navigator::getURIThis() . "&from_login=1"));
			$template_processor->assign("ajaxRelocate", true);
			$template_processor->display("../users/login.tpl");
		}

	}
}
