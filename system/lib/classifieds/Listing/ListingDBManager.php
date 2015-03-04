<?php

class SJB_ListingDBManager extends SJB_ObjectDBManager
{
	/**
	 * @param  $listing SJB_Listing
	 * @return array|bool
	 */
	public static function saveListing($listing, $listingSidsForCopy = array())
	{
		$listing_type_sid = $listing->getListingTypeSID();
		if (!is_null($listing_type_sid)) {
            $keywords = SJB_ListingDBManager::getListingKeywordsArray($listing); // Строчку в низ не переносить, так как после сохранения объекта вытащить кейворды сложнее
			parent::saveObject('listings', $listing, false, $listingSidsForCopy);
			$user_info = SJB_UserManager::getUserInfoBySID($listing->getUserSID());
			$user_keywords = SJB_ListingDBManager::getUserKeywords($user_info);
			if ($user_keywords)
				$keywords[] = $user_keywords;
			SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
			foreach ($keywords as $keyword)
				SJB_ListingDBManager::saveListingKeyword($keyword, $listing->getSID(), $listing->isActive());

			if (!SJB_ListingManager::hasListingProduct($listing->getSID()))
				SJB_ListingManager::insertProduct($listing->getSID(), $listing->getProductInfo());
			
			return SJB_DB::query('UPDATE `?w` SET `listing_type_sid` = ?n, `user_sid` = ?n, `keywords` = ?s, ' .
								 '`activation_date` = ' . ($listing->getActivationDate() == null ? 'NOW()' : "'{$listing->getActivationDate()}'") . ' WHERE `sid` = ?n',
						'listings', $listing_type_sid, $listing->getUserSID(), $listing->getKeywords(), $listing->getSID());
		}
		return false;
	}
	
	public static function getListingsNumberByListingTypeSID($listing_type_sid)
	{
		return SJB_DB::queryValue('SELECT COUNT(*) FROM `?w` WHERE `listing_type_sid`=?n', 'listings', $listing_type_sid);
	}

	public static function getListingsNumberByUserSID($user_sid)
	{
		$userContractsSIDs = SJB_ContractManager::getAllContractsSIDsByUserSID($user_sid);
		$userContractsSIDs = $userContractsSIDs ? implode(',', $userContractsSIDs) : 0;
		return SJB_DB::queryValue("SELECT COUNT(*) FROM `listings` WHERE `user_sid` = ?n AND `contract_id` in ({$userContractsSIDs})", $user_sid);
	}

	public static function getActiveAndApproveListingsNumberByUserSID($user_sid)
	{
		$approved = '';
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as $listingType) {
		    if (!empty($approved))
		        $approved .= ' OR ';
		    if ($listingType['waitApprove']) {
		        $approved .= "(`listing_type_sid` = {$listingType['sid']} AND `status` = 'approved')";
		    }
		    else {
		        $approved .= "(`listing_type_sid` = {$listingType['sid']})";
		    }
		}
		return SJB_DB::queryValue("SELECT COUNT(*) FROM `listings` WHERE `active` = 1 AND `user_sid` = ?n AND ({$approved})", $user_sid);
	}

	public static function getActiveAndApproveJobsNumberForUsers($usersSID, $listingType)
	{
		$approved = '';
		if ($listingType['waitApprove']) {
				$approved .= "`listing_type_sid` = {$listingType['sid']} AND `status` = 'approved'";
		} else {
				$approved .= "`listing_type_sid` = {$listingType['sid']}";
		}
		$results = SJB_DB::query("SELECT COUNT(*) as `count`, `user_sid` FROM `listings` WHERE `user_sid` in (?l) AND {$approved} AND `active` = 1 GROUP BY `user_sid`", $usersSID);

		$users = array();
		foreach ($results as $result) {
			$users[$result['user_sid']] = $result['count'];
		}
		return $users;
	}

	public static function getAllListingSIDs()
	{
		return SJB_DB::query('SELECT `sid`, `sid` as `id` FROM `listings`');
	}

	public static function getListingInfoBySID($listing_sid)
	{
    	return parent::getObjectInfo('listings', $listing_sid);
	}

	public static function getActiveListingsSIDByUserSID($user_sid)
	{
		$listings_info = SJB_DB::query('SELECT * FROM `listings` WHERE `active` = 1 AND `user_sid` = ?n', $user_sid);
		$listings_sid = array();
		foreach ($listings_info as $listing_info)
			$listings_sid[] = $listing_info['sid'];
		return $listings_sid;
	}
	
	public static function getListingsSIDByUserSID($userSid, $subuser = false, $limit = false)
	{
		$subuserFilter = $subuser !== false ? " AND `subuser_sid` = '" . SJB_DB::quote($subuser) . "'" : '';
		$limit = $limit ? ' LIMIT ' . $limit : '';
		
		$query = "SELECT `sid` FROM `listings` WHERE `user_sid` = {$userSid}" . $subuserFilter . $limit;
		$cache = SJB_Cache::getInstance();
		if ($cache->test(md5($query))) {
			$listings_info = $cache->load(md5($query));
		} else {
			$listings_info = SJB_DB::query('SELECT `sid` FROM `listings` WHERE `user_sid` = ?n ' . $subuserFilter . $limit, $userSid);
			$cache->save($listings_info, md5($query), array(SJB_Cache::TAG_LISTINGS));
		}
		$listings_sid = array();
		foreach ($listings_info as $listing_info)
			$listings_sid[] = $listing_info['sid'];
		return $listings_sid;
	}

	public static function activateListingBySID($listing_sid)
	{
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_sid);
		if ($listingInfo['active']) {
			return false;
		}
		
		$extraInfo = $listingInfo['product_info'];
		$featuredPeriod = 0;
		$priorityPeriod = 0;
		if ($extraInfo) {
			$extraInfo = unserialize($extraInfo);
			if ($extraInfo['featured'] && !empty($extraInfo['featured_period'])) {
				$featuredPeriod = $extraInfo['featured_period'];
			}
			if ($extraInfo['priority'] && !empty($extraInfo['priority_period'])) {
				$priorityPeriod = $extraInfo['priority_period'];
			}
		}

		if (SJB_DB::query("UPDATE `listings` SET `active` = 1, `activation_date` = NOW() WHERE `sid` = ?n", $listing_sid)) {
			$numberOfDays = SJB_DB::query('SELECT `number_of_days`, `featured_period`, `priority_period` FROM `listings_active_period` WHERE `listing_sid` = ?n', $listing_sid);
			$numberOfDays = $numberOfDays ? array_pop($numberOfDays) : 0;
			$featuredPeriod = isset($numberOfDays['featured_period']) ? $numberOfDays['featured_period'] : $featuredPeriod;
			$priorityPeriod = isset($numberOfDays['priority_period']) ? $numberOfDays['priority_period'] : $priorityPeriod;
			$numberOfDays = $numberOfDays['number_of_days'];
			$sql = array();
			if ($numberOfDays) {
				$sql[] = " `expiration_date` = NOW() + INTERVAL {$numberOfDays} DAY ";
			}
			if ($featuredPeriod) {
				$sql[] = " `featured_expiration` = NOW() + INTERVAL {$featuredPeriod} DAY ";
			}
			if ($priorityPeriod) {
				$sql[] = " `priority_expiration` = NOW() + INTERVAL {$priorityPeriod} DAY ";
			}
			$sql = implode(', ', $sql);
			if ($sql) {
				SJB_DB::query("UPDATE `listings` SET {$sql} WHERE `sid` = ?n", $listing_sid);
			}
			SJB_ListingManager::activateListingKeywordsBySID($listing_sid);
			return true;
		}
		return false;
	}

	public static function setListingExpirationDateBySid($listing_sid)
	{
		$product_info = SJB_DB::queryValue('SELECT `product_info` FROM `listings` WHERE `sid` = ?n', $listing_sid);
		if (!empty($product_info)) {
			$product_info = unserialize($product_info);
		}
		if (!empty($product_info['listing_duration'])) {
			SJB_DB::queryExec('
				UPDATE `listings` 
				SET `expiration_date` = NOW() + INTERVAL ?n DAY 
				WHERE `sid` = ?n 
				AND (`expiration_date` is NULL OR `expiration_date` < NOW() OR `expiration_date` > (NOW() + INTERVAL ?n DAY))', 
				$product_info['listing_duration'], $listing_sid, $product_info['listing_duration']
			);
		}
		return true;
	}

	public static function deleteListingBySID($listing_sid)
	{
		$listing = SJB_ListingManager::getObjectBySID($listing_sid);
		if (parent::deleteObjectInfoFromDB('listings', $listing_sid))
			SJB_Statistics::addStatistics('deleteListing', $listing->getListingTypeSID(), $listing->getSID());
	}

	public static function deactivateListingBySID($listingSID, $deleteRecordFromActivePeriod = false)
	{
		if (SJB_DB::query('UPDATE `listings` SET `active` = 0 WHERE `sid` = ?n', $listingSID)) {
			if ($deleteRecordFromActivePeriod) {
				SJB_DB::query('DELETE FROM  `listings_active_period` WHERE `listing_sid`=?n', $listingSID);
			} else {
				$numberOfDays = SJB_DB::query('SELECT `number_of_days` FROM `listings_active_period` WHERE `listing_sid` = ?n', $listingSID);
				$expirationDate = SJB_DB::query('SELECT `expiration_date`, `featured_expiration`, `priority_expiration` FROM `listings` WHERE `sid` = ?n', $listingSID);
				$expirationDate = array_pop($expirationDate);
				$featuredExpiration = !empty($expirationDate['featured_expiration']) ? $expirationDate['featured_expiration'] : date('Y-m-d');
				$priorityExpiration = !empty($expirationDate['priority_expiration']) ? $expirationDate['priority_expiration'] : date('Y-m-d');
				$expirationDate = $expirationDate['expiration_date'];
				if ($expirationDate) {
					if ($numberOfDays) {
						SJB_DB::query('UPDATE `listings_active_period` SET `number_of_days` = DATEDIFF(?s, NOW()), `featured_period` = DATEDIFF(?s, NOW()), `priority_period` = DATEDIFF(?s, NOW()) WHERE `listing_sid` = ?n', $expirationDate, $featuredExpiration, $priorityExpiration, $listingSID);
					} else {
						SJB_DB::query('INSERT INTO `listings_active_period` (`listing_sid`, `number_of_days`, `featured_period`, `priority_period`) VALUES (?n, DATEDIFF(?s, NOW()), DATEDIFF(?s, NOW()), DATEDIFF(?s, NOW()))', $listingSID, $expirationDate, $featuredExpiration, $priorityExpiration);
					}
				}
				SJB_ListingManager::deactivateListingKeywordsBySID($listingSID);
			}
			return true;
		}
		return false;
	}
	
	public static function getExpiredListingsSID()
	{
		$listings = SJB_DB::query('SELECT `sid` FROM `listings` WHERE `expiration_date` < NOW() AND `active` = 1');
		if (empty($listings))
			return array();
		$listings_sid = array();
		foreach ($listings as $listing)
			$listings_sid[] = $listing['sid'];
		return $listings_sid;
	}

	public static function getDeactivatedListingsSID()
	{
		$period = SJB_Settings::getSettingByName('period_delete_expired_listings');
		$listings = SJB_DB::query('SELECT `l`.`sid` FROM `listings` `l`
								   LEFT JOIN `listings_active_period` `lap` ON `lap`.`listing_sid` = `l`.`sid`
								   WHERE `l`.`expiration_date` < NOW() - INTERVAL ?n DAY AND `l`.`active` = 0
								   AND (`lap`.`number_of_days` is NULL OR `lap`.`number_of_days` = 0)', $period);
		if (empty($listings))
			return array();
		$listings_sid = array();
		foreach ($listings as $listing)
			$listings_sid[] = $listing['sid'];
		return $listings_sid;
	}

	public static function getIfListingHasExpiredBySID($listingSID)
	{
		$listing = SJB_DB::query('SELECT `sid` FROM `listings` WHERE `expiration_date` < NOW() AND `listings`.`sid` = ?n LIMIT 1', $listingSID );
		if (!empty($listing))
			return true;
		return false;
	}

	public static function getUserSIDByListingSID($listing_sid)
	{
		return SJB_DB::queryValue('SELECT `user_sid` FROM `listings` WHERE `sid` = ?n', $listing_sid);
	}

	/**
	 * Save listing keyword
	 * 
	 * @param string $keyword
	 * @param int $listing_sid
	 * @param bool $active
	 */
	public static function saveListingKeyword($keyword, $listing_sid, $active = false)
	{
		if (!empty($keyword) && !SJB_ListingDBManager::isKeywordExistsByListingSID($listing_sid, $keyword)) {
			SJB_DB::query('INSERT INTO `listings_keywords` (`keywords`, `listing_sid`, `active`) VALUES (?s, ?n, ?n)', $keyword, $listing_sid, $active);
		}
	}

	/**
	 * 
	 * Check whether keyword is already exists by listing SID
	 * @param unknown_type $listing_sid
	 */
	public static function isKeywordExistsByListingSID($listing_sid, $keyword)
	{
		$keywords = SJB_DB::query('SELECT `keywords` FROM `listings_keywords` WHERE `listing_sid` = ?n AND `keywords` = ?s', $listing_sid, $keyword);
		return !empty($keywords);
	}
	
	/**
	 * delete keywords listing keywords
	 *
	 * @param int $listing_sid
	 */
	public static function deleteListingKeywords($listing_sid)
	{
		if (!empty($listing_sid))
			SJB_DB::query('DELETE FROM `listings_keywords` WHERE `listing_sid` = ?n', $listing_sid);
	}

	public static function getListingKeywordsArray($listing)
	{
		$properties = $listing->getProperties();
		$keywords = array();
		foreach ($properties as $property) {
			$property_value = $property->getKeywordValueForAutocomplete();
			if (!empty($property_value)) {
				if (is_array($property_value)) {
					foreach($property_value as $value) {
						if (!empty($value))
							$keywords[] = trim($value);
					}
				} elseif (!empty($property_value)) {
					$keywords[] = $property_value;
				}
			}
		}
		array_unique($keywords);
		return $keywords;
	}

	public static function getAllPreviewListingsByUserSID($userSID) 
	{
		return SJB_DB::query("SELECT * FROM `listings` WHERE `user_sid` = ?n AND `preview` = 1", $userSID);
	}

	public static function getUserKeywords($user_info)
	{
		if (!empty($user_info['CompanyName']))
			return $user_info['CompanyName'];
		return false;
	}

	public static function activateListingKeywordsBySID($listingSID)
	{
		return SJB_DB::query("UPDATE `listings_keywords` SET `active` = 1 WHERE `listing_sid` = ?s", $listingSID);
	}

	public static function deactivateListingKeywordsBySID($listingSID)
	{
		return SJB_DB::query("UPDATE `listings_keywords` SET `active` = 0 WHERE `listing_sid` = ?s", $listingSID);
	}

	public static function getNumberOfCheckoutedListingsByProductSID($productSID, $currentUserID)
	{
		$serializedProductSID = SJB_ProductsManager::generateQueryBySID($productSID);
		return SJB_DB::queryValue("SELECT COUNT(`sid`) FROM `listings` WHERE `checkouted` = 0 AND `complete` = 1 AND `contract_id` = 0 AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSID})'", $currentUserID);
	}

	/**
	 * @param string $permissionName
	 * @param int    $listingSID
	 * @return string
	 */
	public static function getPermissionByListingSid($permissionName, $listingSID)
	{
		$listingInfo        = SJB_ListingManager::getListingInfoBySID($listingSID);
		$productInfo        = unserialize($listingInfo['product_info']);
		$productPermissions = SJB_Acl::getInstance()->getPermissions('product', $productInfo['product_sid']);
		return isset($productPermissions[$permissionName]) ? $productPermissions[$permissionName]['value'] : 'inherit';
	}
}
