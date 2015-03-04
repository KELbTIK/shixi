<?php

class SJB_Admin_Classifieds_EditList extends SJB_Function
{
	public function isAccessible()
	{
		$permissionLabels = array('manage_common_listing_fields', 'manage_listing_types_and_specific_listing_fields');
		$this->setPermissionLabel($permissionLabels);
		return parent::isAccessible();
	}

	public function execute()
	{
		$edit_list_controller = new SJB_ListingEditListController($_REQUEST);
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
					if ($item_order = SJB_Request::getVar('item_order')) {
						$edit_list_controller->saveNewItemsOrder($item_order);
					}
					break;
				case 'sort':
					$edit_list_controller->sortItems(SJB_Request::getVar('field_sid'), SJB_Request::getVar('sorting_order'));
					$template_processor->assign("sorting_order", SJB_Request::getVar('sorting_order'));
					break;
			}

			$display_list_controller = new SJB_ListingDisplayListController($_REQUEST);
			$display_list_controller->setTemplateProcessor($template_processor);
			$display_list_controller->display("listing_list_editing.tpl");
		}
	}
}
