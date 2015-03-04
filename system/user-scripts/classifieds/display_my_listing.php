<?php

class SJB_Classifieds_DisplayMyListing extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$display_form = new SJB_Form();
		$display_form->registerTags($tp);

		$errors = array();
		$criteria_saver = new SJB_ListingCriteriaSaver ('MyListings');

		$listingSID = SJB_Request::getVar("listing_id");
		if (isset ($_REQUEST ['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listingSID = isset ($passed_parameters_via_uri [0]) ? $passed_parameters_via_uri [0] : null;
		}

		$template = SJB_Request::getVar('display_template', 'display_listing.tpl');

		if (is_null($listingSID)) {
			$errors ['UNDEFINED_LISTING_ID'] = true;
		} elseif (is_null($listing = SJB_ListingManager::getObjectBySID($listingSID))) {
			$errors ['WRONG_LISTING_ID_SPECIFIED'] = true;
		} elseif (!$listing->isActive() && $listing->getUserSID() != SJB_UserManager::getCurrentUserSID()) {
			$errors ['LISTING_IS_NOT_ACTIVE'] = true;
		} else {

			$listing->addPicturesProperty();

			if ($listing->getUserSID() != SJB_UserManager::getCurrentUserSID())
				$errors ['NOT_OWNER'] = true;

			$display_form = new SJB_Form ($listing);
			$display_form->registerTags($tp);

			$form_fields = $display_form->getFormFieldsInfo();

			$listingOwner = SJB_UserManager::getObjectBySID($listing->user_sid);

			// listing preview @author still
			$listingTypeSID = $listing->getListingTypeSID();
			$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSID);
			if (SJB_Request::getInstance()->page_config->uri == '/' . strtolower($listingTypeID) . '-preview/') {
				if (!empty($_SERVER['HTTP_REFERER']) && (stristr($_SERVER['HTTP_REFERER'], 'edit-' . $listingTypeID)
					|| stristr($_SERVER['HTTP_REFERER'], 'clone-job'))) {
						$tp->assign('referer', $_SERVER['HTTP_REFERER']);
				} else {
					$lastPage = SJB_PostingPagesManager::getPagesByListingTypeSID($listingTypeSID);
					$lastPage = array_pop($lastPage);
					$tp->assign('referer', SJB_System::getSystemSettings('SITE_URL') . '/add-listing/'
							. $listingTypeID . '/'
							. $lastPage['page_id'] . '/' . $listing->getSID());
				}
				$tp->assign('checkouted', SJB_ListingManager::isListingCheckOuted($listing->getSID()));
				$tp->assign('contract_id', $listing->contractID);
			}

			$listingStructure = SJB_ListingManager::createTemplateStructureForListing($listing, array('comments', 'ratings'));
			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				SJB_UploadFileManager::openFile($filename, $listingSID);
				$errors ['NO_SUCH_FILE'] = true;
			}
			$prev_and_next_listing_id = $criteria_saver->getPreviousAndNextObjectID($listingSID);
			$metaDataProvider =  SJB_ObjectMother::getMetaDataProvider();
			$tp->assign('METADATA', array('listing' => $metaDataProvider->getMetaData($listingStructure ['METADATA']), 'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields)));

			$comments = '';
			$comments_total = '';
			if (SJB_Settings::getSettingByName('show_comments') == '1') {
				$comments = SJB_CommentManager::getEnabledCommentsToListing($listingSID);
				$comments_total = count($comments);
			}

			$tp->assign('show_rates', SJB_Settings::getSettingByName('show_rates'));
			$tp->assign('show_comments', SJB_Settings::getSettingByName('show_comments'));
			$tp->assign('comments', $comments);
			$tp->assign('comments_total', $comments_total);
			$tp->assign('listing_id', $listingSID);
			$tp->assign('form_fields', $form_fields);
			$tp->assign('video_fields', SJB_HelperFunctions::takeMediaFields($form_fields));
			$tp->filterThenAssign("listing", $listingStructure);
			$tp->assign('prev_next_ids', $prev_and_next_listing_id);
			$tp->assign('preview_listing_sid', SJB_Request::getVar('preview_listing_sid'));
			$tp->assign('listingOwner', $listingOwner);

			if (SJB_Request::getVar('action', false) == 'download_pdf_version') {
				$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_PDF, SJB_Array::getPath($listingStructure, 'type/id'));
				$formBuilder->setChargedTemplateProcessor($tp);
				$tpl = 'resume_to_pdf.tpl';
				$filename = $listingStructure['user']['FirstName'] . ' ' . $listingStructure['user']['LastName'] . '_' . $listingStructure['Title'] . '.pdf';
				try {
					$tp->assign('myListing', 1);
					$html = $tp->fetch($tpl);
					$html = preg_replace('/<div[^>]*>/', '', $html);
					$html = str_replace('</div>', '', $html);
					SJB_HelperFunctions::html2pdf($html, $filename, str_replace('http://', '' , SJB_HelperFunctions::getSiteUrl()));
					exit();
				} catch (Exception $e) {
					SJB_Error::writeToLog($e->getMessage());
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/my-resume-details/' . $listingSID . '/?error=TCPDF_ERROR');
				}
			} else {
				$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY, SJB_Array::getPath($listingStructure, 'type/id'));
				$formBuilder->setChargedTemplateProcessor($tp);
			}
		}

		$search_criteria_structure = $criteria_saver->createTemplateStructureForCriteria();

		$tp->filterThenAssign('search_criteria', $search_criteria_structure);
		$tp->assign('errors', $errors);
		$tp->assign('myListing', true);
		$tp->display($template);
	}
}
