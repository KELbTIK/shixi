<?php

class SJB_Admin_Classifieds_EditListingType extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();

		$listingTypeSID = isset($_REQUEST['sid']) ? $_REQUEST['sid'] : null;

		if (!is_null($listingTypeSID)) {
			$form_submitted = SJB_Request::getVar('action', '');
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($listingTypeSID);
			$approveSettingChanged = $listing_type_info['waitApprove'] != SJB_Request::getVar('waitApprove');
			$listing_type_info = array_merge($listing_type_info, $_REQUEST);
			$listingType = new SJB_ListingType($listing_type_info);
			$listingType->setSID($listingTypeSID);
			$edit_form = new SJB_Form($listingType);
			$listingTypeEmailAlert = $listingType->getPropertyValue('email_alert');
			if (empty($listingTypeEmailAlert)) {
				$listingType->setPropertyValue('email_alert', 0);
			}
			$listingTypeEmailAlertForGuests = $listingType->getPropertyValue('guest_alert_email');
			if (empty($listingTypeEmailAlertForGuests)) {
				$listingType->setPropertyValue('guest_alert_email', 0);
			}
			$errors = array();
			if ($form_submitted && $edit_form->isDataValid($errors)) {
				SJB_Breadcrumbs::updateBreadcrumbsByListingTypeSID($listingTypeSID, $listingType->getPropertyValue('name'));
				SJB_PageManager::updatePagesByListingTypeSID($listingTypeSID, $listingType->getPropertyValue('name'));
				SJB_ListingTypeManager::saveListingType($listingType);
				if ($approveSettingChanged) {
					SJB_BrowseDBManager::rebuildBrowses();
				}
				if ($form_submitted == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/listing-types/');
				}
			}

			$template_processor->assign('errors', $errors);
			$template_processor->assign('listing_type_sid', $listingTypeSID);

			$listing_fields_info = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listingTypeSID);
			$listing_fields = array();
			$listing_field_sids = array();

			foreach ($listing_fields_info as $listing_field_info) {
				if ($listing_field_info['type'] == 'logo') {
					continue;
				}
				$listing_field = new SJB_ListingField($listing_field_info);
				$listing_field->setSID($listing_field_info['sid']);
				$listing_fields[] = $listing_field;
				$listing_field_sids[] = $listing_field_info['sid'];
			}

			$edit_form->registerTags($template_processor);

			$template_processor->assign("listing_type_info", $listing_type_info);
			$template_processor->assign("form_fields", $edit_form->getFormFieldsInfo());
			$template_processor->display("edit_listing_type.tpl");

			$form_collection = new SJB_FormCollection($listing_fields);
			$form_collection->registerTags($template_processor);

			$template_processor->assign("listing_field_sids", $listing_field_sids);
			$template_processor->display("listing_type_fields.tpl");
		}
	}
}
