<?php

class SJB_StatesManager 
{
	public static function getAllStates($countrySID = false, $limit = 'all', $numRows = false)
	{
		$where = '';
		if ($countrySID) {
			$where = " WHERE `country_sid` = '".intval($countrySID)."' ";
		}
		if ($limit == 'all' && !$numRows) {
			return SJB_DB::query("SELECT *, `state_name` as stateName FROM `states` {$where} ORDER BY `order`");
		} else {
			return SJB_DB::query("SELECT *, `state_name` as stateName FROM `states` {$where} ORDER BY `order` LIMIT ?w, ?w", $limit, $numRows);
		}
	}

	public static function getAllActiveStates($countrySID, $displayAS)
	{
		$where = '';
		if ($countrySID) {
			$where = " AND `country_sid` = ?n ";
		}
		if (!$displayAS) {
			$displayAS = 'state_name';
		}
		$cache = SJB_Cache::getInstance();
		$cacheId = md5('SJB_StatesManager::getAllActiveStates' . $countrySID . $displayAS);
		if ($cache->test($cacheId)) {
			return $cache->load($cacheId);
		}
		$activeStates = SJB_DB::query("SELECT `sid`, `state_code`, `state_name` as stateName, ?w as `state_name` FROM `states` WHERE `active` = 1 {$where} ORDER BY `order`",
			$displayAS, $countrySID);
		$cache->save($activeStates, $cacheId, array(SJB_Cache::TAG_FIELDS));
		return $activeStates;
	}
	
	public static function getStateInfoBySID($sid)
	{
		$state = SJB_DB::query("SELECT * FROM `states` WHERE `sid` = ?n LIMIT 1", $sid);
		return $state ? array_pop($state) : array();
	}
	
	public static function countStates($countrySID = false)
	{
		if ($countrySID) {
			return SJB_DB::queryValue("SELECT count(*) FROM `states` WHERE `country_sid` = ?n", $countrySID);
		}
		return SJB_DB::queryValue("SELECT count(*) FROM `states`");
	}
	
	public static function activateStateBySID($sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("UPDATE `states` SET `active` = '1' WHERE `sid` = ?n", $sid);
	}
	
	public static function deactivateStateBySID($sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("UPDATE `states` SET `active` = '0' WHERE `sid` = ?n", $sid);
	}
	
	public static function deleteStateBySID($sid)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("DELETE FROM `states` WHERE `sid` = ?n", $sid);
	}
	
	public static function deleteStatesByCountrySID($countrySID)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		return SJB_DB::query("DELETE FROM `states` WHERE `country_sid` = ?n", $countrySID);
	}

	/**
	 * @param SJB_State $state
	 */
	public static function saveState(SJB_State $state)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS));
		$stateSID = $state->getSID();
		SJB_ObjectDBManager::saveObject('states', $state);
		if (!$stateSID) {
			self::setLastOrder($state);
		}
	}

	/**
	 * @param SJB_State $state
	 */
	public static function setLastOrder(SJB_State $state)
	{
		$max_order = SJB_DB::queryValue('SELECT MAX(`order`) FROM `states`');
		$max_order = empty($max_order) ? 1 : $max_order;
		SJB_DB::query('UPDATE `states` SET `order` = ?n WHERE `sid` = ?n', ++$max_order, $state->getSID());
	}
	
	public static function getStateSIDByStateCode($stateCode, $countrySID = null)
	{
		$where = '';
		if ($countrySID) {
			$where .= " AND `country_sid` = '{$countrySID}' ";
		}
		return SJB_DB::queryValue("SELECT `sid` FROM `states` WHERE `state_code` = ?s {$where} ", $stateCode);
	}

	public static function getStatesNamesByCountry($countrySID = false, $active = false, $displayAS = false)
	{
		$cacheID = 'StatesNamesByCountry' . $countrySID . $active . $displayAS;
		if (SJB_MemoryCache::has($cacheID)) {
			$statesNames = SJB_MemoryCache::get($cacheID);
		} else {
			if ($active) {
				$states = self::getAllActiveStates($countrySID, $displayAS);
			} else {
				$states = self::getAllStates($countrySID);
			}
			$statesNames = array();
			foreach ($states as $state) {
				$statesNames[] = array(
					'id' 		=> $state['sid'],
					'Code' 		=> $state['state_code'],
					'Name' 		=> $state['stateName'],
					'caption'	=> $state['state_name']);
			}
			SJB_MemoryCache::set($cacheID, $statesNames);
		}
		return $statesNames;
	}

	public static function getStateNamesBySid($sid = false, $displayAS = false)
	{
		$stateNames = array();
		$cacheID = 'StateNamesBySid' . $sid . $displayAS;
		if (SJB_MemoryCache::has($cacheID)) {
			$stateNames = SJB_MemoryCache::get($cacheID);
		} else {
			if (!$displayAS) {
				$displayAS = 'state_name';
			}
			$states = SJB_DB::query("SELECT `sid`, `state_code`, `state_name` as stateName, ?w as `state_name` FROM `states`
				WHERE `sid` = ?n  ORDER BY `order`", $displayAS, $sid);

			foreach ($states as $state) {
				$stateNames[] = array(
						'id'      => $state['sid'],
						'Code'    => $state['state_code'],
						'Name'    => $state['stateName'],
						'caption' => $state['state_name']
				);
			}
			SJB_MemoryCache::set($cacheID, $stateNames);
		}
		return $stateNames;
	}
	
	public static function getStateSIDByStateName($stateName) 
	{
		return SJB_DB::queryValue("SELECT `sid` FROM `states` WHERE `state_name` = ?s LIMIT 1", $stateName);
	}
	
	public static function getHashedListItems($displayAS)
	{
		$countrySID = SJB_Settings::getSettingByName('default_country');
		$states = self::getAllActiveStates($countrySID, $displayAS);
		$list_items = array();
		foreach ($states as $state) {
			$list_items[$state['sid']] = $state['state_name'];
		}
		
		return $list_items;
	}

	/**
	 * @param int $stateSID
	 * @return bool
	 * @throws Exception
	 */
	public static function moveUpStateBySID($stateSID)
	{
		$stateInfo = self::getStateInfoBySID($stateSID);
		if (empty($stateInfo)) {
			throw new Exception('Invalid item SID');
		}

		$currentOrder = $stateInfo['order'];
		$upOrder = SJB_DB::queryValue('SELECT MAX(`order`) FROM `states` WHERE `order` < ?n', $currentOrder);
		if ($upOrder == 0) {
			throw new Exception('Highest order reached');
		}

		$result = SJB_DB::query('UPDATE `states` SET `order` = ?n WHERE `order` = ?n', $currentOrder, $upOrder);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		$result = SJB_DB::query('UPDATE `states` SET `order` = ?n WHERE `sid` = ?n', $upOrder, $stateSID);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		return true;
	}

	/**
	 * @param int $stateSID
	 * @return bool
	 * @throws Exception
	 */
	public static function moveDownStateBySID($stateSID)
	{
		$stateInfo = self::getStateInfoBySID($stateSID);
		if (empty($stateInfo)) {
			throw new Exception('Invalid item SID');
		}

		$currentOrder = $stateInfo['order'];
		$lessOrder = SJB_DB::queryValue('SELECT MIN(`order`) FROM `states` WHERE `order` > ?n', $currentOrder);
		if ($lessOrder == 0) {
			throw new Exception('Lowest order reached');
		}

		$result = SJB_DB::query('UPDATE `states` SET `order` = ?n WHERE `order` = ?n', $currentOrder, $lessOrder);
		if (!$result) {
			throw new Exception ('Order was not saved completely');
		}

		$result = SJB_DB::query('UPDATE `states` SET `order` = ?n WHERE `sid` = ?n', $lessOrder, $stateSID);
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
			$result = SJB_DB::query('UPDATE `states` SET `order` = ?n WHERE sid = ?n', $count++, $itemSID);
			if (!$result) {
				throw new Exception ('Order was not saved completely');
			}
		}

		return true;
	}

	/**
	 * @param string $stateName
	 * @return string
	 */
	public static function getStateCodeByStateName($stateName)
	{
		return SJB_DB::queryValue('SELECT `state_code` FROM `states` WHERE state_name = ?s', $stateName);
	}
} 


class SJB_State extends SJB_Object
{
	public function __construct($stateInfo = array())
	{
		$this->db_table_name = 'states';
		$this->details = new SJB_StateDetails($stateInfo);
	}
}


class SJB_StateDetails extends SJB_ObjectDetails
{
	public $properties;
	public $details;
	
	public function __construct($stateInfo = array())
	{
		$detailsInfo = self::getDetails();
		foreach ($detailsInfo as $detailInfo) {
		    $detailInfo['value'] = '';
			if (isset($stateInfo[$detailInfo['id']]))
				$detailInfo['value'] = $stateInfo[$detailInfo['id']];
				
			$this->properties[$detailInfo['id']] = new SJB_ObjectProperty($detailInfo);
		}
	}
	
	public static function getDetails()
	{
		$details =  array (
			    array (
					'id'			=> 'state_code',
					'caption'		=> 'State/Region Code',
					'type'			=> 'unique_string',
			    	'table_name' 	=> 'states',
					'length'		=> '20',
			    	'validators' => array(
						'SJB_IdWithSpaceValidator',
						'SJB_UniqueStateSystemValidator'
					),
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> 0,
				),
				array (
					'id'			=> 'state_name',
					'caption'		=> 'State/Region Name',
					'type'			=> 'unique_string',
					'table_name' 	=> 'states',
					'validators' => array(
						'SJB_IdWithSpaceValidator',
						'SJB_UniqueStateSystemValidator'
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
					'table_name' 	=> 'states',
					'length'		=> '20',
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 0,
				),
			);
		return $details;
	}
} 

class SJB_ImportedStateProcessor
{
	public $propertiesNames;
	public $propertiesValues;
	public $propertiesQuantity;
	public $treeColumns;
	public $currentKey = 0;
	
	public function __construct($inputData, $state) 
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
