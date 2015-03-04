<?php

class SJB_UserProfileFieldTreeManager
{
	public static function getTreeValuesBySID($field_sid)
	{
		$tree_values = SJB_DB::query("SELECT *, sid AS id FROM user_profile_field_tree WHERE field_sid = ?n ORDER BY `order`", $field_sid);
		$result = array();
		
		foreach ($tree_values as $tree_value) {
			$result[$tree_value['parent_sid']][] = $tree_value;
		}
		return $result;
	}

	public static function getTreePropertyByTreeSID($tree_sid)
	{
		$res = SJB_DB::query('SELECT * FROM `user_profile_fields` WHERE `sid` = ?n', $tree_sid);
		return array_pop($res);
	}

	/**
	 *
	 * @param int $fieldSID
	 * @param int $parentSID
	 * @return array
	 */
	public static function getTreeItemsByParentSIDAndFieldSID($fieldSID, $parentSID = 0)
	{
		$tree_values = SJB_DB::query('SELECT *, `sid` AS id FROM `user_profile_field_tree` WHERE `field_sid` = ?n AND `parent_sid` = ?n ORDER BY `order`', $fieldSID, $parentSID);
		$result = array();
		foreach ($tree_values as $tree_value)
			$result[$tree_value['parent_sid']][] = $tree_value;
		return $result;
	}

	public static function getSIDByCaption($field_sid, $parent_sids, $filterValue)
	{
		$join_sql = "";
		$and_sql = "";
		$main = 'main';
		foreach ($parent_sids as $i => $sid) {
			if ($sid > 0){
				$join_sql .= "
							INNER join user_profile_field_tree parent_"  . $i . "
							ON parent_"  . $i . ".sid = " . $main . ".parent_sid";						
				$and_sql .= " AND parent_"  . $i . ".sid = " . $sid;
				$main = "parent_"  . $i;
			}
		};
		
		if (empty($parent_sids))
			$and_sql .= "AND main.parent_sid = 0 ";

		$sql= "	SELECT	main.sid
				FROM	user_profile_field_tree main
					$join_sql
				WHERE
					main.field_sid = ?n
					AND main.caption = ?s
					$and_sql";
		$data = SJB_DB::query($sql, $field_sid, $filterValue);
		if (empty($data))
			return -1;
		return $data[0]['sid'];
	}

	public static function getTreeValuesByParentSID($field_sid, $parent_sid)
	{
		$tree_node_values = SJB_DB::query("SELECT *, sid AS id FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n ORDER BY `order`", $field_sid, $parent_sid);
		$result_values = array();
		if (!empty($tree_node_values)) {
			foreach ($tree_node_values as $tree_node_value)
				$result_values[$tree_node_value['sid']] = $tree_node_value['caption'];
		}
		return $result_values;
	}

	public static function getTreeItemLevelBySID($item_sid)
	{
		$level = SJB_DB::queryValue("SELECT level FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		return empty($level) ? 0 : $level;
	}

	public static function addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		$parent_level = SJB_UserProfileFieldTreeManager::getTreeItemLevelBySID($parent_sid);
		$level = $parent_level + 1;
		$max_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n", $field_sid, $parent_sid);
		$max_order = empty($max_order) ? 0 : $max_order;
		$new_order = $max_order + 1;
		return SJB_DB::query("INSERT INTO user_profile_field_tree SET field_sid = ?n, parent_sid = ?n, level = ?n, caption = ?s, `order` = ?n", 
					$field_sid, $parent_sid, $level, $tree_item_value, $new_order);
	}

	public static function addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		$parent_level = SJB_UserProfileFieldTreeManager::getTreeItemLevelBySID($parent_sid);
		$level = $parent_level + 1;
		$min_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n", $field_sid, $parent_sid);
		$min_order = empty($min_order) ? 0 : $min_order;
		$new_order = $min_order;
		SJB_DB::queryExec("UPDATE user_profile_field_tree SET `order` = `order` + 1 WHERE parent_sid = ?n", $parent_sid);
		return SJB_DB::query("INSERT INTO user_profile_field_tree SET field_sid = ?n, parent_sid = ?n, level = ?n, caption = ?s, `order` = ?n", 
					$field_sid, $parent_sid, $level, $tree_item_value, $new_order);
	}

	public static function addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid)
	{
		$parent_level = SJB_UserProfileFieldTreeManager::getTreeItemLevelBySID($parent_sid);
		$level = $parent_level + 1;
		$after_order = SJB_DB::queryValue("SELECT `order` FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n AND sid = ?n", $field_sid, $parent_sid, $after_tree_item_sid);
		$after_order = empty($after_order) ? 0 : $after_order;
		$new_order = $after_order + 1;
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = `order` + 1 WHERE parent_sid = ?n AND `order` > ?n", $parent_sid, $after_order);
		return SJB_DB::query("INSERT INTO user_profile_field_tree SET field_sid = ?n, parent_sid = ?n, level = ?n, caption = ?s, `order` = ?n", 
					$field_sid, $parent_sid, $level, $tree_item_value, $new_order);
	}

	public static function deleteTreeItemBySID($item_sid)
	{
		$children = SJB_DB::query("SELECT sid FROM user_profile_field_tree WHERE parent_sid = ?n", $item_sid);
		foreach ($children as $child)
			SJB_UserProfileFieldTreeManager::deleteTreeItemBySID($child['sid']);
		return SJB_DB::query("DELETE FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
	}

	public static function moveUpTreeItem($item_sid)
	{
		$item_info = SJB_DB::query("SELECT * FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		if (empty($item_info))
			return false;
		$item_info = array_pop($item_info);
		$current_order = $item_info['order'];
		$up_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n AND `order` < ?n",
								$item_info['field_sid'], $item_info['parent_sid'], $current_order);
		if ($up_order > 0) {
			SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE `order` = ?n AND parent_sid = ?n", $current_order, $up_order, $item_info['parent_sid']);
			SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE sid = ?n", $up_order, $item_sid);
			return true;
		} 
		return false;
	}

	public static function moveDownTreeItem($item_sid)
	{
		$item_info = SJB_DB::query("SELECT * FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		if (empty($item_info)) {
			return false;
		} else {
			$item_info = array_pop($item_info);
			$current_order = $item_info['order'];
			$down_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n AND `order` > ?n",
									$item_info['field_sid'], $item_info['parent_sid'], $current_order);
			if ($down_order) {
				SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE `order` = ?n AND parent_sid = ?n", $current_order, $down_order, $item_info['parent_sid']);
				SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE sid = ?n", $down_order, $item_sid);
				return true;
			}
			return false;
		}
	}

	public static function getTreeItemInfoBySID($item_sid)
	{
		$item_info = SJB_DB::query("SELECT *, sid AS id FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		return empty($item_info) ? null : array_pop($item_info);
	}

	public static function updateTreeItemBySID($item_sid, $tree_item_value)
	{
		return SJB_DB::query("UPDATE user_profile_field_tree SET caption = ?s WHERE sid = ?n", $tree_item_value, $item_sid);
	}

	public static function getTreeNodePath($node_sid)
	{
		$node_info = SJB_UserProfileFieldTreeManager::getTreeItemInfoBySID($node_sid);
		$path = array($node_info);
		$parent_sid = $node_info['parent_sid'];
		while (!is_null($parent_sid)) {
			$parent_node_info = SJB_UserProfileFieldTreeManager::getTreeItemInfoBySID($parent_sid);
			array_unshift($path, $parent_node_info);
			$parent_sid = $parent_node_info['parent_sid'];
		}
		return $path;
	}

	public static function getParentSID($item_sid)
	{
		return SJB_DB::queryValue("SELECT parent_sid FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
	}

	public static function getTreeDisplayValueBySID($item_sid)
	{
		$item_info = SJB_UserProfileFieldTreeManager::getTreeItemInfoBySID($item_sid);
		$values = array();
		while (!is_null($item_info)) {
			array_unshift($values, $item_info['caption']);
			$item_info = SJB_UserProfileFieldTreeManager::getTreeItemInfoBySID($item_info['parent_sid']);
		}
		return $values;
	}

	public static function getChildrenSIDBySID($item_sid)
	{
		$children_sids = array();
		$children = SJB_DB::query("SELECT sid FROM user_profile_field_tree WHERE parent_sid = ?n", $item_sid);
		if (!empty($children)) {
			foreach ($children as $child) {
				$children_sids[] = $child['sid'];
				$children_sids = array_merge($children_sids, SJB_UserProfileFieldTreeManager::getChildrenSIDBySID($child['sid']));
			}
		}
		return $children_sids;
	}

	public static function getTreeDepthBySID($field_sid)
	{
		$tree_depth = SJB_DB::queryValue("SELECT MAX(level) FROM user_profile_field_tree WHERE field_sid = ?n", $field_sid);
		return empty($tree_depth) ? 0 : $tree_depth;
	}

	public static function importTreeItem($field_sid, $imported_row)
	{
		if (!is_array($imported_row))
		    return false;
		
		$parent_sid = 0;
		$inserted =  false;
		foreach ($imported_row as $item_caption) {
			if (empty($item_caption))
			    break;
			
			$item_sid = SJB_DB::queryValue("SELECT sid FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n AND caption = ?s",
										$field_sid, $parent_sid, $item_caption);
			if (!empty($item_sid)) {}
			elseif ($item_sid = SJB_UserProfileFieldTreeManager::addTreeItemToEndByParentSID($field_sid, $parent_sid, $item_caption)) {
				$inserted =  true;
			} else {
				break;
			}
			$parent_sid = $item_sid;
		}
		return $inserted;
	}

	public static function getRequestToRoot($property)
	{
		$tree_browser = new SJB_UserProfileTreeBrowser($property);
		$tree_browser->getRequestForAllNodes();
	}

	public static function moveItemToBeginBySID($item_sid)
	{
		$item_info = SJB_DB::query("SELECT * FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		
		if (empty($item_info)) {
			return false;
		} 
		$item_info = array_pop($item_info);
		$current_order = $item_info['order'];
		$min_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n",
								$item_info['field_sid'], $item_info['parent_sid']);
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = `order` + 1 WHERE `order` < ?n AND parent_sid = ?n", $current_order, $item_info['parent_sid']);
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE sid = ?n", $min_order, $item_sid);
		return true;
	}

	public static function moveItemToEndBySID($item_sid)
	{
		$item_info = SJB_DB::query("SELECT * FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		if (empty($item_info))
			return false;
		$item_info = array_pop($item_info);
		$current_order = $item_info['order'];
		$max_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM user_profile_field_tree WHERE field_sid = ?n AND parent_sid = ?n",
								$item_info['field_sid'], $item_info['parent_sid']);
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = `order` - 1 WHERE `order` > ?n AND parent_sid = ?n", $current_order, $item_info['parent_sid']);
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE sid = ?n", $max_order, $item_sid);
		return true;
	}

	public static function moveItemAfterBySID($item_sid, $after_tree_item_sid)
	{
		if ($item_sid == $after_tree_item_sid)
		    return false;
		
		$item_info = SJB_DB::query("SELECT * FROM user_profile_field_tree WHERE sid = ?n", $item_sid);
		if (empty($item_info))
			return false;
		$item_info = array_pop($item_info);
		$current_order = $item_info['order'];
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = `order` - 1 WHERE `order` > ?n AND parent_sid = ?n", $current_order, $item_info['parent_sid']);
		$after_order = SJB_DB::queryValue("SELECT `order` FROM user_profile_field_tree WHERE sid = ?n", $after_tree_item_sid);
		$new_order = $after_order + 1;
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = `order` + 1 WHERE `order` > ?n AND parent_sid = ?n", $after_order, $item_info['parent_sid']);
		SJB_DB::query("UPDATE user_profile_field_tree SET `order` = ?n WHERE sid = ?n", $new_order, $item_sid);
		return true;
	}

	public static function addMultupleTreeItem($item_sid, $node_sid, $tree_item_value, $order, $after_tree_item_sid = '')
	{
		$tree_item_value = str_replace("\r", " ", $tree_item_value);
		$tree_item_value = explode("\n", $tree_item_value);
		$result = true;
		foreach ($tree_item_value as $tree_item) {
			$tree_item = trim($tree_item);
			if ($tree_item != "") {
				if ($order == 'begin') {
					if (!SJB_UserProfileFieldManager::addTreeItemToBeginByParentSID($item_sid, $node_sid, $tree_item))
						$result = false;
				}
				elseif ($order == 'end') {
					if (!SJB_UserProfileFieldManager::addTreeItemToEndByParentSID($item_sid, $node_sid, $tree_item))
						$result = false;
				}
				elseif ($order == 'after') {
					if (!SJB_UserProfileFieldManager::addTreeItemAfterByParentSID($item_sid, $node_sid, $tree_item, $after_tree_item_sid))
						$result = false;
				}
			}
		}
		return $result;
	}

}
