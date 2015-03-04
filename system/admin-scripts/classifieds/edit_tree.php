<?php

class SJB_Admin_Classifieds_EditTree extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_common_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$field_sid = SJB_Request::getVar('field_sid', null);
		$field_info = SJB_ListingFieldManager::getFieldInfoBySID($field_sid);
		$node_sid = SJB_Request::getVar('node_sid', 0);
		$action = SJB_Request::getVar('action');
		$template_processor = SJB_System::getTemplateProcessor();

		if (empty($field_info)) {
			$errors['INVALID_FIELD_SID'] = 1;
		} else {
			switch ($action) {
				case 'add':
					$tree_item_value = $_REQUEST['tree_item_value'];
					$order = $_REQUEST['order'];
					if ($tree_item_value == '') {
						$field_errors['Value'] = 'EMPTY_VALUE';
					} else {
						if ($order == 'begin') {
							SJB_ListingFieldManager::addTreeItemToBeginByParentSID($field_sid, $node_sid, $tree_item_value);
						}
						elseif ($order == 'end') {
							SJB_ListingFieldManager::addTreeItemToEndByParentSID($field_sid, $node_sid, $tree_item_value);
						}
						elseif ($order == 'after') {
							$after_tree_item_sid = $_REQUEST['after_tree_item_sid'];
							SJB_ListingFieldManager::addTreeItemAfterByParentSID($field_sid, $node_sid, $tree_item_value, $after_tree_item_sid);
						}
						$treeLevelsNumber = SJB_ListingFieldTreeManager::getTreeDepthBySID($field_sid);
						SJB_ListingFieldManager::addLevelField($treeLevelsNumber);
					}
					break;

				case 'save':
					$tree_item_value = $_REQUEST['tree_item_value'];
					if (empty($tree_item_value)) {
						$field_errors['Value'] = 'EMPTY_VALUE';
					} else {
						SJB_ListingFieldManager::updateTreeItemBySID($node_sid, $tree_item_value);

						$order = SJB_Request::getVar('order', null);
						if ($order == 'begin') {
							SJB_ListingFieldManager::moveTreeItemToBeginBySID($node_sid);
						}
						elseif ($order == 'end') {
							SJB_ListingFieldManager::moveTreeItemToEndBySID($node_sid);
						}
						elseif ($order == 'after') {
							$after_tree_item_sid = $_REQUEST['after_tree_item_sid'];
							SJB_ListingFieldManager::moveTreeItemAfterBySID($node_sid, $after_tree_item_sid);
						}

					}
					break;

				case 'delete':
					$item_sid = SJB_Request::getVar('item_sid');
					if (is_array($item_sid)) {
						foreach ($item_sid as $sid => $val) {
							SJB_ListingFieldManager::deleteTreeItemBySID($sid);
						}
					}
					else if (isset($item_sid)) {
						SJB_ListingFieldManager::deleteTreeItemBySID($item_sid);
					}
					break;

				case 'move_up':
					$item_sid = SJB_Request::getVar('item_sid');
					SJB_ListingFieldManager::moveUpTreeItem($item_sid);
					break;

				case 'move_down':
					$item_sid = SJB_Request::getVar('item_sid');
					SJB_ListingFieldManager::moveDownTreeItem($item_sid);
					break;

				case 'save_order':
					$item_order = SJB_Request::getVar('item_order', array());
					SJB_ListingFieldManager::saveNewTreeItemsOrder($item_order);
					break;

				case 'sort':
					$node_sid = (isset($_REQUEST['node_sid'])) ? SJB_Request::getInt('node_sid') : 0;
					SJB_ListingFieldManager::sortTreeItems(SJB_Request::getVar('field_sid'), $node_sid, SJB_Request::getVar('sorting_order'));
					$template_processor->assign("sorting_order", SJB_Request::getVar('sorting_order'));
					break;

				case 'add_multiple':
					$node_sid = (isset($_REQUEST['node_sid'])) ? SJB_Request::getInt('node_sid') : 0;
					$item_sid = SJB_Request::getVar('field_sid');
					$tree_item_value = SJB_Request::getVar('tree_multiItem_value', false);
					$after_tree_item_sid = SJB_Request::getVar('after_tree_item_sid', 0);
					$order = SJB_Request::getVar('order', false);
					if ($tree_item_value == '') {
						$field_errors['Value'] = 'EMPTY_VALUE';
					}
					SJB_ListingFieldTreeManager::addMultupleTreeItem($item_sid, $node_sid, $tree_item_value, $order, $after_tree_item_sid);
					break;
			}

			$tree_items = SJB_ListingFieldManager::getTreeValuesByParentSID($field_sid, $node_sid);
			$parent_sid = SJB_ListingFieldManager::getTreeParentSID($node_sid);
			$tree_parent_items = SJB_ListingFieldManager::getTreeValuesByParentSID($field_sid, $parent_sid);

		}


		$template_processor->assign("field_sid", $field_sid);
		$template_processor->assign("node_sid", $node_sid);
		$template_processor->assign("field_info", $field_info);
		$template_processor->assign("tree_parent_items", $tree_parent_items);
		$template_processor->assign("tree_items", $tree_items);

		$node_info = SJB_ListingFieldManager::getTreeItemInfoBySID($node_sid);
		$node_path = SJB_ListingFieldManager::getTreeNodePath($node_sid);

		$node_path[0] = array('caption' => 'Root', 'sid' => 0);
		$node_info['node_path'] = $node_path;

		$template_processor->assign("node_info", $node_info);

		$current_level = isset($node_info['level']) ? $node_info['level'] : 0;

		$template_processor->assign("current_level", $current_level);
		$template_processor->assign("type_sid", $field_info['listing_type_sid']);

		$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($field_info['listing_type_sid']);

		$template_processor->assign("type_info", $listing_type_info);
		$template_processor->assign("errors", isset($errors) ? $errors : null);
		$template_processor->assign("field_errors", isset($field_errors) ? $field_errors : null);

		$template_processor->display("edit_tree.tpl");
	}
}
