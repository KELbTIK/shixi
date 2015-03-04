<?php

class SJB_GuestAlertManager extends SJB_ObjectManager
{
	const DB_TABLE_NAME = 'guest_alerts';

	public static function getGuestAlertInfoBySID($sid)
	{
		return parent::getObjectInfoBySID(self::DB_TABLE_NAME, $sid);
	}

	/**
	 * @param int $guestAlertSID
	 * @return SJB_GuestAlert
	 * @throws Exception
	 */
	public static function getObjectBySID($guestAlertSID)
	{
		$guestAlertInfo = self::getGuestAlertInfoBySID($guestAlertSID);
		if (empty($guestAlertInfo)) {
			throw new Exception ('No such Guest Alert found');
		}
		$guestAlert = new SJB_GuestAlert($guestAlertInfo);
		$guestAlert->setSID($guestAlertSID);
		$guestAlert->addConfirmationKeyProperty(SJB_Array::get($guestAlertInfo, 'confirmation_key'));
		$guestAlert->addStatusProperty(SJB_Array::get($guestAlertInfo, 'status'));
		return $guestAlert;
	}

	/**
	 * @param array $criteriaData
	 * @return string
	 */
	public static function getListingTypeIDFromCriteria($criteriaData)
	{
		$listingTypeID = '';
		$listingTypeCriteria = SJB_Array::get($criteriaData, 'listing_type');
		if (is_array($listingTypeCriteria))
			$listingTypeID = array_pop($listingTypeCriteria);
		return trim((string) $listingTypeID);
	}

	/**
	 * @param string $key
	 * @return SJB_GuestAlert
	 */
	public static function getGuestAlertByKey($key)
	{
		$guestAlertInfoReceived = SJB_GuestAlertManager::getGuestAlertInfoFromKey($key);
		$guestAlertSID = SJB_Array::get($guestAlertInfoReceived, 'sid');
		$keyReceived = SJB_Array::get($guestAlertInfoReceived, 'key');
		$guestAlert = SJB_GuestAlertManager::getObjectBySID($guestAlertSID);
		$guestAlert->validateReceivedKey($keyReceived);
		return $guestAlert;
	}

	/**
	 * @param string $key
	 * @return array
	 * @throws Exception
	 */
	public static function getGuestAlertInfoFromKey($key)
	{
		$dataFromKey = SJB_GuestAlert::getDataFromKey($key);
		if (!SJB_Array::get($dataFromKey,'sid') || !SJB_Array::get($dataFromKey, 'key'))
			throw new Exception('PARAMETERS_MISSED');
		return $dataFromKey;
	}

	public static function getGuestAlertsToNotify()
	{
		return SJB_DB::query('
				SELECT `sid`, `email`, `data`, `last_send`
				FROM `guest_alerts`
				WHERE `status` = ?s AND (
						(`last_send` != CURDATE()
							AND (`email_frequency` = \'daily\'
								OR `email_frequency` = \'\'
							)
						)
						OR (`last_send` <= (CURDATE() - INTERVAL 7 DAY)
							AND  `email_frequency` = \'weekly\'
						)
						OR (`last_send` <= (CURDATE() - INTERVAL 1 MONTH)
							AND  `email_frequency` = \'monthly\'
						)
						OR `last_send` IS NULL
					)
		  ', SJB_GuestAlert::STATUS_ACTIVE);
	}

	public static function deleteGuestAlertBySID($guestAlertSID)
	{
		return SJB_DB::query('DELETE FROM `guest_alerts` WHERE `sid` = ?n', $guestAlertSID);
	}

	public static function markGuestAlertAsSentBySID($guestAlertSID)
	{
		return SJB_DB::query('UPDATE `guest_alerts` SET `last_send` = CURDATE() WHERE `sid` = ?n', $guestAlertSID);
	}

	/**
	 * @param string $email
	 * @return bool|int|mixed
	 * @throws Exception
	 */
	public static function getGuestAlertSIDByEmail($email)
	{
		$sid = (int) SJB_DB::queryValue('SELECT `sid` FROM `guest_alerts` WHERE `email` = ?s', $email);
		if (!$sid) {
			throw new Exception ('GUEST_ALERT_NOT_FOUND_IN_SYSTEM');
		}
		return $sid;
	}

	public static function isGuestAlertUnSubscribedByEmail($email)
	{
		return SJB_DB::queryValue('SELECT `sid` FROM `guest_alerts` WHERE `email` = ?s AND `status` = ?s', $email, SJB_GuestAlert::STATUS_UNSUBSCRIBED);
	}
}
