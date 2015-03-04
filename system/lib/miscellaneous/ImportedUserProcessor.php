<?php

class SJB_ImportedUserProcessor
{
	var $properties_names;
	var $properties_values;
	//var $pictures_key;
	var $current_key = 0;
	var $tree_columns;
	//var $listing_type;
	
	var $user_group;

	function SJB_ImportedUserProcessor($input_data, $user) 
	{
		$this->user_group = $user->getUserGroupSID();
		$this->properties_names = $input_data;
		$tree_parser = new SJB_TreeParser($this->properties_names);
		$this->tree_columns = $tree_parser->getTreeColumns();
		$this->_unsetTreeColumnsNames($tree_parser->columns);
		$this->properties_values = $input_data;
	}
	
	function getPropertiesNames()
	{
	    return $this->properties_names;
	}
	
	function getData($values = array())
	{
		$listFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('list');
		$multilistFieldsInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('multilist');
		$fieldsInfo = array_merge($listFieldsInfo, $multilistFieldsInfo);
		foreach ($fieldsInfo as $key => $fieldInfo) {
			if($fieldInfo['user_group_sid'] == $this->user_group && empty($fieldInfo['parent_sid'])) { 
				$fieldsInfo[$fieldInfo['id']] = $fieldInfo;
			}
			unset($fieldsInfo[$key]);
		}
		
		$result	= array();
		foreach($this->properties_names as $key => $property_name) {
			if (in_array($property_name, array_keys($fieldsInfo)) && isset($values[$key])) {
				$fieldInfo = SJB_UserProfileFieldManager::getFieldInfoBySID($fieldsInfo[$property_name]['sid']);
				switch ($fieldInfo['type']) {
					case 'list':
						foreach ($fieldInfo['list_values'] as $listValues) {
							if ($listValues['caption'] == $values[$key]) {
								$result[$property_name]= $listValues['id'];
								break;
							}
						}
						break;
					case 'multilist':
						$multilistValues = explode(',', $values[$key]);
						$multilistDisplayValues = array();
						foreach ($fieldInfo['list_values'] as $listValues) {
							if (in_array($listValues['caption'], $multilistValues)) 
								$multilistDisplayValues[] = $listValues['id'];
						}
						$result[$property_name] = implode(',', $multilistDisplayValues);
						break;
				}
			}
			else
				$result[$property_name] = isset($values[$key]) ? $values[$key] : null;
		}
		$result = $result + $this->_getTreeValues($values);
		return $result;
	}
	
	function _getTreeValues($values)
	{
		$result = array();
		foreach($this->tree_columns as $tree_column_name => $tree_column_indexes) {
			$tree_captions = $this->_getTreeColumnValue($tree_column_indexes, $values);
			
			if ($tree_captions) {
				$tree_sid_searcher = new SJB_TreeUserSearcher($tree_column_name, $tree_captions);
				$tree_info = $tree_sid_searcher->getInfo();
				
				$result[$tree_column_name] = $tree_info ? $tree_info['sid'] : null;
			}
		}
		return $result;
	}

	function _getTreeColumnValue($columns, $values)
	{
		$result = array();
		foreach($columns as $column)
			if (isset($values[$column]) )$result[] = $values[$column];
		return $result;
	}

	function _unsetTreeColumnsNames($repeated_columns)
	{
		foreach($repeated_columns as $repeated_column)
			if (!is_null (array_search($repeated_column, $this->properties_names) ))
				unset($this->properties_names[array_search($repeated_column, $this->properties_names)]);
	}
}
