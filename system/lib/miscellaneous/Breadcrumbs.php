<?php

class SJB_Breadcrumbs
{
	/**
	 * get breadcrumbs for user
	 * @return array
	 */
	function getBreadcrumbs()
	{
		$structure = SJB_DB::query('SELECT * FROM `breadcrumbs_structure` ORDER BY `parent_id` ASC');

		$currentPage = $GLOBALS['uri'];
		$breadcrumbs = array();
		
		$arr = explode('/', $currentPage);
		foreach ($arr as $key=>$val) {
			if ($val == '')
				unset($arr[$key]);
		}
		
		while (count($arr)) {
			$currentPage = '/';
			foreach ($arr as $val) {
				if ($val != '')
					$currentPage .= "{$val}/";
			}
			$breadcrumbs = self::getCrumbs('uri', $currentPage, $structure, $breadcrumbs);
			$breadcrumbs = array_reverse($breadcrumbs);
			if (count($breadcrumbs)) {
				break;
			}
			array_pop($arr);
		}
		
		return $breadcrumbs;
	}

	/**
	 * recursive walk in structure from current page to root
	 * @static
	 * @param  $keyName
	 * @param  $needle
	 * @param  $array
	 * @param  $breadcrumbs
	 * @return array
	 */
	private static function getCrumbs($keyName, $needle, $array, &$breadcrumbs) {
		foreach ($array as $elem) {
			// look for current $elem of breadcrumbs in current page uri
			if ($needle == $elem[$keyName]) {
				$breadcrumbs[] = $elem;
				if ($elem['parent_id'] != 0) {
					self::getCrumbs('id', $elem['parent_id'], $array, $breadcrumbs);
				}
			}
		}
		return $breadcrumbs;
	}
	
	/**
	 * получаем и сортируем всю структуру по иерархии элементов
	 * @return array
	 */
	function makeStructure()
	{
		$structure = SJB_DB::query('SELECT * FROM `breadcrumbs_structure` ORDER BY `parent_id` ASC');
		$end_structure = array();
		$this->createOrder($structure, $end_structure);
		return $end_structure;
	}
	
	/**
	 * рекурсивная функция сортировки всей структуры bread crumbs по иерархии
	 * @param  $array
	 * @param  $resultArray
	 * @param string $parent_id
	 * @param string $sublevel
	 * @return void
	 */
	function createOrder($array, &$resultArray, $parent_id = '', $sublevel = '')
	{
		if ($parent_id == '') {
			$parent_id = 0;
			$sublevel = 0;
		} else {
			$sublevel++;
		}
		foreach ( $array as $elem ) {
			if ($elem['parent_id'] == $parent_id) {
				$resultArray[] = array_merge($elem, array('sublevel' => $sublevel) );
				$id = $elem['id'];
				$this->createOrder($array, $resultArray, $id, $sublevel);
			}
		}
	}
	
	function getElement($id)
	{
		$element = SJB_DB::query('SELECT * FROM `breadcrumbs_structure` WHERE `id` = ?n', $id);
		return array_pop($element);
	}
	
	function deleteElement($id)
	{
		$struct = $this->makeStructure(); // обходим всю структуру и удаляем все дочерние элементы узла c id=$id
		$this->delete($id, $struct); // удаляем сам узел
		SJB_DB::query('DELETE FROM `breadcrumbs_structure` WHERE `id` = ?n', $id);
	}
	
	/**
	 * по parent_id удаляем дочерние элементы узла
	 * @param  $parent_id
	 * @param  $struct
	 * @return void
	 */
	function delete($parent_id, &$struct)
	{
		foreach ($struct as $key => $elem) {
			if ($elem['parent_id'] == $parent_id) {
				$this->delete($elem['id'], $struct);
				array_splice($struct, $key, 1);
				SJB_DB::query('DELETE FROM `breadcrumbs_structure` WHERE `id` = ?n LIMIT 1', $elem['id']);
			}
		}
	}
	
	function addElement( $item_name, $item_uri, $parent_id )
	{
		SJB_DB::query('INSERT INTO `breadcrumbs_structure` SET `name` = ?s, `uri` = ?s, `parent_id` = ?n', $item_name, $item_uri, $parent_id);
	}

	function updateElement( $item_name, $item_uri, $element_id )
	{
		SJB_DB::query('UPDATE `breadcrumbs_structure` SET `name` = ?s, `uri` = ?s WHERE `id` = ?n', $item_name, $item_uri, $element_id);
	}

	public function getElementByUri($uri)
	{
		return SJB_DB::queryValue('SELECT `id` FROM `breadcrumbs_structure` WHERE `uri` = ?s', $uri);
	}

	public static function updateBreadcrumbsByListingTypeSID($listingTypeSID, $newListingTypeName)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSID);
		if (!in_array($listingTypeID, array('Job', 'Resume'))) {
			$newListingTypeName = $newListingTypeName . ' Listing';
		}
		$breadcrumb = new SJB_Breadcrumbs();
		$uris = self::getBreadcrumbsUrisByListingTypeID($listingTypeID);
		foreach ($uris as $uri) {
			$title = '';
			switch ($uri) {
				case '/my-listings/' . $listingTypeID . '/':
					$title = 'My ' . $newListingTypeName . 's';
					break;
				case '/my-' . strtolower($listingTypeID) . '-details/':
					$title = 'My ' . $newListingTypeName . ' Preview';
					break;
				case '/edit-' . strtolower($listingTypeID) . '/':
					$title = 'Edit ' . $newListingTypeName;
					break;
				case '/manage-' . strtolower($listingTypeID) . '/':
					$title = 'Manage ' . $newListingTypeName;
					break;
			}
			if (empty($title)) {
				continue;
			}
			$breadcrumbID = $breadcrumb->getElementByUri($uri);
			$breadcrumb->updateElement($title, $uri, $breadcrumbID);
		}
	}

	public static function deleteBreadcrumbsByListingTypeSID($listingTypeSID)
	{
		$listingTypeID = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSID);
		$uris = self::getBreadcrumbsUrisByListingTypeID($listingTypeID);
		SJB_DB::query("DELETE FROM `breadcrumbs_structure` WHERE `uri` IN (?l)", $uris);
	}

	public static function getBreadcrumbsUrisByListingTypeID($listingTypeID)
	{
		return array(
			'/my-listings/' . $listingTypeID . '/',
			'/my-' . strtolower($listingTypeID) . '-details/',
			'/edit-' . strtolower($listingTypeID) . '/',
			'/manage-' . strtolower($listingTypeID) . '/',
		);
	}
}
