<?php

class SJB_PageManager
{
	public static function save_page($uri, $module, $function, $template, $parameters)
	{
		$page_config = SJB_System::getPageConfig($uri);
		$page_config->SetPageConfig($module, $function, $template, $parameters);
		if ($page_config ->pageExists()) {
			SJB_PageManager::update_page($page_config);
		}
		else {
			SJB_PageManager::addPage($page_config);
		}
	}

	public static function doesPageExists($uri, $access_type)
	{
		$uri = rawurldecode($uri);
		$sql_result = SJB_DB::query("SELECT `uri` FROM `pages` WHERE (`uri` = ?s OR `uri` = ?s) AND `access_type` = ?s LIMIT 1", $uri, $uri . '/', $access_type);
		return !empty($sql_result);
	}

	public static function extract_page_info($uri, $access_type)
	{
		$uri = rawurldecode($uri);
		$sql_result = SJB_DB::query("SELECT * FROM `pages` WHERE (`uri` = ?s OR `uri` = ?s) AND `access_type` = ?s LIMIT 1", $uri, $uri . '/', $access_type);
		if (!empty($sql_result)) {
			return array_pop($sql_result);
		}
		return null;
	}
	
	public static function delete_page($uri, $access_type = 'user')
	{
		SJB_BrowseDBManager::deleteBrowseByUri($uri);
		$sql_result = SJB_DB::query("DELETE FROM `pages` WHERE `uri` = ?s AND `access_type` = ?s", $uri, $access_type);
		return !empty($sql_result);
	}

	public static function get_page($uri, $access_type)
	{
		$sql_result = SJB_DB::query("SELECT * FROM `pages` WHERE `access_type` = ?s AND `uri` = ?s", $access_type, $uri);
		if ($sql_result) {
			$page_data = $sql_result[0];
			if (empty($page_data['parameters'])) {
				$page_data['parameters'] = array();
			}
			else {
				$page_data['parameters'] = unserialize($page_data['parameters']);
			}
			return $page_data;
		}
		return null;
	}

	public static function getPageById($id)
	{
		$result = SJB_DB::query("SELECT * FROM `pages` WHERE `id` = ?n", $id);
		if ($result) {
			$pageData = $result[0];
			if (empty($pageData['parameters'])) {
				$pageData['parameters'] = array();
			} else {
				$pageData['parameters'] = unserialize($pageData['parameters']);
			}
			return $pageData;
		}
		return null;
	}

	public static function get_pages($access_type='user', $order = 'ASC')
	{
		$sql_result = SJB_DB::query("SELECT * from `pages` WHERE `access_type` = ?s ORDER BY `uri` ?w", $access_type, $order);
		if ($sql_result != false)
			return $sql_result;
		return null;
	}

	public static function addPage($pageInfo)
	{
		$uri			=	$pageInfo['uri'];
		$module			=	$pageInfo['module'];
		$function		= 	$pageInfo['function'];
		$template		= 	$pageInfo['template'];
		$title 			= 	$pageInfo['title'];
		$accessType 	=	$pageInfo['access_type'];
		$keywords	 	=	$pageInfo['keywords'];
		$description 	=	$pageInfo['description'];
		$pass_parameters_via_uri = isset($pageInfo['pass_parameters_via_uri']) ? 1 : 0;


		if (empty ($uri) || empty ($module) || empty ($function) || empty ($accessType))
			 return false;
		if (SJB_PageManager::doesPageExists ($uri, $accessType))
			return false;
		
		$serialized_parameters = serialize($pageInfo['parameters']);
		
		$sql_result = SJB_DB::query (
				"INSERT INTO pages(`uri`, `module`, `function`, `template`, `title`, `parameters`, `keywords`, `access_type`, `description`, `pass_parameters_via_uri`)"
				." VALUES(?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?n)"
				, $uri, $module, $function, $template, $title, $serialized_parameters, $keywords, $accessType, $description, $pass_parameters_via_uri);

		if ($sql_result != false) {
			SJB_BrowseDBManager::addBrowseByUri($uri);
			return $sql_result;
		}
		
		return false;
	}

	public static function update_page($pageInfo)
	{
    	$ID 			=	$pageInfo['ID'];
		$uri			=	$pageInfo['uri'];
		$module			=	$pageInfo['module'];
		$function		= 	$pageInfo['function'];
		$template		= 	$pageInfo['template'];
		$title 			= 	$pageInfo['title'];
		$keywords	 	=	$pageInfo['keywords'];
		$description 	=	$pageInfo['description'];
		$pass_parameters_via_uri = isset($pageInfo['pass_parameters_via_uri']) ? 1 : 0;
		
		if (empty($uri) || empty($module) || empty($function)) {
			 return false;
		}
		
		$previousPage = self::getPageById($ID);
		
		$serialized_parameters = serialize($pageInfo['parameters']);
		$sql_result = SJB_DB::query(
				"UPDATE `pages` SET `uri`=?s, `module`=?s, `function`=?s,"
				." `template`=?s, `title`=?s, `parameters`=?s, `keywords`=?s,"
				." `description`=?s, `pass_parameters_via_uri`=?n"
				." WHERE `ID`=?s"
				, $uri, $module, $function, $template, $title, $serialized_parameters, $keywords, $description,  $pass_parameters_via_uri, $ID);

		if ($sql_result != false) {
			if ($previousPage['function'] == 'browse') {
				SJB_BrowseDBManager::deleteBrowseByUri($previousPage['uri']);
			}
			if ($function == 'browse') {
				SJB_BrowseDBManager::addBrowseByUri($uri);
			}
			return $sql_result;
		}
		return false;
	}	
	
	public static function doesParentPageExist($uri, $access_type)
	{
		$parentUri = SJB_PageManager::getPageParentURI($uri, $access_type);
		return !empty($parentUri) && $parentUri != '/';
	}
	
	public static function getPageParentURI($uri, $access_type, $isPassParamViaUri = true)
	{
		$uri_parts = explode("/", $uri);
		$temp_uri = $uri;
		$queryParam = '';
		if ($isPassParamViaUri == true) {
			$queryParam = ' AND `pass_parameters_via_uri` = 1';
		}
		for ($i = count($uri_parts) - 1; $i >= 0; $i--) {
			$temp_uri = substr($temp_uri, 0, strlen($temp_uri) - strlen("/".$uri_parts[$i]));
			$sql_result = SJB_DB::query("SELECT * from `pages` WHERE (`uri` = ?s OR `uri` = ?s) AND `access_type` = ?s{$queryParam}",
								$temp_uri, $temp_uri . '/', $access_type);
			if (!empty($sql_result)) {
				return $temp_uri . '/';
			}
		}
		return false;
	}

	/**
	 * @return array
	 */
	public static function getPageModule()
	{
		$modules = SJB_DB::query("SELECT `module` FROM `pages` WHERE `uri` = ?s", SJB_Navigator::getURI());
		$moduleNames = array();
		foreach ($modules as $module) {
			$moduleNames[] = $module['module'];
		}
		return $moduleNames;
	}

	public static function updatePagesByListingTypeSID($listingTypeSID, $newListingTypeName)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSID);
		$listingTypeID = strtolower($listingTypeID);
		$pagesInfo = SJB_DB::query("SELECT `uri`, `title` FROM `pages` WHERE `access_type` = 'user' AND `uri` LIKE '%{$listingTypeID}%'");
		if (!in_array($listingTypeID, array('Job', 'Resume'))) {
			$newListingTypeName = $newListingTypeName . ' Listing';
		}
		foreach($pagesInfo as $pageInfo) {
			switch($pageInfo['uri']) {
				case '/' . strtolower($listingTypeID) . '-preview/' :
					$title = $newListingTypeName . ' Preview';
					self::updatePageTitleByUri($title, $pageInfo['uri']);
					break;
				case '/my-' . strtolower($listingTypeID) . '-details/' :
					$title = $newListingTypeName . ' Details';
					self::updatePageTitleByUri($title, $pageInfo['uri']);
					break;
				case '/print-my-' . strtolower($listingTypeID) . '/' :
					$title = 'Print ' . $newListingTypeName;
					self::updatePageTitleByUri($title, $pageInfo['uri']);
					break;
				case '/print-' . strtolower($listingTypeID) . '/';
					$title = 'Print ' . $newListingTypeName;
					self::updatePageTitleByUri($title, $pageInfo['uri']);
					break;
				case '/edit-' . strtolower($listingTypeID) . '/';
					$title = 'Edit ' . $newListingTypeName;
					self::updatePageTitleByUri($title, $pageInfo['uri']);
					break;
				case '/manage-' . strtolower($listingTypeID) . '/';
					$title = 'Manage ' . $newListingTypeName;
					self::updatePageTitleByUri($title, $pageInfo['uri']);
					break;
			}
		}
	}
	
	public static function getSpecialPages()
	{
		return SJB_DB::query("SELECT * from `pages` WHERE `special_page` = '1'");
	}

	private static function updatePageTitleByUri($title, $uri)
	{
		SJB_DB::queryExec("UPDATE `pages` SET `title` = ?s WHERE `uri` = ?s", $title, $uri);
	}
}
