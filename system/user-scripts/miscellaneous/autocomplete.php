<?php

class SJB_Miscellaneous_Autocomplete extends SJB_Function
{
	public function execute()
	{
		header('Content-Type: text/html; charset=utf-8');

		$requestUri = $_SERVER['REQUEST_URI'];

		preg_match('(.*/autocomplete/([a-zA-Z0-9:_]*)/?)', $requestUri, $fieldParam);
		$field = !empty($fieldParam[1]) ? $fieldParam[1] : '';

		preg_match("(.*/autocomplete/{$field}/([a-zA-Z]*)/?)", $requestUri, $fieldType);
		$fieldType = !empty($fieldType[1]) ? $fieldType[1] : '';

		preg_match("(.*/autocomplete/{$field}/{$fieldType}/([a-zA-Z]*)/?)", $requestUri, $tablePrefix);
		$tablePrefix = SJB_DB::quote(!empty($tablePrefix[1]) ? $tablePrefix[1] : '');

		preg_match("(.*/autocomplete/{$field}/{$fieldType}/{$tablePrefix}/([a-zA-Z]*)/?)", $requestUri, $viewType);
		$viewType = SJB_DB::quote(!empty($viewType[1]) ? $viewType[1] : '');

		preg_match("(.*/autocomplete/{$field}/{$fieldType}/{$tablePrefix}/$viewType/([a-zA-Z]*)/?)", $requestUri, $listingTypeID);
		$listingTypeID = SJB_DB::quote(!empty($listingTypeID[1]) ? $listingTypeID[1] : '');

		$query = SJB_Request::getVar('q', false);

		if (!empty($query) && $field && $fieldType && $tablePrefix && $viewType && $listingTypeID) {
            $queryCriterion = $query . '%';
            if ($fieldType == 'text' && $field == 'keywords') {
                $result = SJB_DB::query('SELECT `keywords` as `value`, COUNT(*) `count` FROM `listings_keywords` WHERE `keywords` LIKE ?s AND `active` = 1 GROUP BY `keywords` ORDER BY `count` DESC LIMIT 0 , 5', $queryCriterion);
            }
            elseif ($fieldType == 'geo') {
                $result = SJB_DB::query('SELECT DISTINCT `name` as `value`, COUNT(*) `count` FROM `locations` WHERE `name` <> \'\' AND `name` LIKE ?s GROUP BY `value` LIMIT 0 , 100', $queryCriterion);
            }
            elseif ($fieldType == 'location') {
				if (preg_match('/[a-z\d]+\d+/i', $query)) {
					$result = SJB_DB::query('SELECT DISTINCT `name` as `value`, `city`, `state_code`, COUNT(*) `count` FROM `locations` WHERE `name` <> \'\' AND `name` LIKE ?s GROUP BY `value`, `country_sid` LIMIT 0 , 10', $queryCriterion);
            	} else {
            		$country = SJB_DB::query("SELECT `country_name` as `value` FROM `countries` WHERE `country_code` = ?s AND `active` = 1", $query);
            		$countries = SJB_DB::query("SELECT `country_name` as `value` FROM `countries` WHERE `country_name` LIKE ?s AND `country_code` != ?s AND `active` = 1", $queryCriterion, $query);
             		$countries = array_merge($country, $countries);
					$states = SJB_DB::query("SELECT `state_name` as `value` FROM `states` INNER JOIN `countries` ON `states`.`country_sid` = `countries`.`sid` WHERE `countries`.`active` = 1 AND `states`.`state_name` LIKE ?s AND `states`.`active` = 1", $queryCriterion);
            		foreach ($states as $key => $state) {
            			$state = trim(preg_replace('/(\s+|[^\'"_\w\dÀ-ÿ])/ui', '', strip_tags($state['value'])));
            			$states[$state] = $states[$key];
            			unset($states[$key]);
            		}
					$cities = SJB_DB::query("
						SELECT
							`locations`.`state_code`, `city` as `value`
						FROM
							`locations`
						INNER JOIN
							`countries` ON `locations`.`country_sid` = `countries`.`sid`
						LEFT JOIN
							`states` ON `locations`.`state_code` = `states`.`state_code`
						WHERE
							`countries`.`active` = 1 AND
							`locations`.`city` LIKE ?s AND
							(`states`.`active` = 1 OR
							LENGTH(`locations`.`state_code`) = 0)", $queryCriterion
					);
            	    foreach ($cities as $key => $city) {
            	    	$state = trim(preg_replace('/(\s+|[^\'"_\w\dÀ-ÿ])/ui', '', strip_tags($city['state_code'])));
            			$city = trim(preg_replace('/(\s+|[^\'"_\w\dÀ-ÿ])/ui', '', strip_tags($city['value'])));
            			$cities[$city][$state] = $cities[$key];
            			unset($cities[$key]);
            		}
            		$result = array();
            		$i = 0;
            		foreach ($states as $key => $state) {
            			$result[$i] = $state;
            			$i++;
            			if (isset($cities[$key])) {
            				$result[$i] = $cities[$key];
            				unset ($cities[$key]);
            				$i++;
            			}
            		}
            		$result = array_merge($countries, $result);
            		$result = array_merge($result, $cities);
            	}
            }
            elseif ($fieldType == 'string') {

	            $additionalCondition = '';
	            $fieldParents        = explode('_', $field);
	            $fieldName           = array_pop($fieldParents);

	            if ($fieldName == 'City') {
		            if ($viewType == 'input') {
			            $tablePrefix = 'locations';
			            $field       = 'City';
		            }
					elseif ($viewType == 'search' && $tablePrefix == 'listings') {
			            $listingTypeSid      = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
			            $additionalCondition = '`listing_type_sid` = ' . $listingTypeSid . ' AND';
		            }
	            }

	            $result = SJB_DB::query("SELECT DISTINCT `{$field}` as `value`, COUNT(*) `count` FROM `{$tablePrefix}` WHERE " . $additionalCondition . " `{$field}` LIKE ?s GROUP BY `{$field}` ORDER BY `count` DESC LIMIT 0 , 5", $queryCriterion);
            }
            if (!empty($result)) {
				foreach ($result as $rowBase) {
					if (empty($rowBase['value']) && is_array($rowBase)) {
						foreach ($rowBase as $rowBase) {
							$res = strpos(strtolower($rowBase['value']), strtolower($query));
							if ($res !== false || $fieldType == 'location') {
								$rowBase['value'] = trim($rowBase['value']);
								if (isset($rowBase['city']) && isset($rowBase['state_code'])) {
									print $rowBase['value'] . ', ' . $rowBase['city'] . ', ' . $rowBase['state_code'] . "\n";
								}
								elseif (!empty($rowBase['state_code'])) {
									print $rowBase['value'] . ', ' . $rowBase['state_code'] . "\n";
								}
								elseif (!isset($rowBase['count'])) {
									print $rowBase['value'] . "\n";
								} else {
									print $rowBase['value'] . '|' . $rowBase['count'] . "\n";
								}
							}
						}
					} else {
						$res = strpos(strtolower($rowBase['value']), strtolower($query));
						if ($res !== false || $fieldType == 'location') {
							$rowBase['value'] = trim($rowBase['value']);
							if (isset($rowBase['city']) && isset($rowBase['state_code'])) {
								print $rowBase['value'] . ', ' . $rowBase['city'] . ', ' . $rowBase['state_code'] . "\n";
							}
							elseif (!empty($rowBase['state_code'])) { 
								print $rowBase['value'] . ', ' . $rowBase['state_code'] . "\n";
							}
							elseif (!isset($rowBase['count'])) { 
								print $rowBase['value'] . "\n";
							} else {
								print $rowBase['value'] . '|' . $rowBase['count'] . "\n";
							}
						}
					}
				}
			}
		}
	}
}
