<?php

class SJB_SearchCriterion
{
	var $value			= null;
	var $field_value 	= null;
	var $property_name 	= null;
	var $property 		= null;
	var $type			= null;


	function SJB_SearchCriterion($criterion_type)
	{
		$this->type = $criterion_type;
	}

	function setPropertyName($property_name)
	{
		$this->property_name = $property_name;
	}

	function getPropertyName()
	{
		return $this->property_name;
	}

	function setProperty($property)
	{
		$this->property = $property;
	}

	function getProperty()
	{
		return $this->property;
	}

	function setValue($value)
	{
		$this->value=$value;
	}

	function getValue()
	{
		return array($this->type => $this->value);
	}

	function getRawValue()
	{
		return $this->value;
	}

	function setFieldValue($value)
	{
		$this->field_value = $value;
	}

	function getFieldValue()
	{
		return $this->field_value;
	}

	function getType()
	{
		return $this->type;
	}

	function getSQL()
	{
		return null;
	}

	function getSystemSQL($table = '')
	{
		return null;
	}

	function setSQLValue()
	{
		if (!empty($this->property)) {
			$this->property->setValue($this->value);
			$this->value = $this->property->getSQLValue($this);
		}
	}

	public static function getCriterionByType($criteria_type)
	{
		$CRITERIA_TYPES = array
						(
							'equal'			=>	'SJB_EqualCriterion',
							'not_equal'		=>	'SJB_NotEqualCriterion',
							'like'			=>	'SJB_LikeCriterion',
							'multi_like'	=>	'SJB_MultiLikeCriterion',
							'multi_like_and'=>	'SJB_MultiLikeANDCriterion',
							'in'			=>	'SJB_InCriterion',
							'more'			=>	'SJB_MoreCriterion',
							'less'			=>	'SJB_LessCriterion',
							'not_more'		=>	'SJB_LessEqualCriterion',
							'not_less'		=>	'SJB_MoreEqualCriterion',
							'not_empty'		=>	'SJB_NotEmptyCriterion',
							'tree'			=>	'SJB_TreeCriterion',
							'geo'			=>	'SJB_GeoCriterion',
							'geo_coord'		=>	'SJB_GeoCoordCriterion',
							'is_null'		=>	'SJB_NullCriterion',
							'simple_equal'	=>	'SJB_SimpleEqual',
							'first_char_like'	=>	'SJB_FirstCharLikeCriterion',
							'in_set'		=> 'SJB_InSetCriterion',
							'monetary'      => 'SJB_MonetaryCriterion',
						
							'exact_phrase'  =>  'SJB_ExactPhraseCriterion',
							'any_words'     =>  'SJB_AnyWordsCriterion',
							'all_words'		=>	'SJB_AllWordsCriterion',
							'boolean'		=>  'SJB_BooleanCriterion',
						
							'accessible'	=> 'SJB_AccessibleCriterion',
							'company_like'  => 'SJB_CompanyLikeCriterion',
							'relevance'     => 'SJB_RelevanceCriterion',
							'fields_or' 	=> 'SJB_FieldsOr',
							'location'	 	=> 'SJB_LocationCriterion',
						);

		$criteria_type = strtolower($criteria_type);
		
		if (!isset($CRITERIA_TYPES[$criteria_type]))
			return null;
		return new $CRITERIA_TYPES[$criteria_type]($criteria_type);
	}
}

class SJB_NullCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		return "(`id` = '{$this->property_name}' AND isnull(`value`))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		return "isnull(`{$this->property_name}`)";
	}

	function isValid()
	{
		return true;
	}
}

class SJB_NotEqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` != '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		$value = SJB_DB::quote($this->value);
		return "`{$this->property_name}` != '{$value}'";
	}

	function isValid()
	{
		return true;
	}
}

class SJB_EqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` = '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		return "`{$this->property_name}` = '{$value}'";
	}

	function isValid()
	{
		return $this->value !== '';
	}
}

class SJB_MultiLikeCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$res = '';
		$id = SJB_DB::quote($this->property_name);
		if (is_array($this->value)) {
			foreach ($this->value as $value) {
				if ($value === "0" || $value === "")
					continue;
				$val = SJB_DB::quote($value);
				if ($res == "") {
					$res .= " FIND_IN_SET('{$val}', `value`) ";
				} else {
					$res .= " OR FIND_IN_SET('{$val}', `value`) ";
				}
			}
		}
		else {
			$value = SJB_DB::quote($this->value);
			if ($value !== "0") {
				$res = " FIND_IN_SET('{$value}', `value`) ";
			}
		}
		if ($res === '')
			$res = 'true';
		return "(`id` = '{$id}' AND ({$res}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		$value = $this->value;
		if (is_array($value))
			$value = implode(',', $value);
		$vals = explode(',', SJB_DB::quote($value));
		$res = '';
		foreach ($vals as $val) {
			if ($res == '') {
				$res .= " FIND_IN_SET('{$val}', {$tablePrefix}`{$this->property_name}`) ";
			} else {
				$res .= " OR FIND_IN_SET('{$val}', {$tablePrefix}`{$this->property_name}`) ";
			}
		}
		return "($res)";
	}

	function isValid()
	{
		$valid = true;
		if (is_array($this->value)) {
			$valid = false;
			foreach ($this->value as $val) {
				if (!empty($val)) {
					$valid = true;
					break;
				}
			}
		}
		return !empty($this->value) && $valid;
	}
}

class SJB_MultiLikeAndCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$res = "";
		$id = SJB_DB::quote($this->property_name);
		$search = array('%', '_');
		$replace = array('\%', '\_');
		if (is_array($this->value)) {
			foreach ($this->value as $value) {
				if ($value === '0' || $value === '')
					continue;
				$val = SJB_DB::quote($value);
				$val = str_replace($search, $replace, $val);
				if ($res == '')
					$res .= " `value` LIKE '%{$val}%'";
				else
					$res .= " AND `value` LIKE '%{$val}%'";
			}
		}
		else {
			$value = SJB_DB::quote($this->value);
			$value = str_replace($search, $replace, $value);
			if ($value !== '0')
				$res = "`value` LIKE '%{$value}%'";
		}
		if ($res === '')
			$res = 'true';

		return "(`id` = '{$id}' AND ($res))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = $this->value;
		if (is_array($value))
			$value = implode(',', $value);
		$vals = explode(',', SJB_DB::quote($value));
		$res = '';
		foreach ($vals as $val) {
			$val = str_replace($search, $replace, $val);
			if ($res == '')
				$res .= "{$tablePrefix}`{$this->property_name}` LIKE '%{$val}%'";
			else 
				$res .= " OR {$tablePrefix}`{$this->property_name}` LIKE '%{$val}%'";
		}
		return "($res)";
	}

	function isValid()
	{
		$valid = true;
		if (is_array($this->value)) {
			$valid = false;
			foreach ($this->value as $val)
				if (!empty($val)) {
					$valid = true;
					break;
				}
		}
		return !empty($this->value) && $valid;
	}
}

class SJB_LikeCriterion extends SJB_SearchCriterion
{
	function getSQL( $table_name = '' )
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);
		if($table_name)
			return "(`{$table_name}`.`id` = '".$id . "' AND `{$table_name}`.`value` LIKE '%{$value}%')";
		else
			return "(`id` = '".$id . "' AND `value` LIKE '%{$value}%')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');

		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix = "`{$table}`.";
		}
		if (is_array($this->value)) {
			$sql = '';
			foreach ($this->value as $value) {
				$value = SJB_DB::quote($value);
				$value = str_replace($search, $replace, $value);
				if (!empty($sql))
					$sql .= ' OR ';
				$sql .= "{$tablePrefix}`{$this->property_name}` LIKE '%{$value}%'";
			}
			return $sql;
		}
			
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		return "{$tablePrefix}`{$this->property_name}` LIKE '%{$value}%'";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_FirstCharLikeCriterion  extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);
		if ($value == 'any_char') 
			return "(`id` = '{$id}' AND `value` REGEXP '^[^a-zA-Z].*')";
		else
			return "(`id` = '{$id}' AND `value` LIKE '{$value}%')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		if ($value == 'any_char') 
			return "`{$this->property_name}` REGEXP '^[^a-zA-Z].*'";
		else
			return "`{$this->property_name}` LIKE '{$value}%'";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_InCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		$value = $this->getSQLValue();
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` IN ({$value}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;

		$value = $this->getSQLValue();
		return "`{$this->property_name}` IN ({$value})";
	}

	function isValid()
	{
		return !empty($this->value);
	}

	function _wrapValueWithApostrof($value)
	{
		return "'" . SJB_DB::quote($value) . "'";
	}
	
	function _wrapArrayWithApostrof($array)
	{
		return array_map(array($this,"_wrapValueWithApostrof"), $array);
	}
	
	function getSQLValue()
	{
		$value 		= '';
		if (is_array($this->value))
			$value = join($this->_wrapArrayWithApostrof($this->value), ', ');
		if (empty($value))
			$value = 'NULL';
		return $value;
	}
}

class SJB_MoreCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
	 	if (!$this->isValid())
	 		return null;

		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` > {$this->value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		return "`{$this->property_name}` > {$this->value}";
	}

	function isValid()
	{
		return is_numeric($this->value);
	}
}

class SJB_LessCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` < {$this->value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		return "`{$this->property_name}` < {$this->value}";
	}

	function isValid()
	{
		return is_numeric($this->value);
	}
}

class SJB_MoreEqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` >= {$value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		return "`{$this->property_name}` >= {$value}";
	}

	function isValid()
	{
		if (!empty($this->property)) {
			$this->property->setValue($this->value);
			$is_valid = $this->property->isSearchValueValid();
			$this->setValue($this->property->getValue());
		}
		else {
			$value = trim($this->value);
			$is_valid = !empty($value);
		}

		return $is_valid;
	}
}

class SJB_LessEqualCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` <= {$value})";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$this->setSQLValue();
		$value = preg_replace("/^'+([^'\"]+)'+$/u", '$1', $this->value);
		$value = is_numeric($value) ? $value : "'" . SJB_DB::quote($value) . "'";
		
		return "`{$this->property_name}` <= {$value}";
	}

	function isValid()
	{
		if (!empty($this->property)) {
			$this->property->setValue($this->value);
			$is_valid = $this->property->isSearchValueValid();
			$this->setValue($this->property->getValue());
		}
		else {
			$value = trim($this->value);
			$is_valid = !empty($value);
		}

		return $is_valid;
	}
}

class SJB_GeoCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$geoLocation = new SJB_GeoLocation();
		$radius_search_unit = SJB_System::getSettingByName('radius_search_unit');
		
		$id = SJB_DB::quote($this->property_name);
		$distance = $radius_search_unit == 'kilometers' ? $this->value['radius'] : $this->value['radius'] * 1.60934;
		$zipCode = SJB_DB::query("SELECT `longitude`, `latitude` FROM `locations` WHERE `name` = ?s", $this->value['location']);
		$zipCode = $zipCode ? array_pop($zipCode) : false;
		if ($zipCode) {
			$myLocation = $geoLocation->fromDegrees($zipCode['latitude'], $zipCode['longitude']);
			$sql = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);
			return "(`id` = '{$id}' AND `value` IN (SELECT `name` FROM `locations` WHERE {$sql}))";
		}
		return null;
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;

		$geoLocation = new SJB_GeoLocation();
		$radius_search_unit = SJB_System::getSettingByName('radius_search_unit');

		$id = SJB_DB::quote($this->property_name);
		$location = SJB_DB::quote($this->value['location']);
		$distance = $radius_search_unit == 'kilometers' ? $this->value['radius'] : $this->value['radius'] * 1.60934;
		$zipCode = SJB_DB::query("SELECT `longitude`, `latitude` FROM `locations` WHERE `name` = ?s", $this->value['location']);
		$zipCode = $zipCode ? array_pop($zipCode) : false;
		if ($zipCode) {
			$myLocation = $geoLocation->fromDegrees($zipCode['latitude'], $zipCode['longitude']);
			$sql = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);
			return "`{$id}` IN (SELECT `name` FROM `locations` WHERE {$sql})";
		}
		return null;
	}

	function isValid()
	{
		return (!empty($this->value['radius']) && !empty($this->value['location']) && is_numeric($this->value['radius']));
	}

	function getValue()
	{
		return $this->value;
	}
}


/**
 * Special GeoCriterion.
 * Used in iPhone API.
 * @author janson
 *
 */
class SJB_GeoCoordCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}

		$latitude  = $this->value['latitude'];
		$longitude = $this->value['longitude'];
		$distance  = $this->value['distance'] * 1.60934;

		$id = SJB_DB::quote($this->property_name);

		$geoLocation = new SJB_GeoLocation();
		$myLocation = $geoLocation->fromDegrees($latitude, $longitude);
		$sql = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);
        
		return "(`id` = '{$id}' AND `value` IN (SELECT `name` FROM `locations` WHERE {$sql}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}

		$latitude  = SJB_DB::quote($this->value['latitude']);
		$longitude = SJB_DB::quote($this->value['longitude']);
		$distance  = SJB_DB::quote($this->value['distance'] * 1.60934);

		$id = SJB_DB::quote($this->property_name);

		$geoLocation = new SJB_GeoLocation();
		$myLocation = $geoLocation->fromDegrees($latitude, $longitude);
		$sql = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);
		
		return " `{$id}` IN (SELECT `name` FROM `locations` WHERE {$sql}) ";
	}

	function isValid()
	{
		return (!empty($this->value['distance']) && is_numeric($this->value['distance']) && !empty($this->value['latitude']) && is_numeric($this->value['latitude']) && !empty($this->value['longitude']) && is_numeric($this->value['longitude']));
	}

	function getValue()
	{
		return $this->value;
	}
}


class SJB_NotEmptyCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		if (empty($this->value))
			return null;

		return "(`id` = '{$this->property_name}' AND `value` != '')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;

		if (empty($this->value))
			return null;

		return "`{$this->property_name}` != ''";
	}

	function isValid()
	{
		return true;
	}
}

class SJB_TreeCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;

		$id = SJB_DB::quote($this->property_name);
		$value_array = explode(',', SJB_DB::quote($this->value));
		$res = '';
		$counter = count($value_array);

		foreach ($value_array as $key => $val) {
			$res = $res . "FIND_IN_SET('{$val}', `{$id}`)<>0";

			if ($key < ($counter - 1))
				$res = $res . ' OR ';
		}

		return "(`id` = '{$id}' AND ({$res}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;

		$tablePrefix = '';
		if ($table != '') {
			$tablePrefix .= "`{$table}`.";
		}

		$id = SJB_DB::quote($this->property_name);
		$value_array = explode(',', SJB_DB::quote($this->value));
		$res = '';
		$counter = count($value_array);

		foreach ($value_array as $key => $val) {
			$res = $res . "FIND_IN_SET('{$val}', {$tablePrefix}`{$id}`)<>0";

			if ($key < ($counter - 1))
				$res = $res . ' OR ';
		}

		return "({$res})";
	}

	function isValid()
	{
		return !empty($this->value);
	}

	function getValue()
	{
		return $this->value;
	}

	function setValue($value)
	{
		//	in order to search child items also
		if (!empty($value) && !is_array($value) && $this->property && $this->property->type->getDisplayAsSelectBoxes()) {
			$values = explode(',', $value);
			$valuesWithChild = $values;
			foreach ($values as $parentNode) {
				$childSIDs = SJB_ListingFieldTreeManager::getChildrenSIDBySID($parentNode);
				$valuesWithChild = array_merge($valuesWithChild, $childSIDs);
			}
			$value = implode(',', $valuesWithChild);
		}
		$this->value = $value;
	}
}


class SJB_SimpleEqual extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`{$id}` = '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		return "`{$this->property_name}` = '{$value}'";
	}

	function isValid()
	{
		return $this->value !== '';
	}
}


class SJB_InSetCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$id		= SJB_DB::quote($this->property_name);
		$value	= SJB_DB::quote($this->value);
		
		return "(`id` = '{$id}' AND FIND_IN_SET('{$value}', `value`))";
	}
	
	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$id		= SJB_DB::quote($this->property_name);
		$value	= SJB_DB::quote($this->value);
		
		return "FIND_IN_SET('{$value}', `{$id}`)";
	}
	
	function isValid()
	{
		return !empty($this->value);
	}
	
	function getValue()
	{
		return $this->value;
	}
}

class SJB_MonetaryCriterion extends SJB_SearchCriterion
{
	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}
		$value = $this->value;
		$currency = $this->value['currency'];
		$id = SJB_DB::quote($this->property_name);
		$search = array('%', '_');
		$replace = array('\%', '\_');
		if ($currency) {
			$course = SJB_CurrencyManager::getCurrencyByCurrCode($currency);
		}
		$course = isset($course['course']) ? $course['course'] : 1;
		if (!empty($value['not_less']) && !is_numeric($value['not_less'])) {
			$value['not_less'] = SJB_DB::quote($value['not_less']);
			$value['not_less'] = str_replace($search, $replace, $value['not_less']);
			return "(`{$id}` LIKE '%{$value['not_less']}%')";
		}
		if (!empty($value['not_more']) && !is_numeric($value['not_more'])) {
			$value['not_more'] = SJB_DB::quote($value['not_more']);
			$value['not_more'] = str_replace($search, $replace, $value['not_more']);
			return "(`{$id}` LIKE '%{$value['not_more']}%')";
		}
		$notLess = intval($value['not_less'] / $course);
		$notMore = intval($value['not_more'] / $course);
		$allCurrency = SJB_CurrencyManager::getActiveCurrencyList();
		$where = '';
		if (count($allCurrency) > 0) {
			$where = '(';
			foreach ($allCurrency as $currency) {
				if ($this->value['currency']) {
					$notLessVal = $notLess * $currency['course'];
					$notMoreVal = $notMore * $currency['course'];
					$addCurrency = "AND `{$id}_parameter`={$currency['sid']}";
				} else {
					$notLessVal = $notLess;
					$notMoreVal = $notMore;
					$addCurrency = '';
				}
				if ($notLessVal > 0 && $notMoreVal > 0) {
					$where .= "((`{$id}` BETWEEN {$notLessVal} AND {$notMoreVal}) {$addCurrency}) OR ";
				}
				elseif ($notLessVal > 0) {
					$where .= "(`{$id}` >= {$notLessVal} {$addCurrency}) OR ";
				}
				elseif ($notMoreVal > 0) {
					$where .= "(`{$id}` BETWEEN 1 AND {$notMoreVal} {$addCurrency}) OR ";
				} else {
					$where .= "(`{$id}` >= '0') OR ";
				}
			}
			$where = substr($where, 0, -4);
			$where .= ')';
		}
		return "{$where}";
	}

	function isValid()
	{
		return isset($this->value['not_less'], $this->value['not_more']) && ($this->value['not_less'] !== '' || $this->value['not_more'] !=='');
	}
}

class SJB_ExactPhraseCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		$value = str_replace($search, $replace, $value);
		return "(`id` = '{$id}' AND `value` like '%{$value}%')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}

		$userId = SJB_UserManager::getUserIdByKeywords($this->value);
		$userCriteria = '';
		if ($userId && $table == 'listings') {
			$userCriteria = " OR `listings`.`user_sid` = " . $userId;
		}
		if ($table) {
			$table = "`{$table}`.";
		}

		$value = SJB_DB::quote($this->value);
		$value = str_replace(array('%', '_'), array('\%', '\_'), $value);

		return "({$table}`{$this->property_name}` like '%{$value}%' {$userCriteria})";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_AnyWordsCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}
		$res = '';
		$id = SJB_DB::quote($this->property_name);
		$this->value = trim($this->value);
		$value = SJB_DB::quote($this->value);
		$values = explode(' ', $value);
		$values = array_map(array('SJB_HelperFunctions','trimValue'), $values);
		$search = array('%', '_');
		$replace = array('\%', '\_');
		if (is_array($values)) {
			foreach ($values as $value) {
				if (!empty($value)) {
					$val = SJB_DB::quote($value);
					$val = str_replace($search, $replace, $val);
					if ($res == '') {
						$res .= "`value` like '%{$val}%'";
					} else {
						$res .= " OR `value` like '%{$val}%'";
					}
				}
			}
		}
		else if ($value != '0') {
			$value = str_replace($search, $replace, $value);
			$res = "`value` like '%{$value}%'";
		}
		if ($res == '') {
			$res = 'true';
		}
		return "(`id` = '{$id}' AND ({$res}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}
		$this->value = trim($this->value);
		$values = explode(' ', SJB_DB::quote($this->value));
		$values = array_map(array('SJB_HelperFunctions','trimValue'), $values);
		$id = SJB_DB::quote($this->property_name);
		$search = array('%', '_');
		$replace = array('\%', '\_');

		$userId = SJB_UserManager::getUserIdByKeywords($this->value);
		$userCriteria = '';
		if ($userId && $table == 'listings') {
			$userCriteria = " OR `listings`.`user_sid` = " . $userId;
		}
		if (!empty($table)) {
			$table = "`{$table}`.";
		}
		$res = '';
		foreach ($values as $val) {
			$val = str_replace($search, $replace, $val);
			if (!empty($val)) {
				if ($res == '') {
					$res .= "{$table}`{$id}` like '%{$val}%'";
				} else {
					$res .= " OR {$table}`{$id}` like '%{$val}%'";
				}
			}
		}
		return "({$res} {$userCriteria})";
	}

	function isValid()
	{
		$values = explode(' ', $this->value);
		$valid = true;
		if (is_array($values)) {
			$valid = false;
			foreach ($values as $val) {
				if (!empty($val)) {
					$valid = true;
					break;
				}
			}
		}
		return !empty($values) && $valid;
	}
}

class SJB_AllWordsCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}
		$res = '';
		$id = SJB_DB::quote($this->property_name);
		$this->value = trim($this->value);
		$values = explode(' ', SJB_DB::quote($this->value));
		$values = array_map(array('SJB_HelperFunctions','trimValue'), $values);
		$search = array('%', '_');
		$replace = array('\%', '\_');

		if (is_array($values)) {
			foreach ($values as $value) {
				$val = SJB_DB::quote($value);
				$val = str_replace($search, $replace, $val);
				if ($res == '') {
					$res .= "`value` like '%{$val}%'";
				} else {
					$res .= " AND `value` like '%{$val}%'";
				}
			}
		}
		else {
			$value = SJB_DB::quote($this->value);
			$value = str_replace($search, $replace, $value);
			if ($value != '0') {
				$res = "`value` like '%{$value}%'";
			}
		}
		if ($res == '') {
			$res = 'true';
		}
		return "(`id` = '{$id}' AND ({$res}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}
		$this->value = trim($this->value);
		$values = explode(' ', SJB_DB::quote($this->value));
		$values = array_map(array('SJB_HelperFunctions','trimValue'), $values);
		$id = SJB_DB::quote($this->property_name);
		$res = '';
		$search = array('%', '_');
		$replace = array('\%', '\_');

		$userId = SJB_UserManager::getUserIdByKeywords($this->value);
		$userCriteria = '';
		if ($userId && $table == 'listings') {
			$userCriteria = " OR `listings`.`user_sid` = " . $userId;
		}
		if ($table) {
			$table = "`{$table}`.";
		}
		foreach ($values as $val) {
			$val = str_replace($search, $replace, $val);
			if ($res == '') {
				$res .= "{$table}`{$id}` like '%{$val}%'";
			}
			else {
				$res .= " AND {$table}`{$id}` like '%{$val}%'";
			}
		}
		if ($userCriteria) {
			$res .= $userCriteria;
		}
		return "({$res})";
	}

	function isValid()
	{
		$values = explode(' ', $this->value);
		$valid = true;

		if (is_array($values)) {
			$valid = false;
			foreach ($values as $val) {
				if (!empty($val)) {
					$valid = true;
					break;
				}
            }
		}
		return !empty($values) && $valid;
	}
}

class SJB_BooleanCriterion extends SJB_SearchCriterion
{

	function getSQL()
	{
		if (!$this->isValid()) {
			return null;
		}
		$val = SJB_BooleanEvaluator::parse($this->value, false, '`value`');

		if ($val === null) {
			return null;
		}
			
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND ({$val}))";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}

		$userId = SJB_UserManager::getUserIdByKeywords($this->value);
		$userCriteria = '';
		if ($userId && $table == 'listings') {
			$userCriteria = " OR `listings`.`user_sid` = " . $userId;
		}

		$id = SJB_DB::quote($this->property_name);
		if ($table) {
			$table = "`{$table}`.`{$id}`";
		}
		$val = SJB_BooleanEvaluator::parse($this->value, false, $table);
		if ($val === null) {
			$val = '';
		}
		return "({$val} {$userCriteria})";
	}
	
	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_AccessibleCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return "(`id` = '{$id}' AND `value` = '{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		
		$access_list	= 'access_list';
		$value			= SJB_DB::quote($this->value);
		
		$sql = " (
			(`{$table}` . `{$this->property_name}` = 'everyone') OR
			(`{$table}` . `{$this->property_name}` = 'only' AND FIND_IN_SET('{$value}', `{$table}` . `{$access_list}`) ) OR
			(`{$table}` . `{$this->property_name}` = 'except' AND (FIND_IN_SET('{$value}', `{$table}` . `{$access_list}`) = 0 OR FIND_IN_SET('{$value}', `{$table}` . `{$access_list}`) IS NULL) )
			)";
		
		return $sql;
	}

	function isValid()
	{
		return $this->value !== '';
	}
}

class SJB_CompanyLikeCriterion extends SJB_SearchCriterion
{
	function getSQL( $table_name = 'users_properties' )
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);

		return "(`{$table_name}`.`id` = '".$id . "' AND `{$table_name}`.`value` LIKE '%{$value}%')";
	}

	function getSystemSQL($table = 'users')
	{
		if (!$this->isValid())
			return null;
		$search = array('%', '_');
		$replace = array('\%', '\_');
		$value = SJB_DB::quote($this->value);
		$value = str_replace($search, $replace, $value);
		$id = SJB_DB::quote($this->property_name);

		return "(`{$table}`.`{$id}` LIKE '%{$value}%')";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_RelevanceCriterion extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		$id = SJB_DB::quote($this->property_name);
		return " MATCH(`{$id}`) AGAINST ('{$value}')";
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid())
			return null;
		$value = SJB_DB::quote($this->value);
		return " MATCH(`{$this->property_name}`) AGAINST ('{$value}') ";
	}

	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_FieldsOr extends SJB_SearchCriterion
{
	function getSQL()
	{
		if (!$this->isValid() || !is_array($this->value))
			return null;
			
		$sql = array();

		foreach ($this->value as $fieldName => $fieldValue) {
			foreach ($fieldValue as $criteriaType => $value) {
				$new_criterion = SJB_SearchCriterion::getCriterionByType($criteriaType);
				$new_criterion->setPropertyName($fieldName);
				$new_criterion->setValue($value);
				$sql[] = $new_criterion->getSystemSQL();
			}
		}
		
		if ($sql)	
			return "(".implode(' OR ', $sql).")";
			
		return null;
	}

	function getSystemSQL($table = '')
	{
		if (!$this->isValid() || !is_array($this->value))
			return null;
			
		$sql = array();

		foreach ($this->value as $fieldName => $fieldValue) {
			foreach ($fieldValue as $criteriaType => $value) {
				$new_criterion = SJB_SearchCriterion::getCriterionByType($criteriaType);
				$new_criterion->setPropertyName($fieldName);
				$new_criterion->setValue($value);
				$sql[] = $new_criterion->getSystemSQL();
			}
		}
		
		if ($sql)	
			return "(".implode(' OR ', $sql).")";
			
		return null;
	}
	
	function isValid()
	{
		return !empty($this->value);
	}
}

class SJB_LocationCriterion extends SJB_SearchCriterion
{
	function getSystemSQL($table = '')
	{
		if (!$this->isValid()) {
			return null;
		}
		
		$city  = '';
		$id    = SJB_DB::quote($this->property_name);
		$value = explode(',', $this->value['value']);
		$locationValues = array();
		foreach ($value as $key => $val) {
			$locationValues[$key] = trim($val);
		}
		$value = implode(',', $locationValues);
		$selectFields = "`name`";
		if (!empty($this->value['radius']) && is_numeric($this->value['radius'])) {
			$selectFields = "`name`, `longitude`, `latitude`, `state_code`, `country_sid`";
		}
		$findZipCodes = SJB_DB::query("SELECT {$selectFields} FROM `locations` WHERE FIND_IN_SET(`name`, ?s)", $value);
		if (!$findZipCodes) {
			$locationValue = trim($this->value['value']);
			$isCountry = SJB_DB::queryValue("SELECT `sid` FROM `countries` WHERE `country_name` = ?s LIMIT 1", $locationValue);
			if ($isCountry) {
				return " `{$table}`.{$id}_Country = '{$isCountry}' ";
			}
			$isState = SJB_DB::queryValue("SELECT `sid` FROM `states` WHERE `state_name` = ?s OR `state_code` = ?s LIMIT 1", $locationValue, $locationValue);
			if ($isState) {
				return " `{$table}`.{$id}_State = '{$isState}' ";
			}
		}
		if (!$findZipCodes) {
			if (count($locationValues) == 1) {
				$findZipCodes = SJB_DB::query("SELECT {$selectFields} FROM `locations` WHERE `city` = ?s", $value);
				$city = SJB_DB::quote($value);
				if ($findZipCodes && empty($this->value['radius'])) {
					return " `{$table}`.{$id}_City = '{$city}' ";
				}
			} else {
				$findZipCodes = SJB_DB::query("SELECT {$selectFields} FROM `locations` WHERE `city` = ?s AND (`state` = ?s OR `state_code` = ?s)", $locationValues[0], $locationValues[1], $locationValues[1]);
				$city = SJB_DB::quote($locationValues[0]);
				$stateSID = SJB_DB::queryValue("SELECT `sid` FROM `states` WHERE `state_name` = ?s OR `state_code` = ?s LIMIT 1", $locationValues[1], $locationValues[1]);
				if ($findZipCodes && empty($this->value['radius'])) {
					return " (`{$table}`.{$id}_City = '{$city}' AND `{$table}`.{$id}_State = '{$stateSID}') ";
				}
			}
			if (empty($findZipCodes)) {
				$city = '';
			}
		}
		
		if ($findZipCodes) {
			if (!empty($this->value['radius']) && is_numeric($this->value['radius'])) {
				$zipCodes = $this->getQueryForZipCodesByRadius($findZipCodes, $city);
			} else {
				$location = array();
				foreach ($findZipCodes as $zipCode) {
					$location[] = "'" . SJB_DB::quote($zipCode['name']) . "'";
				}
				$zipCodes = implode(',', $location);
			}
			
			if ($zipCodes) {
				$zipCodes = "`{$table}`.`{$id}_ZipCode` IN ({$zipCodes})";
			}
			
			if ($city) {
				if (!empty($stateSID)) {
					$city = "(`{$table}`.{$id}_City = '{$city}' AND `{$table}`.{$id}_State = '{$stateSID}')";
				} else {
					$city = "`{$table}`.{$id}_City = '{$city}'";
				}
			}
			
			if ($city && $zipCodes) {
				return " ({$city} OR {$zipCodes})";
			}
			else if ($city) {
				return " ({$city})";
			}
			else if ($zipCodes) {
				return " ({$zipCodes})";
			}
			
			return '';
		}
		$listValues = $this->getListValues();
		$value = SJB_DB::quote(implode(' ', $listValues));
		return " (MATCH(`{$table}`.`{$id}`) AGAINST ('{$value}' IN BOOLEAN MODE))";
	}

	/**
	 * @param array $findZipCodes
	 * @param string $city
	 * @return string
	 */
	private function getQueryForZipCodesByRadius(array $findZipCodes, $city)
	{
		$geoLocation      = new SJB_GeoLocation();
		$radiusSearchUnit = SJB_System::getSettingByName('radius_search_unit');
		
		if ($city) {
			$minLatitude  = $maxLatitude  = $findZipCodes[0]['latitude'];
			$minLongitude = $maxLongitude = $findZipCodes[0]['longitude'];
			$stateCode    = $findZipCodes[0]['state_code'];
			$countrySid   = $findZipCodes[0]['country_sid'];
			foreach ($findZipCodes as $zipCode) {
				if ($stateCode != $zipCode['state_code'] || $countrySid != $zipCode['country_sid']) {
					return '';
				} else {
					$zipLatitude  = $zipCode['latitude'];
					$zipLongitude = $zipCode['longitude'];
					if ($zipLatitude < $minLatitude) {
						$minLatitude = $zipLatitude;
					}
					else if ($zipLatitude > $maxLatitude) {
						$maxLatitude = $zipLatitude;
					}
					if ($zipLongitude < $minLongitude) {
						$minLongitude = $zipLongitude;
					}
					else if ($zipLongitude > $maxLongitude) {
						$maxLongitude = $zipLongitude;
					}
				}
			}
			$distance = SJB_LocationManager::getDistanceBetweenPointsInKm($minLatitude, $minLongitude, $maxLatitude, $maxLongitude);
			$distance /= 2;
			$distance += $radiusSearchUnit == 'kilometers' ? $this->value['radius'] : $this->value['radius'] * 1.60934;
			
			$centralLatitude  = ($minLatitude + $maxLatitude) / 2;
			$centralLongitude = ($minLongitude + $maxLongitude) / 2;
			$centralLocation  = $geoLocation->fromDegrees($centralLatitude, $centralLongitude);
			
			$query = SJB_LocationManager::findPlacesWithinDistance($centralLocation, $distance);
			$query .= " AND (`city` != '" . SJB_DB::quote($city) ."')";
		} else {
			$query    = array();
			$distance = $radiusSearchUnit == 'kilometers' ? $this->value['radius'] : $this->value['radius'] * 1.60934;
			foreach ($findZipCodes as $zipCode) {
				$myLocation = $geoLocation->fromDegrees($zipCode['latitude'], $zipCode['longitude']);
				$query[]    = SJB_LocationManager::findPlacesWithinDistance($myLocation, $distance);
			}
			$query = implode(' OR ', $query);
		}
		
		return "SELECT `name` FROM `locations` WHERE {$query}";
	}

	function isValid()
	{
		return !empty($this->value['value']);
	}
	
	public function getListValues()
	{
		$listValues = str_replace(',', ' ', $this->value['value']);
		$listValues = explode(' ', $listValues);
		$listValues = array_diff($listValues, array(''));
		$correctedValues = array();
		foreach ($listValues as $key => $value) {
			$value = trim($value);
			while(preg_match('/^[+\-><()~*"]/u', $value)) {
				$value = preg_replace('/^[+\-><()~*"]/u', '', $value);
			}
			$listValues[$key] = $value;
			$len = strlen($value);
			if ($len < 4) {
				for ($i = $len; $i < 4; $i++) {
					$value .= '_';
				}
				$correctedValues[] = $value;
			}
		}
		$listValues = array_merge($listValues, $correctedValues);
		$listValues = array_diff($listValues, array(''));
		return $listValues;
	}
}