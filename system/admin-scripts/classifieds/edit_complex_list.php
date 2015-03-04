<?php

class SJB_Admin_Classifieds_EditComplexList extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$edit_list_controller = new SJB_ListingComplexEditListController($_REQUEST);
		$template_processor = SJB_System::getTemplateProcessor();

		if (!$edit_list_controller->isvalidFieldSID()) {
			echo 'Invalid Listing Field SID is specified';
		} else {
			switch ($edit_list_controller->getAction()) {

				case 'add':
					if ($edit_list_controller->isValidValueSubmitted()) {
						if (!$edit_list_controller->saveItem())
							$template_processor->assign("error", 'LIST_VALUE_ALREADY_EXISTS');
					} else {
						$template_processor->assign("error", 'LIST_VALUE_IS_EMPTY');
					}
					break;

				case 'add_multiple':
					if ($edit_list_controller->isValidMultiValueSubmitted()) {
						if (!$edit_list_controller->saveItem(true))
							$template_processor->assign("error", 'LIST_VALUE_ALREADY_EXISTS');
					} else {
						$template_processor->assign("error", 'LIST_VALUE_IS_EMPTY');
					}
					break;

				case 'delete': //$edit_list_controller->deleteItem(); break;
					$item_sid = SJB_Request::getVar('item_sid');
					if (is_array($item_sid)) {
						foreach ($item_sid as $sid => $val)
							$edit_list_controller->deleteItem($sid);
					} else {
						$edit_list_controller->deleteItem();
					}
					break;

				case 'move_up':
					$edit_list_controller->moveUpItem();
					break;

				case 'move_down':
					$edit_list_controller->moveDownItem();
					break;

				case 'save_order':
					$item_order = SJB_Request::getVar('item_order');
					$edit_list_controller->saveNewItemsOrder($item_order);
					break;
			}

			$display_list_controller = new SJB_ListingComplexDisplayListController($_REQUEST);
			$field_info = $display_list_controller->field_info;
			$parentFieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($field_info['field_sid']);
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($parentFieldInfo['listing_type_sid']);
			$display_list_controller->field_info['type_info'] = $listing_type_info;
			$display_list_controller->field_info['parent_field'] = $parentFieldInfo;
			$display_list_controller->display("listing_complex_list_editing.tpl");
		}
	}
}
