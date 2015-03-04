<?php

define('PM_STATUS_NEW', 0);
define('PM_STATUS_READ', 1);
define('PM_STATUS_REPLIED', 2);

class SJB_PrivateMessage
{

	/**
	 * @var int
	 */
	var $user_id = 0;

	/**
	 * get page navigation
	 *
	 * @param unknown_type $page
	 * @param unknown_type $total
	 * @param unknown_type $per_page
	 * @return unknown
	 */
	public static function getNavigate($page = 1, $totalMessages = 1, $per_page = 10)
	{
		$total_page = ceil ( $totalMessages / $per_page );
		
		$tmp = array ();
		for ($i = 1; $i <= $total_page; $i ++) {
			if ($i > $total_page)
				break;
			if ($total_page == 1)
				break;
			$tmp [$i] = ($i == $page ? '' : $i);
		}
		return $tmp;
	}
	
	/**
	 * Mark messages as read. 
	 * Incoming param is hash of private messages SIDs or single message SID.
	 *
	 * @param array|integer $messageSIDs
	 */
	public static function markAsRead($messageSIDs)
	{
		if (empty($messageSIDs))
			return;
		if (is_array ( $messageSIDs )) {
			foreach ($messageSIDs as $key => $sid) {
				if (!SJB_PrivateMessage::isMyMessage($sid) || empty($sid))
					unset($messageSIDs[$key]);
			}
			SJB_DB::query('UPDATE `private_message` SET `status` = ?n WHERE `id` IN (?l)', PM_STATUS_READ, $messageSIDs);

		} else {
			$sid = intval($messageSIDs);
			if (SJB_PrivateMessage::isMyMessage($sid))
				SJB_DB::query ( 'UPDATE `private_message` SET `status` = ?n WHERE `id` = ?n', PM_STATUS_READ, $sid );
		}
	}
	
	
	/**
	 * Delete private messages.
	 * Incoming param is hash of private messages SIDs or single message SID.
	 * @param array|integer $messageSIDs
	 */
	public static function delete($messageSIDs)
	{
		if (empty($messageSIDs))
			return;
		if (is_array ( $messageSIDs )) {
			foreach ($messageSIDs as $key => $sid) {
				if (!SJB_PrivateMessage::isMyMessage( $sid ) || !is_numeric($sid) || empty($sid))
					unset($messageSIDs[$key]);
			}
			$sidsString = join(',', $messageSIDs);
			
			SJB_DB::query('DELETE FROM `private_message` WHERE `id` IN (?w)', $sidsString);
		} else {
			$sid = intval($messageSIDs);
			if (SJB_PrivateMessage::isMyMessage($sid))
				SJB_DB::query('DELETE FROM `private_message` WHERE `id` = ?n', $sid);
		}
	}
	
	/**
	 * Send private message
	 *
	 * @param integer $from
	 * @param integer $to
	 * @param string $subject
	 * @param string $message
	 * @param boolean $copy       Save copy in outbox
	 * @param boolean $reply_id   if we reply to message with $reply_id, mark it as replied
	 */
	public static function sendMessage($from, $to, $subject, $message, $copy = true, $reply_id = false, $cc = false, $anonym = 0)
	{
		$date = date ( 'Y-m-d H:i:s' );
		// to anonymous resume
		$anonym = $anonym ? $anonym : 0;

		$query = 'INSERT INTO `private_message` SET `from_id`=?n, `to_id`=?n, `data`=?s, `subject`=?s, `message`=?s, `anonym` = ?n';
		$mess_id = SJB_DB::query($query, $from, $to, $date, $subject, $message, $anonym);

		if ($mess_id) {
			if (SJB_UserNotificationsManager::isUserNotifiedOnNewPersonalMessage($to)) {
				$message_for_notification = SJB_PrivateMessage::readMessage($mess_id, true);
				SJB_Notifications::sendNewPrivateMessageLetter($to, $from, $message_for_notification, $cc);
			}
			if ($copy) {
				SJB_DB::query('INSERT INTO `private_message`
					SET `from_id`=?n, `to_id`=?n, `data`=?s, `subject`=?s, `message`=?s, `outbox`=1, `anonym` = ?n', $from, $to, $date, $subject, $message, $anonym);
			}
			if ($reply_id)
				SJB_DB::query('UPDATE `private_message` SET `status`=?n WHERE id = ?n', PM_STATUS_REPLIED, $reply_id);
		}
	}
	
	/**
	 * Get list of inbox messages by user id
	 *
	 * @param integer $user_id
	 * @param integer $page
	 * @param integer $per_page
	 * @return array
	 */
	public static function getListInbox($user_id, $page = 1, $per_page = 10)
	{
		$from = ($page - 1) * $per_page;
		$res = SJB_DB::query("SELECT * FROM `private_message`
			WHERE `to_id` = ?n AND `outbox` = 0 
			ORDER BY `id` DESC 
			LIMIT {$from}, {$per_page}", $user_id);
			
		$list = array();
		foreach ($res as $one)
			$list[] = SJB_PrivateMessage::readMessage($one['id'], true);
		return $list;
	}
	
	/**
	 * Get list of outbox messages by user id
	 *
	 * @param integer $user_id
	 * @param integer $page
	 * @param integer $per_page
	 * @return array
	 */
	public static function getListOutbox($user_id, $page = 1, $per_page = 10)
	{
		$from = ($page - 1) * $per_page;
		$res = SJB_DB::query("
			SELECT * FROM `private_message` 
			WHERE `from_id` = ?n AND `outbox` = 1 
			ORDER BY `id` DESC 
			LIMIT {$from}, {$per_page}", $user_id );
			
		$list = array();
		foreach ($res as $one)
			$list[] = SJB_PrivateMessage::readMessage($one ['id'], true);
		return $list;
	}

	/**
	 * read private message
	 *
	 * @param integer $id
	 * @param boolean $system   true - if admin
	 * @return array|boolean
	 */
	public static function readMessage($id, $system = false)
	{
		$res = SJB_DB::query('SELECT * FROM `private_message` WHERE `id`=?n', $id);
		if (isset($res[0]['data'])) {
			$status = $res[0]['status'];
			if (!$system && $status != PM_STATUS_REPLIED)
				SJB_DB::query('UPDATE `private_message` SET `status` = ?n WHERE id = ?n', PM_STATUS_READ, $id);
			
			$from_user = SJB_UserManager::getUserInfoBySID($res[0]['from_id']); // LastName FirstName username
			$to_user   = SJB_UserManager::getUserInfoBySID($res[0]['to_id']);
			
			$res[0]['from_name']       = $from_user['username'];
			$res[0]['from_first_name'] = (isset($from_user['FirstName']) ? $from_user['FirstName'] : $from_user['ContactName']);
			$res[0]['from_last_name']  = (isset($from_user['LastName']) ? $from_user['LastName'] : '');
			
			$res[0]['to_name']       = $to_user['username'];
			$res[0]['to_first_name'] = (isset($to_user['FirstName']) ? $to_user['FirstName'] : $to_user['ContactName']);
			$res[0]['to_last_name']  = (isset($to_user['LastName']) ? $to_user['LastName'] : '');
			$res[0]['time']          = strtotime($res[0]['data']);
			$res[0]['message']       = stripslashes($res[0]['message']);
			return $res [0];
		}
		return false;
	}
	
	/**
	 * Strip tags from string
	 *
	 * @param string $string
	 * @return string
	 */
	public static function cleanText($string)
	{
		if (SJB_Settings::getValue('escape_html_tags') === 'htmlpurifier' && SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != 'admin') {
			$filters = str_replace(',', '', SJB_Settings::getSettingByName('htmlFilter')); // выбираем заданные админом тэги для конвертации
			$string = strip_tags($string, $filters);
		}
		return $string;
	}

	/**
	 * Check message owner by message id
	 *
	 * @param integer $id
	 * @return boolean
	 */
	public static function isMyMessage($id)
	{
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE' ) == 'admin')
			return true;
		
		$user_id = SJB_UserManager::getCurrentUserSID();
		$mes     = SJB_PrivateMessage::readMessage ( $id, true );
		
		if ($mes)
			return ($mes['from_id'] == $user_id || $mes['to_id'] == $user_id);
		return false;
	}
	
	// PAGING - COUNTING	
	/**
	 * Get total count of inbox messages 
	 *
	 * @param integer $user_id
	 * @return integer
	 */
	public static function getTotalInbox($user_id)
	{
		$result = SJB_DB::query('SELECT COUNT(*) AS `num` FROM `private_message`
			WHERE `to_id` = ?n AND `outbox` = 0', $user_id);
		return (isset($result[0]['num']) ? $result[0]['num'] : 0);
	}
	
	/**
	 * Get total count of outbox messages
	 *
	 * @param integer $user_id
	 * @return integer
	 */
	public static function getTotalOutbox($user_id)
	{
		$result = SJB_DB::query('SELECT COUNT(*) AS `num` FROM `private_message`
			WHERE `from_id` = ?n AND `outbox` = 1', $user_id );
		
		return (isset($result[0]['num']) ? $result[0]['num'] : 0);
	}

	/**
	 * Get count of unread private messages by user id
	 *
	 * @param integer $user_id
	 * @return integer
	 */
	public static function getCountUnreadMessages($user_id)
	{
		$result = SJB_DB::query('SELECT COUNT(*) AS `num` FROM `private_message`
			WHERE `to_id` = ?n AND `status` = 0 AND `outbox` = 0', $user_id);
		return (isset($result[0]['num']) ? $result[0]['num'] : 0);
	}

	/**
	 * Get count of contacts by user id
	 *
	 * @param integer $userID
	 * @return integer
	 */
	public static function getTotalContacts($userID)
	{
		return SJB_DB::queryValue('SELECT COUNT(*) FROM `private_message_contacts` WHERE `user_sid` = ?n', $userID);
	}

	/**
	 * Get list of contacts by user id
	 *
	 * @param integer $userSID
	 * @param integer $page
	 * @param integer $perPage
	 * @return array
	 */
	public static function getContacts($userSID, $page = 1, $perPage = 10)
	{
		$from = ($page - 1) * $perPage;
		$res = SJB_DB::query("SELECT * FROM `private_message_contacts` WHERE `user_sid` = ?n LIMIT {$from}, {$perPage}", $userSID);
		$list = array();
		foreach ($res as $contactInfo) {
			$userInfo = SJB_UserManager::getUserInfoBySID($contactInfo['contact_sid']);
			$userInfo['user_group_id'] = SJB_UserGroupManager::getUserGroupIDBySID($userInfo['user_group_sid']);
			$list[$contactInfo['contact_sid']] = $userInfo;
		}
		return $list;
	}

	/**
	 *
	 * @static
	 * @param int $userSID
	 * @param int $contactSID
	 * @param $error
	 * @return array|bool|int
	 */
	public static function saveContact($userSID, $contactSID, &$error = '')
	{
		if ($userSID && $contactSID) {
			if (self::getContact($userSID, $contactSID)) {
				$error = 'This user is already in your contact list';
				return false;
			}

			$contactInfo = SJB_UserManager::getUserInfoBySID($contactSID);

			if ($contactInfo) {
				return SJB_DB::query('INSERT INTO `private_message_contacts` SET `user_sid` = ?n, `contact_sid` = ?n', $userSID, $contactSID);
			}
			else {
				$error = 'User Profile Information was not saved';
			}
		}
		else {
			$error = 'Missed parameters';
		}
		return false;
	}

	/**
	 * Delete contact(s) from database by UserSID, ContactSID
	 * @static
	 * @param int $userSID
	 * @param int|array $contactSID
	 * @return array|bool|int
	 */
	public static function deleteContact($userSID, $contactSID)
	{
		if (empty($contactSID))
			return false;

		if (is_array($contactSID)) {

			foreach ($contactSID as $key => $sid) {
				if (!is_numeric($sid) || empty($sid))
					unset($contactSID[$key]);
			}

			$sidsString = implode(',', $contactSID);
			SJB_DB::query('DELETE FROM `private_message_contacts` WHERE `contact_sid` IN (?w) AND `user_sid` = ?n', $sidsString, $userSID);
		}
		else {
			return SJB_DB::query('DELETE FROM `private_message_contacts` WHERE `contact_sid` = ?n AND `user_sid` = ?n', $contactSID, $userSID);
		}
	}

	/**
	 * to check if such contact alredy exists for user
	 * @static
	 * @param int $userSID
	 * @param int $contactSID
	 * @return array|bool|int
	 */
	public static function getContact($userSID, $contactSID)
	{
		return SJB_DB::query('SELECT `contact_sid` FROM `private_message_contacts` WHERE `user_sid` = ?n AND `contact_sid` = ?n', $userSID, $contactSID);
	}

	/**
	 * retrieve user info like template structure
	 * @static
	 * @param int $userSID
	 * @param int $contactSID
	 * @return array|null
	 */
	public static function getContactInfo($userSID, $contactSID)
	{
		$result = SJB_DB::query('SELECT `contact_sid`, `note` FROM `private_message_contacts` WHERE `user_sid` = ?n AND `contact_sid` = ?n', $userSID, $contactSID);

		if (!empty($result)) {
			$contactInfo = array_pop($result);
			$contact = SJB_UserManager::getObjectBySID($contactInfo['contact_sid']);
			$contactInfo2 = !empty($contact) ? SJB_UserManager::createTemplateStructureForUser($contact) : null;
			if ($contactInfo2) {
				return array_merge($contactInfo, $contactInfo2);
			}

		}
		return null;
	}

	/**
	 * @static
	 * @param int $userSID
	 * @param int $contactSID
	 * @param string $note
	 * @return array|bool|int
	 */
	public static function saveContactNote($userSID, $contactSID, $note)
	{
		$note = trim($note);
		if ($userSID && $contactSID && $note) {
			return SJB_DB::query('UPDATE `private_message_contacts` SET `note` = ?s WHERE `user_sid` = ?n AND `contact_sid` = ?n',
				$note, $userSID, $contactSID);
		}
		return false;
	}

	public static function deleteNonexistentContacts($userSID)
	{
		$contacts = SJB_DB::query("SELECT `contact_sid` FROM `private_message_contacts` WHERE `user_sid` = ?n", $userSID);
		foreach ($contacts as $contactInfo) {
			$userInfo = SJB_UserManager::getUserInfoBySID($contactInfo['contact_sid']);
			if (empty($userInfo)) {
				SJB_PrivateMessage::deleteContact($userSID, $contactInfo['contact_sid']);
			}
		}
	}
}