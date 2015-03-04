<?php

class SJB_ListingTypeManager
{
	public static function getAllListingTypesInfo()
	{
		$cacheId = md5('SJB_ListingTypeManager::getAllListingTypesInfo');
		$cache = SJB_Cache::getInstance();
		if ($cache->test($cacheId)) {
			return $cache->load($cacheId);
		}
		$listingTypes = SJB_ListingTypeDBManager::getAllListingTypesInfo();
		$cache->save($listingTypes, $cacheId, array(SJB_Cache::TAG_LISTING_TYPES));
		return $listingTypes;
	}

	public static function saveListingType($listing_type)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTING_TYPES));
		return SJB_ListingTypeDBManager::saveListingType($listing_type);	
	}
	
	public static function getListingTypeInfoBySID($listing_type_sid)
	{
		return SJB_ListingTypeDBManager::getListingTypeInfoBySID($listing_type_sid);
	}

	public static function deleteListingTypeBySID($listing_type_sid)
	{
		SJB_ListingFieldManager::deleteListingFieldsByListingTypeSID($listing_type_sid);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTING_TYPES));
		return SJB_ListingTypeDBManager::deleteListingTypeBySID($listing_type_sid);
	}
	
	public static function getListingTypeSIDByID($listing_type_id)
	{
		return SJB_ListingTypeDBManager::getListingTypeSIDByID($listing_type_id);
	}
	
	public static function getListingTypeIDBySID($listing_type_sid)
	{
		return SJB_ListingTypeDBManager::getListingTypeIDBySID($listing_type_sid);
	}

	public static function createTemplateStructureForListingTypes()
	{
		$listing_types_info = SJB_ListingTypeManager::getAllListingTypesInfo();
		$structure = array();
		foreach ($listing_types_info as $listing_type_info) {
			$structure[$listing_type_info['id']] = array(
				'sid'				=> $listing_type_info['sid'],
				'id'				=> $listing_type_info['id'],
				'caption'			=> $listing_type_info['name'],
				'listing_number'	=> SJB_ListingManager::getListingsNumberByListingTypeSID($listing_type_info['sid']),
			);
		}

		return $structure;
	}
	
	public static function getWaitApproveSettingByListingType($listing_type_sids)
	{
		if (empty($listing_type_sids) )
			return false;
		$waitApproveSetting = false;
		if (is_array($listing_type_sids)) {
			foreach ($listing_type_sids as $listing_type_sid) {
				$typeInfo			= SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sid);
				if (!$waitApproveSetting || $waitApproveSetting == 0)
					$waitApproveSetting	= $typeInfo['waitApprove'];
			}
		}
		else {
			$typeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sids);
			$waitApproveSetting	= $typeInfo['waitApprove'];
		}

		return $waitApproveSetting;
	}
	
	public static function getListingTypeByUserSID($sid)
	{
		if (empty($sid))
			return false;
	    $types = array();
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as $listingType) {
		    if (SJB_Acl::getInstance()->isAllowed('post_' . $listingType['id'], $sid))
		        $types[] = $listingType['sid'];
		}
		return $types;
	}
	
	public static function getListingAllTypesForListType()
	{
		$cacheId = 'SJB_ListingTypeManager::getListingAllTypesForListType';
		if (SJB_MemoryCache::has($cacheId))
			return SJB_MemoryCache::get($cacheId);
		$listingTypes = self::getAllListingTypesInfo();
		$listTypes = array();
		foreach ($listingTypes as $listingType) {
			$listTypes[] = array(
				'id' => $listingType['sid'],
				'key' => $listingType['id'],
				'caption' => $listingType['name']
			);
		}
		SJB_MemoryCache::set($cacheId, $listTypes);
		return $listTypes;
	}

	/**
	 * @static
	 * @param $listingTypeSID
	 * @return int|mixed
	 */
	public static function getListingTypeEmailTemplate($listingTypeSID)
	{
		$result = SJB_DB::query('SELECT `email_alert` FROM `listing_types` WHERE `sid` = ?n', $listingTypeSID);
		if (!empty($result)) {
			$result = array_pop($result);
			return SJB_Array::get($result, 'email_alert');
		}
		return 0;
	}

	public static function getListingTypeEmailTemplateForGuestAlert($listingTypeSID)
	{
		return SJB_DB::queryValue('SELECT `guest_alert_email` FROM `listing_types` WHERE `sid` = ?n', $listingTypeSID);
	}

	public static function getListingTypeNameBySID($listingTypeSID)
	{
		return SJB_DB::queryValue('SELECT `name` FROM `listing_types` WHERE `sid` = ?n', $listingTypeSID);
	}

	/**
	 * @param $listingTypeInfo
	 * @return mixed
	 */
	public static function createTemplateStructure($listingTypeInfo)
	{
		if (in_array($listingTypeInfo['id'], array('Resume', 'Job'))) {
			$result['link']   = strtolower($listingTypeInfo['id']) . 's';
			$result['id']	  = $listingTypeInfo['id'];
			$result['name']   = $listingTypeInfo['name'];
		} else {
			$result['link']   = strtolower($listingTypeInfo['id']) . '-listings';
			$result['id']	  = $listingTypeInfo['id'];
			$result['name']	  = "'" . $listingTypeInfo['name'] . "'" . ' Listing';
		}
		return $result;
	}
}

