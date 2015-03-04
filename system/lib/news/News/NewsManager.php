<?php

class SJB_NewsManager extends SJB_ObjectManager
{
	
	static $uploadFileManager = null;
	
	/**
	 * get instance of upload file manager
	 * @return SJB_UploadFileManager
	 */
	private static function getUploadFileManager()
	{
		if (self::$uploadFileManager == null)
			self::$uploadFileManager = new SJB_UploadFileManager();
		return self::$uploadFileManager;
	}

	/**
	 * Save article in database
	 * @param SJB_NewsArticle $articleObject
	 */
	public static function saveNewsArticle($articleObject)
	{
		SJB_NewsDBManager::saveNewsArticle($articleObject);
	}

 	public static function getAllNews($lang = 'en', $active = false)
 	{
 		if ($active)
 			return SJB_NewsDBManager::getAllActiveNewsByLanguage($lang);
 		return SJB_NewsDBManager::getAllNewsByLanguage($lang);
 	}
 	
 	/**
 	 * delete article by SID
 	 * @param integer $articleSid
 	 */
 	public static function deleteArticleBySID($articleSid)
 	{
 		// delete picture
		$image = self::getImageFileIDByArticleSID($articleSid);
		if ($image) {
			$uploadFileManager = self::getUploadFileManager();
			$uploadFileManager->deleteUploadedFileByID($image);
		}
		// delete article
 		SJB_NewsDBManager::deleteArticleBySID($articleSid);
 	}

 	/**
 	 * Delete image of news article by article SID
 	 * @param integer $articleSid
 	 */
 	public static function deleteArticleImageByArticleSid($articleSid)
 	{
 		// delete picture
		$image = self::getImageFileIDByArticleSID($articleSid);
		if ($image) {
			$uploadFileManager = self::getUploadFileManager();
			$uploadFileManager->deleteUploadedFileByID($image);
		}
 	}
	
 	/**
 	 * get count of all news
 	 * @param integer $categorySid
 	 * @return integer
 	 */
 	public static function getAllNewsCount($categorySid = null, $lang = 'en', $active = false)
 	{
 		$activeParam = '';
 		if ($active) {
 			if (empty($categorySid) && ($lang === null || $lang == 'all') ) {
 				$activeParam = ' WHERE active = 1 ';
 			} else {
 				$activeParam = ' AND active = 1 ';
 			}
 		}
 		if (empty($categorySid)) {
 			if ($lang === null || $lang == 'all') {
 				return SJB_DB::queryValue("SELECT count(*) as count FROM `news` {$activeParam} ORDER BY `date`");
 			} else {
 				return SJB_DB::queryValue("SELECT count(*) as count FROM `news` WHERE `language` = ?s {$activeParam} ORDER BY `date`", $lang);
 			}
 		}
		if ($lang === null || $lang = 'all') {
			return SJB_DB::queryValue("SELECT count(*) as count FROM `news` WHERE `category_id` = ?n {$activeParam} ORDER BY `date`", $categorySid);
		}
		return SJB_DB::queryValue("SELECT count(*) as count FROM `news` WHERE `category_id` = ?n AND `language` = ?s {$activeParam} ORDER BY `date`", $categorySid, $lang);
 	}

 	/**
 	 * get news by current page
 	 * @param integer $page
 	 * @param integer $itemsPerPage
 	 * @param integer $categorySid
 	 */
 	public static function getNewsByPage($page = 1, $itemsPerPage = 10, $categorySid = null, $lang = 'en', $active = false)
 	{
 		$start = ($page - 1) * $itemsPerPage;

 		$activeParam = '';
 		if ($active) {
 			$activeParam = 'AND `active` = 1 AND `date` < NOW()';
 		}
 		
 		if (empty($categorySid)) {
 			$result = SJB_DB::query("SELECT * FROM `news` WHERE `language` = ?s {$activeParam} ORDER BY `date` DESC LIMIT ?n,?n", $lang, $start, $itemsPerPage);
 		} else {
 			$result = SJB_DB::query("SELECT * FROM `news` WHERE `category_id` = ?n AND `language` = ?s {$activeParam} ORDER BY `date` DESC LIMIT ?n,?n", $categorySid, $lang, $start, $itemsPerPage);
 		}


 		$upload_manager = new SJB_UploadFileManager();

 		foreach ($result as $key => $value) {
 			$result[$key]['image_link'] = '';
 			if (!empty($value['image'])) {
 				$result[$key]['image_link'] = $upload_manager->getUploadedFileLink($value['image']);
 			}
 		}
 		return $result;
 	}
 	
 	/**
 	 * activate news item by SID
 	 * @param integer $itemSID
 	 */
 	public static function activateItemBySID($itemSID)
 	{
 		return SJB_NewsDBManager::activateItemBySID($itemSID);
 	}
 	
 	/**
 	 * deactivate news item by SID
 	 * @param integer $itemSID
 	 */
 	public static function deactivateItemBySID($itemSID)
 	{
 		return SJB_NewsDBManager::deactivateItemBySID($itemSID);
 	}
 	
 	 /**
 	 * get latest news by count
 	 * @param integer $count
 	 * @return array
 	 */
 	public static function getLatestNews($count = 4, $lang = 'en', $selectionMode = 'latest')
 	{
        if ($selectionMode == 'rotation')
            return SJB_NewsDBManager::getRandomNewsByCount($count, $lang);
		return SJB_NewsDBManager::getLatestNewsByCount($count, $lang);
 	}
 	
 	/**
 	 * get expired news
 	 * @return array
 	 */
 	public static function getExpiredNews()
 	{
 		return SJB_DB::query("SELECT * FROM `news` WHERE `expiration_date` < NOW() ORDER BY `date`");
 	}
 	
 	/**
 	 * get active news item by SID
 	 * @param integer $itemSID
 	 * @return boolean|array
 	 */
 	public static function getActiveItemBySID($itemSID)
 	{
 		$result = SJB_DB::query("SELECT * FROM `news` WHERE `active` = 1 AND `sid` = ?n", $itemSID);
 		if (empty($result))
 			return false;
 		$result = array_pop($result);
 		$upload_manager = new SJB_UploadFileManager();
 		$result['image_link'] = '';
 		if (!empty($result['image']))
 			$result['image_link'] = $upload_manager->getUploadedFileLink($result['image']);
 		return $result;
 	}
 	
 	/**
 	 * Get all news categories list
 	 */
 	public static function getCategories($langId = 'en')
 	{
 		$result     = SJB_DB::query("SELECT * FROM `news_categories` ORDER BY `order`");
 		$categories = array();
 		
 		if (empty($result)) {
 			return $categories;
 		}
 		
 		foreach ($result as $item) {
 			$articles = self::getActiveArticlesByCategorySid($item['id'], 'id', 'ASC', $langId);
 			$counter  = count($articles);
 			$categories[] = array(
 				'sid'  => $item['id'],
 				'name' => $item['name'],
 				'count' => $counter,
 			);
 		}
 		return $categories;
 	}
 	
 	/**
 	 * Add new category in News Category list
 	 *
 	 * @param string $name
 	 * @return integer|false
 	 */
 	public static function addCategory($name)
 	{
 		$maxOrder = SJB_DB::queryValue("SELECT MAX(`order`) FROM `news_categories`");
		$maxOrder = empty($maxOrder) ? 0 : $maxOrder + 1;
 		return SJB_DB::query("INSERT INTO `news_categories` SET `name` = ?s, `order` = ?n", $name, $maxOrder);
 	}
 	
 	/**
 	 * Rename category in News Category list
 	 *
 	 * @param integer $sid
 	 * @param string $name
 	 * @return unknown
 	 */
	public static function updateCategory($sid, $name)
 	{
 		return SJB_DB::query("UPDATE `news_categories` SET `name` = ?s WHERE `id` = ?n", $name, $sid);
 	}
 	
 	/**
 	 * check exists category by name
 	 *
 	 * @param string $name
 	 * @return boolean
 	 */
 	public static function checkExistsCategoryName($name)
 	{
 		$result = SJB_DB::query("SELECT * FROM `news_categories` WHERE `name` = ?s LIMIT 1", $name);
 		return !empty($result);
 	}

 	/**
 	 * Delete category by SID
 	 *
 	 * @param integer $sid
 	 * @return unknown
 	 */
 	public static function deleteCategoryBySid($sid)
 	{
 		$category = self::getCategoryBySid($sid);
 		// DO NOT DELETE ARCHIVE!!!
 		if (!empty($category) && strtolower($category['name']) == 'archive') {
 			return false;
 		}
 		
 		// DELETE ALL NEWS BY THIS CATEGORY
 		SJB_DB::query("DELETE FROM `news` WHERE `category_id` = ?n", $sid);
 		return SJB_DB::query("DELETE FROM `news_categories` WHERE `id` = ?n LIMIT 1", $sid);
 	}
 	
 	/**
 	 * Get category data by SID
 	 *
 	 * @param integer $sid
 	 * @return array
 	 */
 	public static function getCategoryBySid($sid)
 	{
 		$result = SJB_DB::query("SELECT * FROM `news_categories` WHERE `id` = ?n LIMIT 1", $sid);
 		if (empty($result)) {
 			return false;
 		}
 		$result        = array_pop($result);
 		$result['sid'] = $result['id'];
 		
 		return $result;
 	}
 	
 	/**
 	 * Get category data by category name
 	 *
 	 * @param string $name
 	 * @return array
 	 */
  	public static function getCategoryByName($name)
 	{
 		$result = SJB_DB::query("SELECT * FROM `news_categories` WHERE `name` = ?s LIMIT 1", $name);
 		if (empty($result)) {
 			return false;
 		}
 		$result        = array_pop($result);
 		$result['sid'] = $result['id'];
 		
 		return $result;
 	}
 	
	public static function getNewsArticleInfoBySid($sid)
 	{
 		return SJB_NewsDBManager::getObjectInfo('news', $sid);
 	}

 	public static function getNewsArticleBySid($sid)
 	{
 		$articleInfo = self::getNewsArticleInfoBySid($sid);
 		$articleObj  = null;
 		if (!empty($articleInfo)) {
 			$articleObj  = new SJB_NewsArticle($articleInfo);
 			$articleObj->setSID($sid);
 		}
		return $articleObj;
 	}
 	
 	/**
 	 * Move article to archive category
 	 *
 	 * @param integer $articleSid
 	 */
 	public static function moveArticleToArchiveBySid($articleSid)
 	{
 		$archiveCategory = self::getCategoryByName('Archive');
 		return SJB_DB::query("UPDATE `news` SET `category_id` = ?n, active = 0 WHERE `sid` = ?n", $archiveCategory['sid'], $articleSid);
 	}

 	/**
 	 * Move article to category
 	 *
 	 * @param integer $articleSid
 	 * @param integer $categorySid
 	 */
 	public static function moveArticleToCategoryBySid($articleSid, $categorySid)
 	{
 		return SJB_DB::query("UPDATE `news` SET `category_id` = ?n WHERE `sid` = ?n", $categorySid, $articleSid);
 	}

 	// TODO: fix to work with objects
 	/**
 	 * Get all articles by category ID
 	 *
 	 * @param integer $categorySid
 	 * @return null|array
 	 */
 	public static function getArticlesByCategorySid($categorySid, $sortingField = 'id', $sortingOrder = 'ASC', $page = 1, $itemsPerPage = 10)
 	{
		$start = ($page - 1) * $itemsPerPage;

 		if ($sortingField == 'id') {
 			$sortingField = 'sid';
 		}
 		if ($sortingOrder != 'ASC' && $sortingOrder != 'DESC') {
 			$sortingOrder = 'ASC';
 		}
 		
 		$result = SJB_DB::query("SELECT * FROM `news` WHERE `category_id` = ?n ORDER BY `{$sortingField}` {$sortingOrder} LIMIT ?n,?n", $categorySid, $start, $itemsPerPage);
 		if (empty($result)) {
 			return null;
 		}
 		
 		return $result;
 	}
 	
 	// TODO: fix to work with objects
  	/**
 	 * Get all active articles by category ID
 	 *
 	 * @param integer $categorySid
 	 * @return null|array
 	 */
 	public static function getActiveArticlesByCategorySid($categorySid, $sortingField = 'id', $sortingOrder = 'ASC', $lang = 'en')
 	{
 		if ($sortingField == 'id') {
 			$sortingField = 'sid';
 		}
 		if ($sortingOrder != 'ASC' && $sortingOrder != 'DESC') {
 			$sortingOrder = 'ASC';
 		}
 		
 		$result = SJB_DB::query("SELECT * FROM `news` WHERE `category_id` = ?n AND `active` = 1 AND `language` = ?s ORDER BY `{$sortingField}` {$sortingOrder}", $categorySid, $lang);
 		if (empty($result)) {
 			return null;
 		}
 		
 		return $result;
 	}
 	
 	/**
 	 * Check URL, and add http, if need
 	 *
 	 * @param string $link
 	 */
 	public static function prepareURL($link)
 	{
 		if (empty($link))
 			return '';
 		$regex = "|http://(.*)|";
 		if (preg_match($regex, $link))
 			return $link;
 		return 'http://' . $link;
 	}

  	/**
 	 * Get image ID by news sid
 	 *
 	 * @param integer $articleSid
 	 * @return string|false
 	 */
 	public static function getImageFileIDByArticleSID($articleSid)
 	{
 		return SJB_NewsDBManager::getImageFileIDByArticleSID($articleSid);
 	}
 	
 	// TODO: fix to work with objects
 	/**
 	 * Count articles found by text
 	 *
 	 * @param string $text
 	 * @param string $lang
 	 * @return integer
 	 */
	public static function getAllNewsCountBySearchText($text, $lang = 'en', $active = false)
	{
		$text = SJB_DB::quote($text);
		$activeQuery = '';
		if ($active) {
			$activeQuery = '`active` = 1';
		}

		return SJB_DB::queryValue("SELECT count(*) as count FROM `news` WHERE (`brief` LIKE '%{$text}%' OR `text` LIKE '%{$text}%' OR `title` LIKE '%{$text}%') AND `language` = ?s AND {$activeQuery} ORDER BY `date` ASC", $lang);
	}

 	// TODO: fix to work with objects
 	/**
 	 * Get articles by search text
 	 *
 	 * @param string $text
 	 * @return array
 	 */
 	public static function searchArticles($text, $lang = 'en', $active = false)
 	{
		$text = SJB_DB::quote($text);
		$activeQuery = '';
		if ($active) {
			$activeQuery = '`active` = 1';
		}

		$result = SJB_DB::query("SELECT * FROM `news` WHERE (`brief` LIKE '%{$text}%' OR `text` LIKE '%{$text}%' OR `title` LIKE '%{$text}%') AND `language` = ?s AND {$activeQuery} ORDER BY `date` ASC", $lang);
 		if (empty($result)) {
 			return array();
 		}

		$upload_manager = new SJB_UploadFileManager();

		foreach ($result as $key => $value) {
			$result[$key]['image_link'] = '';
			if (!empty($value['image'])) {
				$result[$key]['image_link'] = $upload_manager->getUploadedFileLink($value['image']);
			}
		}

 		return $result;
 	}

 	/**
 	 * check date.
 	 * If date in past (relative to current time) - return true
 	 * Otherwise - return false
 	 *
 	 * @param string $date
 	 * @param string $dateFormat
 	 * @return boolean
 	 */
 	public static function needActivate($date, $dateFormat)
 	{
 		$currentTimestamp = time();
 		$parseDate = strptime($date, $dateFormat);
		$timestamp = mktime($parseDate['tm_hour'], $parseDate['tm_min'], $parseDate['tm_sec'], ($parseDate['tm_mon'] + 1), $parseDate['tm_mday'], ($parseDate['tm_year'] + 1900) );
		return $currentTimestamp > $timestamp;
 	}
 	
 	public static function moveUpCategoryBySID($sid)
 	{
		$category_info = SJB_DB::query("SELECT * FROM `news_categories` WHERE  id = ?n", $sid);
		if (empty($category_info))
		    return false;
		$category_info = array_pop($category_info);
		$current_order = $category_info['order'];
		$up_order = SJB_DB::queryValue("SELECT MAX(`order`) FROM `news_categories` WHERE `order` < ?n", $current_order);
		SJB_DB::query("UPDATE `news_categories` SET `order` = ?n WHERE `order` = ?n", 
					$current_order, $up_order);
		SJB_DB::query("UPDATE `news_categories` SET `order` = ?n WHERE id = ?n", $up_order, $sid);
		return true;
	}

	public static function moveDownCategoryBySID($sid)
	{
		$category_info = SJB_DB::query("SELECT * FROM `news_categories` WHERE id = ?n", $sid);
		if (empty($category_info))
		    return false;
		$category_info = array_pop($category_info);
		$current_order = $category_info['order'];
		$less_order = SJB_DB::queryValue("SELECT MIN(`order`) FROM `news_categories` WHERE `order` > ?n", $current_order);
		if ($less_order == 0)
		    return false;
		SJB_DB::query("UPDATE `news_categories` SET `order` = ?n WHERE `order` = ?n",
					$current_order, $less_order);
		SJB_DB::query("UPDATE `news_categories` SET `order` = ?n WHERE id = ?n", $less_order, $sid);
		return true;
	}
	
	
	/**
	 * Create structure for templates
	 * 
	 * @param SJB_NewsArticle $article
	 * @return array:
	 */
	public static function createTemplateStructureForNewsArticle($article)
	{
		$articleInfo = parent::getObjectInfo($article);
		
		if (is_null(self::$uploadFileManager)) {
			self::$uploadFileManager = new SJB_UploadFileManager();
		}
		
		foreach ($article->getProperties() as $property) {
			if ($property->isComplex()) {
				$isPropertyEmpty = true;
				$properties = $property->type->complex->getProperties();
				$properties = is_array($properties) ? $properties : array();
				foreach ($properties as $subProperty) {
					if (!empty($articleInfo['user_defined'][$property->getID()][$subProperty->getID()]) && is_array($articleInfo['user_defined'][$property->getID()][$subProperty->getID()])) {
						foreach ($articleInfo['user_defined'][$property->getID()][$subProperty->getID()] as $subValue) {
							if (!empty($subValue))
								$isPropertyEmpty = false;
						}
					}
				}
				if ($isPropertyEmpty) {
					$articleInfo['user_defined'][$property->getID()] = '';
				}
			}
		}

		
		$structure = array
        (
        	'sid'				=> $articleInfo['system']['id'],
			'id'				=> $articleInfo['system']['id'],
			'date'	            => $articleInfo['system']['date'],
			'expiration_date'	=> $articleInfo['system']['expiration_date'],
			'active'			=> $articleInfo['system']['active'],
			'image_link'        => self::$uploadFileManager->getUploadedFileLink($articleInfo['system']['image']),
        );
        
        if (!empty($articleInfo['system']['subuser_sid'])) {
        	$structure['subuser'] = SJB_UserManager::getUserInfoBySID($articleInfo['system']['subuser_sid']);
        }
      
        $structure['METADATA'] = array 
		( 
			'date'	=> array('type' => 'date'), 
			'expiration_date'	=> array('type' => 'date'), 
			
		); 

		$structure = array_merge($structure, $articleInfo['user_defined']); 
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($article)); 

        return array_merge($structure, $articleInfo['user_defined']);
	}
	
 }