<?php

class SJB_Admin_Classifieds_EditComplexListItem extends SJB_Function
{
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
				if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
					if (empty($_REQUEST['list_item_value'])) {
						$errors = array('Value' => 'EMPTY_VALUE');
					} else {
						$list_item->setValue(trim($_REQUEST['list_item_value']));
						$ListingFieldListItemManager->saveListItem($list_item);
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/edit-listing-field/edit-fields/edit-list/?field_sid=" . $_REQUEST['field_sid']);
					}
				}

				$listing_field = SJB_ListingComplexFieldManager::getFieldInfoBySID($_REQUEST['field_sid']);
				$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field['field_sid']);

				$template_processor->assign("listing_field_info", $listing_field_info);
				$template_processor->assign("listing_type_sid", $listing_field_info['listing_type_sid']);
				$template_processor->assign("complex", 1);
				$template_processor->assign("field_sid", $_REQUEST['field_sid']);
				$template_processor->assign("field_info", $listing_field);
				$template_processor->assign("item_sid", $_REQUEST['item_sid']);
				$template_processor->assign("list_item_value", htmlspecialchars($list_item->getValue()));
				$template_processor->assign("errors", $errors);
				$template_processor->assign("listing_type_info", SJB_ListingTypeManager::getListingTypeInfoBySID($listing_field_info['listing_type_sid']));
				$template_processor->display("listing_list_item_editing.tpl");

			}

		}

	}
}
