<?php

class SJB_SearchSqlTranslator
{
	var $object_table_prefix	= null;
	var $valid_criterion_number	= null;
	private $isComplex = false;
	private $isMonetary = false;
	private $isRelevance = false;
	private $limit = 0;
	private $groupByField = null;

	public function __construct($object_table_prefix)
	{
		$this->object_table_prefix = $object_table_prefix;
	}

	function buildSQLQuery($criteria, &$valid_criterion_number, $sorting_fields, $inner_join = false, $count = false, $relevance = false)
	{
		foreach ($criteria['common'] as $crit) {
			foreach($crit as $criterion) {
				if ($criterion->isValid() && $criterion->getProperty() !== null && $criterion->getProperty()->getType() == 'monetary') {
					$this->isMonetary = true;
					break;
				}
				if ($criterion->isValid() && $criterion->getProperty() !== null && $criterion->getProperty()->isComplex()) {
					$this->isComplex = true;
					break;
				}
			}
		}
		if ($relevance)
			$this->isRelevance = true;
		$sorting_block = $this->_getSortingStatement($sorting_fields);
		if ($count)
			$select_block = $this->_getSelectCountStatement();
		elseif ($relevance)
			$select_block = $this->_getSelectRelevanceStatement($relevance);
		else
			$select_block = $this->_getSelectStatement();
		$from_block	= $this->_getFromStatement($inner_join);
		$where_block = $this->_getWhereStatement($criteria);

		$group_block = '';
		if (SJB_DB::table_exists($this->object_table_prefix.'_properties'))
			$group_block = $this->_getGroupStatement();

		$having_block = '';
		if ($this->valid_criterion_number != 0 && !empty($group_block)) {
	        $having_block = " HAVING `count` = {$this->valid_criterion_number} ";
		}

		$limit_block = '';
		if ($this->limit)
			$limit_block = ' LIMIT ' . $this->limit;

		$valid_criterion_number = $this->valid_criterion_number;
		return $select_block . $from_block . $where_block . $group_block . $having_block . $sorting_block . $limit_block;
	}

	function _getSortingStatement($sorting_fields)
	{
		$sorting_block = null;

		if (!empty($sorting_fields)) {
			$sorting_block = " ORDER BY ";
			$delimiter = null;

			foreach($sorting_fields as $sorting_field_name => $sorting_order) {
				$sorting_block .= $delimiter . $sorting_field_name . " " . $sorting_order;
				$delimiter = ", ";
			}
		}

		return $sorting_block;
	}

	function _getGroupStatement()
	{
		if ($this->isComplex || $this->isMonetary)
			return "GROUP BY `{$this->object_table_prefix}_properties`.`object_sid`";
		elseif ($this->isRelevance)
			return "GROUP BY `{$this->object_table_prefix}`.`sid`";
		elseif (is_array($this->groupByField)) {
			foreach ($this->groupByField as $table => $field) {
				return "GROUP BY `{$table}`.`{$field}`";
			}
		}
		return '';
	}

	function _getSelectStatement()
	{
		if (SJB_DB::table_exists($this->object_table_prefix.'_properties')){
			if ($this->object_table_prefix == 'listings' && $this->isComplex) {
				return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid`, if( COUNT( `complex_enum` ) >0, COUNT( DISTINCT `id` ), COUNT( *  ) ) `count` ";
			}
			else {
				if ($this->isComplex || $this->isMonetary)
					return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid`, COUNT(*) as `count` ";
				return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid`";
			}
		}
		return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid` ";
	}
	
	function _getSelectRelevanceStatement($criteria)
	{
		$value = SJB_DB::quote($criteria['value']);
		if (SJB_DB::table_exists($this->object_table_prefix.'_properties')){
			if ($this->object_table_prefix == 'listings' && $this->isComplex) {
				return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid`, if( COUNT( `complex_enum` ) >0, COUNT( DISTINCT `id` ), COUNT( *  ) ) `count`,  MATCH(`{$criteria['field']}`) AGAINST ('{$value}') as relevance ";
			}
			else {
				return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid`, COUNT(*) as `count` ";
			}
		}
		return "SELECT `{$this->object_table_prefix}`.`sid` as `object_sid`,  MATCH`(`{$criteria['field']}`) AGAINST ('{$value}') as relevance";
	}

	function _getSelectCountStatement()
	{
		return "SELECT count(*) as `count` ";
	}

	function _getFromStatement($inner_join = false)
	{
		$sql = '';
		$inner = '';
		if (!empty($inner_join)){
			foreach ($inner_join as $key => $val) {
				if (str_replace('_2second', '', $key)) {
					$as = $key;
					$table = str_replace('_2second', '', $key);
				}
				if (isset($val['sort_field']) && !isset($val['noPresix']))
					$sql .= ", `{$key}`.{$val['sort_field']} ";
				elseif (isset($val['select_field']))
					$sql .= ", `{$key}`.{$val['select_field']} ";
				if (isset($val['count'])) 
					$sql .= ", {$val['count']} ";

				if(isset($val['main_table']))
				if (isset($as))
					$inner .= $val['join']." `{$table}` as {$as} ON `{$as}`.{$val['join_field']} = `{$this->object_table_prefix}`.{$val['join_field2']} ";
				else
					$inner .= $val['join']." `{$key}`  ON `{$key}`.{$val['join_field']} = `{$this->object_table_prefix}`.{$val['join_field2']} ";
			}	
		}
		if (SJB_DB::table_exists($this->object_table_prefix	.'_properties') && ($this->isComplex || $this->isMonetary)) {
			$from_block = "FROM `{$this->object_table_prefix}` INNER JOIN `{$this->object_table_prefix}_properties` ON `{$this->object_table_prefix}`.`sid` = `{$this->object_table_prefix}_properties`.`object_sid` ";
		} else {
			$from_block =  "FROM `{$this->object_table_prefix}` ";
		}
		return $sql. $from_block.'  '.$inner;
	}

	function _getWhereStatement($criteria)
	{
		$this->valid_criterion_number = 0;
		return $this->_getWhereSystemStatement($criteria['system']) . $this->_getWhereCommonStatement($criteria['common']);
	}

	function _getWhereCommonStatement($criteria)
	{
		$where_common_block	= 'AND (0 ';
		foreach($criteria as $property_criteria) {
			$where_AND_block = '';
			foreach ($property_criteria as $criterion) {
				if ($criterion->isValid() && SJB_ListingFieldManager::getListingFieldSIDByID($criterion->property_name)) {
					$sql = $criterion->getSQL();
					if ($sql !== null)
						$where_AND_block .= 'AND ' . $sql . ' ';
				}
			}

			if (!empty($where_AND_block)) {
				$where_common_block .= sprintf('OR(1 %s) ', $where_AND_block);
				$this->valid_criterion_number++;
			}
		}
		if ($this->valid_criterion_number == 0)
			$where_common_block = '';
		else
			$where_common_block .= ') ';

		return $where_common_block;
	}
	
	function _getWhereSystemStatement($criteria)
	{
		$where_system_block	= 'WHERE 1 ';

		foreach ($criteria as $property_criteria) {
			$where_AND_block = '';
			foreach ($property_criteria as $criterion) {
				if ($criterion->isValid()) {
					if ($criterion instanceof SJB_NullCriterion ||
						$criterion instanceof SJB_MultiLikeCriterion ||
						$criterion instanceof SJB_SimpleEqual ||
						$criterion instanceof SJB_RelevanceCriterion ||
						$criterion instanceof SJB_TreeCriterion ||
						$criterion instanceof SJB_InSetCriterion ||
						$criterion instanceof SJB_LikeCriterion ||
						$criterion instanceof SJB_MultiLikeANDCriterion ||
						$criterion instanceof SJB_FieldsOr) {
						$where_AND_block .= 'AND ' . $criterion->getSystemSQL($this->object_table_prefix) . ' ';
					}
					else {
						if ($criterion instanceof SJB_CompanyLikeCriterion) {
							$sql = $criterion->getSystemSQL();
							if ($sql !== null) {
								$where_AND_block .= 'AND '.$sql.' ';
							}
						}
						else if ($criterion instanceof SJB_AccessibleCriterion ||
								 $criterion instanceof SJB_AnyWordsCriterion ||
								 $criterion instanceof SJB_AllWordsCriterion ||
								 $criterion instanceof SJB_BooleanCriterion ||
								 $criterion instanceof SJB_LocationCriterion ||
								 $criterion instanceof SJB_ExactPhraseCriterion ||
								 $criterion instanceof SJB_MonetaryCriterion) {
							$sql = $criterion->getSystemSQL($this->object_table_prefix);
							if ($sql !== null) {
								$where_AND_block .= 'AND '.$sql.' ';
							}
						}
						else {
							$sql = $criterion->getSystemSQL();
							if ($sql !== null) {
								$where_AND_block .= "AND `{$this->object_table_prefix}`.".$sql.' ';
							}
						}
					}
				}
			}

			if (!empty($where_AND_block))
				$where_system_block .= $where_AND_block;
		}

		return $where_system_block;
	}

	public function setLimit($limit)
	{
		$this->limit = $limit;
	}

	/**
	 * @param array $groupByField
	 */
	public function setGroupByField($groupByField)
	{
		$this->groupByField = $groupByField;
	}


}

