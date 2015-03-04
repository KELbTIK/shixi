<?php

class SJB_ImportedDataProcessor
{
	
	var $properties_names;
	var $properties_values;
	var $pictures_key;
	var $current_key = 0;
	var $tree_columns;
	var $listing_type;

	function SJB_ImportedDataProcessor($input_data, $listing) 
	{
		$this->listing_type = $listing->getListingTypeSID();
		
		$this->properties_names = $input_data;

		$tree_parser = new SJB_TreeParser($this->properties_names);
		$this->tree_columns = $tree_parser->getTreeColumns();
		$this->_unsetTreeColumnsNames($tree_parser->columns);
		
		$this->properties_values = $input_data;
		$this->pictures_key = array_search('pictures', $this->properties_names);
		if ($this->pictures_key !== false)
			$this->_setPicturesPath();
	}
	
	function getPropertiesNames()
	{
	    return $this->properties_names;
	}
	
	function getData($non_existed_values_flag, $values = array()) 
	{
		$listFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('list');
		$multilistFieldsInfo = SJB_ListingFieldManager::getFieldsInfoByType('multilist');
		$fieldsInfo = array_merge($listFieldsInfo, $multilistFieldsInfo);
		foreach ($fieldsInfo as $key => $fieldInfo) {
			if (empty($fieldInfo['parent_sid'])) {
				$fieldsInfo[$fieldInfo['id']] = $fieldInfo;
			}
			unset($fieldsInfo[$key]);
		}
		$result	= array();
		foreach($this->properties_names as $key => $property_name) {
			if (in_array($property_name, array_keys($fieldsInfo)) && isset($values[$key])) {
				$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($fieldsInfo[$property_name]['sid']);
				if (!empty($fieldInfo['parent_sid']) && $fieldInfo['id'] == 'State') {
					$fieldInfo['list_values'] = SJB_StatesManager::getStatesNamesByCountry(false, true);
				}
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
			else {
				if (strpos($property_name, 'date') && strtotime($values[$key]) == false) {
					if ($property_name == 'activation_date') {
						$values[$key] = date('Y-m-d H:i:s');
					} else {
						$values[$key] = '';
					}
				}
				$result[$property_name] = isset($values[$key]) ? $values[$key] : null;
			}
		}
		$result = $result + $this->_getTreeValues($values, $non_existed_values_flag);
		return $result;
	}
	
	function _getTreeValues($values, $non_existed_values_flag) 
	{
		$result = array();
		foreach($this->tree_columns as $tree_column_name => $tree_column_indexes) {
			$tree_captions = $this->_getTreeColumnValue($tree_column_indexes, $values);
			
			if ($tree_captions) {
				$tree_sid_searcher = new SJB_TreeInfoSearcher($tree_column_name, $tree_captions, $this->listing_type);
				$tree_info = $tree_sid_searcher->getInfo();
				
				if (empty($tree_info) && $non_existed_values_flag == 'add') {
					$tree_info = $this->_createTreeInfo($tree_column_name, $tree_captions, $this->listing_type);
				}
				
				$result[$tree_column_name] = $tree_info ? $tree_info['sid'] : null;
			}
		}
		return $result;
	}

	function _getTreeColumnValue($columns, $values)
	{
		$result = array();
		foreach($columns as $column)
			if(isset($values[$column]) )$result[] = $values[$column];
		return $result;
	}

	function _setPicturesPath()
	{
		if ( isset($this->properties_values[$this->pictures_key]) ) 
			$this->properties_values[$this->pictures_key] = $this->_getPathesForPicturesInfo($this->properties_values[$this->pictures_key]);
		else 
			$this->properties_values[$this->pictures_key] = array();
	}

	function _unsetTreeColumnsNames($repeated_columns)
	{
		foreach($repeated_columns as $repeated_column)
			if (!is_null (array_search($repeated_column, $this->properties_names) ))
				unset($this->properties_names[array_search($repeated_column, $this->properties_names)]);
	}

	function _getPathesForPicturesInfo($pictures_info)
	{
	    return explode(';', $pictures_info);
	}

	function _createTreeInfo($tree_column_name, $tree_captions)
	{
		$tree_sid_searcher = new SJB_TreeInfoSearcher($tree_column_name, array($tree_captions[0]), $this->listing_type);
		$tree_info = $tree_sid_searcher->getInfo();

		$field_info = SJB_ListingFieldDBManager::getListingFieldInfoByID($tree_column_name);
		$field_sid 	= $field_info['sid'];
		
		if ($tree_info == null)
			SJB_ListingFieldTreeManager::addTreeItemToEndByParentSID($field_sid, 0, $tree_captions[0]);

		$tree_info = $tree_sid_searcher->getInfo();
		SJB_ListingFieldTreeManager::addTreeItemToEndByParentSID($field_sid, $tree_info['sid'], $tree_captions[1]);
		
		$tree_sid_searcher = new SJB_TreeInfoSearcher($tree_column_name, $tree_captions, $this->listing_type);
		$tree_info = $tree_sid_searcher->getInfo();
				
		return $tree_info;
	}

}
