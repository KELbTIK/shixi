<?php

class SJB_Admin_Classifieds_AddListingField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_common_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_field = new SJB_ListingField($_REQUEST);
		/**
		 * add infilll instructions field
		 */
		//$listing_field->addInfillInstructions(SJB_Request::getVar('instructions'));
		$template_processor = SJB_System::getTemplateProcessor();

		$add_listing_field_form = new SJB_Form($listing_field);
		$add_listing_field_form->registerTags($template_processor);
		$form_is_submitted = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add');
		$errors = null;
		$pages = SJB_PostingPagesManager::getFirstPageEachListingType();

		if ($form_is_submitted && $add_listing_field_form->isDataValid($errors)) {
			$pages = SJB_PostingPagesManager::getFirstPageEachListingType();
			SJB_ListingFieldManager::saveListingField($listing_field, $pages);
			$listing_sid = $listing_field->getSID();
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/attention-listing-type-field/?listing_sid=$listing_sid");

		} else {

			$pagesNum = SJB_PostingPagesManager::getNumAllPages();
			$pageCount = 0;
			foreach ($pagesNum as $val) {
				if ($val['num'] > 1)
					$pageCount = 1;
			}
			$template_processor->assign("pageCount", $pageCount);
			$template_processor->assign("errors", $errors);
			$add_listing_field_form->registerTags($template_processor);
			$template_processor->assign("form_fields", $add_listing_field_form->getFormFieldsInfo());
			$template_processor->display("add_listing_field.tpl");
		}

	}
}
