<?php

class SJB_RefineSearch
{
	public static function addField($field_id, $listing_type_sid, $userField=0)
	{
		$max_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM `refine_search` WHERE listing_type_sid = ?n", $listing_type_sid);
		$max_order = empty($max_order) ? 0 : $max_order;
		return SJB_DB::query("INSERT INTO `refine_search` (`field_id`,`listing_type_sid`,`order`, `user_field`) VALUES (?n,?n,?n,?n)",$field_id, $listing_type_sid, ++$max_order, $userField);
	}
	
	public static function removeField($field_sid)
	{
		return SJB_DB::query("DELETE FROM `refine_search` WHERE `id`=?n", $field_sid);
	}
	
	public static function  moveUpFieldBySID($field_sid, $listing_type_sid) 
	{
		$field_info = SJB_DB::query("SELECT * FROM `refine_search` WHERE  `id` = ?n", $field_sid);
		if (empty($field_info)) return false;
		
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		
		$up_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM `refine_search` WHERE `listing_type_sid` = ?n AND `order` < ?n", $listing_type_sid, $current_order);
		if ($up_order == 0)
			return false;
		
		SJB_DB::query("UPDATE `refine_search` SET `order` = ?n WHERE `order` = ?n AND `listing_type_sid` = ?n", $current_order, $up_order, $listing_type_sid);
		SJB_DB::query("UPDATE `refine_search` SET `order` = ?n WHERE id = ?n", $up_order, $field_sid);
		return true;
	}

	public static function moveDownFieldBySID($field_sid, $listing_type_sid) 
	{
		$field_info = SJB_DB::query("SELECT * FROM `refine_search` WHERE id = ?n", $field_sid);
		if (empty($field_info))
			return false;
		
		$field_info = array_pop($field_info);
		$current_order = $field_info['order'];
		
		$less_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM `refine_search` WHERE `listing_type_sid` = ?n AND `order` > ?n", $listing_type_sid, $current_order);
		if ($less_order == 0)
			return false;
		
		SJB_DB::query("UPDATE `refine_search` SET `order` = ?n WHERE `order` = ?n AND `listing_type_sid` = ?n",$current_order, $less_order, $listing_type_sid);
		SJB_DB::query("UPDATE `refine_search` SET `order` = ?n WHERE `id` = ?n", $less_order, $field_sid);
		
		return true;
	}
	
	public static function getFieldsByListingTypeSID($listing_type_sid)
	{
		$listingFields = SJB_DB::query("SELECT rs.*, lf.`id` as field_name, `lf`.`caption`, `lf`.`type`, lf.`parent_sid` FROM `refine_search` rs INNER JOIN `listing_fields` lf ON rs.`field_id`=lf.`sid` WHERE rs.`listing_type_sid`=?n  AND rs.`user_field`=0 ORDER BY `order` ASC", $listing_type_sid);
		$userFields = SJB_DB::query("SELECT rs.*, uf.`id` as field_name,`uf`.`caption`, `uf`.`type`, uf.`parent_sid` FROM `refine_search` rs INNER JOIN `user_profile_fields` uf ON rs.`field_id`=uf.`sid` WHERE rs.`listing_type_sid`=?n  AND rs.`user_field`=1 ORDER BY `order` ASC", $listing_type_sid);
		$fields = array_merge($listingFields, $userFields);
		$result = array();
		foreach ($fields as $field) {
			$result[$field['order']] = $field;
		}
		ksort($result);
		return $result;
	}
	
	public static function getFieldByFieldSIDListingTypeSID($field_id, $listing_type_sid, $userField=0) 
	{
		return SJB_DB::query("SELECT * FROM `refine_search` WHERE `listing_type_sid`=?n AND `field_id`=?n AND `user_field`=?n", $listing_type_sid, $field_id, $userField);
	}

	/**
	 * @param $fieldName
	 * @param $fieldID
	 * @param SJB_SearchResultsTP $stp
	 * @param $userField
	 * @return mixed
	 */
	public static function countListingsByFieldName($fieldName, $fieldID, $stp, $userField)
	{
		$refineSearchLimit = SJB_Settings::getSettingByName('refine_search_items_limit');
		$limit = $refineSearchLimit ? ' LIMIT 0, ' . $refineSearchLimit : '';

		$listing = new SJB_Listing(array(), $stp->listing_type_sid);
		$id_alias_info = $listing->addIDProperty();
		$listing->addActivationDateProperty();
		$listing->addFeaturedProperty();
		$username_alias_info = $listing->addUsernameProperty();
		$listing_type_id_info = $listing->addListingTypeIDProperty();
		$listing->addCompanyNameProperty();
		$requestedCriteria = $stp->criteria_saver->getCriteria();
		if (isset($requestedCriteria['PostedWithin']) && $requestedCriteria['PostedWithin']['multi_like'][0] != '') {
			$within_period = $requestedCriteria['PostedWithin']['multi_like'][0];
			$i18n = SJB_I18N::getInstance();
			$requestedCriteria['activation_date']['not_less'] = $i18n->getDate(date('Y-m-d', strtotime("- {$within_period} days")));
			unset ($requestedCriteria['PostedWithin']);
		}
		if (isset($requestedCriteria['CompanyName']['multi_like_and'][0])) {
			$userName = SJB_UserManager::getUserNameByCompanyName($requestedCriteria['CompanyName']['multi_like_and'][0]);
			unset($requestedCriteria['CompanyName']);
			if ($userName) {
				$requestedCriteria['username']['equal'] = $userName;
			}
		}
		$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($requestedCriteria, $listing);
		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($id_alias_info);
		$aliases->addAlias($username_alias_info);
		$aliases->addAlias($listing_type_id_info);
        $aliases->changeAliasValuesInCriteria($criteria);
		$sqlTranslator = new SJB_SearchSqlTranslator('listings');

		$whereStatement = $sqlTranslator->_getWhereStatement($criteria);

		$objectSids = implode(',', $stp->found_listings_sids);
		if ($userField == 1)
			$field = SJB_UserProfileFieldManager::getFieldInfoBySID($fieldID);
		else
			$field = SJB_ListingFieldDBManager::getListingFieldInfoBySID($fieldID);
		$result = array();
		$cache = SJB_Cache::getInstance();

		if (!empty($field['parent_sid'])) {
			$parentInfo = SJB_ListingFieldManager::getFieldInfoBySID($field['parent_sid']);
			$fieldName = $parentInfo['id']."_".$fieldName;
			$field['id'] = $fieldName;
			$field['parentID'] = $parentInfo['id'];
		}
		
		switch ($field['type']) {
			case 'list':
			case 'multilist':
				if ($userField == 1) {
					$query = "SELECT up.`{$fieldName}` as caption, count(`listings`.`sid`) as count
																 FROM `listings`
																 INNER JOIN `users` `up` ON `listings`.`user_sid` = `up`.`sid`
																 {$whereStatement}
																 AND up.`{$fieldName}` != ''
																 GROUP BY `up`.`{$fieldName}` ORDER BY count DESC";
					if (!$result = $cache->load(md5($query))) {
						$result = SJB_DB::query($query);
						$cache->save($result, md5($query), array(SJB_Cache::TAG_LISTINGS, SJB_Cache::TAG_USERS));
					}
				}
				else {
					$query = "SELECT `{$fieldName}` as caption, count(`{$fieldName}`) as count FROM `listings` {$whereStatement} AND `{$fieldName}` != '' GROUP BY `{$fieldName}` ORDER BY count DESC";
					if (!$result = $cache->load(md5($query))) {
						$result = SJB_DB::query($query);
						$cache->save($result, md5($query), array(SJB_Cache::TAG_LISTINGS));
					}
                }
				self::breakMultiCategory($result);
				$newResult = array();
				$listItem = new SJB_ListingFieldListItemManager();
				foreach ($result as $key => $val) {
					if (!empty($field['parent_sid'])) {
						$caption = '';
						if ($field['id'] == $field['parentID'].'_State') 
							$listValues = SJB_StatesManager::getStatesNamesByCountry(false, true, $field['display_as']);
						else
							$listValues = $field['list_values'];
						foreach ($listValues as $listValue) {
							if ($listValue['id'] == $val['caption']) {
								$caption = $listValue['caption'];
								break;
							}
						}
					} else {
						$itemInfo = $listItem->getListItemBySID($val['caption']);
						$caption = $itemInfo ? $itemInfo->getValue() : null;
					}
					if ($caption != null) {
						$newResult[$key]['count'] = $val['count'];
						$newResult[$key]['value'] = $caption;
						$newResult[$key]['sid'] = $val['caption'];
					}
				}
				arsort($newResult);
				$result = $newResult;
				if (count($result) > $refineSearchLimit) {
					$result = array_slice($result, 0, $refineSearchLimit);
				}
				break;
			case 'tree':
				$query = "SELECT `lt`.`sid` as `sid`, `lt`.`caption` as `value`, count(`listings`.`sid`) as `count`
																	FROM `listings`
																	LEFT JOIN `listing_field_tree` `lt` ON `lt`.`field_sid` = {$field['sid']} AND find_in_set(`lt`.`sid`, `listings`.`{$fieldName}`)
																	{$whereStatement} GROUP BY `lt`.`sid` having `lt`.`sid` IS NOT NULL ORDER BY `count` DESC {$limit}";
				if (!$propertyValue = $cache->load(md5($query))) {
					$propertyValue = SJB_DB::query($query);
					$cache->save($propertyValue, md5($query), array(SJB_Cache::TAG_LISTINGS, SJB_Cache::TAG_FIELDS));
				}
				foreach ($propertyValue as $value)
					$result[$value['sid']] = $value;
				break;
			default:
				if ($userField == 1) {
					$companyColumn = "up.`{$fieldName}`";
					$query = "SELECT {$companyColumn} as `value`, count(listings.`sid`) as `count`
											 FROM `listings`
											 INNER JOIN `users` `up` ON `listings`.`user_sid` = `up`.`sid`
											 {$whereStatement}
											 GROUP BY {$companyColumn} ORDER BY `count` DESC {$limit}";
					if (!$result = $cache->load(md5($query))) {
						$result = SJB_DB::query($query);
						$cache->save($result, md5($query), array(SJB_Cache::TAG_LISTINGS, SJB_Cache::TAG_USERS));
					}
				} else {
					if ($field['type'] == 'complex')
						$query = "SELECT `value`, count(`value`) as count FROM `listings_properties` WHERE `id`='{$fieldName}' AND `value` != '' AND `object_sid` in ({$objectSids}) GROUP BY `value` ORDER BY count DESC {$limit}";
					else 
						$query = "SELECT `{$fieldName}` as value, count(`{$fieldName}`) as count FROM `listings` {$whereStatement} AND `{$fieldName}` != '' GROUP BY `{$fieldName}` ORDER BY count DESC {$limit}";
					if (!$result = $cache->load(md5($query))) {
						$result = SJB_DB::query($query);
						$cache->save($result, md5($query), array(SJB_Cache::TAG_LISTINGS));
					}
				}
				break;
		}
		$returnArr['caption'] = $field['caption'];
		$returnArr['values'] = $result;
		return $returnArr;
	}
	
	public static function getCurrentSearchByCriteria($criteria)
	{
		$returnArray = array();
		$locationFields = SJB_ListingFieldManager::getFieldsInfoByType('location');
		foreach ($criteria as $fieldName => $field) {
			if (!in_array($fieldName, array('listing_type', 'active', 'username', 'status', 'CompanyName', 'keywords', 'PostedWithin', 'anonymous'))) {
				$result = array();
				$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($fieldName);
				if (!$fieldInfo) {
					foreach ($locationFields as $locationField) {
						$locationSubFields = SJB_ListingFieldManager::getListingFieldsInfoByParentSID($locationField['sid']);
						foreach ($locationSubFields as $locationSubField) {
							if ($fieldName == $locationField['id']."_".$locationSubField['id']) {
								$fieldInfo = $locationSubField;
								$fieldInfo['id'] = $locationField['id']."_".$locationSubField['id'];
							}
						}
					}
				}
				
				foreach ($field as $fieldType => $fieldValue) {
					switch ($fieldType) {
						case 'geo':
							if ($fieldValue['location'] !== '')
								$result[$fieldName][$fieldType][$fieldValue['location']] = $fieldValue['location'];
							break;
						case 'location':
							if (!empty($fieldValue['value']))
								$result[$fieldName][$fieldType][$fieldValue['value']] = $fieldValue['value'];
							break;
						case 'monetary':
							if (!empty($fieldValue['not_less']) && $fieldValue['not_less'] !== '')
								$result[$fieldName][$fieldType][$fieldValue['not_less']] = $fieldValue['not_less'];
							if (!empty($fieldValue['not_more']) && $fieldValue['not_more'] !== '')
								$result[$fieldName][$fieldType][$fieldValue['not_more']] = $fieldValue['not_more'];
							break;
						case 'multi_like':
							$listItem = new SJB_ListingFieldListItemManager();
							if (is_array($fieldValue)) {
								foreach ($fieldValue as $value) {
									if ($value !== '')  {
										if ($fieldInfo['type'] == 'tree') {
											$name = SJB_DB::queryValue("SELECT `caption` FROM `listing_field_tree` WHERE `sid` = '{$value}'");
											$name = $name ? $name : '';
											$result[$fieldName][$fieldType][$value] = $name;
										}
										elseif ($fieldInfo['type'] == 'multilist' || $fieldInfo['type'] == 'list') {
											if (!empty($fieldInfo['parent_sid'])) {
												if ($fieldInfo['id'] == $fieldInfo['parentID'] . '_State') {
													$listValues = SJB_StatesManager::getStatesNamesByCountry(false, true, $fieldInfo['display_as']);
												} else {
													$listValues = $fieldInfo['list_values'];
												}
												foreach ($listValues as $listValue) {
													if ($listValue['id'] == $value) {
														$result[$fieldName][$fieldType][$value] = $listValue['caption'];
														break;
													}
												}
											} else {
												$itemInfo = $listItem->getListItemBySID($value);
												$caption = $itemInfo ? $itemInfo->getValue() : $value;
												$result[$fieldName][$fieldType][$value] = $caption;
											}
										} else {
											$result[$fieldName][$fieldType][$value] = $value;
										}
									}
								}
							}
							elseif ($fieldValue !== '')	{
								$itemInfo = $listItem->getListItemBySID($fieldValue);
								$caption = $itemInfo ? $itemInfo->getValue() : $fieldValue;
								$result[$fieldName][$fieldType][$fieldValue] = $caption;
							}
							break;
						case 'tree':
							$fieldValue = $fieldValue?explode(',', $fieldValue):"";
							if (is_array($fieldValue)) {
								foreach ($fieldValue as $value) {
									if ($value !== '') {
										$name = SJB_DB::queryValue("SELECT `caption` FROM `listing_field_tree` WHERE `sid` = '{$value}'");
										$name = $name ? $name : '';
										$result[$fieldName][$fieldType][$value] = $name;
									}
								}
							}
							break;
						case 'multi_like_and':
							if (is_array($fieldValue)) {
								$listItem = new SJB_ListingFieldListItemManager();
								foreach ($fieldValue as $value) {
									if ($value !== '') {
										if ($fieldInfo['type'] == 'tree') {
											$name = SJB_DB::queryValue("SELECT `caption` FROM `listing_field_tree` WHERE `sid` = '{$value}'");
											$name = $name ? $name : '';
											$result[$fieldName][$fieldType][$value] = $name;
										}
										elseif ($fieldInfo['type'] == 'multilist' || $fieldInfo['type'] == 'list') {
											if (!empty($fieldInfo['parent_sid'])) {
												if ($fieldInfo['id'] == $fieldInfo['parentID'].'_State') 
													$listValues = SJB_StatesManager::getStatesNamesByCountry(false, true, $fieldInfo['display_as']);
												else
													$listValues = $fieldInfo['list_values'];
												foreach ($listValues as $listValue) {
													if ($listValue['id'] == $value) {
														$result[$fieldName][$fieldType][$value] = $listValue['caption'];
														break;
													}
												}
											}
											else {
												$itemInfo = $listItem->getListItemBySID($value);
												$caption = $itemInfo?$itemInfo->getValue():$value;
												$result[$fieldName][$fieldType][$value] = $caption;
											}
										}
										else 
											$result[$fieldName][$fieldType][$value] = $value;
									}
								}
							}
							elseif ($fieldValue !== '')	
								$result[$fieldName][$fieldType][$fieldValue] = $fieldValue;
							break;
						default:
							if (is_array($fieldValue)) {
								foreach ($fieldValue as $value) {
									if ($value !== '') 
										$result[$fieldName][$fieldType][$value] = $value;
								}
							}
							elseif ($fieldValue !== '')	
								$result[$fieldName][$fieldType][$fieldValue] = $fieldValue;
							break;
					}
				}
				if ($result && !empty($fieldInfo)) {
					$returnArray[$fieldInfo['id']]['name'] = $fieldInfo['caption'];
					$returnArray[$fieldInfo['id']]['field'] = $result[$fieldInfo['id']];
				}
			}
			elseif ($fieldName == 'CompanyName') {
				$result = array();
				$userFieldSID = SJB_DB::queryValue("SELECT `sid` FROM `user_profile_fields` WHERE `id` = 'CompanyName'");
				if ($userFieldSID) {
					$fieldInfo = SJB_UserProfileFieldManager::getFieldInfoBySID($userFieldSID);
					foreach ($field as $fieldType => $fieldValue) {
						switch ($fieldType) {
							case 'multi_like_and':
								if (is_array($fieldValue)) {
									foreach ($fieldValue as $value) {
										if ($value !== '') {
											$result[$fieldName][$fieldType][$value] = $value;
										}
									}
								}
								elseif ($fieldValue !== '')	
									$result[$fieldName][$fieldType][$fieldValue] = $fieldValue;
								break;
						}
					}
				}
				if ($result && !empty($fieldInfo)) {
					$returnArray[$fieldInfo['id']]['name'] = $fieldInfo['caption'];
					$returnArray[$fieldInfo['id']]['field'] = $result[$fieldInfo['id']];
                }
			}
			elseif ( $fieldName == 'keywords') {
				foreach ($field as $key => $val) {
					if ($val) {
						$returnArray['keywords']['field'][$key][$val] = $val;
					}
				}
				if (isset($returnArray['keywords']))
					$returnArray['keywords']['name'] = 'Keywords';
			}
		}
		return $returnArray;
	}
	
	private static function breakMultiCategory(&$catArray) 
	{
		$keys = array_keys($catArray);
		foreach ($keys as $key) {
			if ( strpos($catArray[$key]['caption'], ",") !== false ) {
				$categories = explode(",", $catArray[$key]['caption']);
				$counter = $catArray[$key]['count'];
				foreach ($categories as $category) {
					self::updateCountCategory($catArray, trim($category), $counter);
				}
				unset($catArray[$key]);
			}
		}
	}
	
	private static function updateCountCategory(&$catArray, $category, $counter) 
	{
		$inc = 0;
		foreach ($catArray as $key => $elem) {
			if ($elem['caption'] == $category) {
				$elem['count'] += $counter;
				$catArray[$key] = $elem;
				$inc	= 1;
			}
		}
		if ($inc == 0) {
			$catArray[] = array('caption' => $category, 'count' => $counter);
		}
	}

	public static function getRefineFieldsByCriteria(SJB_SearchResultsTP $searchResultsTP, $searchCriteria)
	{
		$refineFields = SJB_RefineSearch::getFieldsByListingTypeSID($searchResultsTP->listing_type_sid);

		foreach ($refineFields as $refineFieldKey => &$refineField) {
			$fieldName = $refineField['field_name'];
			if (!empty($refineField['parent_sid'])) {
				$parentID = SJB_ListingFieldManager::getListingFieldIDBySID($refineField['parent_sid']);
				$refineField['field_name'] = $parentID . '_' . $fieldName;
			}
			$criteria = self::getSearchCriteriaByField($searchCriteria, $refineField['field_name']);
			$foundListingsByFieldName = self::getFieldValues($refineField, $criteria, $searchResultsTP, $fieldName);
			$refineField['criteria'] = $criteria;
			$refineField['caption'] = $foundListingsByFieldName['caption'];

			self::markToShowOrNot($foundListingsByFieldName['values'], $refineField, $criteria);

			if (empty($foundListingsByFieldName['values'])) {
				unset($refineFields[$refineFieldKey]);
			} else {
				$refineField['search_result'] = $foundListingsByFieldName['values'];
				$refineField['count_results'] = count($foundListingsByFieldName['values']);
			}
		}

		return $refineFields;
	}

	public static function getFieldValues($refineField, $criteria, SJB_SearchResultsTP $searchResultsTP, $fieldName)
	{
		$isMultiSelectFieldType   = in_array($refineField['type'], array('multilist', 'tree'));
		$foundListingsByFieldName = array();

		if ($isMultiSelectFieldType && !empty($criteria)) {
			$foundListingsByFieldName = $searchResultsTP->criteria_saver->getSessionForRefine($refineField['field_name']);
		}

		if (empty($foundListingsByFieldName)) {
			$foundListingsByFieldName = SJB_RefineSearch::countListingsByFieldName($fieldName, $refineField['field_id'], $searchResultsTP, $refineField['user_field']);
		}

		if ($isMultiSelectFieldType && empty($criteria)) {
			$searchResultsTP->criteria_saver->setSessionForRefine($refineField['field_name'], $foundListingsByFieldName);
		}
		return $foundListingsByFieldName;
	}

	public static function getSearchCriteriaByField($searchCriteria, $fieldName)
	{
		$fieldCriteria = isset($searchCriteria[$fieldName]) ? $searchCriteria[$fieldName] : false;

		$criteria = array();
		if ($fieldCriteria) {
			$arrayPopped = array_pop($fieldCriteria);
			if (!is_array($arrayPopped) || (is_array($arrayPopped) && count($arrayPopped) > 1)) {
				$criteria = $arrayPopped;
			} elseif (is_array($arrayPopped)) {
				$criteria = array_pop($arrayPopped);
			}
		}
		return $criteria;
	}

	public static function markToShowOrNot(&$foundListingsByFieldName, &$refineField, $criteria)
	{
		foreach ($foundListingsByFieldName as $key => &$elem) {
			if (in_array($refineField['type'], array('multilist', 'tree', 'list'))) {
				if (is_array($criteria) && in_array($elem['sid'], $criteria)) {
					unset($foundListingsByFieldName[$key]);
					continue;
				} elseif ($elem['sid'] != $criteria) {
					$elem['show'] = 1;
				} else {
					unset($foundListingsByFieldName[$key]);
					continue;
				}
			} elseif (in_array($refineField['type'], array('string', 'integer', 'float', 'text', 'boolean'))) {
				if ($elem['value'] != $criteria) {
					$elem['show'] = 1;
				} else {
					unset($foundListingsByFieldName[$key]);
					continue;
				}
			}

			if (!is_array($criteria)) {
				$refineField['show'] = 1;
				continue;
			}

			$elemSID = isset($elem['sid']) ? $elem['sid'] : null;
			if (isset($elem['value'])
					&& !in_array($elem['value'], $criteria)
					&& (!$elemSID || ($elemSID && !in_array($elemSID, $criteria)))
			) {
				$refineField['show'] = 1;
			}
		}
	}

	public static function mergeCriteria($criteriaOld, $criteriaNew)
	{
		foreach ($criteriaNew as $criteriaID => &$criteriaVal) {
			if (isset($criteriaVal['multi_like']) && isset($criteriaOld[$criteriaID], $criteriaOld[$criteriaID]['multi_like'])) {
				$criteriaVal['multi_like'] = array_merge($criteriaOld[$criteriaID]['multi_like'], $criteriaVal['multi_like']);
				unset($criteriaOld[$criteriaID]);
			}
		}
		return array_merge($criteriaOld, $criteriaNew);
	}
}
