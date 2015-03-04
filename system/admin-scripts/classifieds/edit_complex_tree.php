<?php

class SJB_Admin_Classifieds_EditComplexTree extends SJB_Function
{
	public function execute()
	{
		$field_sid = SJB_Request::getVar('field_sid', null);
		$field_info = SJB_ListingComplexFieldManager::getFieldInfoBySID($field_sid);
		$node_sid = SJB_Request::getVar('node_sid', 0);
		$action = SJB_Request::getVar('action');

		if (empty($field_info)) {
			$errors['INVALID_FIELD_SID'] = 1;
		} else {

			if ($action == 'add') {
				$tree_item_value = $_REQUEST['tree_item_value'];
				$order = $_REQUEST['order'];
				if ($tree_item_value == '') {
					$field_errors['Value'] = 'EMPTY_VALUE';
				} else {
					if ($order == 'begin') {
						SJB_ListingComplexFieldManager::addTreeItemToBeginByParentSID($field_sid, $node_sid, $tree_item_value);
					}
					elseif ($order == 'end') {
						SJB_ListingComplexFieldManager::addTreeItemToEndByParentSID($field_sid, $node_sid, $tree_item_value);
					}
					elseif ($order == 'after') {
						$after_tree_item_sid = $_REQUEST['after_tree_item_sid'];
						SJB_ListingComplexFieldManager::addTreeItemAfterByParentSID($field_sid, $node_sid, $tree_item_value, $after_tree_item_sid);
					}
				}

			} elseif ($action == 'save') {
				$tree_item_value = $_REQUEST['tree_item_value'];
				if (empty($tree_item_value)) {
					$field_errors['Value'] = 'EMPTY_VALUE';
				} else {
					SJB_ListingComplexFieldManager::updateTreeItemBySID($node_sid, $tree_item_value);
					$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : null;
					if ($order == 'begin') {
						SJB_ListingComplexFieldManager::moveTreeItemToBeginBySID($node_sid);
					}
					elseif ($order == 'end') {
						SJB_ListingComplexFieldManager::moveTreeItemToEndBySID($node_sid);
					}
					elseif ($order == 'after') {
						$after_tree_item_sid = $_REQUEST['after_tree_item_sid'];
						SJB_ListingComplexFieldManager::moveTreeItemAfterBySID($node_sid, $after_tree_item_sid);
					}

				}
			} elseif ($action == 'delete') {
				$item_sid = SJB_Request::getVar('item_sid');
				if (is_array($item_sid)) {
					foreach ($item_sid as $sid => $val)
						SJB_ListingComplexFieldManager::deleteTreeItemBySID($sid);
				} else {
					SJB_ListingComplexFieldManager::deleteTreeItemBySID($item_sid);
				}

			} elseif ($action == 'move_up') {
				$item_sid = SJB_Request::getVar('item_sid');
				SJB_ListingComplexFieldManager::moveUpTreeItem($item_sid);
			} elseif ($action == 'move_down') {
				$item_sid = SJB_Request::getVar('item_sid');
				SJB_ListingComplexFieldManager::moveDownTreeItem($item_sid);
			} elseif ($action == 'save_order') {
				$item_order = SJB_Request::getVar('item_order');
				SJB_ListingComplexFieldManager::saveNewTreeItemsOrder($item_order);
			}

			$tree_items = SJB_ListingComplexFieldManager::getTreeValuesByParentSID($field_sid, $node_sid);
			$parent_sid = SJB_ListingComplexFieldManager::getTreeParentSID($node_sid);
			$tree_parent_items = SJB_ListingComplexFieldManager::getTreeValuesByParentSID($field_sid, $parent_sid);
		}

		$tp = SJB_System::getTemplateProcessor();

		$listing_field = SJB_ListingComplexFieldManager::getFieldInfoBySID($field_sid);
		$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field['field_sid']);

		$tp->assign("field_sid", $field_sid);
		$tp->assign("listing_field_info", $listing_field_info);
		$tp->assign("listing_type_sid", $listing_field_info['listing_type_sid']);
		$tp->assign("listing_type_info", SJB_ListingTypeManager::getListingTypeInfoBySID($listing_field_info['listing_type_sid']));
		$tp->assign("node_sid", $node_sid);
		$tp->assign("field_info", $field_info);
		$tp->assign("tree_parent_items", $tree_parent_items);
		$tp->assign("tree_items", $tree_items);

		$node_info = SJB_ListingComplexFieldManager::getTreeItemInfoBySID($node_sid);
		$node_path = SJB_ListingComplexFieldManager::getTreeNodePath($node_sid);

		$node_path[0] = array('caption' => 'Root', 'sid' => 0);
		$node_info['node_path'] = $node_path;
		$tp->assign("node_info", $node_info);
		$current_level = isset($node_info['level']) ? $node_info['level'] : 0;
		$tp->assign("current_level", $current_level);
		$tp->assign("complex", 1);
		$tp->assign("errors", isset($errors) ? $errors : null);
		$tp->assign("field_errors", isset($field_errors) ? $field_errors : null);
		$tp->display("edit_tree.tpl");
	}
}
