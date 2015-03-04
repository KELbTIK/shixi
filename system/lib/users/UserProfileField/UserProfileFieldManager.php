<?php

class SJB_UserProfileFieldManager extends SJB_ObjectManager
{
	public static function getFieldsInfoByUserGroupSID($user_group_sid)
	{
        if (!isset($GLOBALS["UserProfileFieldManagerCache"][$user_group_sid]))
			$GLOBALS["UserProfileFieldManagerCache"][$user_group_sid] = SJB_UserProfileFieldDBManager::getFieldsInfoByUserGroupSID($user_group_sid);
		return $GLOBALS["UserProfileFieldManagerCache"][$user_group_sid];
	}
	
	public static function getFieldInfoBySID($user_profile_field_sid)
	{
		return SJB_UserProfileFieldDBManager::getUserProfileFieldInfoBySID($user_profile_field_sid);
	}

	public static function saveUserProfileField($user_profile_field)
	{
		$result = SJB_UserProfileFieldDBManager::saveUserProfileField($user_profile_field);
		return $result;		
	}

	public static function deleteUserProfileFieldBySID($user_profile_field_sid)
	{
		return SJB_UserProfileFieldDBManager::deleteUserProfileFieldInfo($user_profile_field_sid);
	}

	public static function getUserProfileFieldIDBySID($user_profile_field_sid)
    {
		$user_profile_field_info = SJB_UserProfileFieldManager::getFieldInfoBySID($user_profile_field_sid);
		if (empty($user_profile_field_info))
			return null;
		return $user_profile_field_info['id'];
	}

    public static function getFieldBySID($user_profile_field_sid)
    {
		$user_profile_field_info = SJB_UserProfileFieldDBManager::getUserProfileFieldInfoBySID($user_profile_field_sid);
		if (empty($user_profile_field_info))
			return null;
		$user_profile_field = new SJB_UserProfileField($user_profile_field_info);
		$user_profile_field->setUserGroupSID($user_profile_field_info['user_group_sid']);
		return $user_profile_field;
	}

	public static function moveUpFieldBySID($field_sid)
	{
		SJB_UserProfileFieldDBManager::moveUpFieldBySID($field_sid);
	}

	public static function moveDownFieldBySID($field_sid)
	{
		SJB_UserProfileFieldDBManager::moveDownFieldBySID($field_sid);	
	}

	public static function changeUserPropertyIDs($user_group_sid, $user_profile_field_old_id, $user_profile_field_new_id)
	{
		return SJB_DB::query("UPDATE users_properties SET id = ?s WHERE id = ?s AND object_sid IN (SELECT sid FROM users WHERE user_group_sid = ?n)",
						$user_profile_field_new_id, $user_profile_field_old_id, $user_group_sid);
	}

	public static function getTreeValuesByParentSID($field_sid, $parent_sid)
	{
		return SJB_UserProfileFieldTreeManager::getTreeValuesByParentSID($field_sid, $parent_sid);
	}

	public static function addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		return SJB_UserProfileFieldTreeManager::addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value);
	}

	public static function addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		return SJB_UserProfileFieldTreeManager::addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value);
	}

	public static function addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid)
	{
		return SJB_UserProfileFieldTreeManager::addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid);
	}

	public static function deleteTreeItemBySID($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::deleteTreeItemBySID($item_sid);
	}

	public static function moveUpTreeItem($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::moveUpTreeItem($item_sid);
	}

	public static function moveDownTreeItem($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::moveDownTreeItem($item_sid);
	}

	public static function getTreeItemInfoBySID($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::getTreeItemInfoBySID($item_sid);
	}

	public static function updateTreeItemBySID($item_sid, $tree_item_value)
	{
		return SJB_UserProfileFieldTreeManager::updateTreeItemBySID($item_sid, $tree_item_value);
	}

	public static function getTreeNodePath($node_sid)
	{
		return SJB_UserProfileFieldTreeManager::getTreeNodePath($node_sid);
	}

	public static function getTreeParentSID($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::getParentSID($item_sid);
	}

	public static function moveTreeItemToBeginBySID($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::moveItemToBeginBySID($item_sid);
	}

	public static function moveTreeItemToEndBySID($item_sid)
	{
		return SJB_UserProfileFieldTreeManager::moveItemToEndBySID($item_sid);
	}

	public static function moveTreeItemAfterBySID($item_sid, $after_tree_item_sid)
	{
		return SJB_UserProfileFieldTreeManager::moveItemAfterBySID($item_sid, $after_tree_item_sid);
	}

	public static function getAllFieldsInfo()
	{	
		return SJB_UserProfileFieldDBManager::getAllFieldsInfo();
	}
	
	public static function getFieldsInfoByType($type)
	{
		return SJB_DB::query("SELECT * FROM `user_profile_fields` WHERE `type`=?s", $type);
	}
	
	public static function addLevelField($level)
	{
		if (!SJB_DB::query("SHOW COLUMNS FROM `user_profile_fields` WHERE `Field` = ?s", 'level_'.$level)) {	
			$fieldLevel = 'level_'.$level;
			if ($level > 1) {
				$prevLevel = 'level_'.($level-1);
				SJB_DB::query("ALTER TABLE `user_profile_fields` ADD `{$fieldLevel}` VARCHAR( 255 ) NULL AFTER `{$prevLevel}`") ;
			}
			else {
				SJB_DB::query("ALTER TABLE `user_profile_fields` ADD `{$fieldLevel}` VARCHAR( 255 ) NULL") ;
			}
		}
	}

	public static function getListItemValueBySID($sid)
	{
		$result = SJB_DB::query('SELECT `value` FROM `user_profile_field_list` WHERE `sid` = ?n', $sid);
		if (!empty($result)) {
			$result = array_pop($result);
			$result = SJB_Array::get($result, 'value');
		}
		return $result;
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
	
	public static function getUserProfileFieldsInfoByParentSID($parentSID, $hideHidden = false) 
	{
		$where = '';
		if ($hideHidden)
			$where = " AND `hidden` = 0 ";
		$sids = SJB_DB::query("SELECT `sid` FROM `user_profile_fields` WHERE `parent_sid` = ?n {$where} ORDER BY `order`", $parentSID);
		$parentID = SJB_DB::queryValue("SELECT `id` FROM `user_profile_fields` WHERE `sid` = ?n", $parentSID);
		$fireldsInfo = array();
		foreach ($sids as $sid) {
			$fireldsInfo[$sid['sid']] = self::getFieldInfoBySID($sid['sid']);
			$fireldsInfo[$sid['sid']]['parentID'] = $parentID;
			$fireldsInfo[$sid['sid']]['is_system'] = true;
		}
		
		return $fireldsInfo;	
	}
	
	public static function getDefaultCountryByParentSID($parentSID)
	{
		$result = SJB_DB::queryValue("SELECT `default_value` FROM `user_profile_fields` WHERE `id` = 'Country' AND `parent_sid` = ?n", $parentSID);
		if ($result == 'default_country') {
			return SJB_Settings::getSettingByName('default_country');
		}
		return $result;
	}
	
	public static function getUserProfileFieldInfoByID($user_field_id)
	{
		$cache = SJB_Cache::getInstance();
		$cacheId = md5('SJB_UserProfileFieldManager::getUserProfileFieldInfoByID' . $user_field_id);
		if ($cache->test($cacheId))
			return $cache->load($cacheId);

		$result = null;
		$sid = self::getUserProfileFieldsValue($user_field_id, 'id');
		if (!empty($sid)) {
			$user_field_sid = $sid[0]['sid'];
			$result = SJB_ObjectDBManager::getObjectInfo('user_profile_fields', $user_field_sid);
		}
		$cache->save($result, $cacheId, array(SJB_Cache::TAG_FIELDS));
		return $result;
	}
	
	public static function getUserProfileFieldsValue($value,$key='sid')
	{
		if (!isset($GLOBALS['user_profile_fields']))
			self::getUserProfileFields();
		$result = array();
		foreach ($GLOBALS['user_profile_fields'] as $row) {
			if ($row[$key] == $value )
				$result[] =  $row;
		}
		if (count($result) == 0)
			return array();	

		return $result;
	}
	
	public static function getUserProfileFields()
	{
		$GLOBALS['user_profile_fields'] = SJB_DB::query('SELECT * FROM user_profile_fields WHERE `parent_sid` IS NULL ORDER BY `order`');
	}
}

