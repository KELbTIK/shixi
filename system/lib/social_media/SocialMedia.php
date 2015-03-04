<?php

class SJB_SocialMedia extends  SJB_Object
{
	public $db_table_name;
	public $details;
	public $common_fields;

	/**
	 * @param $feed
	 */
	public function saveFeed($feed)
	{
		SJB_ObjectDBManager::saveObject($this->db_table_name, $feed);
	}

	/**
	 * @param $sid
	 * @return array|bool|int
	 */
	public function deleteFeed($sid)
	{
		return SJB_ObjectDBManager::deleteObject($this->db_table_name, $sid);
	}

	/**
	 * @param string $networkID
	 * @param string $active
	 * @return array|bool|int|null
	 */
	public static function getFeedsInfoByNetworkID($networkID = '', $active = '')
	{
		$queryForActive = '';
		if (!empty($active)) {
			$queryForActive = ' WHERE `active` = ' . $active;
		}
		if ($networkID) {
			$tableName = $networkID . "_feeds";
			return SJB_DB::query("SELECT * FROM `?w`{$queryForActive} ORDER BY `sid`", $tableName);
		}
		return null;
	}

	/**
	 * @param $networkID
	 * @param $sid
	 * @return array|null
	 */
	public static function getFeedInfoByNetworkIdAndSID($networkID, $sid)
	{
		$feedInfo = SJB_ObjectDBManager::getObjectInfo($networkID . "_feeds", $sid);
		if (!empty($feedInfo)) {
			$feedInfo['id'] = $feedInfo['sid'];
			return $feedInfo;
		}
		return null;
	}

	public static function updateFeedStatus($tableName, $active, $sid)
	{
		SJB_DB::query('UPDATE ?w SET `active` = ?n WHERE `sid` = ?n', $tableName, $active, $sid);
	}

	/**
	 * @param array $feedInfo
	 * @param string|null $currentDate date format Y-m-d
	 * @return bool
	 */
	public static function isFeedExpired(array $feedInfo, $currentDate = null)
	{
		if (!$currentDate) {
			$currentDate = date('Y-m-d', time());
		}
		
		return isset($feedInfo['expiration_date']) && $feedInfo['expiration_date'] && $currentDate > $feedInfo['expiration_date'];
	}
}
