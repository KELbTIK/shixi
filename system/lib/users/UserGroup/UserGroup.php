<?php

class SJB_UserGroup extends SJB_Object
{
	function SJB_UserGroup($user_group_info = null)
	{
		$this->db_table_name = 'user_groups';
		$this->details = new SJB_UserGroupDetails($user_group_info);
	}

	public function getNotifications()
	{
		return $this->details->getNotificationGroups()->getNotifications();
	}

	public function getNotificationsGroups()
	{
		return $this->details->getNotificationGroups()->getGroups();
	}
}
