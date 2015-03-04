<?php


class SJB_Admin_Classifieds_AddListingTypeField extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_type_sid = isset($_REQUEST['listing_type_sid']) ? $_REQUEST['listing_type_sid'] : null;

		if (!is_null($listing_type_sid)) {

			$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing_type_sid);

			$pages_list = array();
			if (count($pages) > 1) {
				$pages_list[] = array('id' => 'no',
					'caption' => 'Don’t add to a page');
				foreach ($pages as $page) {

					$pages_list[] = array('id' => $page['sid'],
						'caption' => $page['page_name']);
				}
			}

			$listing_field = new SJB_ListingField($_REQUEST, $listing_type_sid, $pages_list);
			/**
			 * add infilll instructions field
			 */
			//	$listing_field->addInfillInstructions(SJB_Request::getVar('instructions'));

			$add_listing_field_form = new SJB_Form($listing_field);
			$form_is_submitted = (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add');
			$errors = null;

			if ($form_is_submitted && $add_listing_field_form->isDataValid($errors)) {
				$page = array();
				if (count($pages) == 1) {
					$pages = array_pop($pages);
					$page = array(array('sid' => $pages['sid'], 'listing_type_sid' => $listing_type_sid));
				}
				else {
					$posting_page = $listing_field->getProperty('posting_page');
					$listing_field->deleteProperty('posting_page');
					if ($posting_page->value != "no")
						$page = array(array('sid' => $posting_page->value, 'listing_type_sid' => $listing_type_sid));
				}
				SJB_ListingFieldManager::saveListingField($listing_field, $page);
				$listing_sid = $listing_field->getSID();
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/attention-listing-type-field/?listing_sid=$listing_sid");

			}
			else {

				$template_processor = SJB_System::getTemplateProcessor();
				$template_processor->assign("errors", $errors);
				$template_processor->assign("listing_type_sid", $listing_type_sid);
				$template_processor->assign("listing_type_info", SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sid));
				$add_listing_field_form->registerTags($template_processor);
				$template_processor->assign("form_fields", $add_listing_field_form->getFormFieldsInfo());

				$template_processor->display("add_listing_type_field.tpl");
			}
		}
		else {
			echo 'The system cannot proceed as Listing Type SID is not set';
		}

	}
}
