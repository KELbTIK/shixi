<?php

class SJB_UserNotificationsManager
{
	/**
	 * @var SJB_NotificationGroups
	 */
	private $notificationGroups;

	/**
	 * @var SJB_User
	 */
	private $user;

	private $userGroupNotificationsInfo;

	/**
	 * @param SJB_User $user
	 */
	public function __construct(SJB_User $user)
	{
		$this->user = $user;
		$this->notificationGroups = new SJB_NotificationGroups();
	}

	public function addDefaultUserNotifications()
	{
		$userGroupNotificationsInfo = $this->getUserGroupNotificationsInfo();
		$userNotifications = new SJB_UserNotifications($userGroupNotificationsInfo);
		$userNotifications->setUserSID($this->user->getSID());
		$userNotifications->save();
	}

	public function getUserGroupNotificationsInfo()
	{
		if (is_null($this->userGroupNotificationsInfo))
			$this->retrieveUserGroupNotificationsInfo();

		return $this->userGroupNotificationsInfo;
	}

	public function retrieveUserGroupNotificationsInfo()
	{
		$userGroupNotificationsInfo = array();
		$userGroupNotifications = $this->getUserGroupNotificationsFromDB($this->user->getUserGroupSID());
		foreach ($userGroupNotifications as $notificationInfo) {
			$userGroupNotificationsInfo[$notificationInfo['id']] = $notificationInfo['value'];
		}
		$this->userGroupNotificationsInfo = $userGroupNotificationsInfo;
	}

	/**
	 * @return SJB_NotificationGroups
	 */
	public function getNotificationGroups()
	{
		return $this->notificationGroups;
	}

	/**
	 * @return array
	 */
	public function getEnabledForGroupUserNotifications()
	{
		$userGroupNotificationsInfo = $this->getUserGroupNotificationsInfo();
		$availableUserNotifications = $this->getNotificationGroups()->getUserSideNotifications();
		$enabledUserNotifications 	= array();

		foreach ($availableUserNotifications as $notificationGroupID => $notificationGroups) {
			foreach ($notificationGroups as $notificationID => $notificationInfo) {
				$userGroupNotificationValue = SJB_Array::get($userGroupNotificationsInfo, $notificationID);
				if (!empty($userGroupNotificationValue)) {
					$notificationInfo['group_value'] = $userGroupNotificationValue;
					$enabledUserNotifications[$notificationGroupID][$notificationID] = $notificationInfo;
				}
			}
		}

		return $enabledUserNotifications;
	}

	/**
	 * @return array
	 */
	public function getUserNotificationsInfo()
	{
		$result = SJB_DB::query('SELECT * FROM `users_notifications` WHERE `user_sid` = ?n', $this->user->getSID());
		$result = array_pop($result);
		return !empty($result) ? $result : array();
	}

	/**
	 * @param int $userGroupSID
	 * @return array
	 */
	public function getUserGroupNotificationsFromDB($userGroupSID)
	{
		$result = SJB_DB::query('SELECT `id`, `value` FROM `user_groups_properties` WHERE `object_sid` = ?n', $userGroupSID);
		return is_array($result) ? $result : array();
	}

	/**
	 * @param string $setting_name
	 * @param int $user_sid
	 * @return string|null
	 */
	public static function getSettingByName($setting_name, $user_sid)
	{
		$settings = SJB_DB::query('SELECT * FROM `users_notifications` WHERE `user_sid` = ?n', $user_sid);
		$settings = empty($settings) ? array() : array_pop($settings);
		return isset($settings[$setting_name]) ? $settings[$setting_name] : null;
	}
	
	public static function isUserNotifiedOnListingActivation($user_sid)
    {
		return SJB_UserNotificationsManager::getSettingByName('notify_on_listing_activation', $user_sid);
	}

	public static function isUserNotifiedOnListingDeactivation($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_user_on_listing_deactivation', $user_sid);
	}

	public static function isUserNotifiedOnListingDeletion($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_user_on_listing_deletion', $user_sid);
	}
	
	public static function isUserNotifiedOnListingExpiration($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_on_listing_expiration', $user_sid);
	}
	
	public static function isUserNotifiedOnContractExpiration($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_on_contract_expiration', $user_sid);
	}
	
	public static function isUserNotifiedOnListingApprove($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_on_listing_approve', $user_sid);
	}

	public static function isUserNotifiedOnListingReject($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_on_listing_reject', $user_sid);
	}

	public static function isUserNotifiedOnNewPersonalMessage($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_on_private_message', $user_sid);
	}

    public static function isUserNotifiedOnSubscriptionActivation($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_subscription_activation', $user_sid);
	}

	public static function getUsersAndDaysOnSubscriptionExpirationRemind()
	{
		return $result = SJB_DB::query('SELECT `user_sid`, `notify_subscription_expire_date_days` as `days` FROM `users_notifications` WHERE `notify_subscription_expire_date` = 1');
	}
	
	public static function getUsersAndDaysOnListingExpirationRemind()
	{
		return $result = SJB_DB::query('SELECT `user_sid`, `notify_listing_expire_date_days` as `days` FROM `users_notifications` WHERE `notify_listing_expire_date` = 1');
	}
	
	public static function isUserNotifiedOnApplicationsApproval($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_on_application_approve', $user_sid);
	}

    public static function isUserNotifiedOnApplicationsRejection($user_sid)
    {
        return SJB_UserNotificationsManager::getSettingByName('notify_on_application_reject', $user_sid);
    }

	public static function isUserNotifiedOnProfileDeactivation($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_user_on_deactivation', $user_sid);
	}

	public static function isUserNotifiedOnProfileDeletion($user_sid)
	{
		return SJB_UserNotificationsManager::getSettingByName('notify_user_on_deletion', $user_sid);
	}
}
