<?php

class SJB_Admin_Users_EditTree extends SJB_Function
{
	public function execute()
	{
		$field_sid = isset($_REQUEST['field_sid']) ? $_REQUEST['field_sid'] : null;
		$field_info = SJB_UserProfileFieldManager::getFieldInfoBySID($field_sid);
		$node_sid = isset($_REQUEST['node_sid']) ? $_REQUEST['node_sid'] : 0;
		$user_group_sid = isset($_REQUEST['user_group_sid']) ? $_REQUEST['user_group_sid'] : null;
		$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);

		if (empty($field_info)) {
			$errors['INVALID_FIELD_SID'] = 1;
		} else {
			if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add') {
				$tree_item_value = $_REQUEST['tree_item_value'];
				$order = $_REQUEST['order'];
				if ($tree_item_value == '') {
					$field_errors['Value'] = 'EMPTY_VALUE';
				} else {
					if ($order == 'begin') {
						SJB_UserProfileFieldManager::addTreeItemToBeginByParentSID($field_sid, $node_sid, $tree_item_value);
					}
					elseif ($order == 'end') {
						SJB_UserProfileFieldManager::addTreeItemToEndByParentSID($field_sid, $node_sid, $tree_item_value);
					}
					elseif ($order == 'after') {
						$after_tree_item_sid = $_REQUEST['after_tree_item_sid'];
						SJB_UserProfileFieldManager::addTreeItemAfterByParentSID($field_sid, $node_sid, $tree_item_value, $after_tree_item_sid);
					}
					$treeLevelsNumber = SJB_UserProfileFieldTreeManager::getTreeDepthBySID($field_sid);
					SJB_UserProfileFieldManager::addLevelField($treeLevelsNumber);
				}

			} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save') {
				$tree_item_value = $_REQUEST['tree_item_value'];
				if (empty($tree_item_value)) {
					$field_errors['Value'] = 'EMPTY_VALUE';
				} else {
					SJB_UserProfileFieldManager::updateTreeItemBySID($node_sid, $tree_item_value);
					$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : null;
					if ($order == 'begin') {
						SJB_UserProfileFieldManager::moveTreeItemToBeginBySID($node_sid);
					}
					elseif ($order == 'end') {
						SJB_UserProfileFieldManager::moveTreeItemToEndBySID($node_sid);
					}
					elseif ($order == 'after') {
						$after_tree_item_sid = $_REQUEST['after_tree_item_sid'];
						SJB_UserProfileFieldManager::moveTreeItemAfterBySID($node_sid, $after_tree_item_sid);
					}
				}
			} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
				$item_sid = isset($_REQUEST['item_sid']) ? $_REQUEST['item_sid'] : null;
				SJB_UserProfileFieldManager::deleteTreeItemBySID($item_sid);
			} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'move_up') {
				$item_sid = isset($_REQUEST['item_sid']) ? $_REQUEST['item_sid'] : null;
				SJB_UserProfileFieldManager::moveUpTreeItem($item_sid);
			} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'move_down') {
				$item_sid = isset($_REQUEST['item_sid']) ? $_REQUEST['item_sid'] : null;
				SJB_UserProfileFieldManager::moveDownTreeItem($item_sid);
			} elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_multiple') {
				$node_sid = (isset($_REQUEST['node_sid'])) ? SJB_Request::getInt('node_sid') : 0;
				$item_sid = SJB_Request::getVar('field_sid');
				$tree_item_value = SJB_Request::getVar('tree_multiItem_value', false);
				$after_tree_item_sid = SJB_Request::getVar('after_tree_item_sid', 0);
				$order = SJB_Request::getVar('order', false);
				if ($tree_item_value == '') {
					$field_errors['Value'] = 'EMPTY_VALUE';
				}
				SJB_UserProfileFieldTreeManager::addMultupleTreeItem($item_sid, $node_sid, $tree_item_value, $order, $after_tree_item_sid);
			}

			$tree_items = SJB_UserProfileFieldManager::getTreeValuesByParentSID($field_sid, $node_sid);
			$parent_sid = SJB_UserProfileFieldManager::getTreeParentSID($node_sid);
			$tree_parent_items = SJB_UserProfileFieldManager::getTreeValuesByParentSID($field_sid, $parent_sid);
		}

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign("field_sid", $field_sid);
		$tp->assign("node_sid", $node_sid);
		$tp->assign("user_group_sid", $user_group_sid);
		$tp->assign("user_group_info", $user_group_info);
		$tp->assign("field_info", $field_info);
		$tp->assign("tree_parent_items", $tree_parent_items);
		$tp->assign("tree_items", $tree_items);
		$node_info = SJB_UserProfileFieldManager::getTreeItemInfoBySID($node_sid);
		$node_path = SJB_UserProfileFieldManager::getTreeNodePath($node_sid);
		$node_path[0] = array('caption' => 'Root', 'sid' => 0);
		$node_info['node_path'] = $node_path;
		$tp->assign("node_info", $node_info);
		$current_level = isset($node_info['level']) ? $node_info['level'] : 0;
		$tp->assign("current_level", $current_level);
		$tp->assign("errors", isset($errors) ? $errors : null);
		$tp->assign("field_errors", isset($field_errors) ? $field_errors : null);
		$tp->display("edit_tree.tpl");
	}
}
