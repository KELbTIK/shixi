<?php

class SJB_Session
{
	public static function init($url)
	{
		// get setting from config.php
		$storageType = SJB_System::getSystemSettings('SESSION_STORAGE');
		if ($storageType != 'files') {
			$sessionStorage = new SessionStorage();
			session_set_save_handler(
				array($sessionStorage, 'open'),
				array($sessionStorage, 'close'),
				array($sessionStorage, 'read'),
				array($sessionStorage, 'write'),
				array($sessionStorage, 'destroy'),
				array($sessionStorage, 'gc')
			);
		}
		
		$path = SJB_Session::getSessionCookiePath();
		SJB_WrappedFunctions::ini_set('session.cookie_path', $path);
		Zend_Session::start();
		
		self::identificationUserSign();
	}
	
	private static function identificationUserSign()
	{
		if (SJB_Settings::getValue('sessionBindIP')) {
			$userIdentity = md5(SJB_Request::getvar('HTTP_USER_AGENT', '', 'SERVER') . SJB_Request::getvar('REMOTE_ADDR', '', 'SERVER'));
			if (self::getValue('userSign') !== $userIdentity) {
				session_unset();
				Zend_Session::regenerateId();
				self::setValue('userSign', $userIdentity);
			}
		}
	}
	
	public static function getSessionCookiePath()
	{
		$url_info = parse_url(SJB_System::getSystemSettings('USER_SITE_URL'));
		if (empty($url_info['path']))
			return '/';
		
		$path = $url_info['path'];
		if ($path[strlen($path) - 1] != '/')
			$path .= '/';
		return $path;
	}

	public static function getValue($name)
	{
		switch($name) {
			case 'tmp_file_storage':
				$result = SJB_DB::query("SELECT `tmp_file_storage` FROM `user_session_data_storage` WHERE `session_id` = ?s", self::getSessionId());
				if (!empty($result)) {
					$data = $result[0]['tmp_file_storage'];
					if (!empty($data))
						return unserialize($data);
				}
				return null;
				break;
		}

		if (isset($_SESSION[$name]))
			return $_SESSION[$name];
		return null;
	}

	public static function setValue($name, $value)
	{
		switch($name) {
			case 'current_user':
				// update user_session_data_storage for logged user
				SJB_DB::query("UPDATE `user_session_data_storage` SET `user_sid` = ?n WHERE `session_id` = ?s", $value['sid'], self::getSessionId());
				break;

			case 'tmp_file_storage':
				if (is_array($value))
					$value = serialize($value);
				// update user_session_data_storage for uploaded files value
				SJB_DB::query("UPDATE `user_session_data_storage` SET `tmp_file_storage` = ?s WHERE `session_id` = ?s", $value, self::getSessionId());
				// return: don't need to store this value in real session storage
				return;
				break;

			default:
				break;
		}

		$_SESSION[$name] = $value;
	}

	public static function unsetValue($name)
	{
		switch ($name) {
			case 'tmp_file_storage':
				// clear temporary data
				SJB_DB::query("UPDATE `user_session_data_storage` SET `tmp_file_storage` = NULL WHERE `session_id` = ?s", self::getSessionId());
				break;
		}
		unset($_SESSION[$name]);
	}

	public static function getSessionId()
	{
		return session_id();
	}

	public static function clearTemporaryData($maxLifeTime = null)
	{
		if (is_null($maxLifeTime)) {
			// get session.lifetime value by default
			$maxLifeTime = (integer) ini_get('session.gc_maxlifetime');
		}

		$expirationTime = time();
		//deleting pictures with temporary listings sids after 1 hour of storaging
		$unloadedPicturesSids = SJB_DB::query('SELECT * FROM `listings_pictures` WHERE LENGTH(`listing_sid`) >= ?n', strlen($expirationTime)-1);
		if (!empty($unloadedPicturesSids)) {
			$gallery = new SJB_ListingGallery();
			foreach ($unloadedPicturesSids as $k => $v) {
				if ( $v['listing_sid'] + 60*60*1 < $expirationTime)
					$gallery->deleteImageBySID($v['sid']);
			}
		}

		$uploadedFiles = SJB_DB::query("SELECT * FROM `uploaded_files` WHERE (`id` LIKE '%video_tmp') OR (`id` LIKE '%Logo_tmp') OR (`id` LIKE '%Resume_tmp')");
		foreach ($uploadedFiles as $key => $value) {
			if (!empty($value['creation_time'])) {
				if ($value['creation_time'] +60*60*1 < $expirationTime) {
					SJB_UploadFileManager::deleteUploadedFileByID($value['id']);
				}
			}
		}

		// clear temporary data from `user_session_data_storage`
		SJB_DB::query("DELETE FROM `user_session_data_storage` WHERE `last_activity` <= DATE_SUB(NOW(), INTERVAL ?n SECOND)", $maxLifeTime);

		// clear temporary uploaded files from sessions, where last activity is older than $maxLifeTime
		// 1. get from `session` all records older than $maxLifeTime
		$expiredSessions = SJB_DB::query("SELECT `session_id` FROM `session` WHERE `time` <= (UNIX_TIMESTAMP() - ?n)", $maxLifeTime);
		// 2. check uploaded_files for values with ID's of expired sessions
		$expiredFiles = array();
		foreach ($expiredSessions as $session) {
			$sessionId = $session['session_id'];
			$tmpFiles = SJB_DB::query("SELECT `id` FROM `uploaded_files` WHERE `id` LIKE '?w_%_tmp'", $sessionId);
			foreach ($tmpFiles as $tmpFile)
				$expiredFiles[] = $tmpFile['id'];
		}
		if (!empty($expiredFiles)) {
			// 3. clean temporary ID value from `listings_properties` table
			SJB_DB::query("UPDATE `listings_properties` SET `value` = '' WHERE `value` IN (?l)", $expiredFiles);
			// 4. delete temporary uploaded files by ID's
			foreach ($expiredFiles as $fileId)
				SJB_UploadFileManager::deleteUploadedFileByID($fileId);
		}

		return true;
	}
}



class SessionStorage
{

	public static function open($save_path, $session_name)
	{
		return true;
	}

	public static function close()
	{
		return true;
	}

	public static function read($id)
	{
		$res = SJB_DB::query('select * from session where `session_id` = ?s', $id);
		if (count($res) > 0)
			return (string) $res[0]['data'];
		return '';
	}

	public static function write($id, $session_data)
	{
		$user_sid = 0;
		if (isset($_SESSION['current_user']))
			$user_sid = $_SESSION['current_user']['sid'];
		if (count(SJB_DB::query('select * from session where `session_id` = ?s', $id)) > 0)
			SJB_DB::query('update session set `data` = ?s, `time` = ?s, `user_sid` = ?n where `session_id` = ?s', $session_data, time(), $user_sid, $id);
		else
			SJB_DB::query('insert into session (`session_id`, `data`, `time`, `user_sid`) values (?s, ?s, ?s, ?n)', $id, $session_data, time(), $user_sid);
		return true;
	}

	public static function destroy($id)
	{
		SJB_DB::query('delete from `session` where `session_id` = ?s', $id);
		return true;
	}

	public static function gc($maxLifeTime)
	{
		$expirationTime = time();
		//deleting pictures with temporary listings sids after 1 hour of storage
		$unloaded_pictures_sids = SJB_DB::query('SELECT * FROM `listings_pictures` WHERE LENGTH(`listing_sid`) >= ?n', strlen($expirationTime)-1);
		if (!empty($unloaded_pictures_sids)) {
			$gallery = new SJB_ListingGallery();			
			foreach ($unloaded_pictures_sids as $k => $v) {
				if ( $v['listing_sid'] + 60*60*1 < $expirationTime)
					$gallery->deleteImageBySID($v['sid']);
			}
		}		
		
		SJB_DB::query("delete from `session` where `time` + {$maxLifeTime} < {$expirationTime}");
		return true;
	}
	
}

