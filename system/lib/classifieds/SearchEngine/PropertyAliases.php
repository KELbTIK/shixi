<?php

class SJB_PropertyAliases
{
	var $aliases = array();

	function SJB_PropertyAliases($aliases = array())
	{
		$this->aliases = $aliases;
	}

	function addAlias($alias)
	{
		$this->aliases[] = $alias;
	}

	function changeAliasValuesInCriteria(&$criteria_data)
	{
		foreach ($criteria_data as $system_or_common => $criteria) {
			foreach ($criteria as $criteria_property_name => $property_criteria) {
				foreach ($property_criteria as $index => $criterion) {
					if ($this->_aliasNameExists($criteria_property_name)) {
						if ($criterion->getType() == 'like') {
							if ($criteria_property_name != 'product_info_sid') {
						    	$criterion = $this->_changeCriterionType($criterion, 'in');
							}
						    $criteria_data[$system_or_common][$criteria_property_name][$index] = $criterion;
						}

						$alias = $this->_getAliasByName($criteria_property_name);
						$criteria_data[$system_or_common][$criteria_property_name][$index]->setPropertyName($alias['real_id']);
						if (!empty($alias['transform_function'])) {
							$raw_value = $criterion->getRawValue();	
							$criteria_data[$system_or_common][$criteria_property_name][$index]->setFieldValue($raw_value);
							list($class_name, $function_name) = explode('::', $alias['transform_function']);
							$value = call_user_func(array($class_name, $function_name), $raw_value);
							
							if (!empty($value)) {
								$criteria_data[$system_or_common][$criteria_property_name][$index]->setValue($value);
							}
						}
					}
				}
			}
		}
	}
	
	function changePropertiesInfo($object_info)
	{			
		foreach ($this->aliases as $alias) {
			list($class_name, $function_name) = explode('::',$alias['transform_function']);
			$value = call_user_func(array($class_name, $function_name),$object_info[$alias['real_id']]);
			$object_info[$alias['id']] = $value;
		}
		return $object_info;
	}

	function _aliasNameExists($alias_name)
	{
		$alias = $this->_getAliasByName($alias_name);
		return !empty($alias);
	}

	function _getAliasByName($alias_name)
	{
		foreach ($this->aliases as $alias) {
			if ($alias['id'] == $alias_name) {
				return $alias;
			}
		}
		return array();
	}

	function _changeCriterionType($criterion, $new_type)
	{
		$new_criterion = SJB_SearchCriterion::getCriterionByType($new_type);
		$new_criterion->setProperty($criterion->getProperty());
		$new_criterion->setPropertyName($criterion->getPropertyName());
		$new_criterion->setValue($criterion->getRawValue());
		$new_criterion->setFieldValue($criterion->getFieldValue());
		return $new_criterion;
	}
}

