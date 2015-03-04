<?php

class SJB_Admin_Classifieds_DisplayListing extends SJB_Function
{
	public function isAccessible()
	{
		$listingId = SJB_Request::getVar('listing_id', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
		$listingType = !in_array($listingTypeId, array('Resume', 'Job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel('manage_' . strtolower($listingType));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$listing_id = SJB_Request::getVar('listing_id', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
		$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listingInfo['listing_type_sid']);

		$display_form = new SJB_Form();
		$display_form->registerTags($tp);

		if (is_null($listing_id)) {
			$errors['LISTING_ID_DOESNOT_SPECIFIED'] = $listing_id;
		}
		else {
			$listing = SJB_ListingManager::getObjectBySID($listing_id);

			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				$file = SJB_UploadFileManager::openFile($filename, $listing_id);
				$errors['NO_SUCH_FILE'] = true;
			}

			if (!empty($listing)) {
				$listing->addPicturesProperty();

				if ($listing->listing_type_sid == 6) {
					$listing->deleteProperty('access_type');
					$listing->deleteProperty('anonymous');
				}

				$access_type_properties = $listing->getProperty('access_type');
				$tp->assign('access_type_properties', $access_type_properties);

				$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
				$tp->assign("listing", $listing_structure);

				$display_form = new SJB_Form($listing);
				$display_form->registerTags($tp);
				$form_fields = $display_form->getFormFieldsInfo();
				$tp->assign("form_fields", $form_fields);

				$waitApprove = SJB_ListingTypeManager::getWaitApproveSettingByListingType($listing->listing_type_sid);
				$tp->assign('wait_approve', $waitApprove);
			}
			else {
				$errors['LISTING_DOESNOT_EXIST'] = $listing_id;
			}
		}

		$comments        = SJB_CommentManager::getEnabledCommentsToListing($listing_id);
		$comments_total  = count($comments);
		$rate            = SJB_Rating::getRatingNumToListing($listing_id);
		$displayTemplate = SJB_Request::getVar('display_template', 'display_listing.tpl');
		$videoFileId     = SJB_Request::getVar('videoFileId', false);

		if ($videoFileId) {
			$videoFileLink = SJB_UploadFileManager::getUploadedFileLink($videoFileId);
			$tp->assign('videoFileLink', $videoFileLink);
		}
		$tp->assign('listingType', SJB_ListingTypeManager::createTemplateStructure($listingTypeInfo));
		$tp->assign('errors', $errors);
		$tp->assign('comments_total', $comments_total);
		$tp->assign('rate', $rate);
		SJB_System::setGlobalTemplateVariable('wikiExtraParam', $listingTypeInfo['id']);

		$tp->display($displayTemplate);
	}
}
