<?php

class SJB_PostingPagesManager extends SJB_ObjectManager
{
	public static function getPagesByListingTypeSID($listing_type_sid) 
	{
		$pages = SJB_DB::query('SELECT *  FROM `posting_pages` WHERE `listing_type_sid`=?n ORDER BY `order` ', $listing_type_sid);
		foreach ($pages as $key => $page)
			$pages[$key]['fields_num'] = SJB_DB::queryValue('SELECT count(`page_sid`) as `fields_num` FROM `relations_listing_fields_posting_pages` WHERE `page_sid`=?n', $page['sid']);
		return $pages;
	}
	
	public static function getNumAllPages()
	{
		return SJB_DB::query('SELECT count(*) as `num` FROM `posting_pages` GROUP BY `listing_type_sid`');
	}
	
	public static function getFirstPageEachListingType()
	{
		$pages = SJB_DB::query('SELECT `listing_type_sid`, MIN(`order`) as `ord` FROM `posting_pages` GROUP BY `listing_type_sid`');
		$pageResult = array();
		foreach ($pages as $page) {
			$result = SJB_DB::query('SELECT * FROM `posting_pages` WHERE `listing_type_sid` = ?n AND `order` = ?n', $page['listing_type_sid'], $page['ord']);
			$pageResult[] = array_pop($result);
		}
		return $pageResult;
	}
	
	public static function savePage($info) 
	{
		SJB_PostingPagesDBManager::savePage($info);
	}
	
	public static function getPageInfoBySID($pageSID) 
	{
		$page = SJB_DB::query('SELECT * FROM `posting_pages` WHERE `sid` = ?n', $pageSID);
		return $page ? array_pop($page) : array();
	}
	
	public static function moveUpPageBySID($pageSID)
	{
		$pageInfo = SJB_DB::query('SELECT * FROM `posting_pages` WHERE `sid` = ?n', $pageSID);
		if (empty($pageInfo))
		    return false;
		$pageInfo = array_pop($pageInfo);
		$current_order = $pageInfo['order'];
		$up_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM `posting_pages` WHERE `listing_type_sid` = ?n AND `order` < ?n',
								$pageInfo['listing_type_sid'], $current_order);
		if ($up_order == 0)
		    return false;
		SJB_DB::query('UPDATE posting_pages SET `order` = ?n WHERE `order` = ?n AND listing_type_sid = ?n',
					$current_order, $up_order, $pageInfo['listing_type_sid']);
		SJB_DB::query('UPDATE posting_pages SET `order` = ?n WHERE sid = ?n', $up_order, $pageSID);
		return true;
	}
	
	public static function moveDownPageBySID($pageSID)
	{
		$pageInfo = SJB_DB::query('SELECT * FROM `posting_pages` WHERE `sid` = ?n', $pageSID);
		if (empty($pageInfo))
		    return false;
		$pageInfo = array_pop($pageInfo);
		$current_order = $pageInfo['order'];
		$less_order = SJB_DB::queryValue('SELECT MIN(`order`) FROM `posting_pages` WHERE `listing_type_sid` = ?n AND `order` > ?n',
								$pageInfo['listing_type_sid'], $current_order);
		if ($less_order == 0)
		    return false;
		SJB_DB::query('UPDATE `posting_pages` SET `order` = ?n WHERE `order` = ?n AND `listing_type_sid` = ?n',
					$current_order, $less_order, $pageInfo['listing_type_sid']);
		SJB_DB::query('UPDATE `posting_pages` SET `order` = ?n WHERE `sid` = ?n', $less_order, $pageSID);
		return true;
	}
	
	public static function moveUpFieldBySID($field_sid, $pageSID)
	{
		$pageInfo = SJB_DB::query('SELECT * FROM `relations_listing_fields_posting_pages` WHERE `field_sid` = ?n and `page_sid` = ?n', $field_sid, $pageSID);
		if (empty($pageInfo))
		    return false;
		$pageInfo = array_pop($pageInfo);
		$current_order = $pageInfo['order'];
		$up_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM `relations_listing_fields_posting_pages` WHERE `page_sid` = ?n AND `order` < ?n',
								$pageInfo['page_sid'], $current_order);				

		if ($up_order == 0)
		    return false;
		SJB_DB::query('UPDATE `relations_listing_fields_posting_pages` SET `order` = ?n WHERE `order` = ?n AND `page_sid` = ?n ',
					$current_order, $up_order, $pageInfo['page_sid']);
		SJB_DB::query('UPDATE `relations_listing_fields_posting_pages` SET `order` = ?n WHERE `field_sid` = ?n AND `page_sid` = ?n ', $up_order, $field_sid, $pageInfo['page_sid']);
		return true;
	}
	
	public static function moveDownFieldBySID($field_sid, $pageSID)
	{
		$pageInfo = SJB_DB::query('SELECT * FROM `relations_listing_fields_posting_pages` WHERE  `field_sid` = ?n and `page_sid`=?n', $field_sid, $pageSID);
		if (empty($pageInfo))
		    return false;

		$pageInfo = array_pop($pageInfo);
		$current_order = $pageInfo['order'];
		$less_order = SJB_DB::queryValue('SELECT MIN(`order`) FROM `relations_listing_fields_posting_pages` WHERE `page_sid` = ?n AND `order` > ?n',
								$pageInfo['page_sid'], $current_order);
		if ($less_order == 0)
		    return false;

		SJB_DB::query('UPDATE `relations_listing_fields_posting_pages` SET `order` = ?n WHERE `order` = ?n AND `page_sid` = ?n',
					$current_order, $less_order, $pageInfo['page_sid']);
		SJB_DB::query('UPDATE `relations_listing_fields_posting_pages` SET `order` = ?n WHERE `field_sid` = ?n AND page_sid = ?n', $less_order, $field_sid, $pageInfo['page_sid']);
		return true;
	}
	
	public static function getNumPagesByListingTypeSID($listing_type_sid)
	{
		return SJB_DB::queryValue('SELECT count(*) FROM `posting_pages` WHERE `listing_type_sid`=?n', $listing_type_sid);
	}
	
	public static function deletePageBySID($padeSID) 
	{
		SJB_DB::query('DELETE FROM `relations_listing_fields_posting_pages` WHERE `page_sid`=?n', $padeSID);
		SJB_DB::query('DELETE FROM `posting_pages` WHERE `sid`=?n', $padeSID);
	}
	
	public static function getListingFieldsInfo($listing_type_sid) 
	{
		$listingFields = SJB_DB::query('SELECT * FROM `listing_fields` 
										WHERE (`listing_type_sid` = ?n OR `listing_type_sid` = 0) AND (`parent_sid` IS NULL OR `parent_sid` = 0) ORDER BY `caption`', $listing_type_sid);
		$listing_fields_info = array();
		$i = 0;
		foreach ($listingFields as $listingField) {
			$listing_fields_info[$i] = SJB_ListingFieldDBManager::getListingFieldInfoBySID($listingField['sid']);
			$used = SJB_DB::queryValue('SELECT count(`sid`) FROM `relations_listing_fields_posting_pages` WHERE `field_sid`=?n AND `listing_type_sid`=?n', $listingField['sid'], $listing_type_sid);
			$listing_fields_info[$i]['used'] = $used > 0 ? 1 : 0;
			$i++;
		}
		return $listing_fields_info;
	}
	
	public static function getAllFieldsByPageSID($pageSID)
	{
		$fields = SJB_DB::query('SELECT * FROM `relations_listing_fields_posting_pages` WHERE `page_sid`=?n ORDER BY `order`',$pageSID);
		$listingFieldsInfo = array();
		foreach ($fields as $field) {
			$listingFieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoBySID($field['field_sid']);
			$listingFieldInfo['relationId'] = $field['sid'];
			$listingFieldsInfo[] = $listingFieldInfo;
		}
		return $listingFieldsInfo;
	}
	
	public static function getAllFieldsByPageSIDForForm($pageSID)
	{
		$fields = SJB_DB::query('SELECT * FROM `relations_listing_fields_posting_pages` WHERE `page_sid` = ?n ORDER BY `order`', $pageSID);
		$form_fields = array();
		foreach ($fields as $field) {
			$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($field['field_sid']);
			if (empty($fieldInfo))
				continue;
			$form_field['caption'] 		    = isset($fieldInfo['caption'])?$fieldInfo['caption']:'';
			$form_field['is_system'] 		= isset($fieldInfo['is_system'])?$fieldInfo['is_system']:'';
			$form_field['id']           	= isset($fieldInfo['id'])?$fieldInfo['id']:'';
			$form_field['is_required'] 		= isset($fieldInfo['is_required'])?$fieldInfo['is_required']:'';
			$form_field['disabled'] 		= false;
			$form_field['order'] 			= isset($fieldInfo['order'])?$fieldInfo['order']:'';
			$form_field['comment'] 			= isset($fieldInfo['comment'])?$fieldInfo['comment']:'';
			$form_field['type'] 			= isset($fieldInfo['type'])?$fieldInfo['type']:'';
			$form_field['instructions']     = isset($fieldInfo['instructions'])?$fieldInfo['instructions']:'';
			$form_field['use_autocomplete'] = isset($fieldInfo['use_autocomplete'])?$fieldInfo['use_autocomplete']:'';
			$form_field['is_classifieds'] 	= isset($fieldInfo['is_classifieds'])?$fieldInfo['is_classifieds']:'';
			$form_fields[$fieldInfo['id']] = $form_field;
		}

		return $form_fields;
	}
	
	public static function addListingFieldOnPage($listing_field, $pageSID, $listing_type_sid)
	{
		SJB_DB::query('DELETE FROM `relations_listing_fields_posting_pages` WHERE `field_sid`=?n AND `listing_type_sid`=?n', $listing_field, $listing_type_sid);
		$max_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM `relations_listing_fields_posting_pages` WHERE `page_sid` = ?n  ', $pageSID);
		$max_order = empty($max_order) ? 0 : $max_order;
		return SJB_DB::query('INSERT INTO `relations_listing_fields_posting_pages` (`field_sid`, `page_sid`, `listing_type_sid`, `order`) VALUES (?n, ?n, ?n, ?n)', $listing_field, $pageSID, $listing_type_sid, ++$max_order);
	}
	
	public static function moveFieldToPade($field_sid, $movePageID, $listing_type_sid)
	{
		$max_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM `relations_listing_fields_posting_pages` WHERE `page_sid` = ?n  ', $movePageID);
		$max_order = empty($max_order) ? 0 : $max_order;
		SJB_DB::query('UPDATE `relations_listing_fields_posting_pages` SET `page_sid` = ?n, `order` = ?n  WHERE `field_sid`=?n AND `listing_type_sid`=?n', $movePageID, $max_order+1, $field_sid, $listing_type_sid);
	}
	
	public static function removeFieldFromPage($field_sid, $listing_type_sid)
	{
		if (!$listing_type_sid)
			SJB_DB::query('DELETE FROM `relations_listing_fields_posting_pages`  WHERE `field_sid`=?n', $field_sid);
		else
			SJB_DB::query('DELETE FROM `relations_listing_fields_posting_pages`  WHERE `field_sid`=?n AND `listing_type_sid`=?n', $field_sid, $listing_type_sid);
	}

	public static function removeFieldFromPageById($relationId)
	{
		SJB_DB::query('DELETE FROM `relations_listing_fields_posting_pages`  WHERE `sid` = ?n', $relationId);
	}
	
	public static function getPostingPageSIDByID($pageID, $listing_type_sid)
	{
		$sid = SJB_DB::queryValue('SELECT `sid` FROM `posting_pages` WHERE `page_id`=?s AND `listing_type_sid`=?n', $pageID, $listing_type_sid);
		if (!$sid)
			return 0;
		return $sid;
	}
	
	public static function isLastPageByID($pageSID, $listing_type_sid)
	{
		if (!$pageSID)
			return true;
		$result = SJB_DB::query('SELECT `sid` FROM `posting_pages`
										WHERE `listing_type_sid`=?n AND  `order`=(SELECT MAX( `order` )
										FROM `posting_pages`
										WHERE `listing_type_sid` =?n )', $listing_type_sid, $listing_type_sid);
		$sid = array_pop($result);
		return $sid['sid'] == $pageSID;
	}
	
	public static function getNextPage($pageSID)
	{
		$pageInfo = SJB_DB::query('SELECT * FROM posting_pages WHERE  sid = ?n', $pageSID);
		if (empty($pageInfo))
		    return false;
		$pageInfo = array_pop($pageInfo);
		$current_order = $pageInfo['order'];
		$less_order = SJB_DB::queryValue('SELECT MIN(`order`) FROM posting_pages WHERE listing_type_sid = ?n AND `order` > ?n',
								$pageInfo['listing_type_sid'], $current_order);
		if ($less_order == 0)
		    return false;
		$page_id = SJB_DB::queryValue('SELECT `page_id` FROM  posting_pages WHERE `order` = ?n AND listing_type_sid = ?n', $less_order, $pageInfo['listing_type_sid']);
		if (!$page_id)
			return false;
		return $page_id;
	}
	
	public static function getPrevPage($pageSID)
	{
		$pageInfo = SJB_DB::query('SELECT * FROM posting_pages WHERE  sid = ?n', $pageSID);
		if (empty($pageInfo))
		    return false;
		$pageInfo = array_pop($pageInfo);
		$current_order = $pageInfo['order'];
		$up_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM posting_pages WHERE listing_type_sid = ?n AND `order` < ?n',
								$pageInfo['listing_type_sid'], $current_order);
		if ($up_order == 0)
		    return false;
		$page_id = SJB_DB::queryValue('SELECT `page_id` FROM  posting_pages WHERE `order` = ?n AND listing_type_sid = ?n', $up_order, $pageInfo['listing_type_sid']);
		if (empty($page_id))
			return false;
		return $page_id;
	}
	
	public static function saveNewJobFieldsOrder($item_sids, $pageSID)
	{
		$count = 1;
		foreach ($item_sids as $item_sid=>$val) {
			SJB_DB::query('UPDATE `relations_listing_fields_posting_pages` SET `order` = ?n WHERE `field_sid` = ?n and `page_sid`=?n', $count, $item_sid, $pageSID);
			$count++;
		}
		return true;
	}
}


class SJB_PostingPagesDBManager extends SJB_ObjectDBManager 
{
	public static function savePage($info) 
	{
		parent::saveObject('posting_pages', $info);
		if ($info->getOrder())
		    return true;
		$max_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM posting_pages WHERE listing_type_sid = ?n', $info->getListingTypeSID());
		$max_order = empty($max_order) ? 0 : $max_order;
		return SJB_DB::query('UPDATE posting_pages SET listing_type_sid = ?n, `order` = ?n WHERE sid = ?n',
							$info->getListingTypeSID(), ++$max_order, $info->getSID());
	}
}