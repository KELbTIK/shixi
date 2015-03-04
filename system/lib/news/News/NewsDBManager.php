<?php

class SJB_NewsDBManager extends SJB_ObjectDBManager
{
	
	public static function saveNewsArticle($articleObject)
	{
		parent::saveObject('news', $articleObject);
	}

	public static function deleteArticleBySID($articleSid)
	{
		parent::deleteObjectInfoFromDB('news', $articleSid);
	}
	
	public static function getLatestNewsByCount($count, $lang)
	{
		// try to object operation
		$sids = SJB_DB::query("SELECT `sid` FROM `news` WHERE `active` = 1 AND (`date` < NOW() OR `date` IS NULL) AND `language` = ?s ORDER BY `date` DESC LIMIT ?n", $lang, $count);
		$articles = array();
		foreach ($sids as $item) {
			$articleInfo = SJB_NewsDBManager::getObjectInfo('news', $item['sid']);
			$articleObj  = new SJB_NewsArticle($articleInfo);
			$articleObj->setSID($item['sid']);
			$articles[$item['sid']] = $articleObj;
		}
		return $articles;
	}
	
	public static function getRandomNewsByCount($count, $lang)
	{
		// try to object operation
		$sids = SJB_DB::query("SELECT `sid` FROM `news` WHERE `active` = 1 AND (`date` < NOW() OR `date` IS NULL) AND `language` = ?s ORDER BY RAND() DESC LIMIT ?n", $lang, $count);
		$articles = array();
		foreach ($sids as $item) {
			$articleInfo = SJB_NewsDBManager::getObjectInfo('news', $item['sid']);
			$articleObj  = new SJB_NewsArticle($articleInfo);
			$articleObj->setSID($item['sid']);
			$articles[$item['sid']] = $articleObj;
		}
		return $articles;
	}

	public static function getAllNewsByLanguage($lang = 'en')
 	{
 		$sids = SJB_DB::query("SELECT `sid` FROM `news` WHERE `language` = ?s ORDER BY `date` DESC", $lang);
 		$articles = array();
		foreach ($sids as $item) {
			$articleInfo = SJB_NewsDBManager::getObjectInfo('news', $item['sid']);
			$articleObj  = new SJB_NewsArticle($articleInfo);
			$articleObj->setSID($item['sid']);
			$articles[$item['sid']] = $articleObj;
		}
		return $articles;
 	}
 	
	public static function getAllActiveNewsByLanguage($lang = 'en')
 	{
 		$sids = SJB_DB::query("SELECT `sid` FROM `news` WHERE `language` = ?s AND active = 1 ORDER BY `date` DESC", $lang);
 		$articles = array();
		foreach ($sids as $item) {
			$articleInfo = SJB_NewsDBManager::getObjectInfo('news', $item['sid']);
			$articleObj  = new SJB_NewsArticle($articleInfo);
			$articleObj->setSID($item['sid']);
			$articles[$item['sid']] = $articleObj;
		}
		return $articles;
 	}

  	/**
 	 * Get image ID by news sid
 	 * @param integer $articleSid
 	 * @return string|false
 	 */
 	public static function getImageFileIDByArticleSID($articleSid)
 	{
 		$result = SJB_DB::query("SELECT `image` FROM `news` WHERE `sid` = ?n", $articleSid);
 		if (empty($result)) {
 			return false;
 		}
 		return $result[0]['image'];
 	}
 	
	/**
 	 * activate news item by SID
 	 * @param integer $itemSID
 	 */
 	public static function activateItemBySID($itemSID)
 	{
 		return SJB_DB::query("UPDATE `news` SET `active` = 1 WHERE `sid` = ?n", $itemSID);
 	}
 	
 	/**
 	 * deactivate news item by SID
 	 * @param integer $itemSID
 	 */
 	public static function deactivateItemBySID($itemSID)
 	{
 		return SJB_DB::query("UPDATE `news` SET `active` = 0 WHERE `sid` = ?n", $itemSID);
 	}
}