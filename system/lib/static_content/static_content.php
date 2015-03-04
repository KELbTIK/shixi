<?php

/**
 * @package ContentManagement
 * @subpackage StaticPages
 */
class SJB_StaticContent
{
	/**
	 * getting information about static pages
	 * Function gets information about static pages and returns it as array
	 * @return mixed it can be array (information about pages) or bool (only 'false' if operation has fallen)
	 */
	public static function getStaticContents()
	{
		$res = SJB_DB::query("SELECT * FROM `stat_pages`");
		if (empty($res)) {
			return false;
		}
		$pages = array();
		foreach ($res as $row)
			$pages[$row['sid']] = $row;
		return $pages;
	}

	/**
	 * getting information about static page
	 * Function gets information about static page and retunrs it as array
	 * @param integer $page_id ID of page
	 * @return mixed it can be array (information about page) or bool (only 'false' if operation has fallen)
	 */
	public static function getStaticContent($page_sid)
	{
		$res = SJB_DB::query("SELECT * FROM `stat_pages` WHERE `sid` = ?s", $page_sid);
		if (empty($res)) {
			return $res;
		}
		return array_shift($res);
	}

	public static function getStaticContentByIDAndLang($page_id, $lang)
	{
		$res = SJB_DB::query("SELECT * FROM `stat_pages` WHERE `id` = ?s AND `lang` = ?s", $page_id, $lang);
		if (empty($res)) {
			return false;
		}
		return array_shift($res);
	}

	/**
	 * adding new static page
	 * Function creates static pages
	 * @param string $name name of page
	 * @param string $url URL of page
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	public static function addStaticContent($contentInfo)
	{
		$page_id = $contentInfo['id'];
		$name 	 = $contentInfo['name'];
		$lang    = $contentInfo['lang'];
		if (empty($name)) {
			return false;
		}
		return SJB_DB::query("INSERT INTO `stat_pages` (`name`, `id`, `lang`) VALUES (?s, ?s, ?s)",
				$name, $page_id, $lang);
	}

	/**
	 * deleting static page
	 * Function removes static page by ID of it
	 * @param integer $page_id ID of page
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	public static function deleteStaticContent($page_sid)
	{
		if (!SJB_DB::query("DELETE FROM `stat_pages` WHERE `sid`= ?s", $page_sid))
			return false;
		return true;
	}

	/**
	 * changing information about static page
	 * Function changes information about static page by ID of it
	 * @param integer $page_id ID of page
	 * @param string $name name of page
	 * @param string $url URL of page
	 * @return bool 'true' if operation succeeded or 'false' otherwise
	 */
	public static function changeStaticContent($contentInfo, $page_sid)
	{
		$page_id  = $contentInfo['id'];
		$name	  = $contentInfo['name'];
		$content  = $contentInfo['content'];
		$lang     = $contentInfo['lang'];

		return SJB_DB::query("UPDATE `stat_pages` SET `name` = ?s, `id` = ?s, `content`= ?s, `lang` = ?s WHERE `sid` = ?s",
				$name, $page_id, $content, $lang, $page_sid);
	}

}