<?php

class SJB_ListingComplexFieldManager
{
	public static function getCommonListingFieldsInfo()
	{
		return SJB_ListingComplexFieldManager::getListingFieldsInfoByListingType(0);
	}

	public static function saveListingField($listing_field)
	{
		$result = SJB_ListingFieldDBManager::saveListingComplexField($listing_field);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return $result;
	}
	
	public static function getFieldInfoBySID($listing_field_sid)
	{
		return SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid, 'listing_complex_fields');
	}

	public static function deleteListingFieldBySID($listing_field_sid)
	{
        $field_info = SJB_ListingComplexFieldManager::getFieldBySID($listing_field_sid);        
		return SJB_ListingFieldDBManager::deleteComplexListingFieldBySID($field_info) &&
                SJB_ListingFieldDBManager::deleteComplexFieldProperties($listing_field_sid);
	}

	public static function getListingFieldsInfoByListingType($listing_type_sid)
	{
		if (!isset($GLOBALS["ListingFieldManagerCache"][$listing_type_sid]))
			$GLOBALS["ListingFieldManagerCache"][$listing_type_sid] = SJB_ListingFieldDBManager::getListingFieldsInfoByListingType($listing_type_sid);
		return $GLOBALS["ListingFieldManagerCache"][$listing_type_sid];
	}

	public static function deleteListingFieldsByListingTypeSID($listing_type_sid)
	{
		return SJB_ListingFieldDBManager::deleteListingFieldsByListingTypeSID($listing_type_sid);
	}
	
	public static function getFieldBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listing_field_sid, 'listing_complex_fields');
		if (empty($listing_field_info)) {
			return null;
		}
		else {
			$listing_field = new SJB_ListingField($listing_field_info);
			$listing_field->setSID($listing_field_sid);
			return $listing_field;
		}
	}

	public static function getListingFieldIDBySID($listing_field_sid)
	{
		$listing_field_info = SJB_ListingComplexFieldManager::getFieldInfoBySID($listing_field_sid);
		if (empty($listing_field_info))
			return null;
		return $listing_field_info['id'];
	}

	public static function getTreeValuesByParentSID($field_sid, $parent_sid)
	{
		return SJB_ListingFieldTreeManager::getTreeValuesByParentSID($field_sid, $parent_sid);
	}

	public static function addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		return SJB_ListingFieldTreeManager::addTreeItemToBeginByParentSID($field_sid, $parent_sid, $tree_item_value);
	}

	public static function addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value)
	{
		return SJB_ListingFieldTreeManager::addTreeItemToEndByParentSID($field_sid, $parent_sid, $tree_item_value);
	}

	public static function addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid)
	{
		return SJB_ListingFieldTreeManager::addTreeItemAfterByParentSID($field_sid, $parent_sid, $tree_item_value, $after_tree_item_sid);
	}

	public static function deleteTreeItemBySID($item_sid)
	{
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
		return SJB_ListingFieldTreeManager::updateTreeItemBySID($item_sid, $tree_item_value);
	}

	public static function getTreeNodePath($node_sid)
	{
		return SJB_ListingFieldTreeManager::getTreeNodePath($node_sid);
	}

	public static function changeListingPropertyIDs($new_listing_field_id, $old_listing_field_id)
	{
		return SJB_DB::query("UPDATE `listings_properties` SET `id` = ?s WHERE `id` = ?s", $new_listing_field_id, $old_listing_field_id);
	}

	public static function moveUpFieldBySID($field_sid)
	{
		$field_info = SJB_DB::query("SELECT * FROM listing_complex_fields WHERE  sid = ?n", $field_sid);
		if (empty($field_info))
		    return false;
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		$up_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM listing_complex_fields WHERE field_sid = ?n AND `order` < ?n",
								$field_info['field_sid'], $current_order);
		if ($up_order == 0)
		    return false;

		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE `order` = ?n AND field_sid = ?n", 
					$current_order, $up_order, $field_info['field_sid']);
		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE sid = ?n", $up_order, $field_sid);
		return $field_info['field_sid'];
	}

	public static function moveDownFieldBySID($field_sid)
	{
		$field_info = SJB_DB::query("SELECT * FROM listing_complex_fields WHERE sid = ?n", $field_sid);
		if (empty($field_info))
		    return false;
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		$less_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM listing_complex_fields WHERE field_sid = ?n AND `order` > ?n",
								$field_info['field_sid'], $current_order);
		if ($less_order == 0)
		    return false;
		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE `order` = ?n AND field_sid = ?n",
					$current_order, $less_order, $field_info['field_sid']);
		SJB_DB::query("UPDATE listing_complex_fields SET `order` = ?n WHERE sid = ?n", $less_order, $field_sid);
		return $field_info['field_sid'];
	}

	public static function getFieldsInfoByType($type)
	{
		$type_fields = SJB_DB::query("SELECT * FROM `listing_complex_fields` WHERE `type`=?s", $type);
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
	
	public static function getListingFieldsInfoByParentSID($field_sid)
	{
		return SJB_ListingFieldDBManager::getListingFieldsInfoByParentSID($field_sid);
	}
}