<?php

class SJB_ListingRequestCreator extends SJB_DBObjectRequestCreator
{
	var $order_info = null;
	var $orderPriority = null;
	var $orderRelevance = null;

	function SJB_ListingRequestCreator($listing_sid_collection, $order_info, $orderPriority = false, $orderRelevance = false)
	{
		$this->table_prefix = 'listings';
		$this->sid_collection 	= $listing_sid_collection;
		$this->order_info = $order_info;
		if (is_array($this->order_info['property'])) {
			foreach ($this->order_info['property'] as $orderInfoProperty) {
				$this->property_collection[] = $orderInfoProperty->getID();
			}
		} else {
			$this->property_collection = array($this->order_info['property']->getID());
		}
		
		if ($orderPriority)
			$this->orderPriority = $orderPriority;
		if ($orderRelevance)
			$this->orderRelevance = $orderRelevance;		
	}

	function getRequest()
	{
		$what_part = $this->_getWhatPart();
		$join_part = $this->_getJoinPart();
		$where_part = $this->_getWherePart();
		$order_part = $this->_getOrderPart();
		
		return "SELECT $what_part FROM $join_part WHERE $where_part $order_part";
	}

	function _getWhatPart()
	{
		if (is_array($this->order_info['property'])) {
			foreach ($this->order_info['property'] as $orderInfoProperty) {
				$property[] = $orderInfoProperty;
			}
		} else {
			$property = $this->order_info['property'];
		}

		if (is_array($property)) {
			foreach ($property as $propertyItem) {
				if ($propertyItem->isSystem()) {
					return "`{$this->table_prefix}`.sid";
				}
			}
		} elseif ($property->isSystem()) {
			if ($this->orderRelevance) {
				$value = SJB_DB::quote($this->orderRelevance['value']['value']);
				return "`{$this->table_prefix}`.sid,  MATCH(`{$this->orderRelevance['value']['field']}`) AGAINST ('{$value}') as relevance";
			} else {
				return "`{$this->table_prefix}`.sid";
			}
		} elseif($property->getType() != 'tree') {
			$id 		= $property->getID();
			$sql_type 	= $property->getSQLType();
			
			if ($sql_type == 'DECIMAL') { 	// DECIMAL type is available in MYSQL 5.0.8 or higher
				return "{$this->table_prefix}.*, {$id}.value + 0.0 AS {$id}";
			}
			return "{$this->table_prefix}.*, CAST({$id}.value AS {$sql_type}) AS {$id}";
		}
		$tree_browser = new SJB_TreeBrowser($property->getID());
		return "{$this->table_prefix}.*, " . $tree_browser->getWhatPart();
	}

	function _getOrderPart()
	{
		$orderBy = ' ORDER BY ';
		if (!is_null($this->orderPriority)) {
			$orderBy .= "{$this->orderPriority['property']} {$this->orderPriority['sorting_order']}, ";
		} elseif (!is_null($this->orderRelevance)) {
			$orderBy .= "{$this->orderRelevance['property']} {$this->orderRelevance['sorting_order']}, ";
		}
		
		if (is_array($this->order_info['property'])) {
			$sql = $orderBy.' ';
			foreach ($this->order_info['property'] as $key => $orderInfoProperty) {
				$id = $orderInfoProperty->getID();
				if (strstr($id, '_') == '_State') {
					$id = "`states`.`state_code`";
				} elseif (strstr($id, '_') == '_Country') {
					$id = "`countries`.`country_name`";
				}
				if ($key > 0) {
					$sql .= ", {$id} {$this->order_info['sorting_order']}";
				} else {
					$sql .= " {$id} {$this->order_info['sorting_order']}";
				}
			}
			return $sql;
		} elseif ($this->order_info['property']->getType() != 'tree' && ($this->order_info['sorting_order'] === 'ASC' || $this->order_info['sorting_order'] === 'DESC')) {
			$id = $this->order_info['property']->getID();
			return "{$orderBy} {$id} {$this->order_info['sorting_order']} ";
		}
		return str_replace('ORDER BY', $orderBy, $this->_getTreeOrderPart($this->order_info['property']->getID()));
	}

	function _getTreeOrderPart($property)
	{
		$tree_browser = new SJB_TreeBrowser($property);
		return $tree_browser->getOrderPart($this->order_info['sorting_order']);
	}

	function _getJoinPart()
	{
		$result = "`{$this->table_prefix}`";
		foreach($this->property_collection as $property)
			$result .= $this->_getSingleJoinPart($property);
		return $result;
	}

	function _getSingleJoinPart($property)
	{
		if (is_array($this->order_info['property'])) {
			foreach ($this->order_info['property'] as $orderInfoProperty) {
				if ($orderInfoProperty->id == $property) {
					if ($orderInfoProperty->getType() != 'tree') {
						return $this->_getCommonSingleJoinPart($property);
					} else {
						return $this->_getSingleJoinPartForTree($property);
					}
				}
			}
		} elseif ($this->order_info['property']->getType() != 'tree') {
			return $this->_getCommonSingleJoinPart($property);
		}
		return $this->_getSingleJoinPartForTree($property);
	}

	function _getSingleJoinPartForTree($property)
	{
		$tree_browser = new SJB_TreeBrowser($property);
		return $this->_getCommonSingleJoinPart($property) . $tree_browser->getJoinPart();
	}

	function _getCommonSingleJoinPart($property)
	{
		if (is_array($this->order_info['property'])) {
			$sql = '';
			foreach ($this->order_info['property'] as $orderInfoProperty) {
				if ($orderInfoProperty->id == $property) {
					if (strstr($property, '_') == '_State') {
						$sql .= " LEFT JOIN `states` ON `{$this->table_prefix}`.`{$property}` = `states`.`sid` ";
					} elseif (strstr($property, '_') == '_Country') {
						$sql .= " LEFT JOIN `countries` ON `{$this->table_prefix}`.`{$property}` = `countries`.`sid` ";
					}
					if (!$orderInfoProperty->isSystem()) {
						$sql .= " LEFT JOIN `{$this->table_prefix}_properties` `{$property}` ON (`{$this->table_prefix}`.`sid` = `{$property}`.`object_sid` AND `{$property}`.`id` = '{$property}')";
					}
				}
			}
			return $sql;
		} elseif ($this->order_info['property']->isSystem()) {
			return '';
		}
		return " LEFT JOIN `{$this->table_prefix}_properties` `{$property}` ON (`{$this->table_prefix}`.`sid` = `{$property}`.`object_sid` AND `{$property}`.`id` = '{$property}')";
	}

}
