<?php

class SJB_CountriesManager
{
	public static function getAllCountries($limit = 'all', $numRows = false)
	{
		if ($limit == 'all' && !$numRows) {
			return SJB_DB::query('SELECT *, `country_name` as countryName FROM `countries` ORDER BY `order`');
		} else {
			return SJB_DB::query('SELECT *, `country_name` as countryName FROM `countries` ORDER BY `order` LIMIT ?w, ?w', $limit, $numRows);
		}
	}
	
	public static function getAllActiveCountries($displayAS = false)
	{
		if (!$displayAS) {
			$displayAS = 'country_name';
		}
		return SJB_DB::query('SELECT `sid`, `country_name` as countryName, `country_code`, `?w` as country_name FROM `countries` WHERE `active` = 1 ORDER BY `order`', $displayAS);
	}
	
	public static function getCountryInfoBySID($sid)
	{
		$country = SJB_DB::query("SELECT * FROM `countries` WHERE `sid` = ?n", $sid);
		return $country?array_pop($country):array();
	}
	
	public static function countCountries()
	{
		return SJB_DB::queryValue("SELECT count(*) FROM `countries`");
	}
	
	public static function activateCountryBySID($sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("UPDATE `countries` SET `active` = '1' WHERE `sid` = ?n", $sid);
	}
	
	public static function deactivateCountryBySID($sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("UPDATE `countries` SET `active` = '0' WHERE `sid` = ?n", $sid);
	}
	
	public static function deleteCountryBySID($sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		SJB_StatesManager::deleteStatesByCountrySID($sid);
		return SJB_DB::query("DELETE FROM `countries` WHERE `sid` = ?n", $sid);
	}
	
	public static function saveCountry(SJB_Country $country)
	{
		$countrySID = $country->getSID();
		SJB_ObjectDBManager::saveObject('countries', $country);

		if (!$countrySID) {
			self::setLastOrder($country);
		}
	}

	/**
	 * @param SJB_Country $country
	 */
	public static function setLastOrder(SJB_Country $country)
	{
		$max_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM `countries`');
		$max_order = empty($max_order) ? 1 : $max_order;
		SJB_DB::query('UPDATE `countries` SET `order` = ?n WHERE `sid` = ?n', ++$max_order, $country->getSID());
	}

	public static function getCountrySIDByCountryCode($countryCode)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM `countries` WHERE `country_code` = ?s", $countryCode);
	}
	
	public static function getAllCountriesCodesAndNames($active = false, $displayAS = false)
	{
		$cacheId = 'SJB_CountriesManager::getAllCountriesCodesAndNames';
		if (SJB_MemoryCache::has($cacheId)) {
			return SJB_MemoryCache::get($cacheId);
		}
		if ($active) {
			$countries = self::getAllActiveCountries($displayAS);
		} else {
			$countries = self::getAllCountries();
		}
		$countriesCodesAndNames = self::getCountriesCodesAndNames($countries);
		SJB_MemoryCache::set($cacheId, $countriesCodesAndNames);
		return $countriesCodesAndNames;
	}

	/**
	 * @param array $countries
	 * @return array
	 */
	public static function getCountriesCodesAndNames($countries)
	{
		$countriesCodesAndNames = array();
		foreach ($countries as $country) {
			$countriesCodesAndNames[] = array(
				'id' 		=> $country['sid'],
				'key' 		=> $country['country_code'],
				'Code' 		=> $country['country_code'],
				'Name' 		=> $country['countryName'],
				'caption'	=> $country['country_name']);
		}
		return $countriesCodesAndNames;
	}

	/**
	 * @param array $countriesCodeList
	 * @return array
	 */
	public static function getCountriesByCodes($countriesCodeList)
	{
		return SJB_DB::query("SELECT `sid`, `country_code`, `country_name`, `country_name` as `countryName` FROM `countries` WHERE `country_code` in (?l)", $countriesCodeList);
	}

	/**
	 * @param array $countriesCodeList
	 * @return array
	 */
	public static function getCountriesCodesAndNamesByCodes($countriesCodeList)
	{
		$countries = self::getCountriesByCodes($countriesCodeList);
		return self::getCountriesCodesAndNames($countries);
	}

	public static function getCountrySIDByCountryName($countryName)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM `countries` WHERE `country_name` = ?s", $countryName);
	}
	
	public static function getHashedListItems($displayAS = false)
	{
		$countries = self::getAllActiveCountries($displayAS);
		$list_items = array();
		foreach ($countries as $country) {
			$list_items[$country['sid']] = $country['country_name'];
		}
		
		return $list_items;
	}

	/**
	 * @param int $countrySID
	 * @return bool
	 * @throws Exception
	 */
	public static function moveUpCountryBySID($countrySID)
	{
		$countryInfo = self::getCountryInfoBySID($countrySID);
		if (empty($countryInfo)) {
			throw new Exception('Invalid item SID');
		}

		$currentOrder = $countryInfo['order'];
		$upOrder = SJB_DB::queryValue('SELECT MAX(`order`) FROM `countries` WHERE `order` < ?n', $currentOrder);
		if ($upOrder == 0) {
			throw new Exception('Highest order reached');
		}

		$result = SJB_DB::query('UPDATE `countries` SET `order` = ?n WHERE `order` = ?n', $currentOrder, $upOrder);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		$result = SJB_DB::query('UPDATE `countries` SET `order` = ?n WHERE `sid` = ?n', $upOrder, $countrySID);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		return true;
	}

	/**
	 * @param int $countrySID
	 * @return bool
	 * @throws Exception
	 */
	public static function moveDownCountryBySID($countrySID)
	{
		$countryInfo = self::getCountryInfoBySID($countrySID);
		if (empty($countryInfo)) {
			throw new Exception('Invalid item SID');
		}

		$currentOrder = $countryInfo['order'];
		$lessOrder = SJB_DB::queryValue('SELECT MIN(`order`) FROM `countries` WHERE `order` > ?n', $currentOrder);
		if ($lessOrder == 0) {
			throw new Exception('Lowest order reached');
		}

		$result = SJB_DB::query('UPDATE `countries` SET `order` = ?n WHERE `order` = ?n', $currentOrder, $lessOrder);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		$result = SJB_DB::query('UPDATE `countries` SET `order` = ?n WHERE `sid` = ?n', $lessOrder, $countrySID);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		return true;
	}

	/**
	 * @param int $currentPage
	 * @param int $itemsPerPage
	 * @param int $itemSIDs
	 * @return bool
	 * @throws Exception
	 */
	public static function saveItemsOrder($currentPage, $itemsPerPage, $itemSIDs)
	{
		if ($currentPage > 1) {
			$count = ($currentPage - 1) * $itemsPerPage + 1;
		} else {
			$count = 1;
		}

		foreach ($itemSIDs as $itemSID => $val) {
			$result = SJB_DB::query('UPDATE `countries` SET `order` = ?n WHERE sid = ?n', $count++, $itemSID);
			if (!$result) {
				throw new Exception ('Order was not saved completely');
			}
		}

		return true;
	}
}


class SJB_Country extends SJB_Object
{
	public function __construct($countryInfo = array())
	{
		$this->db_table_name = 'countries';
		$this->details = new SJB_CountryDetails($countryInfo);
	}
}


class SJB_CountryDetails extends SJB_ObjectDetails
{
	public $properties;
	public $details;
	
	public function __construct($countryInfo = array())
	{
		$detailsInfo = self::getDetails();
		foreach ($detailsInfo as $detailInfo) {
		    $detailInfo['value'] = '';
			if (isset($countryInfo[$detailInfo['id']]))
				$detailInfo['value'] = $countryInfo[$detailInfo['id']];
				
			$this->properties[$detailInfo['id']] = new SJB_ObjectProperty($detailInfo);
		}
	}
	
	public static function getDetails()
	{
		$details =  array (
			    array (
					'id'			=> 'country_code',
					'caption'		=> 'Country Code',
					'type'			=> 'unique_string',
			    	'table_name' 	=> 'countries',
			    	'validators' => array(
						'SJB_IdValidator',
						'SJB_UniqueSystemValidator'
					),
					'length'		=> '20',
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> 0,
				),
				array (
					'id'			=> 'country_name',
					'caption'		=> 'Country Name',
					'type'			=> 'unique_string',
					'table_name' 	=> 'countries',
					'validators' => array(
						'SJB_IdWithSpaceValidator',
						'SJB_UniqueSystemValidator'
					),
					'length'		=> '20',
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> 0,
				),
				array (
					'id'			=> 'active',
					'caption'		=> 'Active',
					'type'			=> 'boolean',
					'table_name' 	=> 'countries',
					'length'		=> '20',
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 0,
				),
			);
		return $details;
	}
} 

class SJB_ImportedCountryProcessor
{
	public $propertiesNames;
	public $propertiesValues;
	public $propertiesQuantity;
	public $treeColumns;
	public $currentKey = 0;
	
	public function __construct($inputData, $country) 
	{
		$this->propertiesNames = array_shift($inputData);
		$tree_parser = new SJB_TreeParser($this->propertiesNames);
		$this->treeColumns = $tree_parser->getTreeColumns();
		$this->_unsetTreeColumnsNames($tree_parser->columns);
		$this->propertiesValues = $inputData;
		$this->propertiesQuantity = count($this->propertiesValues);
	}
	
	public function _unsetTreeColumnsNames($repeatedColumns)
	{
		foreach($repeatedColumns as $repeatedColumn)
			if (!is_null (array_search($repeatedColumn, $this->propertiesNames) ))
				unset($this->propertiesNames[array_search($repeatedColumn, $this->propertiesNames)]);
	}
	
	public function isEmpty()
	{
	    return $this->currentKey >= $this->propertiesQuantity;
	}
	
	public function getData($values = array())
	{
		$result	= array();
		foreach($this->propertiesNames as $key => $propertyName) {
			$result[$propertyName] = isset($values[$key]) ? $values[$key] : null;
		}
		
		$result = $result + $this->_getTreeValues($values);
		return $result;
	}
	
	public function _getPropertiesData()
	{
	    return $this->propertiesValues[$this->currentKey++];
	}
	
	function _getTreeValues($values)
	{
		$result = array();
		foreach($this->treeColumns as $treeColumnName => $treeColumnIndexes) {
			$treeCaptions = $this->_getTreeColumnValue($treeColumnIndexes, $values);
			
			if ($treeCaptions) {
				$treeSidSearcher = new SJB_TreeUserSearcher($treeColumnName, $treeCaptions);
				$treeInfo = $treeSidSearcher->getInfo();
				
				$result[$treeColumnName] = $treeInfo ? $treeInfo['sid'] : null;
			}
		}
		return $result;
	}
}
