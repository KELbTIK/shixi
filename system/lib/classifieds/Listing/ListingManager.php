<?php

class SJB_ListingManager extends SJB_ObjectManager
{
    
    private static $systemProperties = array();
	
	public static function saveListing(&$listing, $listingSidsForCopy = array())
	{
		return SJB_ListingDBManager::saveListing($listing, $listingSidsForCopy);
	}

	public static function getListingsNumberByListingTypeSID($listing_type_sid)
	{
		return SJB_ListingDBManager::getListingsNumberByListingTypeSID($listing_type_sid);
	}

	public static function getListingsNumberByUserSID($user_sid)
	{
		return SJB_ListingDBManager::getListingsNumberByUserSID($user_sid);
	}

	public static function getAllListingSIDs()
	{
		return SJB_ListingDBManager::getAllListingSIDs();
	}

	public static function getListingInfoBySID($listing_sid)
	{
		$listing_info = SJB_ListingDBManager::getListingInfoBySID($listing_sid);
		if (empty($listing_info))
			return null;
		$listing_info['id'] = $listing_info['sid'];
		return $listing_info;
	}

	/**
	 * Returns Listing object by id 
	 *
	 * @param int $listing_sid
	 * @return SJB_Listing
	 */
	public static function getObjectBySID($listing_sid)
	{
		$listing_info = SJB_ListingManager::getListingInfoBySID($listing_sid);
		$listing = null;
		if (!is_null($listing_info)) {
			$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
			$listing->setSID($listing_sid);
			$listing->setUserSID($listing_info['user_sid']);
			$productInfo = !empty($listing_info['product_info'])?unserialize($listing_info['product_info']):array();
			$listing->setProductInfo($productInfo);
		}
		return $listing;
	}

	public static function getActiveListingsByUserSID($user_sid, $count = false)
	{
		$active_listings_sid = SJB_ListingDBManager::getActiveListingsSIDByUserSID($user_sid);
		if ($count) {
			return count($active_listings_sid);
		}
		$active_listings = array();
		foreach ($active_listings_sid as $active_listing_sid)
			$active_listings[] = SJB_ListingManager::getObjectBySID($active_listing_sid);
		return $active_listings;
	}

	public static function getActiveListingNumberByUserSID($user_sid)
	{
		return SJB_ListingManager::getActiveListingsByUserSID($user_sid, true);
	}

	public static function getListingsByUserSID($user_sid)
	{
		$listings_sid = SJB_ListingDBManager::getListingsSIDByUserSID($user_sid);
		$listings = array();
		foreach ($listings_sid as $listing_sid)
			$listings[] = SJB_ListingManager::getObjectBySID($listing_sid);
		return $listings;
	}

	public static function getListingsInfoByUserSID($user_sid, $subuser = false)
	{
		$listings_sid = SJB_ListingDBManager::getListingsSIDByUserSID($user_sid, $subuser);
		$listings = array();
		foreach ($listings_sid as $listing_sid)
			$listings[] = SJB_ListingManager::getListingInfoBySID($listing_sid);
		return $listings;
	}

	/**
	 * @param array|int $listingSids
	 * @param bool      $updateBrowsePages
	 * @return bool
	 */
	public static function activateListingBySID($listingSids, $updateBrowsePages = true)
	{
		if (is_array($listingSids)) {
			$activatedListings = array();
			foreach ($listingSids as $listingSid) {
				if (self::_activateListingBySID($listingSid)) {
					$activatedListings[] = $listingSid;
				}
			}
			
			if ($updateBrowsePages && !empty($activatedListings)) {
				SJB_BrowseDBManager::addListings($activatedListings);
			}
			return true;
		}
		else if (self::_activateListingBySID($listingSids)) {
			if ($updateBrowsePages) {
				SJB_BrowseDBManager::addListings($listingSids);
			}
			return true;
		}
		
		return false;
	}

	private static function _activateListingBySID($listingSid)
	{
		if (SJB_ListingDBManager::activateListingBySID($listingSid)) {
			if (SJB_ListingManager::setListingExpirationDateBySid($listingSid)) {
				SJB_ListingManager::deleteListingIDFromSendedNotificationsTable($listingSid);
				SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
				SJB_Event::dispatch('listingActivated', $listingSid);
				return true;
			}
		}
		return false;
	}


	public static function setListingExpirationDateBySid($listing_sid)
	{
		return SJB_ListingDBManager::setListingExpirationDateBySid($listing_sid);
	}

	/**
	 * @param array|int $listingSids
	 */
	public static function deleteListingBySID($listingSids)
	{
		SJB_BrowseDBManager::deleteListings($listingSids);
		if (is_array($listingSids)) {
			foreach ($listingSids as $listingSid) {
				self::_deleteListingBySID($listingSid);
			}
		} else {
			self::_deleteListingBySID($listingSids);
		}
	}
	
	private static function _deleteListingBySID($listing_sid)
	{
		SJB_Event::dispatch('beforeListingDelete', $listing_sid);
		$gallery = SJB_ObjectMother::createListingGallery();
		$gallery->setListingSID($listing_sid);
		$gallery->deleteImages();
		SJB_UploadFileManager::deleteUploadedFilesByListingSID($listing_sid);
		SJB_CommentManager::deleteCommentsToListing($listing_sid);
		SJB_ListingManager::deleteListingIDFromSendedNotificationsTable($listing_sid);
		//delete listing keywords
		SJB_ListingDBManager::deleteListingKeywords($listing_sid);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
		SJB_ListingDBManager::deleteListingBySID($listing_sid);
	}

	/**
	 * @param array|int $listingSids
	 * @param bool      $deleteRecordFromActivePeriod
	 * @return bool
	 */
	public static function deactivateListingBySID($listingSids, $deleteRecordFromActivePeriod = false)
	{
		SJB_BrowseDBManager::deleteListings($listingSids);
		
		if (is_array($listingSids)) {
			foreach ($listingSids as $listingSid) {
				self::_deactivateListingBySID($listingSid, $deleteRecordFromActivePeriod);
			}
			return true;
		}
		
		return self::_deactivateListingBySID($listingSids, $deleteRecordFromActivePeriod);
	}

	private static function _deactivateListingBySID($listingSid, $deleteRecordFromActivePeriod = false)
	{
		$result = SJB_ListingDBManager::deactivateListingBySID($listingSid, $deleteRecordFromActivePeriod);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
		SJB_Event::dispatch('listingDeactivated', $listingSid);
		return $result;
	}
	
	public static function getPropertyByPropertyName($property_name, $listing_type_sid = 0)
	{
		$property_info = SJB_ListingFieldDBManager::getListingFieldInfoByID($property_name);
		if (empty($property_info)) {
			$listing_details = SJB_ListingDetails::getDetails($listing_type_sid);
			if (isset($listing_details[$property_name]))
				$property_info = $listing_details[$property_name];
			else
				return null;
		}

		return new SJB_ObjectProperty($property_info);
	}

	public static function propertyIsCommon($property_name)
	{
		$common_property = SJB_ListingManager::getPropertyByPropertyName($property_name);
		return !empty($common_property);
	}

	public static function propertyIsSystem($property_name)
	{
	    if (empty(self::$systemProperties)) {
	        self::$systemProperties = SJB_DB::query("SHOW COLUMNS FROM `listings`");
	    }
		foreach (self::$systemProperties as $property)
			if ($property['Field'] == $property_name)
				return true;
		return false;
	}

	public static function getAllListingPropertiesID($listing_type_id = null)
	{		
		$common_properties = SJB_ListingFieldManager::getCommonListingFieldsInfo();

		$extra_properties  = array();
		if (!empty($listing_type_id)) {
			$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);		
			if (!empty($listing_type_sid))
				$extra_properties  = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listing_type_sid);
		}
		
		$system_properties = array('id','listing_type','username','active','keywords','featured','views','pictures','activation_date','expiration_date');		
				
		return array(
			'system' => $system_properties,
			'common' => $common_properties,
			'extra'  => $extra_properties,
		); 
	}

	public static function getExpiredListingsSID()
	{
		return SJB_ListingDBManager::getExpiredListingsSID();
	}

	public static function getDeactivatedListingsSID()
	{
		return SJB_ListingDBManager::getDeactivatedListingsSID();
	}

        /**
         *
         * @param int $sid
         * @return int | boolean | null
         */
	public static function getIfListingHasExpiredBySID( $sid )
	{
		return SJB_ListingDBManager::getIfListingHasExpiredBySID( $sid );
	}

	public static function getListingsIDByDaysLeftToExpired($user_sid, $days = 0)
	{
		$listings = SJB_DB::query("SELECT sid FROM listings WHERE expiration_date < DATE_ADD( NOW(), INTERVAL ?w DAY ) AND expiration_date != '0000-00-00' AND `user_sid` = ?n AND active = 1", $days, $user_sid);
		$listings_id = array();
		foreach ($listings as $listing) {			
			$listings_id[] = $listing['sid'];			
		}
		return $listings_id;
	}

	public static function isListingNotificationSended($listingSID)
	{
		$result = SJB_DB::queryValue("SELECT * FROM `notifications_sended` WHERE `object_type` = 'listing' AND `object_sid` = ?n", $listingSID);
		return !empty($result);
	}
	
	
	public static function saveListingIDAsSendedNotificationsTable($listingSID)
	{
		$result = false;
		
		if (is_integer($listingSID)) {
			$result = SJB_DB::query("INSERT INTO `notifications_sended` SET `object_sid` = ?n, `object_type` = 'listing'", $listingSID);
		} elseif ( is_array($listingSID)) {
			$insertValues = array();
			foreach ($listingSID as $value) {
				if (!is_numeric($value))
					continue;
				$insertValues[] = "('listing', $value)";
			}
			
			$insert = implode(",", $insertValues);
			$result = SJB_DB::query("INSERT INTO `notifications_sended` (`object_type`, `object_sid`) VALUES $insert");
		}
		
		if ($result === false)
			return false;
		return true;
	}
	
	public static function deleteListingIDFromSendedNotificationsTable($listingSID)
	{
		return SJB_DB::query("DELETE FROM `notifications_sended` WHERE `object_type` = 'listing' AND `object_sid` = ?n", $listingSID);
	}
	
	public static function getUserSIDByListingSID($listing_sid)
	{
		return SJB_ListingDBManager::getUserSIDByListingSID($listing_sid);
	}

	/**
	 * @param SJB_Listing $listing
	 */
	public static function createTemplateStructureForListing($listing, $extraInfo = array())
	{
		$listing_info = parent::getObjectInfo($listing);

		if (SJB_MemoryCache::has('listingTypeInfo'. $listing->getListingTypeSID())) {
			$listing_type_info = SJB_MemoryCache::get('listingTypeInfo'. $listing->getListingTypeSID());
		}
		else {
			$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($listing->getListingTypeSID());
			SJB_MemoryCache::set('listingTypeInfo'. $listing->getListingTypeSID(), $listing_type_info);
		}
		foreach ($listing->getProperties() as $property) {
			if ($property->isComplex()) {
				$isPropertyEmpty = true;
				$properties = $property->type->complex->getProperties();
				$properties = is_array($properties)?$properties:array();
				foreach ($properties as $subProperty) {
					if (!empty($listing_info['user_defined'][$property->getID()][$subProperty->getID()]) && is_array($listing_info['user_defined'][$property->getID()][$subProperty->getID()])) {
						foreach ($listing_info['user_defined'][$property->getID()][$subProperty->getID()] as $subValue) {
							if (!empty($subValue))
								$isPropertyEmpty = false;
						}
					}
				}
				if ($isPropertyEmpty) {
					$listing_info['user_defined'][$property->getID()] = '';
				}
			}
			if ($property->getType() == 'list') {
				$value = $property->getValue();
				$properties =  $property->type->property_info;
				$listValues = isset($properties['list_values'])?$properties['list_values']:array();
				foreach ($listValues as $listValue) {
					if ($listValue['id'] == $value) 
						$listing_info['user_defined'][$property->getID()] = $listValue['caption'];
				}
			}
			elseif ($property->getType() == 'multilist') {
				$value = $property->getValue();
				if (!is_array($property->getValue()))
					$value = explode(',', $property->getValue());
				$properties =  $property->type->property_info;
				$listValues = isset($properties['list_values'])?$properties['list_values']:array();
				$listing_info['user_defined'][$property->getID()] = array();
				foreach ($listValues as $listValue) {
					if (in_array($listValue['id'],$value)) 
						$listing_info['user_defined'][$property->getID()][$listValue['id']] = $listValue['caption'];
				}
			}
			elseif ($property->getType() == 'location' && is_array($listing_info['user_defined'][$property->getID()])) {
				foreach($property->type->fields as $locationField) {
					if (array_key_exists($locationField['id'], $listing_info['user_defined'][$property->getID()])) {
						if ($locationField['id'] == 'State') {
							$listValues = SJB_StatesManager::getStateNamesBySid($property->value['State'], 'state_name');
						} else {
							$listValues = isset($locationField['list_values']) ? $locationField['list_values'] : array();
						}
						$value = $property->getValue();
						$value = isset($value[$locationField['id']]) ? $value[$locationField['id']] : '';
						foreach ($listValues as $listValue) {
							if ($listValue['id'] == $value) {
								$listing_info['user_defined'][$property->getID()][$locationField['id']] = $listValue['caption'];
								$listing_info['user_defined'][$property->getID()][$locationField['id'].'_Code'] = $listValue['Code'];
								$listing_info['user_defined'][$property->getID()][$locationField['id'].'_Name'] = $listValue['Name'];
							}
						}
					}
				}
			}
		}

		$cache = SJB_Cache::getInstance();
		$cacheId = md5('SJB_UserManager::getObjectBySID' . $listing_info['system']['user_sid']);
		$user_info = array();
		if ($cache->test($cacheId)) {
			$user_info = $cache->load($cacheId);
		} else {
			$user = SJB_UserManager::getObjectBySID($listing_info['system']['user_sid']);
			$user_info	= !empty($user) ? SJB_UserManager::createTemplateStructureForUser($user) : null;
			$cache->save($user_info, $cacheId, array(SJB_Cache::TAG_USERS));
		}

		$productInfo = SJB_ProductsManager::createTemplateStructureForProduct($listing_info['system']['product_info']);
		$priceForUpgradeToFeatured = 0;
		$priceForUpgradeToPriority = 0;
		if (!empty($listing_info['system']['product_info'])) {
			$listingProductInfo = unserialize($listing_info['system']['product_info']);
			$priceForUpgradeToFeatured = SJB_Array::get($listingProductInfo, 'upgrade_to_featured_listing_price', 0);
			$priceForUpgradeToPriority = SJB_Array::get($listingProductInfo, 'upgrade_to_priority_listing_price', 0);
		}

		$structure = array
        (
			'id'				        => $listing_info['system']['id'],
			'type'				        => array
											(
												'id' 		=> $listing_type_info['id'],
												'caption' 	=> $listing_type_info['name']
											),
			'user'				        => $user_info,
			'activation_date'	        => $listing_info['system']['activation_date'],
			'expiration_date'	        => $listing_info['system']['expiration_date'],
			'featured'			        => $listing_info['system']['featured'],
			'priority'			        => $listing_info['system']['priority'],
			'views'				        => $listing_info['system']['views'],
			'active'			        => $listing_info['system']['active'],
			'product'			        => $productInfo,
			'contract_id'               => $listing_info['system']['contract_id'],
			'number_of_pictures'        => isset($listing_info['user_defined']['pictures']) ? count($listing_info['user_defined']['pictures']) : 0,
			'approveStatus'		        => $listing_info['system']['status'],
			'complete'			        => $listing_info['system']['complete'],
			'external_id'               => $listing_info['system']['external_id'],
			'priceForUpgradeToFeatured' => $priceForUpgradeToFeatured,
			'priceForUpgradeToPriority' => $priceForUpgradeToPriority,
        );

		if (SJB_Settings::getSettingByName('jobg8Installed') && SJB_PluginManager::isPluginActive('JobG8IntegrationPlugin')) {
			$structure['jobType'] = JobG8::getJobProperty($listing_info['system']['id'], 'jobType');
		}
		if (array_search('comments', $extraInfo)) {
			$structure['comments_num']	= SJB_CommentManager::getCommentsNumToListing($listing_info['system']['id']);
		}
		if (array_search('ratings', $extraInfo)) {
			$structure['rating_num']	= SJB_Rating::getRatingNumToListing($listing_info['system']['id']);
			$structure['rating'] 		= SJB_Rating::getRatingToListing($listing_info['system']['id']);
			$structure['rating_array']	= SJB_Rating::getRatingTplToListing($listing_info['system']['id']);
		}

        if (!empty($listing_info['system']['subuser_sid'])) {
        	$structure['subuser'] = SJB_UserManager::getUserInfoBySID($listing_info['system']['subuser_sid']);
        }
      
        $structure['METADATA'] = array 
		( 
			'activation_date'	=> array('type' => 'date'), 
			'expiration_date'	=> array('type' => 'date'), 
			'views'				=> array('type' => 'integer'), 
			'number_of_pictures'=> array('type' => 'integer'),
			'approveStatus'		=> array('type'	=> 'string'),
			'rejectReason'		=> array('type'	=> 'string'),
		); 

		$structure = array_merge($structure, $listing_info['user_defined']); 
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($listing)); 
		
		$listing_user_meta_data = array();
		if (isset($user_info['METADATA'])) {
			$listing_user_meta_data = $user_info['METADATA'];
			unset($user_info['METADATA']);
		}
		
		$listing_product_meta_data = array();
		if (isset($productInfo['METADATA'])) {
			$listing_product_meta_data = $productInfo['METADATA'];
			unset($productInfo['METADATA']);
		}
		
		$listing_type_meta_data = array('caption' => array('type' => 'string', 'propertyID' => 'listing_type'));
		
		$structure['METADATA'] = array_merge($structure['METADATA'], array ('user' 		=> $listing_user_meta_data,
																			'product' 	=> $listing_product_meta_data,
																			'type' 		=> $listing_type_meta_data));

        return array_merge($structure, $listing_info['user_defined']);
	}
	
	public static function createMetadataForListing($listing, $user)
	{
		$structure['METADATA'] = array 
		( 
			'activation_date'	=> array('type' => 'date'), 
			'expiration_date'	=> array('type' => 'date'), 
			'views'				=> array('type' => 'integer'), 
			'number_of_pictures'=> array('type' => 'integer'),
			'approveStatus'		=> array('type'	=> 'string'),
			'rejectReason'		=> array('type'	=> 'string'),
		); 
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($listing)); 
		$listing_user_meta_data = array(
			'group' => array(
				'caption' => array('type' => 'string', 'propertyID' => 'caption'),
			),
			'registration_date' => array('type' => 'date'),
		);
		$listing_user_meta_data = array_merge($listing_user_meta_data, parent::getObjectMetaData($user)); 
	
		$listing_product_meta_data = array(
    			'caption'			=> array('type' => 'string', 'propertyID' => 'caption'),
    			'description'		=> array('type' => 'text', 'propertyID' => 'short_description'),
		);
		$listing_type_meta_data = array('caption' => array('type' => 'string', 'propertyID' => 'listing_type'));
		$structure['METADATA'] = array_merge($structure['METADATA'], array ('user' 		=> $listing_user_meta_data,
																			'product' 	=> $listing_product_meta_data,
																			'type' 		=> $listing_type_meta_data));
		return $structure['METADATA'];
	}

	public static function getLastListings($number_of_listings, $listing_type)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type);
		$wait_approve = SJB_ListingTypeManager::getWaitApproveSettingByListingType($listing_type_sid);
		
		$approve_status = '';
		if ($wait_approve)
			$approve_status = "AND l.status = 'approved'";
			
		$userSID = SJB_UserManager::getCurrentUserSID();
		$sqlAccess = " AND (
			(l.`access_type` = 'everyone') OR 
			(l.`access_type`  = 'only' AND FIND_IN_SET('{$userSID}',l.`access_list`) ) OR 
			(l.`access_type` = 'except' AND (FIND_IN_SET('{$userSID}', l.`access_list`) = 0 OR FIND_IN_SET('{$userSID}', l.`access_list`) IS NULL) )
			)";
		$listings_info = SJB_DB::query("SELECT l.*, lt.id FROM listings as l
				LEFT JOIN listing_types as lt ON (lt.sid = l.listing_type_sid)
				WHERE lt.id='".$listing_type."' AND l.active = 1 $approve_status $sqlAccess ORDER BY  l.activation_date DESC LIMIT 0, ?n", $number_of_listings);
		
		$listings = array();
		foreach ($listings_info as $listing_info) {
			$listing = SJB_ListingManager::getObjectBySID($listing_info['sid']);
			$listing->addPicturesProperty();
			$listings[] = $listing;
		}
		
		return $listings;
	}

	public static function filterListingSIDByActiveAndType($found_listings_sids, $listing_type_sid)
	{
		$sids_string = join("', '", $found_listings_sids);
		$and_listing_type_sid = '';
		
		if (!empty($listing_type_sid))
			$and_listing_type_sid = ' AND `listing_type_sid`=?n';
		
		$sids = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `sid` IN ('?w') AND `active`=1" . $and_listing_type_sid, $sids_string, $listing_type_sid);
		$result_sids = array();
		foreach ($sids as $sid)
			$result_sids[] = $sid['sid'];

		return $result_sids;
	}

	public static function incrementViewsCounterForListing($listingId, $listing)
	{
		$listingViews = SJB_DB::query('SELECT `views` FROM `listings` WHERE `sid` = ?n limit 1', $listingId);
		$ipAddress = SJB_Request::$remoteAddr;
		if (empty($listingViews) || SJB_Request::isBot() || !self::isViewCanBeCounted($listingId, $ipAddress))
			return false;
		SJB_Statistics::addStatistics('viewListing', $listing->getListingTypeSID(), $listing->getSID());
		self::setListingLatestViewDateByIp($listingId, $ipAddress);
		return SJB_DB::query('UPDATE `listings` SET `views` = `views` + 1 WHERE `sid` = ?n limit 1', $listingId);
	}

	/**
	 * @static
	 * @param int $listingId
	 * @param string $ipAddress
	 */
	public static function setListingLatestViewDateByIp($listingId, $ipAddress)
	{
		$listingViewPageID = self::getListingViewPageID($ipAddress, $listingId);
		$pageId = SJB_System::getUserPageParentURI(SJB_Navigator::getURI());
		if ($listingViewPageID) {
			SJB_DB::query('UPDATE `page_view` SET `date` = NOW() WHERE `id` = ?n', $listingViewPageID);
		} else {
			SJB_DB::query('INSERT INTO `page_view` SET `id_pages` = ?s, `param` = ?n, `ip_address` = ?s, `date` = NOW(), `contract_id` = 0', $pageId, $listingId, $ipAddress);
		}
	}

	/**
	 * @static
	 * @param string $ipAddress
	 * @param int $listingId
	 * @return int|null
	 */
	public static function getListingViewPageID($ipAddress, $listingId)
	{
		$result = SJB_DB::query('SELECT `id` FROM `page_view` WHERE `ip_address` = ?s AND `param` = ?n', $ipAddress, $listingId);
		$result = array_pop($result);
		return empty($result) ? $result : array_pop($result);
	}

	/**
	 * @static
	 * @param int $listingId
	 * @param string $ipAddress
	 * @return bool
	 */
	public static function isViewCanBeCounted($listingId, $ipAddress)
	{
		$result = SJB_DB::query('SELECT * FROM `page_view` WHERE `ip_address` = ?s AND `param` = ?n AND NOW() < DATE_ADD(`date`, INTERVAL 1 DAY)', $ipAddress, $listingId);
		return empty($result) ? true : false;
	}

	public static function getListingSIDByID($id)
	{
		return $id;
	}

	public static function makeCheckoutedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE `listings` SET `checkouted` = 1 WHERE `sid` = ?n", $listing_sid);
	}

	public static function unmakeCheckoutedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE `listings` SET `checkouted` = 0 WHERE `sid` = ?n", $listing_sid);
	}

	public static function makeFeaturedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE listings SET featured = 1 WHERE sid = ?n", $listing_sid);
	}

	public static function unmakeFeaturedBySID($listing_sid)
	{
		return SJB_DB::query("UPDATE listings SET featured = 0 WHERE sid = ?n", $listing_sid);
	}

	public static function makePriorityBySID($listing_sid)
	{
		$result = SJB_DB::query("UPDATE listings SET priority = 1 WHERE sid = ?n", $listing_sid);
		$cache = SJB_Cache::getInstance();
		$cache->clean('matchingTag', array(SJB_Cache::TAG_LISTINGS));
		return $result;
	}

	public static function unmakePriorityBySID($listing_sid)
	{
		$result = SJB_DB::query("UPDATE listings SET priority = 0 WHERE sid = ?n", $listing_sid);
		$cache = SJB_Cache::getInstance();
		$cache->clean('matchingTag', array(SJB_Cache::TAG_LISTINGS));
		return $result;
	}

	/**
	 * Uploaded resumes and jobs statistics
	 * @return array
	 */
	public static function getListingsInfo()
	{
		$res = array();

		// условие запроса сформируем в зависимости от требуемого периода
		$periods = array(
			"Today" => "`l`.`activation_date` >= CURDATE()",
			"This Week" => "`l`.`activation_date` >= FROM_DAYS(TO_DAYS(CURDATE()) - WEEKDAY(CURDATE()))",
			"This Month" => "`l`.`activation_date` >= FROM_DAYS(TO_DAYS(CURDATE()) - DAYOFMONTH(CURDATE()) + 1)");
		$listingTypes = SJB_ListingTypeManager::createTemplateStructureForListingTypes();

		// условие в запрос будем подставлять заранее заготовленное из массива
		// nwy: разбил подсчет общего количества и подсчет активных листингов на 2 запроса
		// так быстрее при большом количестве листингов
		foreach ($listingTypes as $listingType) {
			foreach ($periods as $key => $value) {
				$res[$listingType["id"]]["periods"][$key]['count'] = SJB_DB::queryValue("
					select count(*)
					from listings l
					where {$value} and l.listing_type_sid = {$listingType["sid"]}");
				$res[$listingType["id"]]["periods"][$key]['active'] = SJB_DB::queryValue("
					select count(*)
					from listings l
					where {$value} and l.listing_type_sid = {$listingType["sid"]} and `l`.`active` = 1");
			}
			$res[$listingType["id"]]["total"]['count'] = SJB_DB::queryValue("
				select	count(*)
				from `listings` `l`
				where `l`.`listing_type_sid` = {$listingType["sid"]}");
			$res[$listingType["id"]]["total"]['active'] = SJB_DB::queryValue("
				select	count(*)
				from `listings` `l`
				where `l`.`listing_type_sid` = {$listingType["sid"]} and `l`.`active` = 1");
			$res[$listingType["id"]]["approveInfo"] = SJB_ListingManager::getListingsApproveInfo($listingType["sid"]);
		}
		return $res;
	}
	
	// получим информацию о соотношении одобренных и неодобренных листингов
	public static function getListingsApproveInfo ($listing_type_sid = false)
	{		
		if ($listing_type_sid != false) {
			$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sid);
			if ($listingTypeInfo['waitApprove'] == false ) {
				return false;
			}
			$res = SJB_DB::query("
				SELECT count(*) as `count`, `status`, `listing_type_sid` 
				FROM `listings` 
				WHERE `listing_type_sid` = ?n 
				GROUP BY `status`", $listing_type_sid);
			
			
			$statusInfo = array();
			foreach ($res as $arr) {
				$statusInfo[$arr['status']] = $arr['count'];
			}
			$statusInfo['listing_type_sid'] = $listing_type_sid;
			$statusInfo['listing_type_id'] = SJB_ListingTypeManager::getListingTypeIDBySID($listing_type_sid);

			return $statusInfo;
		}
		
		$res = SJB_DB::query("
				SELECT count(*) as `count`, `listing_type_sid`, `status` 
				FROM `listings` 
				GROUP BY `listing_type_sid`, `status`");
		
		$approve = array();
		foreach ($res as $arr) {
			$approve[$arr['listing_type_sid']][$arr['status']] = $arr['count'];
		}
		return $approve;
	}
	
	public static function getListingApprovalStatusBySID ($sid)
	{		
		if (!$sid)
			return false;
		return SJB_DB::queryValue("SELECT `status` FROM `listings` WHERE `sid` = ?n", $sid);
	}

	public static function copyFilesAndPicturesFromListing($srcListingSid, $dstListingSid, $tmpListingSid)
	{
		$listing = SJB_ListingManager::getObjectBySID($srcListingSid);
		if ($listing) {
			foreach ($listing->getProperties() as $listingProperty) {
				if ($listingProperty->getType() == 'complex') {
					self::copyComplexFiles($dstListingSid, $listingProperty);
				}
				elseif (in_array($listingProperty->getType(), array('file', 'video'))) {
					self::copyFiles($dstListingSid, $listingProperty);
				}
			}
		}

		if ($tmpListingSid) {
			$gallery = new SJB_ListingGallery();
			$gallery->setListingSID($tmpListingSid);
			$numberOfPictures = $gallery->getPicturesAmount();
			if ($numberOfPictures != 0) {
				$picturesInfo = $gallery->getPicturesInfo();
				$gallery->setListingSID($dstListingSid);
				$gallery->deleteImages();
				foreach ($picturesInfo as $pictureInfo) {
					$gallery->uploadImage($pictureInfo['picture_url'], $pictureInfo['caption']);
				}
			}
		}
		SJB_Session::unsetValue('tmp_file_storage');
		SJB_ListingDBManager::setListingExpirationDateBySid($dstListingSid);
	}

	private static function copyComplexFiles($dstListingSid, $listingProperty)
	{
		$complexFields = $listingProperty->type->complex->getProperties();
		foreach ($complexFields as $complexField) {
			if ($complexField->getType() == 'complexfile') {
				foreach ($complexField->getValue() as $value) {
					self::changeFilesFieldValue($dstListingSid, $listingProperty, $value, true);
				}
			}
		}
	}

	private static function copyFiles($dstListingSid, $listingProperty)
	{
		self::changeFilesFieldValue($dstListingSid, $listingProperty);
	}

	private static function changeFilesFieldValue($dstListingSid, $listingProperty, $value = null, $isComplex = false)
	{
		if (!$value) {
			$value = $listingProperty->getValue();
		}
		
		$uploadedFileId   = SJB_Array::get($value, 'file_id');
		$uploadedFileInfo = SJB_UploadFileManager::getUploadedFileInfo($uploadedFileId);
		if ($uploadedFileInfo) {
			$uploadFileManager = new SJB_UploadFileManager();
			
			$fileGroup = $listingProperty->getType() == 'video' ? 'video' : 'files';
			
			if ($isComplex) {
				$newUploadedFileId = strstr($uploadedFileId, '_', true) . '_' .$dstListingSid;
			} else {
				$newUploadedFileId = $listingProperty->getID() . '_' . $dstListingSid;
			}
			
			$uploadFileManager->setFileGroup($fileGroup);
			$uploadFileManager->copyFile($uploadedFileInfo, $newUploadedFileId);
			$listingProperty->setValue($newUploadedFileId);
		}
	}

	public static function setListingApprovalStatus($listingSids, $status, $updateBrowsePages = true)
	{
		$statusValues = array('pending', 'approved', 'rejected');
		if (in_array($status, $statusValues)) {
			if (!is_array($listingSids)) {
				$listingSids = array($listingSids);
			}
			
			if ($updateBrowsePages) {
				SJB_BrowseDBManager::deleteListings($listingSids);
			}
			
			switch($status) {
				case 'pending':
					// set status to 'pending' and clear reject reason
					SJB_DB::queryExec("UPDATE `listings` SET `status`=?s, `reject_reason` = '' WHERE `sid` IN (?l)", $status, $listingSids);
					break;
				case 'approved':
					SJB_DB::queryExec("UPDATE `listings` SET `status`=?s WHERE `sid` IN (?l)", $status, $listingSids);
					break;
				case 'rejected':
					$rejectReason = ($_REQUEST['rejectReason'] != '' ? $_REQUEST['rejectReason'] : 'rejected with no reason');
					SJB_DB::queryExec("UPDATE `listings` SET `status`=?s, `reject_reason` = ?s WHERE `sid` IN (?l)", $status, $rejectReason, $listingSids);
					break;
			}
			SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
			
			if ($updateBrowsePages && $status == 'approved') {
				SJB_BrowseDBManager::addListings($listingSids);
			}
		}
	}

	public static function getListingAccessList($listing_id, $access_type)
	{
		$result = SJB_DB::query("SELECT `access_list` FROM `listings` WHERE `access_type` = ?s AND `sid` =?n ", $access_type, $listing_id);
		if ($result) {
			$result = array_pop($result);
			$result = explode(',', array_pop($result));
		}
		else {
			$result = false;
		}
		$employers = array();
		if (is_array($result)) {
			foreach ($result as $emp){
				if (!empty($emp)) {
					$currEmp	 = SJB_UserManager::getUserInfoBySID($emp);
					$employers[] = array('user_id' => $emp, 'value' => $currEmp['CompanyName']);
				}
			}
			sort($employers);
		}
		return $employers;
	}
	
	public static function isListingAccessableByUser($listingId, $userId)
	{
		$listingRequest  = SJB_DB::query("SELECT `access_type`, `user_sid` FROM `listings` WHERE `sid` = ?n", $listingId);
		$accessType      = '';
		$listingOwnerSid = '';
		if (!empty($listingRequest)) {
			$accessType      = $listingRequest[0]['access_type'];
			$listingOwnerSid = $listingRequest[0]['user_sid'];
		}
		unset($listingRequest);

		if ($listingOwnerSid == $userId) {
			return true;
		}
		
		$access = false;
		switch ($accessType) {
			case 'everyone': 
				$access = true;
				break;
			case 'no_one':
				$access = false;
				break;
			case 'only':
				$result = SJB_DB::queryValue("SELECT FIND_IN_SET(?s,`access_list`) FROM `listings` WHERE `sid` = ?n", $userId, $listingId);
				if ($result != 0) {
					$access = true;
				}
				unset($result);
				break;
			case 'except':
				$result = SJB_DB::queryValue("SELECT FIND_IN_SET(?s,`access_list`) FROM `listings` WHERE `sid` = ?n", $userId, $listingId);
				if ($result == 0) {
					$access = true;
				}
				unset($result);
				break;	
		}

		return $access;
	}


	public static function setListingAccessibleToUser($listing_id, $user_id)
	{
		$accessData = SJB_DB::query("SELECT `access_type`, `access_list` FROM `listings` WHERE `sid` = ?n", $listing_id);
		$accessData = array_pop($accessData);
		switch ($accessData['access_type']) {
			case 'no_one':
				SJB_DB::query("UPDATE `listings` SET `access_type`='only', `access_list`=?s WHERE `sid`=?n",$user_id, $listing_id);
				break;
			case 'only':
				$access_list = $accessData['access_list']!=''?$accessData['access_list'].",".$user_id:$user_id;
				SJB_DB::query("UPDATE `listings` SET `access_list`=?s WHERE `sid`=?n",$access_list, $listing_id);
				break;
			case 'except':
				$access_list = $accessData['access_list']!=''?explode(',',$accessData['access_list']):array();
				if (in_array($user_id,$access_list)) {
					$access_list = array_flip($access_list);
					unset($access_list[$user_id]);
					$access_list = implode(",",array_flip($access_list));
					SJB_DB::query("UPDATE `listings` SET `access_list`=?s WHERE `sid`=?n",$access_list, $listing_id);
				}
				break;	
		}
	}
	
	public static function newValueFromSearchCriteria($listing_structure, $search_criteria_structure)
	{
		foreach ($search_criteria_structure as $key => $criteria) {
			if (isset($criteria['monetary']) && array_key_exists($key, $listing_structure)) {
				$currency = isset($criteria['monetary']['currency'])?SJB_CurrencyManager::getCurrencyByCurrCode($criteria['monetary']['currency']):false;
				$course = SJB_Array::get($listing_structure[$key], 'course');
				if (is_array($listing_structure[$key]) && $course && isset($listing_structure[$key]['add_parameter']) && !empty($currency) && $currency['sid'] != $listing_structure[$key]['add_parameter'] && is_numeric($listing_structure[$key]['value'])) {
					$listing_structure[$key]['value'] = round(($listing_structure[$key]['value']/ $course)*$currency['course']);
					$listing_structure[$key]['currency_sign'] = $currency['currency_sign'];
				}
			}
		}
		return $listing_structure;
	}

	public static function getCountListingsByContractID($contract_id)
	{
		return SJB_DB::queryValue("SELECT count(*) FROM `listings` WHERE `contract_id` = ?n", $contract_id);
	}
	
	/**
	 * Flag listing by listing SID
	 * Set flag marker to listing with some reason and comment.
	 * @param integer $listingSID
	 * @param integer $reason
	 * @param string $comment
	 * @return integer|boolean
	 */
	public static function flagListingBySID($listingSID, $reason, $comment)
	{
		$result = SJB_DB::query("SELECT * FROM `flag_listing_settings` WHERE `sid` = ?n", $reason);
		$reasonText = '';
		if (!empty($result))
			$reasonText = $result[0]['value'];
		$userSID     = SJB_UserManager::getCurrentUserSID();
		$listingInfo = self::getListingInfoBySID($listingSID);
		
		return SJB_DB::query("INSERT INTO `flagged_listings` SET `listing_sid` = ?n, `user_sid` = ?n, `comment` = ?s, `flag_reason` = ?s, `date` = NOW(), `listing_type_sid` = ?n", $listingSID, $userSID, $comment, $reasonText, $listingInfo['listing_type_sid']);
	}
	
	/**
	 * Get and sort flagged listings by listing type
	 * Get sorted and filtered array of flagged listings by listing type, page number and
	 * listings per page value
	 *
	 * @param integer $listingTypeSID
	 * @param integer $page
	 * @param integer $perPage
	 * @return array
	 */
	public static function getFlaggedListings($listingTypeSID = null, $page = 1, $perPage = 10, $sortingField = 'sid', $sortingOrder = 'DESC', $filters = null)
	{
		// PREPARE FILTERS
		$filterFlag  = '';
		$filterUser  = '';
		$filterTitle = '';
		if ($filters !== null) {
			$filterFlag = isset($filters['flag_reason']) ? $filters['flag_reason'] : '';
			$filterUser = isset($filters['username']) ? $filters['username'] : '';
			$filterTitle = isset($filters['title']) ? $filters['title'] : '';
		}
		
		$joinUsers = '';
		if ( !empty($filterFlag)) {
			$filterFlag = SJB_DB::quote($filterFlag);
			$filterFlag = " AND fl.flag_reason LIKE '%{$filterFlag}%' ";
		}
		if (!empty($filterUser)) {
			$filterUser = SJB_DB::quote($filterUser);
			$joinUsers  = " LEFT JOIN `users` u ON (u.sid = l.user_sid) ";
			$filterUser = " AND u.username LIKE '%{$filterUser}%' ";
		}
		if (!empty($filterTitle)) {
			$filterTitle = SJB_DB::quote($filterTitle);
			$filterTitle = " AND l.`Title` LIKE '%{$filterTitle}%' ";
		}
		
		// SET LISTING TYPE FILTER
		$listingTypeFilter = '';
		if (empty($listingTypeSID)) {
			$listingTypeFilter = ' fl.`listing_type_sid` <> 0 ';
		} elseif ( is_numeric($listingTypeSID)) {
			$listingTypeFilter = " fl.`listing_type_sid` = {$listingTypeSID} ";
		}

		$startNum = ($page - 1) * $perPage;
		switch ($sortingField) {
			case 'sid':
			case 'date':
			case 'flag_reason':
			case 'comment':
				$sortingField = "fl." . $sortingField;
				$flaggedListings = SJB_DB::query("
						SELECT fl.*
							FROM `flagged_listings` fl
						LEFT JOIN `listings` l ON (l.sid = fl.listing_sid) 
						{$joinUsers}
							WHERE {$listingTypeFilter} {$filterFlag} {$filterUser} {$filterTitle}
						GROUP BY fl.sid ORDER BY ?w ?w LIMIT ?n, ?n", 
						$sortingField, $sortingOrder, $startNum, $perPage);
				break;
				
			case 'active':
				$sortingField = "l." . $sortingField;
				$flaggedListings = SJB_DB::query("
						SELECT fl.*
							FROM `flagged_listings` fl
						LEFT JOIN `listings` l ON (fl.listing_sid = l.sid)
						{$joinUsers}
							WHERE {$listingTypeFilter} {$filterFlag} {$filterUser} {$filterTitle}
						ORDER BY ?w ?w LIMIT ?n, ?n", 
						$sortingField, $sortingOrder, $startNum, $perPage);
				break;
				
			case 'username':
				$sortingField = "u." . $sortingField;
				$flaggedListings = SJB_DB::query("
						SELECT fl.*
							FROM `flagged_listings` fl
						LEFT JOIN `listings` l ON (fl.listing_sid = l.sid)
						LEFT JOIN `users` u ON (u.sid = l.user_sid)
						WHERE {$listingTypeFilter} {$filterFlag} {$filterUser} {$filterTitle}
						ORDER BY ?w ?w LIMIT ?n, ?n",
						$sortingField, $sortingOrder, $startNum, $perPage);
				break;
				
			case 'flag_user':
				$flaggedListings = SJB_DB::query("
						SELECT fl.* 
							FROM `flagged_listings` fl
						LEFT JOIN `listings` l ON (fl.listing_sid = l.sid)
						{$joinUsers}
						LEFT JOIN `users` u_sort ON (fl.user_sid = u_sort.sid)
						WHERE {$listingTypeFilter} {$filterFlag} {$filterUser} {$filterTitle}
						ORDER BY u_sort.username ?w LIMIT ?n, ?n",
						$sortingOrder, $startNum, $perPage);
				break;
				
			case 'title':
				// при сортировке по Title из списка результатов пропадают листинги, которые были удалены
				// но всё еще находятся в таблице flagged_listings
				$flaggedListings = SJB_DB::query("
						SELECT fl.* 
							FROM `flagged_listings` fl
						LEFT JOIN `listings` l ON (fl.listing_sid = l.sid)
						{$joinUsers}
						WHERE {$listingTypeFilter} {$filterFlag} {$filterUser} {$filterTitle}
						ORDER BY `Title` ?w LIMIT ?n, ?n",
						$sortingOrder, $startNum, $perPage);
				break;
				
			default:
				break;
		}
		
		return $flaggedListings;
	}
	
	
	/**
	 * Get total pages number of flags
	 *
	 * @param integer $listingTypeSID
	 * @param integer $perPage
	 * @return integer
	 */
	public static function getFlaggedTotalPagesNum($listingTypeSID, $perPage = 10, $filters = null)
	{
		$listingsNum = self::getFlagsNumberByListingTypeSID($listingTypeSID, $filters);
		if ($listingsNum == 0)
			return 0;
		return ceil($listingsNum / $perPage);
	}
	
	
	/**
	 * Get flag reasons list by listing type SID
	 *
	 * @param integer $listingTypeSID
	 * @return array
	 */
	public static function getAllFlags($listingTypeSID = null)
	{
		if (empty($listingTypeSID))
			return SJB_DB::query("SELECT * FROM `flag_listing_settings`");
		return SJB_DB::query("SELECT * FROM `flag_listing_settings` WHERE `listing_type_sid` = ?n", $listingTypeSID);
	}
	
	
	/**
	 * Get total flags number by listing type SID
	 * 
	 * Count and return total numbers of flag
	 *
	 * @param integer $listingTypeSID
	 * @param array   $filters
	 * @param boolean $groupByListing
	 * @return integer
	 */
	public static function getFlagsNumberByListingTypeSID($listingTypeSID, $filters = null, $groupByListing = false)
	{
		$filterFlag  = '';
		$filterUser  = '';
		$filterTitle = '';
		if ($filters !== null) {
			$filterFlag  = isset($filters['flag_reason']) ? $filters['flag_reason'] : '';
			$filterUser  = isset($filters['username']) ? $filters['username'] : '';
			$filterTitle = isset($filters['title']) ? $filters['title'] : '';
		}
		
		$joinUsers = '';
		
		if ( !empty($filterFlag)) {
			$filterFlag = SJB_DB::quote($filterFlag);
			$filterFlag = " AND fl.flag_reason LIKE '%{$filterFlag}%' ";
		}
		
		if (!empty($filterUser)) {
			$filterUser = SJB_DB::quote($filterUser);
			$joinUsers  = " LEFT JOIN `users` u ON (u.sid = l.user_sid) ";
			$filterUser = " AND u.username LIKE '%{$filterUser}%' ";
		}
		
		if (!empty($filterTitle)) {
			$filterTitle = SJB_DB::quote($filterTitle);
			$filterTitle = " AND l.`Title` LIKE '%{$filterTitle}%' ";
		}
		
		// SET GROUP PARAM
		$groupOption = '';
		if ($groupByListing) {
			$groupOption = " GROUP BY fl.listing_sid";
		}

		// SET LISTING TYPE FILTER
		if (empty($listingTypeSID)) {
			$listingTypeFilter = ' fl.`listing_type_sid` <> 0 ';
		} elseif (is_numeric($listingTypeSID)) {
			$listingTypeFilter = " fl.`listing_type_sid` = {$listingTypeSID} ";
		}
		
		
		$listingsNum = SJB_DB::query("
			SELECT count(*) count 
				FROM `flagged_listings` fl 
			LEFT JOIN `listings` l ON (l.sid = fl.listing_sid) 
			{$joinUsers}
			WHERE {$listingTypeFilter} {$filterFlag} {$filterUser} {$filterTitle}
			{$groupOption}");

		
		// if group option - get number of flagged LISTINGs
		if ($groupByListing) {
			return count($listingsNum);
		}
		// if no group option - return number of flags
		return $listingsNum[0]['count'];
	}
	
	/**
	 * Remove flag by flag SID
	 *
	 * @param integer $flagSID
	 * @return integer|boolean
	 */
	public static function removeFlagBySID($flagSID)
	{
		if (!is_numeric($flagSID))
			return false;
		return SJB_DB::query("DELETE FROM `flagged_listings` WHERE `sid` = ?n LIMIT 1", $flagSID);
	}
	
	/**
	 * Deactivate listing by flag SID
	 *
	 * @param integer $flagSID
	 * @return bool|mixed
	 */
	public static function deactivateListingByFlagSID($flagSID)
	{
		if (!is_numeric($flagSID))
			return false;
		$listingSID = self::getListingSIDByFlagSID($flagSID);
		if (SJB_ListingManager::isListingExists($listingSID))
			return self::deactivateListingBySID($listingSID);
		return false;
	}

	/**
	 *  Function delete listing by flag SID, if listing exists
	 *
	 * @param integer $flagSID
	 * @return integer|boolean
	 */
	public static function deleteListingByFlagSID($flagSID)
	{
		if (is_numeric($flagSID)) {
			$listingSID = self::getListingSIDByFlagSID($flagSID);
			if (SJB_ListingManager::isListingExists($listingSID))
				self::deleteListingBySID($listingSID);
		}
	}
	
	/**
	 * Get listing SID from flags table by flag SID
	 *
	 * @param integer $flagSID
	 * @return integer|boolean
	 */
	public static function getListingSIDByFlagSID($flagSID)
	{
		if (!is_numeric($flagSID))
			return false;
		$result = SJB_DB::query("SELECT `listing_sid` FROM `flagged_listings` WHERE `sid` = ?n LIMIT 1", $flagSID);
		if (empty($result))
			return false;
		return $result[0]['listing_sid'];
	}
	
	public static function updateKeywords($keywords, $listingSID)
	{
		return SJB_DB::query("UPDATE `listings` SET `keywords` = ?s WHERE `sid`=?n", $keywords, $listingSID);
	}

	/**
	 * Checks if listng with specified external_id exists
	 *
	 * @param string $ext_id
	 * @return boolean
	 */
	public static function isListingExistsByExternalId($ext_id)
	{
		$is_listing_exist = false;
		if (!empty($ext_id)) {
			$is_listing_exist = SJB_DB::query("SELECT `external_id` FROM `listings` WHERE `external_id` = ?s", $ext_id);
			$is_listing_exist = array_pop($is_listing_exist);
		}
		return $is_listing_exist;
	}
	
	/**
	 * Gets listing sid with specified external_id
	 *
	 * @param string $externalId
	 * @return integer|null
	 */
	public static function getListingSidByExternalId($externalId)
	{
		$listingSid = null;
		if (!empty($externalId)) {
			$result = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `external_id` = ?s", $externalId);
			if (!empty($result)) {
				$listingSid = $result[0]['sid'];
			}
		}
		return $listingSid;
	}

	/**
	 * this function is used to receive some listing SID,
	 * for Display Listing FIelds Builder.
	 * 
	 * @param int $listing_type_id
	 * @return int
	 */
	public static function getListingIDByListingTypeID($listing_type_id)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		return SJB_DB::queryValue('SELECT `sid` FROM `listings` WHERE `listing_type_sid` = ?n AND `active` = 1 LIMIT 1', $listing_type_sid);
	}
	
	public static function getLastAddedListingByUserSID($userSID)
	{
		$activeListings = SJB_ListingDBManager::getActiveAndApproveListingsNumberByUserSID($userSID);
		$where = '';
		if ($activeListings)
			$where = " AND `active`=1 ";
		$sid = SJB_DB::queryValue("SELECT `sid` FROM `listings` WHERE `user_sid` = ?n {$where} ORDER BY `activation_date` DESC LIMIT 1",$userSID);
		if (!empty($sid))
			return self::getObjectBySID($sid);
		return false;
	}

	public static function isListingExists($listingId)
	{
		return count(SJB_DB::query('select `sid` from `listings` where `sid` = ?n limit 1', $listingId)) > 0;
	}
	
	public static function hasListingProduct($listing_sid)
	{
		$result = SJB_DB::queryValue("SELECT `product_info` FROM `listings` WHERE `sid`=?n", $listing_sid);
		if ($result)
			return $result;
		return false;
	}
	
	public static function insertProduct($listing_sid, $productInfo)
	{
		$productInfo = serialize($productInfo);
		return SJB_DB::query("UPDATE `listings` SET `product_info` = ?s WHERE `sid` =?n", $productInfo, $listing_sid);
	}

	public static function deletePreviewListingsByUserSID($userSID) 
	{
		$previewListings = SJB_ListingDBManager::getAllPreviewListingsByUserSID($userSID);
		if ($previewListings) {
			foreach ($previewListings as $listing)
				self::deleteListingBySID($listing['sid']);
		}
	}
	
	public static function unFeaturedListings()
	{
		SJB_DB::query("UPDATE  `listings` SET `featured` = 0, `featured_expiration` = NULL WHERE `featured_expiration` < NOW() AND `active` = 1");
	}
	
	public static function unPriorityListings()
	{
		SJB_DB::query("UPDATE  `listings` SET `priority` = 0, `priority_expiration` = NULL WHERE `priority_expiration` < NOW() AND `active` = 1");
	}

	public static function canCurrentUserAddListing(& $error, $listingTypeId = false)
	{
		$acl = SJB_Acl::getInstance();

		if (SJB_UserManager::isUserLoggedIn()) {
			$current_user = SJB_UserManager::getCurrentUser();
			if ($current_user->hasContract()) {
				$contracts_id = $current_user->getContractID();
				$contractsSIDs = $contracts_id ? implode(',', $contracts_id) : 0;
				$resultContractInfo = SJB_DB::query("SELECT `id`, `product_sid`, `expired_date`, `number_of_postings` FROM `contracts` WHERE `id` in ({$contractsSIDs}) ORDER BY `expired_date` DESC");
				$PlanAcces = count($resultContractInfo) > 0 ? true : false;
				if ($PlanAcces && $acl->isAllowed('post_' . $listingTypeId)) {
					$productsInfo = array();
					$is_contract = false;
					foreach ($resultContractInfo as $contractInfo) {
						if ($acl->isAllowed('post_' . $listingTypeId, $contractInfo['id'], 'contract')) {
							$permissionParam = $acl->getPermissionParams('post_' . $listingTypeId, $contractInfo['id'], 'contract');
							if (empty($permissionParam) || $acl->getPermissionParams('post_' . $listingTypeId, $contractInfo['id'], 'contract') > $contractInfo['number_of_postings']) {
								$product = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
								$productsInfo[$contractInfo['id']]['product_name'] = $product['name'];
								$productsInfo[$contractInfo['id']]['expired_date'] = $contractInfo['expired_date'];
								$productsInfo[$contractInfo['id']]['contract_id'] = $contractInfo['id'];
							}
						}
						$is_contract = true;
					}

					if ($is_contract && count($productsInfo) > 0)
						return $productsInfo;
					else
						$error = 'LISTINGS_NUMBER_LIMIT_EXCEEDED';
				}
				else
					$error = 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING';
			}
			else
				$error = 'NO_CONTRACT';
		}
		else {
			$error = 'NOT_LOGGED_IN';
		}
		return false;
	}

	public static function getPropertyByParentID($parentID, $fieldID, $listing_type_sid = 0) 
	{
		$parentSID = SJB_ListingFieldManager::getListingFieldSIDByID($parentID);
		$fields = SJB_ListingFieldManager::getListingFieldsInfoByParentSID($parentSID);
		$fieldSID = null;
		foreach ($fields as $field) {
			if ($field['id'] == $fieldID)
				$fieldSID = $field['sid'];
		}
		if ($fieldSID) {
			$property_info = SJB_ListingFieldDBManager::getListingFieldInfoBySID($fieldSID);
			$property_info['id'] = $parentID."_".$property_info['id'];
			$fieldID = $property_info['id'];
			return new SJB_ObjectProperty($property_info);
		}
		return null;
	}

	public static function activateListingKeywordsBySID($listingSID)
	{
		return SJB_ListingDBManager::activateListingKeywordsBySID($listingSID);
	}

	public static function deactivateListingKeywordsBySID($listingSID)
	{
		return SJB_ListingDBManager::deactivateListingKeywordsBySID($listingSID);
	}

	/**
	 * @param int $userSID
	 * @param int $productSID
	 * @param int $contractID
	 * @param int $listingNumber
	 */
	public static function activateListingsAfterPaid($userSID, $productSID, $contractID, $listingNumber)
	{
		$limit = '';
		if ($listingNumber != null) {
			$limit = 'LIMIT 0,' . $listingNumber;
		}
		$serializedProductSID = SJB_ProductsManager::generateQueryBySID($productSID);
		$listingsSIDsToProceed = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `checkouted` = 0 AND `complete` = 1 AND `contract_id` = 0 AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSID})' ORDER BY `sid` DESC {$limit}", $userSID);
		if (!empty($listingsSIDsToProceed)) {
			foreach ($listingsSIDsToProceed as $listingSIDToProceed) {
				SJB_DB::query('UPDATE `listings` SET `contract_id` = ?n, `checkouted` = 1 WHERE `sid` = ?n', $contractID, $listingSIDToProceed['sid']);
				self::activateListingBySID($listingSIDToProceed['sid']);
			}
			SJB_ProductsManager::incrementPostingsNumber($productSID, count($listingsSIDsToProceed));
			SJB_ContractSql::updatePostingsNumber($contractID, count($listingsSIDsToProceed));
		}
	}

	/**
	 * @param $listingSID
	 * @return string
	 */
	public static function getListingUrlBySID($listingSID)
	{
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSID);
		$lowerListingTypeID = strtolower(SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']));
		return SJB_System::getSystemSettings('USER_SITE_URL') . "/display-{$lowerListingTypeID}/{$listingInfo['id']}/";
	}

	private static function getListingDescriptionPreparedForSharer($listingInfo)
	{
		$description = strip_tags(trim($listingInfo['JobDescription']));
		$description = html_entity_decode($description, ENT_COMPAT, 'UTF-8');
		if (!empty($description)) {
			return htmlspecialchars(mb_substr($description, 0, 300, 'UTF-8'));
		}
		
		return '';
	}

	/**
	 * @param $listingSID
	 */
	public static function setMetaOpenGraph($listingSID)
	{
		if (!empty($listingSID)) {
			$listing           = SJB_ListingManager::getObjectBySID($listingSID);
			$listingStructure  = SJB_ListingManager::createTemplateStructureForListing($listing);
			$siteUrl           = SJB_System::getSystemSettings("SITE_URL");
			$location          = SJB_LocationManager::locationFormat( array("location" => $listingStructure['Location']) );
			$locationFormatted = !empty($location) ? " (" . $location . ")" : '';
			$title             = htmlspecialchars(strip_tags(trim($listingStructure['Title']))) . $locationFormatted;
			$logoImage         = !empty($listingStructure['user']['Logo']['file_url']) ? $listingStructure['user']['Logo']['file_url'] : '';
			$description       = self::getListingDescriptionPreparedForSharer($listingStructure);
			$listingUrl        = SJB_ListingManager::getListingUrlBySID($listingSID);

			$openGraphMetaBlock = "<meta property=\"og:type\" content=\"website\" />\n\t" .
				"<meta property=\"og:url\" content=\"{$listingUrl}\" />\n\t" .
				"<meta property=\"og:title\" content=\"{$title}\" />\n\t" .
				"<meta property=\"og:description\" content=\"{$description}\" />\n\t" .
				"<meta property=\"og:site_name\" content=\"{$siteUrl}\" />\n\t" .
				"<meta property=\"og:image\" content=\"{$logoImage}\" />";

			$head = SJB_System::getPageHead();
			SJB_System::setPageHead($head . ' ' .  $openGraphMetaBlock);
		}
	}

	/**
	 * @param $listingSID
	 * @return int
	 */
	public static function isListingCheckOuted($listingSID)
	{
		return SJB_DB::queryValue("SELECT `checkouted` FROM `listings` WHERE `sid` = ?n", $listingSID);

	}
}
