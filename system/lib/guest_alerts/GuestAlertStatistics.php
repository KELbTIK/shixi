<?php

class SJB_GuestAlertStatistics extends SJB_Statistics
{
	const EVENT_SENT = 'GuestAlertsSent';
	const EVENT_SUBSCRIBED = 'GuestAlertSubscribed';

	public static function saveEventSent($listingTypeSID, $guestAlertSID)
	{
		parent::addStatistics(self::EVENT_SENT, $listingTypeSID, $guestAlertSID);
	}

	public static function saveEventSubscribed($listingTypeSID, $guestAlertSID)
	{
		parent::addStatistics(self::EVENT_SUBSCRIBED, $listingTypeSID, $guestAlertSID);
	}
}
