<?php

class SJB_ListingFieldManager
{
	public static function getCommonListingFieldsInfo($pageID = 0)
	{
		return SJB_ListingFieldManager::getListingFieldsInfoByListingType(0, $pageID);
	}

	public static function saveListingField($listing_field, $pages = array())
	{
		$result = SJB_ListingFieldDBManager::saveListingField($listing_field, $pages);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return $result;
	}
	
	public static function getFieldInfoBySID($listing_field_sid)
	{
		$cache = SJB_Cache::getInstance();
		$id = md5('SJB_ListingFieldDBManager::getListingFieldInfoBySID' . $listing_field_sid);
		if ($cache->test($id))
			return $cache->load($id);
		$listingFieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid);
		$listingFieldInfo = self::setAddParameterToDefaultValueIfExists($listingFieldInfo);
		$cache->save($listingFieldInfo, $id, array(SJB_Cache::TAG_FIELDS));
		return $listingFieldInfo;
	}

	protected static function setAddParameterToDefaultValueIfExists($listingFieldInfo)
	{
		if (!empty($listingFieldInfo['add_parameter']))
			$listingFieldInfo['default_value'] = array(
				'value' => $listingFieldInfo['default_value'],
				'add_parameter' => $listingFieldInfo['add_parameter']);
		return $listingFieldInfo;
	}

	public static function deleteListingFieldBySID($listing_field_sid)
	{
        $field_info = SJB_ListingFieldManager::getFieldBySID($listing_field_sid);
		SJB_FormBuilderManager::deleteListingFieldBySidFromFieldsHolder($listing_field_sid);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldDBManager::deleteListingFieldBySID($listing_field_sid) &&
                SJB_ListingFieldDBManager::deleteFieldProperties($field_info->getPropertyValue('id'), $field_info->getPropertyValue('listing_type_sid')) && SJB_PostingPagesManager::removeFieldFromPage($field_info->sid, $field_info->listing_type_sid);
	}

	public static function getListingFieldsInfoByListingType($listing_type_sid, $pageID = 0)
	{
		if (isset($GLOBALS["ListingFieldManagerCache"][$listing_type_sid][$pageID]))
			return $GLOBALS["ListingFieldManagerCache"][$listing_type_sid][$pageID];
			
		$fields_info = SJB_ListingFieldDBManager::getListingFieldsInfoByListingType($listing_type_sid, $pageID);
		$GLOBALS["ListingFieldManagerCache"][$listing_type_sid][$pageID] = $fields_info;
		return $fields_info;
	}

	public static function deleteListingFieldsByListingTypeSID($listing_type_sid)
	{
		$result = SJB_ListingFieldDBManager::deleteListingFieldsByListingTypeSID($listing_type_sid);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return $result;
	}
	
	public static function getFieldBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid);
		
		if (empty($listing_field_info)) {
			return null;
		}
		else {
			$listing_field = new SJB_ListingField($listing_field_info);
			$listing_field->setListingTypeSID($listing_field_info['listing_type_sid']);
			$listing_field->setSID($listing_field_sid);
			return $listing_field;
		}
	}

	public static function getListingFieldIDBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingFieldManager::getFieldInfoBySID($listing_field_sid);
		if (empty($listing_field_info))
			return null;
		return $listing_field_info['id'];
	}

	public static function getListingFieldSIDByID($listing_field_id)
	{
		$listing_field_info = SJB_ListingFieldDBManager::getListingFieldInfoByID($listing_field_id);

		if (empty($listing_field_info))
			return null;
		return $listing_field_info['sid'];
	}

	public static function getTreeValuesByParentSID($field_sid, $parent_sid)
	{
		return SJB_ListingFieldTreeManager::getTreeValuesByParentSID($field_sid, $parent_sid);
	}

	public static function addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldTreeManager::addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value);
	}

	public static function addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldTreeManager::addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value);
	}

	public static function addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldTreeManager::addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid);
	}

	public static function deleteTreeItemBySID($item_sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldTreeManager::deleteTreeItemBySID($item_sid);
	}

	public static function moveUpTreeItem($item_sid)
	{
		return SJB_ListingFieldTreeManager::moveUpTreeItem($item_sid);
	}

	public static function moveDownTreeItem($item_sid)
	{
		return SJB_ListingFieldTreeManager::moveDownTreeItem($item_sid);
	}

	public static function sortTreeItems($field_sid, $node_sid = 0, $sorting_order = 'ASC')
	{
		return SJB_ListingFieldTreeManager::sortTreeItems($field_sid, $node_sid, $sorting_order);
	}

	public static function saveNewTreeItemsOrder($items_order)
	{
		return SJB_ListingFieldTreeManager::saveNewTreeItemsOrder($items_order);
	}

	public static function getTreeItemInfoBySID($item_sid)
	{
		return SJB_ListingFieldTreeManager::getTreeItemInfoBySID($item_sid);
	}

	public static function updateTreeItemBySID($item_sid, $tree_item_value)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_ListingFieldTreeManager::updateTreeItemBySID($item_sid, $tree_item_value);
	}

	public static function getTreeNodePath($node_sid)
	{
		return SJB_ListingFieldTreeManager::getTreeNodePath($node_sid);
	}

	public static function changeListingPropertyIDs($new_listing_field_id, $old_listing_field_id)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("UPDATE `listings_properties` SET `id` = ?s WHERE `id` = ?s", $new_listing_field_id, $old_listing_field_id);
	}

	public static function moveUpFieldBySID($field_sid)
	{
		return SJB_ListingFieldDBManager::moveUpFieldBySID($field_sid);
	}

	public static function moveDownFieldBySID($field_sid)
	{
		return SJB_ListingFieldDBManager::moveDownFieldBySID($field_sid);
	}

	public static function getFieldsInfoByType($type)
	{
		$type_fields = SJB_DB::query("SELECT * FROM `listing_fields` WHERE `type`=?s", $type);
		return $type_fields;
	}

	public static function getTreeParentSID($item_sid)
	{
		return SJB_ListingFieldTreeManager::getParentSID($item_sid);
	}

	public static function moveTreeItemToBeginBySID($item_sid)
	{
		return SJB_ListingFieldTreeManager::moveItemToBeginBySID($item_sid);
	}

	public static function moveTreeItemToEndBySID($item_sid)
	{
		return SJB_ListingFieldTreeManager::moveItemToEndBySID($item_sid);
	}

	public static function moveTreeItemAfterBySID($item_sid, $after_tree_item_sid)
	{
		return SJB_ListingFieldTreeManager::moveItemAfterBySID($item_sid, $after_tree_item_sid);
	}
	
	public static function addLevelField($level)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		if (!SJB_DB::query("SHOW COLUMNS FROM `listing_fields` WHERE `Field` = ?s", 'level_'.$level)) {	
			$fieldLevel = 'level_'.$level;
			if ($level > 1) {
				$prevLevel = 'level_'.($level-1);
				SJB_DB::query("ALTER TABLE `listing_fields` ADD `{$fieldLevel}` VARCHAR( 255 ) NULL AFTER `{$prevLevel}`") ;
			}
			else {
				SJB_DB::query("ALTER TABLE `listing_fields` ADD `{$fieldLevel}` VARCHAR( 255 ) NULL") ;
			}
		}
	}

	public static function getListItemSIDByValue($fieldValue, $fieldSID)
	{
		$result = SJB_DB::query('SELECT `sid` FROM `listing_field_list` WHERE `value` = ?s AND `field_sid` = ?n',
			$fieldValue, $fieldSID);
		if (!empty($result)) {
			$result = SJB_Array::get(array_pop($result), 'sid');
		}
		return $result;
	}
	
	public static function getListItemValueBySID($itemSID, $fieldSID)
	{
		$result = SJB_DB::queryValue('SELECT `value` FROM `listing_field_list` WHERE `sid` = ?s AND `field_sid` = ?n',
			$itemSID, $fieldSID);
//		if (!empty($result)) {
//			$result = SJB_Array::get(array_pop($result), 'value');
//		}
		return $result;
	}

	public static function getListingFieldsInfoByParentSID($parentSID, $hideHidden = false)
	{
		$where = '';
		if ($hideHidden)
			$where = " AND `hidden` = 0 ";
		$sids = SJB_DB::query("SELECT `sid` FROM `listing_fields` WHERE `parent_sid` = ?n {$where} ORDER BY `order`", $parentSID);
		$parentID = SJB_DB::queryValue("SELECT `id` FROM `listing_fields` WHERE `sid` = ?n", $parentSID);
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
		$result = SJB_DB::query("SELECT `default_value`, `profile_field_as_dv` FROM `listing_fields` WHERE `id` = 'Country' AND `parent_sid` = ?n", $parentSID);
		$result = $result ? array_pop($result) : array();
		if (!empty($result['default_value'])) {
			if ($result['default_value'] == 'default_country') {
				return SJB_Settings::getSettingByName('default_country');
			}
			return $result['default_value'];
		}
		elseif (!empty($result['profile_field_as_dv'])) {
			return $result['profile_field_as_dv'];
		}
			
		return false;
	}
}