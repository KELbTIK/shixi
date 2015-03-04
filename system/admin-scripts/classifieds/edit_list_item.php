<?php

class SJB_Admin_Classifieds_EditListItem extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_common_listing_fields', 'manage_listing_types_and_specific_listing_fields'));
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$errors = array();
		$ListingFieldListItemManager = new SJB_ListingFieldListItemManager();

		if (!isset($_REQUEST['field_sid'], $_REQUEST['item_sid'])) {
			echo 'The system cannot proceed as some key paramaters are missed';
		} else {
			if (is_null($list_item = $ListingFieldListItemManager->getListItemBySID($_REQUEST['item_sid']))) {
				echo 'Wrong parameters are specified';
			} else {
				$list_item_info['value'] = $list_item->getValue();
				$template_processor->assign("list_item_info", $list_item_info);
				$form_submitted = SJB_Request::getVar('action', '');
				if ($form_submitted) {
					if (empty($_REQUEST['list_item_value'])) {
						$errors = array('Value' => 'EMPTY_VALUE');
					} else {
						$list_item->setValue(trim($_REQUEST['list_item_value']));
						$ListingFieldListItemManager->saveListItem($list_item);
						if ($form_submitted == 'save') {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-field/edit-list/?field_sid=" . $_REQUEST['field_sid']);
						}
					}
				}

				$listing_field = SJB_ListingFieldManager::getFieldBySID($_REQUEST['field_sid']);
				$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($_REQUEST['field_sid']);
				$template_processor->assign("listing_field_info", $listing_field_info);
				$template_processor->assign("listing_type_sid", $listing_field->getListingTypeSID());
				$template_processor->assign("field_sid", $_REQUEST['field_sid']);
				$template_processor->assign("item_sid", $_REQUEST['item_sid']);
				$template_processor->assign("list_item_value", htmlspecialchars($list_item->getValue()));
				$template_processor->assign("errors", $errors);
				$template_processor->assign("listing_type_info", SJB_ListingTypeManager::getListingTypeInfoBySID($listing_field->getListingTypeSID()));
				$template_processor->display("listing_list_item_editing.tpl");
			}
		}
	}
}
