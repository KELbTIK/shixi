<?php

class SJB_BrowseDBManager
{
	public static function getBrowseParametersByUri($pageUri)
	{
		return SJB_DB::queryValue("SELECT `parameters` FROM `browse` WHERE `page_uri` = ?s LIMIT 1", $pageUri);
	}
	
	public static function rebuildBrowses()
	{
		$pages = SJB_DB::query("SELECT * FROM `pages` WHERE `function` = 'browse'");
		foreach($pages as $page) {
			self::deleteBrowseByUri($page['uri']);
			self::addBrowse($page);
		}
	}

	/**
	 * When new page with function "browse" was created, save found values into table `browse` using page parameters
	 * @param string $pageUri example(/browse-by-occupations/)
	 */
	public static function addBrowseByUri($pageUri)
	{
		$pages = SJB_DB::query("SELECT * FROM `pages` WHERE `uri` = ?s AND `function` = 'browse'", $pageUri);
		foreach($pages as $page) {
			self::addBrowse($page);
		}
	}

	/**
	 * When page with function "browse" was deleted
	 * @param string $pageUri example(/browse-by-occupations/)
	 */
	public static function deleteBrowseByUri($pageUri)
	{
		SJB_DB::queryExec("DELETE FROM `browse` WHERE `page_uri` = ?s", $pageUri);
	}

	/**
	 * When new page with function "browse" was created, save found values into table `browse` using page parameters
	 * @param array $page full parammeters of site page with function "browse"
	 */
	private static function addBrowse($page)
	{
		$items = self::getItems($page);
		SJB_DB::queryExec("INSERT INTO `browse` (`page_uri`, `parameters`, `data`) VALUES (?s, ?s, ?s)",
			$page['uri'], $page['parameters'], serialize($items));
	}

	/**
	 * Search listing|listings which fits "browse" page parameters
	 * @param array $page full parammeters of site page with function "browse"
	 * @param bool  $decorate false means return only array with values:
	 * page /browse-by-occupations/ would return array('Bank Teller' = 14, 'Electrician' = 8...)
	 * true means return ready html or processed values:
	 * page /browse-by-occupations/ would return "<label><a href='/browse-by-occupations/334/Bank-Teller'>Bank Teller(349)</a>..."
	 * @param array|int $listingSids if not empty search only among passed listing sids
	 * @return array|string array with found values or ready html code
	 */
	private static function getItems($page, $decorate = false, array $listingSids = array())
	{
		if (!SJB_Settings::getValue('enableBrowseByCounter')) {
			return array();
		}
		
		$parameters    = unserialize($page['parameters']);
		$browseManager = SJB_ObjectMother::createBrowseManager($parameters['listing_type_id'], $parameters);
		return $browseManager->getItems($parameters, $decorate, $listingSids);
	}

	/**
	 * If listing|listings affects the page with function "browse". Increase or adds data to table `browse`.
	 * This function must be performed after the listing|listings was deactivated in the database
	 * @param array|int $listingSids Listing sids, can work with one or several listings one time
	 */
	public static function addListings($listingSids)
	{
		if (empty($listingSids)) {
			return;
		}
		self::updateBrowses($listingSids, true);
	}

	/**
	 * If listing|listings affects the page with function "browse". Decrease or removes data from table `browse`.
	 * This function must be performed before the listing|listings will be deactivated in the database
	 * @param array|int $listingSids Listing sids, can work with one or several listings one time
	 */
	public static function deleteListings($listingSids)
	{
		if (empty($listingSids)) {
			return;
		}
		self::updateBrowses($listingSids, false);
	}

	/**
	 * If listing|listings affects the page with function "browse", updates field `data` in table `browse`
	 * @param array|int $listingSids Listing sids, can work with one or several listings one time
	 * @param bool      $addListing true means the request came from function addListings, other way from function deleteListings
	 */
	private static function updateBrowses($listingSids, $addListing)
	{
		if (!SJB_Settings::getValue('enableBrowseByCounter')) {
			return;
		}
		
		if (!is_array($listingSids)) {
			$listingSids = array($listingSids);
		}
		
		$pages = SJB_DB::query("SELECT * FROM `browse`");
		foreach($pages as $page) {
			$items = self::getItems($page, false, $listingSids);
			if (!empty($items)) {
				$pageItems = unserialize($page['data']);
				foreach ($items as $key => $value) {
					if ($addListing) {
						$pageItems[$key] = isset($pageItems[$key]) ? $pageItems[$key] + $value : $value;
					}
					else if (isset($pageItems[$key])) {
						$pageItems[$key] -= $value;
						if ($pageItems[$key] == 0) {
							unset($pageItems[$key]);
						}
					}
				}
				SJB_DB::query("UPDATE `browse` SET `data` = ?s WHERE `page_uri` = ?s", serialize($pageItems), $page['page_uri']);
			}
		}
	}
}
