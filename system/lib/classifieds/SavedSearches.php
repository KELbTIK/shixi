<?php

/**
 * Saved Searches manager
 */
class SJB_SavedSearches
{
	public static function saveSearchOnDB($requested_data, $search_name, $user_sid, $enableNotify = false, $isAlert = false, $emailFrequency = false)
	{
		$is_alert = 0;
		if ($isAlert)
			$is_alert = 1;

		if ($enableNotify)
			$sid = SJB_DB::query("INSERT INTO saved_searches SET user_sid = ?n, name = ?s, data = ?s, is_alert = ?s, auto_notify = '1', last_send = CURDATE(), email_frequency=?s", $user_sid, $search_name, serialize($requested_data), $is_alert, $emailFrequency);
		else
			$sid = SJB_DB::query("INSERT INTO saved_searches SET user_sid = ?n, name = ?s, data = ?s, is_alert = ?s, last_send = CURDATE(), email_frequency=?s", $user_sid, $search_name, serialize($requested_data), $is_alert, $emailFrequency);

		if ($isAlert && $sid) {
			$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($requested_data['listing_type']['equal']);
			SJB_Statistics::addStatistics('addAlert', $listingTypeSID, $sid);
		}
	}

	public static function updateSearchOnDB($requested_data, $search_id, $user_sid, $search_name = false, $emailFrequency = false)
	{
		if ($search_name)
			SJB_DB::query("UPDATE saved_searches SET data = ?s, name = ?s, email_frequency=?s WHERE sid =?n AND user_sid = ?n", serialize($requested_data), $search_name, $emailFrequency, $search_id, $user_sid);
		else {
			if ($user_sid)
				SJB_DB::query("UPDATE saved_searches SET data = ?s, email_frequency=?s WHERE sid =?n AND user_sid = ?n", serialize($requested_data), $emailFrequency, $search_id, $user_sid);
			else
				SJB_DB::query("UPDATE saved_searches SET data = ?s, email_frequency=?s WHERE sid =?n ", serialize($requested_data), $emailFrequency, $search_id);
		}
	}

	public static function getSavedSearchesFromDB($user_sid)
	{
		$saved_searches = SJB_DB::query("SELECT *, sid AS id FROM saved_searches WHERE user_sid = ?n  AND is_alert = 0", $user_sid);
		foreach ($saved_searches as $key => $search_info)
			$saved_searches[$key]['data'] = unserialize($search_info['data']);
		return $saved_searches;
	}

	public static function getSavedJobAlertFromDB($user_sid)
	{
		$saved_searches = SJB_DB::query("SELECT *, sid AS id FROM saved_searches WHERE user_sid = ?n AND is_alert=1", $user_sid);
		foreach ($saved_searches as $key => $search_info)
			$saved_searches[$key]['data'] = unserialize($search_info['data']);
		return $saved_searches;
	}

	public static function getSavedJobAlertFromDBBySearchSID($search_sid)
	{
		$saved_searches = SJB_DB::query("SELECT *, sid AS id FROM saved_searches WHERE  sid = ?n", $search_sid);
		foreach ($saved_searches as $key => $search_info)
			$saved_searches[$key]['data'] = unserialize($search_info['data']);
		return $saved_searches;
	}

	public static function deleteSearchFromDBbySID($search_sid)
	{
		SJB_DB::query("DELETE FROM saved_searches WHERE sid = ?n", $search_sid);
	}

	public static function deleteUserSearchesFromDB($user_sid)
	{
		return SJB_DB::query("DELETE FROM saved_searches WHERE user_sid = ?n", $user_sid);
	}

	public static function disableSearchAutoNotify($user_sid, $saved_search_sid)
	{
		SJB_DB::query("UPDATE saved_searches SET auto_notify = '0' WHERE user_sid = ?n AND sid = ?n", $user_sid, $saved_search_sid);
	}

	public static function enableSearchAutoNotify($user_sid, $saved_search_sid)
	{
		SJB_DB::query("UPDATE saved_searches SET auto_notify = '1' WHERE user_sid = ?n AND sid = ?n", $user_sid, $saved_search_sid);
	}

	public static function getAutoNotifySavedSearches()
	{
		return SJB_DB::query("SELECT ss.*, ss.sid AS id 
							  FROM saved_searches ss 
							  INNER JOIN `users` u ON ss.`user_sid`=u.`sid`
							  WHERE ss.auto_notify = 1 
							  AND ss.is_alert = 1 
							  AND ((ss.last_send != current_date AND  (ss.email_frequency='daily' OR ss.email_frequency='')) 
							  OR (ss.last_send <= (CURDATE() - INTERVAL 7 DAY) AND  ss.email_frequency='weekly') 
							  OR (ss.last_send <= (CURDATE() - INTERVAL 1 MONTH) AND  ss.email_frequency='monthly')) 
							  AND u.`active`=1");
	}

	public static function getAutoNotifySavedSearchesForET()
	{
		$result = SJB_DB::query('SELECT ss.*, ss.sid AS id
							  FROM saved_searches ss
							  INNER JOIN `users` u ON ss.`user_sid`=u.`sid`
							  WHERE ss.auto_notify = 1
							  AND ss.is_alert = 1
							  AND u.`active`=1 LIMIT 1');
		if (!empty($result)) {
			return array_pop($result);
		}
		return false;
	}

	public static function buildCriteriaFields($criteria)
	{
		$criteria_fields = array();
		foreach ($criteria as $criteria_name => $criteria_values)
			$criteria_fields[$criteria_name] = SJB_SavedSearches::buildCriterionField($criteria_name, $criteria_values);
		return $criteria_fields;
	}

	public static function buildCriterionField($criteria_name, $criterion)
	{
		$result = array();
		if (is_array($criterion)) {
			foreach ($criterion as $criterion_name => $criterion_value)
				if (is_array($criterion_value)) {
					foreach ($criterion_value as $ext_criterion_name => $ext_criterion_value) {
						$ext_criterion_value = htmlentities($ext_criterion_value, ENT_QUOTES, "UTF-8");
						$result[] = "<input type='hidden' name='{$criteria_name}[$criterion_name][$ext_criterion_name]' value='$ext_criterion_value' />";
					}
				}
				else {
					$criterion_value = htmlentities($criterion_value, ENT_QUOTES, "UTF-8");
					$result[] = "<input type='hidden' name='{$criteria_name}[$criterion_name]' value='$criterion_value' />";
				}
		}
		else {
			$criterion = htmlentities($criterion, ENT_QUOTES, "UTF-8");
			$result[] = "<input type='hidden' name='{$criteria_name}' value='$criterion' />";
		}

		return $result;
	}
}

