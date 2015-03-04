<?php

class SJB_UserNotificationDetails extends SJB_ObjectDetails
{
	/**
	 * @var SJB_NotificationGroups
	 */
	private $notificationGroups;

	public function __construct($object_info)
	{
		$this->notificationGroups = new SJB_NotificationGroups();
		parent::SJB_ObjectDetails($object_info);
	}

	public function getDetails()
	{
		$notificationGroupsSet = $this->notificationGroups->getNotifications();
		$notifications = array();
		foreach ($notificationGroupsSet as $groupID => $groupNotifications) {
			foreach ($groupNotifications as $notificationID => &$notificationInfo) {
				if (SJB_Array::get($notificationInfo, SJB_NotificationGroups::LABEL_CONFIGURABLE_FOR_USER) < 1)
					unset($groupNotifications[$notificationID]);
				$this->prepareNotification($notificationInfo, $groupID);
			}
			$notifications = array_merge($notifications, $groupNotifications);
		}

		return $notifications;
	}

	/**
	 * @param array $notification
	 * @param string $groupID
	 */
	public function prepareNotification(&$notification)
	{
		if (in_array($notification['id'], $this->notificationGroups->getNotificationsWithIntegerType()))
			$notification['type'] = 'integer';
		else
			$notification['type'] = 'boolean';
		$notification['is_required'] = false;
		$notification['is_system'] = true;
	}
}
