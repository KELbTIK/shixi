<?php

class SJB_ObjectInfoSearcher
{
	var $valid_criterion_number = 0;
	var $table_prefix;
	var $group_table_name;
	var $query = '';
	protected $criteria = array();
	private $limit = 0;
	private $groupByField = null;

    public function __construct($table_prefix)
	{
		$this->table_prefix = $table_prefix;
	}

	function setCriteria($criteria, $property_aliases)
	{
		$this->criteria = $criteria;
		if (!empty ($this->criteria) && !empty($property_aliases)) {
			$property_aliases->changeAliasValuesInCriteria($this->criteria);
		}
	}

	/**
	 * @param $sorting_fields
	 * @param bool|array $inner_join
	 * @param bool $relevance
	 * @return array|bool|false|int|mixed
	 */
	function getObjectInfo($sorting_fields, $inner_join = false, $relevance = false)
	{
		$searchSqlTranslator = new SJB_SearchSqlTranslator($this->table_prefix);
		if ($this->limit)
			$searchSqlTranslator->setLimit($this->limit);
		if ($this->groupByField) {
			$searchSqlTranslator->setGroupByField($this->groupByField);
		}
		$this->query = $searchSqlTranslator->buildSqlQuery($this->criteria, $this->valid_criterion_number, $sorting_fields, $inner_join, false, $relevance);
		$cache = SJB_Cache::getInstance();
		$cacheId = md5($this->query);
		if ($cache->test($cacheId)) {
			return $cache->load($cacheId);
		}
		$result = SJB_DB::query($this->query);
		$cache->save($result, $cacheId, array(SJB_Cache::TAG_LISTINGS, SJB_Cache::TAG_USERS));
		return $result;
	}

	function getValidCriterionNumber()
	{
		return $this->valid_criterion_number;
	}

	public function setLimit($limit)
	{
		$this->limit = $limit;
	}


	/**
	 * Returns count of rows found by specified criteria
	 * @return int
	 */
	public function countRows()
	{
		$searchSqlTranslator = new SJB_SearchSqlTranslator($this->table_prefix);
		$sql = $searchSqlTranslator->buildSqlQuery($this->criteria, $this->valid_criterion_number, null, $this->inner_join, true);
		$cache = SJB_Cache::getInstance();
		$cacheId = md5($sql);
		if ($cache->test($cacheId)) {
			return $cache->load($cacheId);
		}
		$result = SJB_DB::queryValue($sql);
		$cache->save($result, $cacheId, array(SJB_Cache::TAG_LISTINGS, SJB_Cache::TAG_USERS));
		return $result;
	}

	/**
	 * @param array $groupByField
	 */
	public function setGroupByField($groupByField)
	{
		$this->groupByField = $groupByField;
	}
}

