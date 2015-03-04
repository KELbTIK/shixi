<?php

class SJB_Statistics
{
	public static function addStatistics($event, $type = '', $objectSID = 0, $unique = false, $featured = 0, $priority = 0, $userSID = false, $price = 0, $plugin = '', $reactivate = 0)
	{
		if (!$userSID) {
			$userSID = SJB_UserManager::getCurrentUserSID();	
			$userSID = $userSID?$userSID:0;
		}
		$IP = $_SERVER['REMOTE_ADDR'];

		$params = array(
			'ip' => $IP,
			'type' => $type,
			'event' => $event,
			'date' => 'YEAR(CURDATE()) = YEAR(`date`) AND DAYOFYEAR(CURDATE()) = DAYOFYEAR(`date`)',
			'object_sid' => $objectSID,
			'limit' => 1,
			'price' => $price
		);
		if (!in_array($event, array('siteView', 'viewMobileVersion'))) {
			$params['user_sid'] = $userSID;
		}
		$browsingEvents = array('viewListing', 'siteView', 'partneringSites', 'showInSearchResults');
		if (SJB_Request::isBot() && in_array($event, $browsingEvents)) {
			return false;
		} else {
			if ($statistics = self::getStatistics($params)) {
				$statistics = array_pop($statistics);
				if (!$unique) {
					SJB_DB::query("UPDATE `statistics` SET `count` = ?n WHERE `sid` = ?n", ++$statistics['count'], $statistics['sid']);
				}
				elseif ($userSID && $statistics['user_sid'] == 0) {
					SJB_DB::query("UPDATE `statistics` SET `user_sid` = ?n WHERE `sid` = ?n", $userSID, $statistics['sid']);
				}
			} else {
				SJB_DB::query("INSERT INTO `statistics` (`user_sid`, `ip`, `event`, `object_sid`, `type`, `date`, `featured`, `priority`, `reactivate`, `price`, `plugin`) VALUES (?n, ?s, ?s, ?n, ?s, NOW(), ?n, ?n, ?n, ?f, ?s)", $userSID, $IP, $event, $objectSID, $type, $featured, $priority, $reactivate, $price, $plugin);
			}
			return true;
		}
	}

	public static function addStatisticsFromInvoice(SJB_Invoice $invoice)
	{
		$items = $invoice->getItemsInfo();
		if (empty($items)) {
			$items = $invoice->getPropertyValue('items');
		}
		
		foreach ($items['products'] as $key => $productSID) {
			$featured = 0;
			$priority = 0;
			$activate = 0;
			$type     = 'product';
			if ($productSID == -1) {
				$productSID = isset($items['custom_info'][$key]['productSid']) ? $items['custom_info'][$key]['productSid'] : $invoice->getSID();
				$customType = $items['custom_info'][$key]['type'];
				switch ($customType) {
					case 'activateListing':
						$activate = 1;
						break;
					case 'priorityListing':
						$priority = 1;
						break;
					case 'featuredListing':
						$featured = 1;
						break;
					default:
						$type = $customType;
				}
			}
			
			self::addStatistics('payment', $type, $productSID, false, $featured, $priority, $invoice->getUserSID(), $items['amount'][$key], 0, $activate);
		}
	}

	/**
	 * Used to add listings from the search results, browse and featured block into statistics
	 * @param $listingSIDs
	 * @param $listingTypeId
	 */
	public static function addSearchStatistics($listingSIDs, $listingTypeId)
	{
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeId);
		if ($listingSIDs) {
			foreach ($listingSIDs as $listingSID) {
				SJB_Statistics::addStatistics('showInSearchResults', $listingTypeSID, $listingSID);
			}
		}
	}

	public static function deleteStatistics($event, $type, $objectSID, $userSID, $price)
	{
		SJB_DB::query("DELETE FROM `statistics` WHERE  `event` = ?s AND `type` = ?s AND `object_sid` = ?n AND `user_sid` = ?n AND `price` = ?s", $event, $type, $objectSID, $userSID, $price);	
	}
	
	public static function getStatistics($params)
	{
		$where = 'WHERE 1 ';
		$limit = '';
		foreach ($params as $name => $value) {
			switch ($name) {
				case 'date':
					if (is_array($value))
						$where .= " AND `{$name}` = '".SJB_DB::quote($value)."' ";
					else
						$where .= " AND ".SJB_DB::quote($value)." ";
					break;
				case 'limit':
					$limit = " LIMIT {$value}";
					break;
				default:
					$where .= " AND `{$name}` = '".SJB_DB::quote($value)."' ";
			}
		}

		return SJB_DB::query("SELECT `sid`, `count`, `user_sid` FROM `statistics` {$where} {$limit}");
	}
	
	public static function getGeneralStatistics($period = false, $groupBy = 'day', $filter = array())
	{
		$where = 'WHERE 1 ';
		$query = '';
		if (!is_array($period) || (empty($period['from']) && empty($period['to']))) {
			switch ($groupBy) {
				case 'day':
					$where .= " AND `date` >= NOW() - INTERVAL 10 DAY  AND `date` <= NOW() ";
					$period['from'] = date('Y-m-d', strtotime("-9 day"));
				break;
				case 'month':
					$where .= " AND `date` >= NOW() - INTERVAL 12 MONTH  AND `date` <= NOW() ";
					$period['from'] = date('Y-m-d', strtotime("-11 month"));
					break;
				case 'quarter':
					$quarter = ceil(date('n')/3);
					$monthNum = 11;
					for ($i = 11; $i > 8; $i--) {
						if (ceil(date('n', strtotime("-{$i} month"))/3) > $quarter){
							$monthNum = $i;
							break;
						}
					}
					$where .= " AND `date` >= NOW() - INTERVAL {$monthNum} MONTH  AND `date` <= NOW() ";
					$monthNum--;
					$period['from'] = date('Y-m-d', strtotime("-{$monthNum} month"));
					break;
				case 'year':
					$where .= " AND `date` >= NOW() - INTERVAL 10 YEAR  AND `date` <= NOW() ";
					$period['from'] = date('Y-m-d', strtotime("-9 year"));
					break;
			}
			$period['to'] = date('Y-m-d');
		}
		else {
			if (!empty($period['from'])) {
				$period['from'] = SJB_I18N::getInstance()->getInput('date', $period['from']);
				$time = "00:00:00";
				$where .= " AND `date` >= '{$period['from']} {$time}' ";
			}
			if (!empty($period['to'])) {
				$period['to'] = SJB_I18N::getInstance()->getInput('date', $period['to']);
				$time = "23:59:59";
				$where .= " AND `date` <= '{$period['to']} {$time}' ";
			}
			else
				$period['to'] = date('Y-m-d');
		}
		
		$key = 'groupValue';
		switch ($groupBy) {
			case 'day':
				$key = 'date';
				$query = " CONCAT(YEAR(`date`), DAYOFYEAR(`date`)) as groupValue ";
				break;
			case 'month':
				$query = " EXTRACT(YEAR_MONTH FROM `date`) as groupValue ";
				break;
			case 'quarter':
				$query = " CONCAT(YEAR(`date`), QUARTER(`date`)) as groupValue ";
				break;
			case 'year':
				$query = " YEAR(`date`) as groupValue";
				break;
		}
		$eventAccess = array();
		foreach ($filter as $blockName => $val) {
			switch ($blockName) {
				case 'popularity':
					$eventAccess[] = 'siteView';
					$eventAccess[] = 'viewListing';
					break;
				case 'users':
					$eventAccess[] = 'addUser';
					$eventAccess[] = 'addSubAccount';
					$eventAccess[] = 'deleteUser';
					break;
				case 'listings':
					$eventAccess[] = 'addListing';
					$eventAccess[] = 'deleteListing';
					break;
				case 'applications':
					$eventAccess[] = 'apply';
					break;
				case 'alerts':
					$eventAccess[] = 'addAlert';
					$eventAccess[] = 'sentAlert';
					$eventAccess[] = SJB_GuestAlertStatistics::EVENT_SENT;
					$eventAccess[] = SJB_GuestAlertStatistics::EVENT_SUBSCRIBED;
					break;
				case 'sales':
					$eventAccess[] = 'payment';
					$eventAccess[] = SJB_PromotionsManager::STATISTIC_EVENT;
					break;
				case 'plugins':
					$socialPlugins = SJB_SocialPlugin::getAvailablePlugins();
					foreach ($socialPlugins as $socialPluginName) {
						$eventAccess[] = 'addUser' . $socialPluginName;
					}
					$eventAccess[] = 'viewMobileVersion';
					$eventAccess[] = 'partneringSites';
					break;
			}
		}
		
		if ($eventAccess) {
			$eventsSet = "'".implode(",", $eventAccess)."'";
			$where .= " AND FIND_IN_SET(`event`, {$eventsSet}) ";
		}
		
		$statisticsInfo = SJB_DB::query("SELECT *, sum(`count`) as count, {$query}  FROM `statistics` {$where} GROUP BY `event`, `type`, groupValue ORDER BY `date`");
		$statisticsInfoTotal = SJB_DB::query("SELECT *, sum(`count`) as count, 'total'  as groupValue FROM `statistics` {$where} GROUP BY `event`, `type`");
		$statisticsInfo = array_merge($statisticsInfo, $statisticsInfoTotal);
		$totalPayment = SJB_DB::query("SELECT *, sum(`price`) as count, 'totalAmount' as `event`, {$query}  FROM `statistics` {$where} AND `event` = 'payment' GROUP BY groupValue ORDER BY `date`");
		$totalPaymentTotal = SJB_DB::query("SELECT *, sum(`price`) as count, 'totalAmount' as `event`, 'total'  as groupValue  FROM `statistics` {$where} AND `event` = 'payment' GROUP BY groupValue ORDER BY `date`");
		$totalPayment = array_merge($totalPayment, $totalPaymentTotal);

		$promotionEvent = SJB_PromotionsManager::STATISTIC_EVENT;
		$promotions = SJB_DB::query("SELECT *, sum(`price`) as count, '{$promotionEvent}' as `event`, {$query}
										FROM `statistics` {$where} AND `event` = '{$promotionEvent}'
										GROUP BY groupValue ORDER BY `date`");
		$promotionsTotal = SJB_DB::query("SELECT *, sum(`price`) as count, '{$promotionEvent}' as `event`, 'total'  as `groupValue`
											FROM `statistics` {$where} AND `event` = '{$promotionEvent}'
											GROUP BY `groupValue` ORDER BY `date`");
		$promotions = array_merge($promotions, $promotionsTotal);

		$paymentsByGroups = SJB_DB::query("SELECT s.*, sum(s.`price`) as count, u.`user_group_sid`, 'amount' as `event`, {$query}  FROM `statistics` s
										   INNER JOIN `users` u ON u.`sid` = `user_sid` 
										   {$where} AND s.`event` = 'payment'
										   GROUP BY groupValue, u.`user_group_sid` ORDER BY `date`");
		$paymentsByGroupsTotal = SJB_DB::query("SELECT s.*, sum(s.`price`) as count, u.`user_group_sid`, 'amount' as `event`, 'total'  as groupValue  FROM `statistics` s
										   INNER JOIN `users` u ON u.`sid` = `user_sid` 
										   {$where} AND s.`event` = 'payment'
										   GROUP BY groupValue, u.`user_group_sid` ORDER BY `date`");
		$paymentsByGroups = array_merge($paymentsByGroups, $paymentsByGroupsTotal);
		$statisticsInfo = array_merge($statisticsInfo, $totalPayment);
		$statisticsInfo = array_merge($statisticsInfo, $promotions);
		$statisticsInfo = array_merge($statisticsInfo, $paymentsByGroups);
		
		$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();
		$userGroups = SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions();
		foreach ($statisticsInfo as $j => $statistic) {
			switch ($statistic['event']) {
				case 'addAlert':
				case 'sentAlert':
				case SJB_GuestAlertStatistics::EVENT_SUBSCRIBED:
				case SJB_GuestAlertStatistics::EVENT_SENT:
				case 'deleteListing':
				case 'addListing':
				case 'viewListing':
					foreach ($listingTypes as $listingType) {
						if ($listingType['id'] == $statistic['type']) 
							$statistic['event'] = $statistic['event'].$statistic['type'];
					}
					break;
				case 'addUser':
				case 'deleteUser':
				case 'addUserlinkedin':
				case 'addUserfacebook':
				case 'addUsergoogle':
					foreach ($userGroups as $userGroup) {
						if ($userGroup['id'] == $statistic['type'])
							$statistic['event'] = $statistic['event'].$statistic['type'];
					}
					break;
				case 'amount':
					$statistic['event'] = $statistic['event']."_".$statistic['user_group_sid'];
					break;
			}
			if ($statistic['groupValue'] == 'total')
				$statisticsInfo['total'][$statistic['event']] = $statistic;
			elseif ($key == 'date')
				$statisticsInfo[date('Y-m-d', strtotime($statistic['date']))][$statistic['event']] = $statistic;
			else 
				$statisticsInfo[$statistic[$key]][$statistic['event']] = $statistic;
			unset($statisticsInfo[$j]);
		}
		$statistics = self::generalStatisticsView($statisticsInfo, $period, $groupBy, $filter);
		return $statistics;
	}
	
	public static function generateGeneralGraph($dateStatistics, $filter = array())
	{	
		$listingTypes = SJB_ListingTypeManager::getListingAllTypesForListType();
		$userGroups = SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions();
		$nameEvents = array();
		$filterValues = is_array($filter)?array_keys($filter):array();
		foreach ($filter as $blockName => $val) {
			switch ($blockName) {
				case 'popularity':
					$popularityEvents = array('siteView' => 'Number of Website Views', 
							'viewListing' => 'Number of #listingTypeName# Views');
					$nameEvents = array_merge($nameEvents, $popularityEvents);
					break;
				case 'users':
					$usersEvents = array('addUser' => 'Number of #userGroupName# Registered',
							'addSubAccount' => 'Number of Sub-Employers Registered', 
							'deleteUser' => 'Number of Profiles Deleted');
					$nameEvents = array_merge($nameEvents, $usersEvents);
					break;
				case 'listings':
					$listingsEvents = array('addListing' => 'Number of #listingTypeName# Posted', 
							'deleteListing' => 'Number of #listingTypeName# Deleted', );
					$nameEvents = array_merge($nameEvents, $listingsEvents);
					break;
				case 'applications':
					$applyEvents = array('apply' => 'Number of Applications Made');
					$nameEvents = array_merge($nameEvents, $applyEvents);
					break;
				case 'alerts':
					$alertsEvents = array(
						'addAlert' => 'Number of Job Alerts #listingTypeName# for',
						'sentAlert' => 'Number of #listingTypeName# Alerts Sent',
						SJB_GuestAlertStatistics::EVENT_SUBSCRIBED => 'Number of Guest #listingTypeName# Alerts subscribed for',
						SJB_GuestAlertStatistics::EVENT_SENT => 'Number of Guest #listingTypeName# Alerts Sent',
					);
					$nameEvents = array_merge($nameEvents, $alertsEvents);
					break;
				case 'sales':
					$salesEvents = array(
						'payment' => 'Earnings from #userGroupName#',
						SJB_PromotionsManager::STATISTIC_EVENT => 'Promotion Discount',
					);
					$nameEvents = array_merge($nameEvents, $salesEvents);
					break;
				case 'plugins':
					$popularityEvents = array(
						'viewMobileVersion' => 'Number of Mobile Version Views',
						'partneringSites' => 'Number of Redirects to Partnering Sites'
					);
					$socialPlugins = SJB_SocialPlugin::getAvailablePlugins();
					foreach ($socialPlugins as $socialPluginName) {
						$socialRegistrationEventName = 'addUser' . $socialPluginName;
						$popularityEvents[$socialRegistrationEventName] = 'Number of #userGroupName# Registered through ' . $socialPluginName;
					}
					$nameEvents = array_merge($nameEvents, $popularityEvents);
					break;
			}
		}
		$nameEvents = array_unique($nameEvents);
		$graph = array();
		
		foreach ($nameEvents as $nameEvent => $title) {
			switch ($nameEvent) {
				case 'addAlert':
				case 'sentAlert':
				case SJB_GuestAlertStatistics::EVENT_SUBSCRIBED:
				case SJB_GuestAlertStatistics::EVENT_SENT:
				case 'deleteListing':
				case 'addListing':
				case 'viewListing':
					foreach ($listingTypes as $listingTypeInfo) {
						$listingType = $listingTypeInfo['caption'];
						$listingTypeID = $listingTypeInfo['id'];
						if ($nameEvent == 'addListing' || $nameEvent == 'deleteListing')
							if ($listingTypeInfo['key'] == 'Job' || $listingTypeInfo['key'] == 'Resume') 
									$listingType = $listingTypeInfo['key'].'s';
							else {
								$listingType = '"'.$listingType.'" listings ';
							}
						$graph[$nameEvent.$listingTypeID]['title'] = str_replace('#listingTypeName#', $listingType, $title);
						if (!empty($dateStatistics[$nameEvent.$listingTypeID]['type']) && $dateStatistics[$nameEvent.$listingTypeID]['type'] == $listingTypeID) 
							$graph[$nameEvent.$listingTypeID]['statistic'] = $dateStatistics[$nameEvent.$listingTypeID]['count'];
						else 
							$graph[$nameEvent.$listingTypeID]['statistic'] = 0;
						if ($nameEvent == 'addListing') {
							$graph[$nameEvent.'Featured'.$listingTypeID]['title'] = "Number of Featured {$listingType} Posted";
							$graph[$nameEvent.'Priority'.$listingTypeID]['title'] = "Number of Priority {$listingType} Posted";
							$graph[$nameEvent.'Featured'.$listingTypeID]['statistic'] = 0;
							$graph[$nameEvent.'Priority'.$listingTypeID]['statistic'] = 0;
							if (!empty($dateStatistics[$nameEvent.$listingTypeID]['featured']) && $dateStatistics[$nameEvent.$listingTypeID]['featured'] == 1) 
								$graph[$nameEvent.'Featured'.$listingTypeID]['statistic'] = $dateStatistics[$nameEvent.$listingTypeID]['count'];
							if (!empty($dateStatistics[$nameEvent.$listingTypeID]['priority']) && $dateStatistics[$nameEvent.$listingTypeID]['priority'] == 1) 
								$graph[$nameEvent.'Priority'.$listingTypeID]['statistic'] = $dateStatistics[$nameEvent.$listingTypeID]['count'];
						}
					}
					break;
				case 'addUser':
				case 'addUserlinkedin':
				case 'addUserfacebook':
				case 'addUsergoogle':
					foreach ($userGroups as $userGroupInfo) {
						$userGroupID = $userGroupInfo['id'];
						$userGroup = $userGroupInfo['caption'];
						if ($userGroupInfo['key'] == 'JobSeeker' || $userGroupInfo['key'] == 'Employer') {
							$userGroup = $userGroupInfo['key'].'s';
						}
						else {
							$userGroup = '"'.$userGroup.'" Users';
						}
						$graph[$nameEvent.$userGroupID]['title'] = str_replace('#userGroupName#', $userGroup, $title);
						if (!empty($dateStatistics[$nameEvent.$userGroupID]['type']) && $dateStatistics[$nameEvent.$userGroupID]['type'] == $userGroupID)
							$graph[$nameEvent.$userGroupID]['statistic'] = $dateStatistics[$nameEvent.$userGroupID]['count'];
						else
							$graph[$nameEvent.$userGroupID]['statistic'] = 0;
					}
					break;
				case 'deleteUser':
					$countUsers = 0;
					foreach ($userGroups as $userGroupInfo) {
						$userGroupID = $userGroupInfo['id'];
						if (!empty($dateStatistics[$nameEvent.$userGroupID]['type']) && $dateStatistics[$nameEvent.$userGroupID]['type'] == $userGroupID) 
							$countUsers += $dateStatistics[$nameEvent.$userGroupID]['count'];
					}
					$graph['deleteUser']['title'] = $title;
					$graph['deleteUser']['statistic'] = $countUsers;
					break;
				case 'payment':
						$graph['totalAmount']['title'] = 'Total Sales';
						$graph['totalAmount']['statistic'] = !empty($dateStatistics['totalAmount']['count'])?$dateStatistics['totalAmount']['count']:0;
						foreach ($userGroups as $userGroupInfo) {
							$userGroupID = $userGroupInfo['id'];
							$userGroup = $userGroupInfo['caption'];
							if ($userGroupInfo['key'] == 'JobSeeker' || $userGroupInfo['key'] == 'Employer') {
								$userGroup = $userGroupInfo['key'].'s';
							}
							else {
								$userGroup = '"'.$userGroup.'" Users';
							}
							$graph['amount_'.$userGroupID]['title'] = str_replace('#userGroupName#', $userGroup, $title);
							$graph['amount_'.$userGroupID]['statistic'] = !empty($dateStatistics['amount_'.$userGroupID]['count'])?$dateStatistics['amount_'.$userGroupID]['count']:0;
						}
					break;
				case 'apply':
						$graph[$nameEvent]['title'] = $title;
						$graph[$nameEvent]['statistic'] = !empty($dateStatistics[$nameEvent]['count'])?$dateStatistics[$nameEvent]['count']:0;
						$graph[$nameEvent.'Approved']['title'] = "Number of Applications Approved";
						$graph[$nameEvent.'Rejected']['title'] = "Number of Applications Rejected";
						$graph[$nameEvent.'Approved']['statistic'] = 0;
						$graph[$nameEvent.'Rejected']['statistic'] = 0;
						if (!empty($dateStatistics[$nameEvent]['approve']) && $dateStatistics[$nameEvent]['approve'] == 1) 
							$graph[$nameEvent.'Approved']['statistic'] = $dateStatistics[$nameEvent]['count'];
						if (!empty($dateStatistics[$nameEvent]['reject']) && $dateStatistics[$nameEvent]['reject'] == 1) 
							$graph[$nameEvent.'Rejected']['statistic'] = $dateStatistics[$nameEvent]['count'];
						
					break;
					
				default:
					$graph[$nameEvent]['title'] = $title;
					$graph[$nameEvent]['statistic'] = !empty($dateStatistics[$nameEvent]['count'])?$dateStatistics[$nameEvent]['count']:0;
					break;
			}
		}
		return $graph;
	}
	
	public static function generalStatisticsView($statisticsInfo, $period, $groupBy = 'day', $filter = false)
	{
		if (empty($period['from']))
			if ($statisticsInfo) {
				foreach ($statisticsInfo as $statistics) {
					foreach ($statistics as $statistic) {
						$period['from'] = date('Y-m-d', strtotime($statistic['date']));
						break;
					}
					break;
				}
			}
			else 
				$period['from'] = date('Y-m-d', strtotime($period['to'] ."-9 day"));

		$iteration = 0;
		switch ($groupBy) {
			case 'day':
				$interval = strtotime($period['to']) - strtotime($period['from']);
				$iteration = $interval/(60*60*24)+1;
				break;
			case 'month':
				$year = date('Y', strtotime($period['to'])) - date('Y', strtotime($period['from']));
				for ($i = 0; $i <= $year; $i++) {
					switch ($i) {
						case $year:
							if ($year == 0)
								$iteration = date('n', strtotime($period['to'])) - date('n', strtotime($period['from'])) + 1;	
							else
								$iteration += date('n', strtotime($period['to']));	
						break;
						case 0:
							$iteration = 12 - date('n', strtotime($period['from'])) + 1;
						break;
						default:
							$iteration += 12;
						break;
					}
				}
				break;
			case 'quarter':
				$year = date('Y', strtotime($period['to'])) - date('Y', strtotime($period['from']));
				for ($i = 0; $i <= $year; $i++) {
					switch ($i) {
						case $year:
							$iteration += ceil(date('n', strtotime($period['to']))/3);	
						break;
						case 0:
							$iteration = 4 - ceil(date('n', strtotime($period['from']))/3)+ 1;
						break;
						default:
							$iteration += 4;
						break;
					}
				}
				break;
			case 'year':
				$iteration = date('Y', strtotime($period['to'])) - date('Y', strtotime($period['from'])) + 1;
				break;
		}
		if ($iteration > 31)
			return array('errors' => 'SELECTED_PERIOD_TOO_LONG');

		$iteration++;
		$statistics = array();
		$date = $period['from'];

		for ($i = 1; $i <= $iteration; $i++) {
			if ( $i == $iteration)
				$groupBy = 'total';
			
			switch ($groupBy) {
				case 'day':
					$dateStatistics = isset($statisticsInfo[$date])?$statisticsInfo[$date]:array();
					$statistics[$date] = self::generateGeneralGraph($dateStatistics, $filter);
					$statistics[$date]['date'] = $date;
					$date = date('Y-m-d', strtotime($date ."+1 day"));
					break;
				case 'month':
					$key = date('Ym', strtotime($date));
					$dateStatistics = isset($statisticsInfo[$key])?$statisticsInfo[$key]:array();
					$statistics[$key] = self::generateGeneralGraph($dateStatistics, $filter);
					$statistics[$key]['month'] = date('F', strtotime($date));
					$statistics[$key]['year'] = date('Y', strtotime($date));
					$statistics[$key]['date'] = $statistics[$key]['month'].', '.$statistics[$key]['year'];
					$date = date('Y-m-d', strtotime($date ."+1 month"));
					break;
				case 'quarter':
					if ($i == 1) {
						$year = date('Y', strtotime($date));
						$date = ceil(date('n', strtotime($date))/3);
					}
					$key = $year.$date;
					$dateStatistics = isset($statisticsInfo[$key])?$statisticsInfo[$key]:array();
					$statistics[$key] = self::generateGeneralGraph($dateStatistics, $filter);
					$statistics[$key]['quarter'] = $date;
					$statistics[$key]['year'] = $year;
					$statistics[$key]['date'] = $date.', '.$year;
					if ($date == 4) {
						$date = 1;
						$year++;
					}
					else
						$date++;
					break;
				case 'year':
					if ($i == 1)
						$date = date('Y', strtotime($date));
					$dateStatistics = isset($statisticsInfo[$date])?$statisticsInfo[$date]:array();
					$statistics[$date] = self::generateGeneralGraph($dateStatistics, $filter);
					$statistics[$date]['year'] = $date;
					$statistics[$date]['date'] = $date;
					$date ++;
					break;
				case 'total':
					$dateStatistics = isset($statisticsInfo['total'])?$statisticsInfo['total']:array();
					$statistics['total'] = self::generateGeneralGraph($dateStatistics, $filter);
					break;
			}  
		}
		return $statistics;
	}

	public static function getStatisticsByObjectSID($objectSID, $event) 
	{
		$statisticSID = SJB_DB::queryValue("SELECT `sid` FROM `statistics` WHERE `object_sid` = ?n AND `event` = ?s LIMIT 1", $objectSID, $event);
		return $statisticSID ? $statisticSID : false;
	}
	
	public static function updateStatistics($sid, $params)
	{
		$query = array();
		foreach ($params as $name => $val) {
			$query[] = " `{$name}` = '{$val}' ";
		}
		$query = implode(', ', $query);
		if ($query)
			return SJB_DB::query("UPDATE `statistics` SET $query WHERE `sid` = ?n", $sid);
			
		return false;
	}
	
	public static function getListingsStatistics($period, $listingType, $filter, $sorting_field, $sorting_order) 
	{
		$where = '';
		if (!empty($period['from'])) {
			$period['from'] = SJB_I18N::getInstance()->getInput('date', $period['from']);
			$time = "00:00:00";
			$where .= " AND s.`date` >= '{$period['from']} {$time}' ";
		}
		if (!empty($period['to'])) {
			$period['to'] = SJB_I18N::getInstance()->getInput('date', $period['to']);
			$time = "23:59:59";
			$where .= " AND s.`date` <= '{$period['to']} {$time}' ";
		}
		$join = '';
		$groupBy = '';
		$query = '';
		if (in_array($filter, array('Location_Country', 'Location_State', 'Location_City'))) {
			$fieldInfo = SJB_ListingFieldDBManager::getLocationFieldsInfoById($filter);
		} else {
			$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
		}

		if (strstr($filter, 'userGroup_')) {
			$userGroupSID = str_replace('userGroup_', '', $filter);
			$userGroupID = SJB_UserGroupManager::getUserGroupIDBySID($userGroupSID);
			$join = " INNER JOIN `users` u ON s.`user_sid` = u.`sid` ";
			$where .= " AND u.`user_group_sid` = '{$userGroupSID}'";
			$groupBy = " u.`sid`";
			$query = ', u.* ';
		}
		elseif (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'list' && empty($fieldInfo['parent_sid'])) {
			$join = " INNER JOIN `listings` l ON s.`object_sid` = l.`sid` INNER JOIN `listing_field_list` lfl ON l.`{$filter}` = lfl.`sid` ";
			$groupBy = " `{$filter}` ";
			$query = ", lfl.`value` as {$filter} ";
		}
		elseif (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'list' && !empty($fieldInfo['parent_sid'])) {
			if ($filter == 'Location_Country') {
				$join = " INNER JOIN `listings` l ON s.`object_sid` = l.`sid` INNER JOIN `countries` c ON l.`{$filter}` = c.`sid` ";
				$query = ", c.`country_name` as {$filter} ";
			}
			else {
				$join = " INNER JOIN `listings` l ON s.`object_sid` = l.`sid` INNER JOIN `states` st ON l.`{$filter}` = st.`sid` ";
				$query = ", st.`state_name` as {$filter} ";
			}
			$groupBy = " `{$filter}` ";
		}
		else {
			$join = " INNER JOIN `listings` l ON s.`object_sid` = l.`sid` ";
			$where .= " AND l.`{$filter}` != '' AND l.`{$filter}` IS NOT NULL ";
			$groupBy = " l.`{$filter}` ";
			$query = ', l.* ';
		}
		
		$orderBy = '';
		if ($sorting_field == 'username') {
			if (strstr($filter, 'userGroup_')) {
				if ($userGroupID == 'Employer')
					$orderBy = "ORDER BY `CompanyName` {$sorting_order}";
				else 
					$orderBy = "ORDER BY `FirstName`, `LastName` {$sorting_order}";
			}
		}
		else  
			$orderBy = "ORDER BY {$sorting_field} {$sorting_order}";
		
		$statisticsInfo = array();
		
		if (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'multilist') {
			$statisticsInfo = SJB_DB::query("SELECT s.* {$query} FROM `statistics` s {$join} WHERE s.`event` = 'addListing' AND s.`type` = ?s  {$where} GROUP BY {$groupBy}", $listingType);
			$fieldValues = array();
			foreach ($statisticsInfo as $statisticInfo) {
				$values = explode(',', $statisticInfo[$filter]);
				$fieldValues = array_merge($fieldValues, $values);
			}
			if ($fieldValues) {
				$fieldValues = array_unique($fieldValues);
				$fieldSIDs = implode(',', $fieldValues);
				$total = SJB_DB::query("SELECT sum(s.`count`) as count FROM `listing_field_list` lfl, `listings` l INNER JOIN `statistics` s  ON l.`sid` = s.`object_sid`
										WHERE lfl.`sid` in ({$fieldSIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND s.`event` = 'addListing' AND s.`type` = '{$listingType}'", $listingType);
				$total = $total?array_pop($total):array('count' => 0);
				$percent = $total['count']!=0?100/$total['count']:0;
				$percent = $percent==99.99?100:$percent;
				$statisticsSIDs = SJB_DB::query("SELECT lfl.`sid`, sum(s.`count`) as total
					FROM `listing_field_list` lfl, `listings` l
					INNER JOIN `statistics` s  ON l.`sid` = s.`object_sid`
					WHERE lfl.`sid` in ({$fieldSIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND s.`event` = 'addListing' AND s.`type` = ?s {$where} GROUP BY lfl.`sid` ORDER BY total DESC LIMIT 10", $listingType);

				foreach ($statisticsSIDs as $info) {
					$SIDs[] = $info['sid'];
					$key = array_search( $info['sid'], $fieldValues);
					if ($key !== false)
						unset($fieldValues[$key]);
				}

				if (isset($SIDs)) {
					$SIDs = implode(',', $SIDs);
					$statisticsInfo = SJB_DB::query("SELECT s.*, sum(s.`count`) as total, sum(s.`count` - (s.`priority` | s.`featured`)) as regular, sum(s.`count`)*{$percent} as percent, sum(s.`count` * s.`featured`) as FeaturedListings, sum(s.`count` * s.`priority`) as PriorityListings, lfl.`value` as {$filter}
						FROM `listing_field_list` lfl, `listings` l
						INNER JOIN `statistics` s  ON l.`sid` = s.`object_sid`
						WHERE lfl.`sid` in ({$SIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND s.`event` = 'addListing' AND s.`type` = ?s {$where} GROUP BY lfl.`sid` {$orderBy}", $listingType);
				}
				if ($fieldValues) {
					$SIDs = implode(',', $fieldValues);
					$ohter = SJB_DB::query("SELECT s.*, sum(s.`count`) as total, sum(s.`count` - (s.`priority` | s.`featured`)) as regular, sum(s.`count`)*{$percent} as percent, sum(s.`count` * s.`featured`) as FeaturedListings, sum(s.`count` * s.`priority`) as PriorityListings, 'Other' as other
						FROM `listing_field_list` lfl, `listings` l
						INNER JOIN `statistics` s  ON l.`sid` = s.`object_sid`
						WHERE lfl.`sid` in ({$SIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND s.`event` = 'addListing' AND s.`type` = ?s {$where}", $listingType);
					$statisticsInfo = array_merge($statisticsInfo, $ohter);
				}
			}
		}
		else {
			$total = SJB_DB::query("SELECT sum(s.`count`) as count FROM `statistics` s {$join} WHERE s.`event` = 'addListing' AND s.`type` = ?s  {$where}", $listingType);
			$total = $total?array_pop($total):array('count' => 0);
			$percent = $total['count']!=0?100/$total['count']:0;
			$statisticsSIDs = SJB_DB::query("SELECT {$groupBy} as sid, sum(s.`count`) as total FROM `statistics` s {$join} WHERE s.`event` = 'addListing' AND s.`type` = ?s  {$where} GROUP BY {$groupBy} ORDER BY total DESC LIMIT 10", $listingType);
			foreach ($statisticsSIDs as $info) 
				$SIDs[] = "'".$info['sid']."'";
			
			if (isset($SIDs)) {
				$SIDs = implode(',', $SIDs);
				$statisticsInfo = SJB_DB::query("SELECT s.*, sum(s.`count`) as total, sum(s.`count` - (s.`priority` | s.`featured`)) as regular, sum(s.`count`)*{$percent} as percent, sum(s.`count` * s.`featured`) as FeaturedListings, sum(s.`count` * s.`priority`) as PriorityListings {$query} FROM `statistics` s {$join} WHERE {$groupBy} in ({$SIDs}) AND s.`event` = 'addListing' AND s.`type` = ?s  {$where} GROUP BY {$groupBy} {$orderBy}", $listingType);
				$ohter = SJB_DB::query("SELECT s.*, sum(s.`count`) as total, sum(s.`count` - (s.`priority` | s.`featured`)) as regular, sum(s.`count`)*{$percent} as percent, sum(s.`count` * s.`featured`) as FeaturedListings, sum(s.`count` * s.`priority`) as PriorityListings, 'Other' as other {$query} FROM `statistics` s {$join} WHERE {$groupBy} not in ({$SIDs}) AND s.`event` = 'addListing' AND s.`type` = ?s  {$where}", $listingType);
				
				if (!empty($ohter[0]['sid']))
					$statisticsInfo = array_merge($statisticsInfo, $ohter);
			}
		}
		
		$statistics = array();

		foreach ($statisticsInfo as $key => $statisticInfo) {
			$statistics[$key] = $statisticInfo;
			if (isset($statisticInfo['other'])) 
				$statistics[$key]['generalColumn'] = 'Other';
			elseif (strstr($filter, 'userGroup_')) {
				if ($userGroupID == 'Employer')
					$statistics[$key]['generalColumn'] = !empty($statisticInfo['CompanyName'])?$statisticInfo['CompanyName']:$statisticInfo['username'];
				else 
					$statistics[$key]['generalColumn'] = (!empty($statisticInfo['FirstName']) && !empty($statisticInfo['LastName']))?$statisticInfo['FirstName']." ".$statisticInfo['LastName']:$statisticInfo['username'];
			}
			else 
				$statistics[$key]['generalColumn'] = $statisticInfo[$filter];
			
			$statistics[$key]['percent'] = round($statistics[$key]['percent'], 2);
			if ($statistics[$key]['percent'] == 99.99)
				$statistics[$key]['percent'] = 100;
		}
		return $statistics;
	}
	
	public static function getApplicationsAndViewsStatistics($period, $filter, $sortingField, $sorting_order) 
	{
		$where = '';
		if (!empty($period['from'])) {
			$period['from'] = SJB_I18N::getInstance()->getInput('date', $period['from']);
			$time = "00:00:00";
			$where .= " AND s.`date` >= '{$period['from']} {$time}' ";
		}
		if (!empty($period['to'])) {
			$period['to'] = SJB_I18N::getInstance()->getInput('date', $period['to']);
			$time = "23:59:59";
			$where .= " AND s.`date` <= '{$period['to']} {$time}' ";
		}
		$join = '';
		$mainGroupBy = '';
		$from = '';
		if (in_array($filter, array('Location_Country', 'Location_State', 'Location_City'))) {
			$fieldInfo = SJB_ListingFieldDBManager::getLocationFieldsInfoById($filter);
		} else {
			$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
		}

		if (strstr($filter, 'userGroup_')) {
			$userGroupSID = str_replace('userGroup_', '', $filter);
			$userGroupID  = SJB_UserGroupManager::getUserGroupIDBySID($userGroupSID);
			$mainGroupBy  = 'user_sid';
			if ($userGroupID == 'JobSeeker') {
				$join = ' INNER JOIN `users` u ON s.`user_sid` = u.`sid` ';
				$query = ", u.`FirstName`, u.`LastName`, u.`sid` as {$mainGroupBy} ";
			} else {
				$join = ' INNER JOIN `users` u ON l.`user_sid` = u.`sid` ';
				$query = ", u.`CompanyName`, u.`sid` as {$mainGroupBy} ";
			}
			
			$where .= " AND u.`user_group_sid` = '{$userGroupSID}'";
			$groupBy = ' u.`sid`';
		}
		elseif (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'list' && empty($fieldInfo['parent_sid'])) {
			$join = " INNER JOIN `listing_field_list` lfl ON l.`{$filter}` = lfl.`sid` ";
			$groupBy = " `{$filter}` ";
			$query = ", lfl.`value` as {$filter} ";
		}
		elseif (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'list' && !empty($fieldInfo['parent_sid'])) {
			$mainGroupBy = $filter;
			if ($filter == 'Location_Country') {
				$join = " INNER JOIN `countries` c ON l.`{$filter}` = c.`sid` ";
				$query = ", c.`country_name` as {$filter} ";
			} else {
				$join = " INNER JOIN `states` st ON l.`{$filter}` = st.`sid` ";
				$query = ", st.`state_name` as {$filter} ";
			}
			$groupBy = " `{$filter}` ";
		}
		elseif ($filter == 'sid') {
			$mainGroupBy = 'listing_sid';
			$join = " INNER JOIN `users` u ON l.`user_sid` = u.`sid` ";
			$groupBy = " l.`{$filter}` ";
			$query = ", l.`sid`, l.`Title`, l.`sid` as {$mainGroupBy} , u.`CompanyName`, u.`username` ";
		} else {
			$mainGroupBy = $filter;
			$where .= " AND l.`{$filter}` != '' AND l.`{$filter}` IS NOT NULL ";
			$groupBy = " l.`{$filter}` ";
			$query = ', l.* ';
		}
		
		$orderBy = '';
		if (empty($sortingField)) {
			$orderBy = "ORDER BY totalApply DESC, totalView DESC ";
		}
		else if ($sortingField == 'username') {
			if (strstr($filter, 'userGroup_')) {
				if ($userGroupID == 'Employer') {
					$sortingField = 'CompanyName';
					$orderBy = "ORDER BY `CompanyName` {$sorting_order}";
				} else {
					$sortingField = 'FirstName,LastName';
					$orderBy = "ORDER BY `FirstName`, `LastName` {$sorting_order}";
				}
			}
		} else {
			$orderBy = "ORDER BY {$sortingField} {$sorting_order}";
		}
		
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID('Job');
		if (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'multilist') {
			$statisticsInfo = self::executeApplicationsAndViewsMultylistStatistics($from, $join, $where, $groupBy, $orderBy, $filter, $listingTypeSID);
		} else {
			$statisticsInfo = self::executeApplicationsAndViewsStatistics($mainGroupBy, $query, $join, $where, $groupBy, $sortingField, $sorting_order, $listingTypeSID, 10);
		}
		$statistics = array();

		foreach ($statisticsInfo as $key => $statisticInfo) {
			$statistics[$key] = $statisticInfo;
			if (isset($statisticInfo['other'])) 
				$statistics[$key]['generalColumn'] = 'Other';
			elseif (strstr($filter, 'userGroup_')) {
				if ($userGroupID == 'Employer')
					$statistics[$key]['generalColumn'] = !empty($statisticInfo['CompanyName'])?$statisticInfo['CompanyName']:$statisticInfo['username'];
				else 
					$statistics[$key]['generalColumn'] = (!empty($statisticInfo['FirstName']) && !empty($statisticInfo['LastName']))?$statisticInfo['FirstName']." ".$statisticInfo['LastName']:$statisticInfo['username'];
			}
			elseif ($filter == 'sid')
				$statistics[$key]['generalColumn'] = $statisticInfo['Title'];
			else 
				$statistics[$key]['generalColumn'] = $statisticInfo[$filter];
		}
		
		return $statistics;
	}

	/**
	 * @param string $from
	 * @param string $join
	 * @param string $where
	 * @param string $groupBy
	 * @param string $orderBy
	 * @param string $filter
	 * @param int    $listingTypeSID
	 * @return array|null
	 */
	private static function executeApplicationsAndViewsMultylistStatistics($from, $join, $where, $groupBy, $orderBy, $filter, $listingTypeSID)
	{
		$statisticsInfo = SJB_DB::query("SELECT sum( if( s.`event` = 'apply', s.count, 0 ) ) AS totalApply, sum( if( s.`event` = 'viewListing', s.count, 0 ) ) AS totalView, l.`{$filter}`
									FROM {$from} `statistics` s, `listings` l
									{$join}
									WHERE ((s.`event` = 'apply' AND s.`type` = l.`sid`) OR (s.`event` = 'viewListing'AND s.`object_sid` = l.`sid`AND s.`type` = ?n)) {$where} GROUP BY {$groupBy} ORDER BY totalApply, totalView LIMIT 10", $listingTypeSID);
		$fieldValues = array();
		foreach ($statisticsInfo as $statisticInfo) {
			$values = explode(',', $statisticInfo[$filter]);
			$fieldValues = array_merge($fieldValues, $values);
		}
		
		if ($fieldValues) {
			$fieldValues = array_unique($fieldValues);
			$fieldSIDs = "'".implode("','", $fieldValues)."'";
			$statisticsSIDs = SJB_DB::query("SELECT lfl.`sid`, sum( if( s.`event` = 'apply', s.count, 0 ) ) AS totalApply, sum( if( s.`event` = 'viewListing', s.count, 0 ) ) AS totalView
				FROM `listing_field_list` lfl, `statistics` s, `listings` l
				{$join}
				WHERE lfl.`sid` in ({$fieldSIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND ((s.`event` = 'apply' AND s.`type` = l.`sid`) OR (s.`event` = 'viewListing'AND s.`object_sid` = l.`sid`AND s.`type` = ?n)) {$where}  GROUP BY lfl.`sid` ORDER BY totalApply DESC, totalView DESC LIMIT 10", $listingTypeSID);
			
			foreach ($statisticsSIDs as $info) {
				$SIDs[] = $info['sid'];
				$key = array_search( $info['sid'], $fieldValues);
				if ($key !== false) {
					unset($fieldValues[$key]);
				}
			}
			if (isset($SIDs)) {
				$SIDs = implode(',', $SIDs);
				$statisticsInfo = SJB_DB::query("SELECT sum( if( s.`event` = 'apply', s.count, 0 ) ) AS totalApply, sum( if( s.`event` = 'viewListing', s.count, 0 ) ) AS totalView, lfl.`value` as {$filter}
					FROM `listing_field_list` lfl, `statistics` s, `listings` l
					{$join}
					WHERE lfl.`sid` in ({$SIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND ((s.`event` = 'apply' AND s.`type` = l.`sid`) OR (s.`event` = 'viewListing' AND s.`object_sid` = l.`sid`AND s.`type` = ?n)) {$where} GROUP BY lfl.`sid` {$orderBy}", $listingTypeSID);
			}
			if ($fieldValues) {
				$SIDs = "'".implode("','", $fieldValues)."'";
				$ohter = SJB_DB::query("SELECT sum( if( s.`event` = 'apply', s.count, 0 ) ) AS totalApply, sum( if( s.`event` = 'viewListing', s.count, 0 ) ) AS totalView, 'Other' as {$filter}
					FROM `listing_field_list` lfl, `statistics` s, `listings` l
					{$join}
					WHERE lfl.`sid` in ({$SIDs}) AND FIND_IN_SET( lfl.`sid` , l.`{$filter}` ) AND ((s.`event` = 'apply' AND s.`type` = l.`sid`) OR (s.`event` = 'viewListing'AND s.`object_sid` = l.`sid`AND s.`type` = ?n)) {$where} GROUP BY lfl.`sid` {$orderBy}", $listingTypeSID);
				$statisticsInfo = array_merge($statisticsInfo, $ohter);
			}
		}
		
		return $statisticsInfo;
	}

	/**
	 * @param string $mainGroupBy
	 * @param string $query
	 * @param string $join
	 * @param string $where
	 * @param string $groupBy
	 * @param string $sortingField
	 * @param string $sortingOrder
	 * @param string $listingTypeSID
	 * @param int    $limit
	 * @return array
	 */
	private static function executeApplicationsAndViewsStatistics($mainGroupBy, $query, $join, $where, $groupBy, $sortingField, $sortingOrder, $listingTypeSID, $limit)
	{
		$statistics = SJB_DB::query("
				SELECT SUM(apply) AS totalApply, SUM(view) AS totalView, {$mainGroupBy}, t.* FROM (
					(
							SELECT
							sum(s.`count`) AS apply, 0 AS view {$query}
							FROM `statistics` as s
							INNER JOIN `listings` l ON s.`type` = l.`sid`
							{$join}
							WHERE s.`event` = 'apply' {$where} GROUP BY {$groupBy}
					) UNION ALL (
							SELECT
							0 AS apply, sum(s.`count`) AS view {$query}
							FROM `statistics` as s
							INNER JOIN `listings` l ON s.`object_sid` = l.`sid`
							{$join}
							WHERE s.`event` = 'viewListing' AND s.`type` = ?n {$where} GROUP BY {$groupBy}
					)
				) AS t GROUP BY {$mainGroupBy} ORDER BY totalApply DESC, totalView DESC",
			$listingTypeSID);
		
		if (!$statistics) {
			return array();
		}
		
		$other = self::executeOther($statistics, $limit);
		self::sortFields($statistics, $sortingField, $sortingOrder);
		if ($other) {
			$statistics[$limit] = $other;
		}
		
		return $statistics;
	}

	/**
	 * @param array $statistics
	 * @param int   $limit
	 * @return null|array
	 */
	private static function executeOther(array &$statistics, $limit)
	{
		if ($statistics && sizeof($statistics) > $limit) {
			$other = array();
			$other['totalApply'] = 0;
			$other['totalView']  = 0;
			$other['other']      = true;
			$size = sizeof($statistics);
			for ($i = $limit; $i < $size; $i++) {
				$other['totalApply'] += $statistics[$i]['totalApply'];
				$other['totalView']  += $statistics[$i]['totalView'];
			}
			
			array_splice($statistics, $limit, $size);
			
			return $other;
		}
		
		return null;
	}

	/**
	 * @param array  $statistics
	 * @param string $sortingField
	 * @param string $sortingOrder
	 */
	private static function sortFields(&$statistics, $sortingField, $sortingOrder)
	{
		if (!empty($sortingField)) {
			$reverse       = $sortingOrder == 'DESC';
			$sortingFields = explode(',', $sortingField);
			uasort($statistics, function ($a, $b) use ($reverse, $sortingFields) {
				foreach ($sortingFields as $field) {
					if (is_numeric($a[$field])) {
						if ($b[$field] != $a[$field]) {
							return $reverse ? $b[$field] > $a[$field] : $a[$field] > $b[$field];
						}
					} else {
						$value = $reverse ? strcmp($b[$field], $a[$field]): strcmp($a[$field], $b[$field]);
						if ($value) {
							return $value;
						}
					}
				}
				
				return 0;
			});
		}
	}

	public static function getSalesStatistics($period, $filter, $sorting_field, $sorting_order)
	{
		$where = '';
		if (!empty($period['from'])) {
			$period['from'] = SJB_I18N::getInstance()->getInput('date', $period['from']);
			$time = "00:00:00";
			$where .= " AND s.`date` >= '{$period['from']} {$time}' ";
		}
		if (!empty($period['to'])) {
			$period['to'] = SJB_I18N::getInstance()->getInput('date', $period['to']);
			$time = "23:59:59";
			$where .= " AND s.`date` <= '{$period['to']} {$time}' ";
		}
		$join = '';
		$groupBy = '';
		$query = '';
		if (in_array($filter, array('Location_Country', 'Location_State', 'Location_City'))) {
			$fieldInfo = SJB_ListingFieldDBManager::getLocationFieldsInfoById($filter);
		} else {
			$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($filter);
		}

		if (strstr($filter, 'userGroup_')) {
			$userGroupSID = str_replace('userGroup_', '', $filter);
			$userGroupID = SJB_UserGroupManager::getUserGroupIDBySID($userGroupSID);
			$join = " INNER JOIN `users` u ON s.`user_sid` = u.`sid` ";
			$where .= " AND u.`user_group_sid` = '{$userGroupSID}'";
			$groupBy = " u.`sid`";
			$query = ', u.* ';
		}
		elseif (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'list' && empty($fieldInfo['parent_sid'])) {
			$join = " INNER JOIN `users` u ON s.`user_sid` = u.`sid` INNER JOIN `user_profile_field_list` ufl ON u.`{$filter}` = ufl.`sid` ";
			$groupBy = " `{$filter}` ";
			$query = ", ufl.`value` as {$filter} ";
		}
		elseif (!empty($fieldInfo['type']) && $fieldInfo['type'] == 'list' && !empty($fieldInfo['parent_sid'])) {
			if ($filter == 'Location_Country') {
				$join = " INNER JOIN `users` u ON s.`user_sid` = u.`sid` INNER JOIN `countries` c ON u.`{$filter}` = c.`sid` ";
				$query = ", c.`country_name` as {$filter} ";
			}
			else {
				$join = " INNER JOIN `users` u ON s.`user_sid` = u.`sid` INNER JOIN `states` st ON u.`{$filter}` = st.`sid` ";
				$query = ", st.`state_name` as {$filter} ";
			}
			$groupBy = " `{$filter}` ";
		}
		elseif ($filter == 'sid') {
			$join = " INNER JOIN `products` p ON s.`object_sid` = p.`sid` ";
			$where .= " AND s.type = 'product' ";
			$groupBy = " s.`object_sid`, s.`featured`, s.`priority`, s.`reactivate` ";
			$query = ', p.* ';
		}
		else {
			$join = " INNER JOIN `users` u ON s.`user_sid` = u.`sid` ";
			$where .= " AND u.`{$filter}` != '' AND u.`{$filter}` IS NOT NULL ";
			$groupBy = " u.`{$filter}` ";
			$query = ', u.* ';
		}

		$orderBy = '';
		if ($sorting_field == 'username') {
			if (strstr($filter, 'userGroup_')) {
				if ($userGroupID == 'Employer')
					$orderBy = "ORDER BY `CompanyName` {$sorting_order}";
				else 
					$orderBy = "ORDER BY `FirstName`, `LastName` {$sorting_order}";
			}
		}
		else  
			$orderBy = "ORDER BY {$sorting_field} {$sorting_order}";
		
		$statisticsInfo = array();
		$total = $totalSum = SJB_DB::query("SELECT sum(s.`price`*s.`count`) as total, sum(s.`count`) as units_sold, 'Total' as totalSum, 0 as `sid` FROM `statistics` s {$join} WHERE s.`event` = 'payment' {$where}");
		$total = $total?array_pop($total):array('total' => 0);
		$percent = $total['total']!=0?100/$total['total']:0;
		if ($filter == 'sid') {
			$statisticsInfo = SJB_DB::query("SELECT s.*, sum(s.`price`*s.`count`) as total, sum(s.`price`*s.`count`)*{$percent} as percent, sum(s.`count`) as units_sold {$query} 
												 FROM `statistics` s {$join} 
												 WHERE s.`event` = 'payment' {$where} 
												 GROUP BY {$groupBy} {$orderBy}");
		}
		else {
			$statisticsSIDs = SJB_DB::query("SELECT {$groupBy} as sid, sum(s.`price`*s.`count`) as total FROM `statistics` s {$join} WHERE s.`event` = 'payment' {$where} GROUP BY {$groupBy} ORDER BY total DESC LIMIT 10");
			foreach ($statisticsSIDs as $info) 
				$SIDs[] = "'".$info['sid']."'";
			
			if (isset($SIDs)) {
				$SIDs = implode(',', $SIDs);
				$statisticsInfo = SJB_DB::query("SELECT s.*, sum(s.`price`*s.`count`) as total, sum(s.`price`*s.`count`)*{$percent} as percent, sum(s.`count`) as units_sold {$query} 
												 FROM `statistics` s {$join} 
												 WHERE {$groupBy} in ({$SIDs}) AND s.`event` = 'payment' {$where} 
												 GROUP BY {$groupBy} {$orderBy}");
				$ohter = SJB_DB::query("SELECT s.*, sum(s.`price`*s.`count`) as total, sum(s.`price`*s.`count`)*{$percent} as percent, sum(s.`count`) as units_sold, 'Other' as other {$query} FROM `statistics` s {$join} WHERE {$groupBy} not in ({$SIDs}) AND s.`event` = 'payment' {$where}");
				
				if (!empty($ohter[0]['sid'])) 
					$statisticsInfo = array_merge($statisticsInfo, $ohter);
			}
		}
		$statisticsInfo = array_merge($statisticsInfo, $totalSum);
		$statistics = array();

		foreach ($statisticsInfo as $key => $statisticInfo) {
			if ($filter == 'sid') {
				$productInfo = SJB_ProductsManager::getProductInfoBySID($statisticInfo['sid']);
				$statisticInfo['product_type'] = SJB_ProductsManager::getProductTypeByID($productInfo['product_type']);
			}
			$statistics[$key] = $statisticInfo;
			if (isset($statisticInfo['other'])) 
				$statistics[$key]['generalColumn'] = 'Other';
			elseif (isset($statisticInfo['totalSum'])) {
				$statistics[$key]['generalColumn'] = 'Total';
				$statistics[$key]['name'] = 'Total';
				$statistics[$key]['percent'] = '100%';
			}
			elseif (strstr($filter, 'userGroup_')) {
				if ($userGroupID == 'Employer')
					$statistics[$key]['generalColumn'] = !empty($statisticInfo['CompanyName'])?$statisticInfo['CompanyName']:$statisticInfo['username'];
				else 
					$statistics[$key]['generalColumn'] = (!empty($statisticInfo['FirstName']) && !empty($statisticInfo['LastName']))?$statisticInfo['FirstName']." ".$statisticInfo['LastName']:$statisticInfo['username'];
			}
			elseif ($filter == 'sid')
				$statistics[$key]['generalColumn'] = $statisticInfo['name'];
			else 
				$statistics[$key]['generalColumn'] = $statisticInfo[$filter];
			
			$statistics[$key]['percent'] = round($statistics[$key]['percent'], 2);
			if ($statistics[$key]['percent'] == 99.99)
				$statistics[$key]['percent'] = 100;

		}
		return $statistics;
	}

	public static function getGuestAlertsStatistics($period, $listingTypeSID, $statisticEvent, $sorting_field, $sorting_order)
	{
		$where = '';
		if (!empty($period['from'])) {
			$period['from'] = SJB_I18N::getInstance()->getInput('date', $period['from']);
			$time = "00:00:00";
			$where .= " AND s.`date` >= '{$period['from']} {$time}' ";
		}
		if (!empty($period['to'])) {
			$period['to'] = SJB_I18N::getInstance()->getInput('date', $period['to']);
			$time = "23:59:59";
			$where .= " AND s.`date` <= '{$period['to']} {$time}' ";
		}

		$join = ' INNER JOIN `guest_alerts` `ga` ON `s`.`object_sid` = `ga`.`sid` ';
		$groupBy = " `ga`.`email` ";
		$queryAdd = ', `ga`.* ';

		$orderBy = "ORDER BY {$sorting_field} {$sorting_order}";

		$statisticsInfo = array();
		switch ($statisticEvent) {
			case 'subscribed':
				$event = SJB_GuestAlertStatistics::EVENT_SUBSCRIBED;
				break;
			case 'sent':
			default:
				$event = SJB_GuestAlertStatistics::EVENT_SENT;
				break;
		}

		$query = "SELECT SUM(`s`.`count`) AS count
					FROM `statistics` `s`
					{$join}
					WHERE `s`.`event` = '{$event}' AND s.`type` = ?n {$where}";

		$total = SJB_DB::query($query, $listingTypeSID);
		$total = $total ? array_pop($total) : array('count' => 0);
		$percent = ($total['count']!=0) ? 100/$total['count'] : 0;

		$query = "SELECT {$groupBy} AS sid, SUM(s.`count`) AS total
					FROM `statistics` s
					{$join}
					WHERE s.`event` = '{$event}' AND s.`type` = ?n {$where}
					GROUP BY {$groupBy} ORDER BY total DESC LIMIT 10";

		$statisticsSIDs = SJB_DB::query($query, $listingTypeSID);
		foreach ($statisticsSIDs as $info)
			$SIDs[] = "'".$info['sid']."'";

		if (isset($SIDs)) {
			$SIDs = implode(',', $SIDs);
			$query = "SELECT s.*,
							SUM(s.`count`) as `total`,
							SUM(s.`count`)*{$percent} as percent
							{$queryAdd}
						FROM `statistics` s
						{$join}
						WHERE {$groupBy} in ({$SIDs}) AND s.`event` = '{$event}' AND s.`type` = ?n {$where}
						GROUP BY {$groupBy} {$orderBy}";

			$statisticsInfo = SJB_DB::query($query, $listingTypeSID);
		}

		$statistics = array();

		foreach ($statisticsInfo as $key => $statisticInfo) {
			$statistics[$key] = $statisticInfo;
			$statistics[$key]['generalColumn'] = $statisticInfo['email'];
			$statistics[$key]['percent'] = round($statistics[$key]['percent'], 2);
			if ($statistics[$key]['percent'] == 99.99)
				$statistics[$key]['percent'] = 100;
		}
		return $statistics;
	}

	public static function getPromotionsStatistics($period, $sorting_field, $sorting_order)
	{
		$where = '`ph`.`paid` > 0';
		if (!empty($period['from'])) {
			$period['from'] = SJB_I18N::getInstance()->getInput('date', $period['from']);
			$time = "00:00:00";
			$where .= " AND `ph`.`date` >= '{$period['from']} {$time}' ";
		}
		if (!empty($period['to'])) {
			$period['to'] = SJB_I18N::getInstance()->getInput('date', $period['to']);
			$time = "23:59:59";
			$where .= " AND `ph`.`date` <= '{$period['to']} {$time}' ";
		}

		$join = '
				INNER JOIN `promotions` `p` ON `ph`.`code_sid` = `p`.`sid`
				INNER JOIN `invoices` ON `ph`.`invoice_sid` = `invoices`.`sid`
		';
		$groupBy = ' `ph`.`code_sid` ';
		$orderBy = "ORDER BY {$sorting_field} {$sorting_order}";

		$statisticsInfo = array();

		$query = "
			SELECT {$groupBy} AS `sid`, COUNT(`ph`.`code_sid`) AS `total`
			FROM `promotions_history` `ph`
			{$join}
			WHERE {$where}
			GROUP BY {$groupBy} ORDER BY `total` DESC LIMIT 10";
		$statisticsSIDs = SJB_DB::query($query);
		foreach ($statisticsSIDs as $info) {
			$SIDs[] = "'".$info['sid']."'";
		}

		if (isset($SIDs)) {
			$listOfFields = array(
				'`p`.`code` as `promotionCode`',
				'`p`.`type` as `promotionType`',
				'`p`.`discount` as `promotionDiscount`',
				'SUM(`ph`.`amount`) as `discountAmount`',
				'SUM(`ph`.`amount` + `invoices`.`sub_total`)  as `saleSubTotal`',
				'SUM(`invoices`.`sub_total`)  as `saleTotal`',
			);
			$queryAdd = ', ' . implode(', ', $listOfFields);
			$SIDs = implode(',', $SIDs);
			$query = "SELECT COUNT(`ph`.`code_sid`) as `usageCount`
							{$queryAdd}
						FROM `promotions_history` `ph`
						{$join}
						WHERE {$where} AND {$groupBy} in ({$SIDs})
						GROUP BY {$groupBy} {$orderBy}";
			$statisticsInfo = SJB_DB::query($query);
		}

		$statistics = array();

		foreach ($statisticsInfo as $key => $statisticInfo) {
			$statistics[$key] = $statisticInfo;
			$statistics[$key]['generalColumn'] = $statisticInfo['promotionCode'];
		}
		return $statistics;
	}

	/**
	 * Returns quick statistics for employer: current live jobs, jobs posted this month,
	 * job views this month, applications received this month
	 * @param $userSID
	 * @return array
	 */
	public static function getEmployerQuickStatistics($userSID)
	{
		$where = ' AND `s`.`date` >= FROM_DAYS(TO_DAYS(CURDATE()) - DAYOFMONTH(CURDATE()) + 1)';
		$subQuery = "SELECT `l`.`sid` FROM `listings` `l` WHERE `l`.`user_sid` = ?n
					 UNION ALL
					 SELECT `st`.`object_sid` FROM `statistics` `st` WHERE `st`.`user_sid` = ?n AND `st`.`event` = 'deleteListing'";
		$quickStats = SJB_DB::query("
			SELECT IFNULL(SUM(`quickStats`.`countPostedListings`),0) AS `countPostedListings`,
			IFNULL(SUM(`quickStats`.`countViewedListings`),0) AS `countViewedListings`, IFNULL(SUM(`quickStats`.`countApplications`),0) AS `countApplications`
			FROM (
				SELECT SUM(`s`.`count`) AS `countPostedListings`, 0 AS `countViewedListings`, 0 AS `countApplications`
				FROM `statistics` `s`
				WHERE `s`.`user_sid` = ?n AND `s`.`event` = 'addListing' AND `s`.`type`= 6 {$where} GROUP BY `s`.`user_sid`
				UNION ALL
				SELECT 0 AS `countPostedListings`, SUM(`s`.`count`) AS `countViewedListings`, 0 AS `countApplications`
				FROM `statistics` `s`
				WHERE `s`.`event` = 'viewListing' AND `s`.`type`= 6 {$where} AND `s`.`object_sid` IN ( {$subQuery})
				GROUP BY `s`.`user_sid`
				UNION ALL
				SELECT 0 AS `countPostedListings`, 0 AS `countViewedListings`, SUM(`s`.`count`) AS `countApplications`
				FROM `statistics` `s`
				WHERE `s`.`event` = 'apply' {$where} AND `s`.`type` IN ({$subQuery})
				GROUP BY `s`.`user_sid`
			) AS quickStats", $userSID, $userSID, $userSID, $userSID, $userSID);
		$quickStats = array_pop($quickStats);
		$quickStats['countActiveListings'] = SJB_ListingDBManager::getActiveAndApproveListingsNumberByUserSID($userSID);
		return $quickStats;
	}

	/** Returns general statistics for employer: regular, featured and priority jobs posted,
	 * applications received, resumes viewed. Statistics divided into 4 periods
	 * @param $userSID
	 * @return array
	 */
	public static function getEmployerGeneralStatistics($userSID)
	{
		$columns = array('Regular Jobs Posted', 'Featured Jobs Posted', 'Priority Jobs Posted', 'Applications Received', 'Resumes Viewed');
		$periods = array(
			'All' => '',
			'This Month' => ' AND `s`.`date` >= FROM_DAYS(TO_DAYS(CURDATE()) - DAYOFMONTH(CURDATE()) + 1)',
			'This Week' => ' AND `s`.`date` >= FROM_DAYS(TO_DAYS(CURDATE()) - WEEKDAY(CURDATE()))',
			'Today' => ' AND `s`.`date` >= CURDATE()'
		);
		$subQuery = "SELECT `l`.`sid` FROM `listings` `l` WHERE `l`.`user_sid` = ?n
					 UNION ALL
					 SELECT `st`.`object_sid` FROM `statistics` `st` WHERE `st`.`user_sid` = ?n AND `st`.`event` = 'deleteListing'";
		$generalStats =  array();
		foreach ($periods as $key => $value) {
			$statForPeriod = SJB_DB::query("
				SELECT IFNULL(SUM(`generalStats`.`countUsualListings`),0) AS `countUsualListings`, IFNULL(SUM(`generalStats`.`countFeaturedListings`),0) AS `countFeaturedListings`,
				IFNULL(SUM(`generalStats`.`countPriorityListings`),0) AS `countPriorityListings`,
				IFNULL(SUM(`generalStats`.`countApplications`),0) AS `countApplications`, IFNULL(SUM(`generalStats`.`countResumesViewed`),0) AS `countResumesViewed`
				FROM (
					SELECT SUM(`s`.`count`) AS `countUsualListings`, 0 AS `countFeaturedListings`, 0 AS `countPriorityListings`, 0 AS `countApplications`, 0 AS `countResumesViewed`
					FROM `statistics` `s`
					WHERE `s`.`user_sid` = ?n AND `s`.`event` = 'addListing' AND `s`.`type` = 6 AND `s`.`featured` = 0 AND `s`.`priority` = 0  {$value}
					GROUP BY `s`.`user_sid`
					UNION ALL
					SELECT 0 AS `countUsualListings`, SUM(`s`.`count`) AS `countFeaturedListings`, 0 AS `countPriorityListings`, 0 AS `countApplications`, 0 AS `countResumesViewed`
					FROM `statistics` `s`
					WHERE `s`.`user_sid` = ?n AND `s`.`event` = 'addListing' AND `s`.`type` = 6 AND `s`.`featured` = 1  {$value}
					GROUP BY `s`.`user_sid`
					UNION ALL
					SELECT 0 AS `countUsualListings`, 0 AS `countFeaturedListings`, SUM(`s`.`count`) AS `countPriorityListings`, 0 AS `countApplications`, 0 AS `countResumesViewed`
					FROM `statistics` `s`
					WHERE `s`.`user_sid` = ?n AND `s`.`event` = 'addListing' AND `s`.`type` = 6 AND `s`.`priority` = 1 {$value}
					GROUP BY `s`.`user_sid`
					UNION ALL
					SELECT 0 AS `countUsualListings`, 0 AS `countFeaturedListings`, 0 AS `countPriorityListings`, SUM(`s`.`count`) AS `countApplications`, 0 AS `countResumesViewed`
					FROM `statistics` `s`
					WHERE `s`.`event` = 'apply' {$value} AND `s`.`type` IN ({$subQuery})
					GROUP BY `s`.`user_sid`
					UNION ALL
					SELECT 0 AS `countUsualListings`, 0 AS `countFeaturedListings`, 0 AS `countPriorityListings`, 0 AS `countApplications`, SUM(`s`.`count`) AS `countResumesViewed`
					FROM `statistics` `s`
					WHERE `s`.`user_sid` = ?n AND `s`.`event` = 'viewListing' AND `s`.`type` = 7 {$value}
					GROUP BY `s`.`user_sid`
				) AS `generalStats`", $userSID, $userSID, $userSID, $userSID, $userSID, $userSID);
			foreach (array_pop($statForPeriod) as $column => $count) {
				if (!is_numeric($column)) {
					$generalStats[$key][$column] = $count;
				}
			}
		}
		array_unshift($generalStats, null);
		$transposeGeneralStats = call_user_func_array('array_map', $generalStats);
		$finalGeneralStats = array();
		foreach ($transposeGeneralStats as $key => $stats) {
			$finalGeneralStats[$columns[$key]] = $stats;
		}
		return $finalGeneralStats;
	}

	/** Returns statistics for each job: title, date of creation, expiration date, number of job search,
	 * number of job savings, number of views, number of applications
	 * @param $userSID
	 * @param bool $active
	 * @param $sortingField
	 * @param $sortingOrder
	 * @return array
	 */
	public static function getEmployerJobsStatistics($userSID, $active = true, $sortingField, $sortingOrder)
	{
		$where = '';
		if ($active) {
			$where = ' AND `l`.`active` = 1';
		}

		$orderBy = "ORDER BY `{$sortingField}` {$sortingOrder}";
		$jobStats = SJB_DB::query("
			SELECT `jobStats`.`sid` AS `listingSID`,`jobStats`.`Title` AS `Title`, `jobStats`.`postedDate` AS `postedDate`,`jobStats`.`expDate` AS `expDate`,
			IFNULL(SUM(`jobStats`.`countSearches`),0) AS `countSearches`, IFNULL(SUM(`jobStats`.`countSavings`),0) AS `countSavings`,
			IFNULL(SUM(`jobStats`.`countApplications`),0) AS `countApplications`, IFNULL(SUM(`jobStats`.`countViewedListings`),0) AS `countViewedListings`
			FROM (
				SELECT `l`.`sid` AS `sid`, `l`.`Title` AS `Title`, `l`.`activation_date` AS `postedDate`, `l`.`expiration_date` AS `expDate`, 0 AS `countSearches`, 0 AS `countSavings`,
				0 AS `countViewedListings`, 0 AS `countApplications`
				FROM `listings` `l`
				WHERE `l`.`user_sid` = ?n AND `l`.`listing_type_sid` = 6 {$where}  GROUP BY `l`.`sid`
				UNION ALL
				SELECT `l`.`sid` AS `sid`, `l`.`Title` AS `Title`, `l`.`activation_date` AS `postedDate`, `l`.`expiration_date` AS `expDate`, SUM(`s`.`count`) AS `countSearches`,
				0 AS `countSavings`, 0 AS `countViewedListings`, 0 AS `countApplications`
				FROM `statistics` `s`
				INNER JOIN `listings` `l` ON `l`.`sid` = `s`.`object_sid`
				WHERE `l`.`user_sid` = ?n AND `s`.`event` = 'showInSearchResults' AND `l`.`listing_type_sid` = 6 {$where} GROUP BY `s`.`object_sid`
				UNION ALL
				SELECT `l`.`sid` AS `sid`, `l`.`Title` AS `Title`, `l`.`activation_date` AS `postedDate`, `l`.`expiration_date` AS `expDate`, 0 AS `countSearches`,
				SUM(`s`.`count`) AS `countSavings`, 0 AS `countViewedListings`, 0 AS `countApplications`
				FROM `statistics` `s`
				INNER JOIN `listings` `l` ON `l`.`sid` = `s`.`object_sid`
				WHERE `l`.`user_sid` = ?n AND `s`.`event` = 'saveListing' AND `l`.`listing_type_sid` = 6 {$where} GROUP BY `s`.`object_sid`
				UNION ALL
				SELECT `l`.`sid` AS `sid`, `l`.`Title` AS `Title`, `l`.`activation_date` AS `postedDate`, `l`.`expiration_date` AS `expDate`, 0 AS `countSearches`,
				0 AS `countSavings`,  SUM(`s`.`count`) AS `countViewedListings`, 0 AS `countApplications`
				FROM `statistics` `s`
				INNER JOIN `listings` `l` ON `l`.`sid` = `s`.`object_sid`
				WHERE `l`.`user_sid` = ?n AND `s`.`event` = 'viewListing' AND `l`.`listing_type_sid` = 6 {$where} GROUP BY `s`.`object_sid`
				UNION ALL
				SELECT `l`.`sid` as `sid`, `l`.`Title` as `Title`, `l`.`activation_date` as `postedDate`, `l`.`expiration_date` as `expDate`, 0 as `countSearches`,
				0 as `countSavings`, 0 as `countViewedListings`, SUM(`s`.`count`) as `countApplications`
				FROM `statistics` `s`
				INNER JOIN `listings` `l` ON `l`.`sid` = `s`.`type`
				WHERE `l`.`user_sid` = ?n AND `s`.`event` = 'apply' AND `l`.`listing_type_sid` = 6 {$where} GROUP BY `s`.`object_sid`
			) AS jobStats GROUP BY `jobStats`.`sid` {$orderBy}", $userSID, $userSID, $userSID, $userSID, $userSID);
		return $jobStats;
	}
}