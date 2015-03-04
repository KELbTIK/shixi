<?php

class SJB_Admin_Users_EditList extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('edit_user_groups_profile_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$edit_list_controller = new SJB_UserProfileEditListController($_REQUEST);
		if (!$edit_list_controller->isvalidFieldSID()) {
			echo 'Invalid User Profile Field SID is specified';
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
					$item_order = SJB_Request::getVar('item_order', array());
					$edit_list_controller->saveNewItemsOrder($item_order);
					break;
			}

			$display_list_controller = new SJB_UserProfileDisplayListController($_REQUEST);
			$display_list_controller->display("user_profile_list_editing.tpl");
		}
	}
}
