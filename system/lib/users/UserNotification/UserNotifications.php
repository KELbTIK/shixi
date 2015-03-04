<?php

class SJB_UserNotifications extends SJB_Object
{
	/**
	 * @var int
	 */
	private $userSID;

	public function __construct($info = null)
	{
		$this->db_table_name = 'users_notifications';
		$this->details = new SJB_UserNotificationDetails($info);
		$this->sid = SJB_Array::get($info, 'sid');
	}

	public function save()
	{
		SJB_ObjectDBManager::saveObject($this->db_table_name, $this);
		return $this->updateNotificationOwner();
	}

	public function update()
	{
		return SJB_ObjectDBManager::saveObject($this->db_table_name, $this);
	}

	private function updateNotificationOwner()
	{
		return SJB_DB::query('UPDATE `?w` SET `user_sid` = ?n WHERE `sid` = ?n', $this->db_table_name, $this->getUserSID(), $this->getSID());
	}

	/**
	 * @param int $userSID
	 */
	public function setUserSID($userSID)
	{
		$this->userSID = $userSID;
	}

	/**
	 * @return int
	 */
	public function getUserSID()
	{
		return $this->userSID;
	}
}
