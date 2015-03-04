<?php

class SJB_TreeType extends SJB_Type
{
	var $tree_values;
	var $assoc_display = array();
	var $sel_count;
	public $display_value = null;

	/**
	 * @var bool
	 */
	var $displayAsSelectBoxes = false;
	function SJB_TreeType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->tree_values = isset($property_info['tree_values']) ? $property_info['tree_values'] : array();
		$this->default_template = 'tree.tpl';
		if ( !empty($property_info['display_as_select_boxes'] ) )
		{
			$this->displayAsSelectBoxes = true;
			$this->default_template = 'tree_select_bexes.tpl';
		}

		$value = isset($this->property_info['value']) ? $this->property_info['value'] : null;
		$this->setValue($value);
	}
	
	function setValue($value)
	{
		if (!is_array($value))
			$this->display_value = explode(',', $value);
		else
			$this->display_value = $value;
		
		$this->assoc_display = $this->makeAssociateArrayToOutput();
		$this->sel_count = $this->getSelectedCount();
	}
	
	function getSelectedCount()
	{
		$count = 0;
		if (is_array($this->display_value)) {
			foreach ($this->display_value as $one) {
				if (empty($one))
				    continue;
				if (!array_key_exists($one, $this->tree_values))
				    $count++;
			}
		}
		return $count;
	}
	
	function getPropertyVariablesToAssign()
	{
		$propertyVariables = parent::getPropertyVariablesToAssign();
		$propertyVariables['choiceLimit'] = SJB_Array::get($this->property_info, 'choiceLimit');
		
		// useless code block.
		$profileFieldAsDv = SJB_Array::get($propertyVariables, 'profile_field_as_dv');
		if ($profileFieldAsDv) {
			$profileFieldDefaultValue = array();
			$profileFieldValues = explode(',', $profileFieldAsDv);
			$fieldSID = $this->property_info['sid'];
			foreach ($profileFieldValues as $treeItemSID) {
				$fieldValue = SJB_UserProfileFieldManager::getTreeDisplayValueBySID($treeItemSID);
				if ($fieldValue) {
					$fieldValue = array_pop($fieldValue);
					$itemInfo = SJB_ListingFieldTreeManager::getItemInfoByCaption($fieldSID, $fieldValue);
					if (!empty($itemInfo['sid'])) {
						array_push($profileFieldDefaultValue, $itemInfo['sid']);
					}
				}
			}
			$propertyVariables['profile_field_as_dv'] = $profileFieldDefaultValue;
		}

		$value           = $this->property_info['value'];
		$defaultValue    = $this->property_info['default_value'];
		$checkedElements = '';
		if ($value) {
			if (is_array($value)) {
				$checkedElements = $value['tree'];
			} else {
				$checkedElements = $value;
			}
		}
		else if ($defaultValue) {
			$uri = SJB_Navigator::getURI();
			if ($uri == '/registration/' || $uri == '/registration-social/' || strpos($uri, '/add-') !== false) {
				$checkedElements = $defaultValue;
			}
		}
		
		if ($this->property_info['display_as_select_boxes']) {
			$treeValue = $checkedElements;
		} else {
			$treeHelper = new SJB_TreeHelper('');
			$treeValue  = $treeHelper->createTreeObjects($checkedElements);
		}
		
		$newPropertyVariables =  array(
			'sid'           => $this->property_info['sid'],
			'value'         => $treeValue,
			'caption'       => $this->property_info['caption'],
			'tree_values'   => $this->tree_values,
			'display_value' => $this->display_value,
			'assoc_display' => $this->assoc_display,
			'count'         => $this->sel_count,
			'assoc_array'   => $this->makeAssociateArrayToOutput(true),
		);
		$total = 0;
		foreach ($this->tree_values as $item) {
			$total += count($item);
		}
		return array_merge($propertyVariables, $newPropertyVariables, array('countValues' => $total, 'isChoiceLimited' => !empty($this->property_info['choiceLimit'])));
	}

	function isValid()
	{
		return true;
	}
	
	function getSQLValue()
	{
		if (is_array($this->property_info['value'])) {
			$str = implode(',', $this->property_info['value']);
		} else {
			$str = (string) $this->property_info['value'];
		}
		
		return $str;
	}
	
	function getKeywordValue()
	{
		if (is_array($this->display_value)) {
			$tmp = '';
			foreach ($this->display_value as $val) {
				if (!empty($val)) {
					$parents = array();
					$items = SJB_DB::Query("SELECT * FROM `listing_field_tree` WHERE `sid` IN ({$val})");
					if (count($items) > 0) { 
						foreach ($items as $item) {
							if ($item['parent_sid'] != 0 && !isset($parents[$item['parent_sid']])) {
								$tmp .= $this->getTreeStringValueById($item['parent_sid']) . ' ';
								$parents[$item['parent_sid']] = 1;
							}
							$tmp .= $this->getTreeStringValueById($item['sid']) . ' ';
						}
					}
				}
			}
			return $tmp;
		}
		return $this->display_value;
	}

    function getKeywordValueForAutocomplete()
	{
        $keywords = array();
		if (is_array($this->display_value)) {
			foreach ($this->display_value as $val) {
				if (!empty($val)) {
					$items = SJB_DB::Query("SELECT * FROM `listing_field_tree` WHERE `sid` IN ({$val})");
					if (count($items) > 0) {
						foreach ($items as $item)
							$keywords[] = $this->getTreeStringValueById($item['sid']);
					}
				}
			}
			return $keywords;
		}
		return array($this->display_value);
	}
		
	function displayChild($id, $asArray = false)
	{
		$tmp = '';
		if ($asArray)
			$tmp = array();
		if (!empty($this->tree_values[$id]) && is_array($this->tree_values[$id])) {
			foreach ($this->tree_values[$id] as $one) {
				if (in_array($one['sid'], $this->display_value)) {
					if ($asArray) {
						if (!empty($one['caption']))
							$tmp[] = $one['caption'];
					}
					else {
						$tmp .= (!empty($tmp) ? ', ' : '') . $one['caption'];
					}
				}
				if (array_key_exists($one['sid'], $this->tree_values)) {
					if ($asArray) {
						$child = $this->displayChild($one['sid']);
						if (!empty($child))
							$tmp[] = $child;
					}
					else {
						$tmp .= (!empty($tmp) ? ', ' : '') . $this->displayChild($one['sid']);
					}
				}
			}
		}
		return $tmp;
	}
	
	function makeAssociateArrayToOutput($asArray = false)
	{
		$tmp = array();
		if (isset($this->tree_values[0])) {
			foreach ($this->tree_values[0] as $root_item) { // root item
				if (array_key_exists($root_item['sid'], $this->tree_values)) {
					$child = $this->displayChild($root_item['sid'], $asArray);
					if (!empty($child) || ($this->displayAsSelectBoxes && in_array($root_item['sid'], $this->display_value)))
						$tmp[$root_item['caption']] = $child;
				}
				elseif (in_array($root_item['sid'], $this->display_value)) {
					$child = $this->displayChild($root_item['sid'], $asArray);
					if (empty($child))
						$tmp[$root_item['caption']] = '';
				}
                if (in_array($root_item['sid'], $this->display_value) && !empty($tmp[$root_item['caption']]) && !$this->displayAsSelectBoxes) {
					unset($tmp[$root_item['caption']]);
                }
			}
		}
		return $tmp;
	}
	
	function getValue()
	{
		$tmp = ''; 
		foreach ($this->assoc_display as $key => $val) {
		 	$tmp .= "<b>{$key}</b> : {$val}<br/>";
		}
		return $tmp;
	}

	public static function getTreeStringValueById($id)
	{
		$res = SJB_DB::queryValue("SELECT `caption` FROM `listing_field_tree` WHERE `sid` = ?s", $id);
		return $res ? $res : '';
	}
	
	function getDisplayValue()
	{
		if (is_array($this->display_value))
			return implode (', ', $this->display_value);
		return $this->display_value;
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'choiceLimit',
				'caption'	=> 'Number of Values Allowed to Select',
				'type'		=> 'integer',
				'minimum'	=> '0',
				'is_system' => true,
				),
		);
	}

	public static function getDisplayAsDetail($value)
	{
		return array(
					'id' => 'display_as_select_boxes',
					'caption' => 'Display as',
					'type' => 'boolean',
					'table_name' => 'listing_fields',
					'length' => '20',
					'is_required' => false,
					'is_system' => true,
					'value' => $value,
		);
	}
	function isEmpty()
	{
		$value_is_empty = false;
	    if (is_array($this->property_info['value'])) {
	        foreach ($this->property_info['value'] as $field_value) {
	        	$field_value = trim($field_value);
	            if ($field_value == '') {
	                $value_is_empty = true;
	                break;
	            }
	        }
	    } else {
	    	$this->property_info['value'] = trim($this->property_info['value']);
	        $value_is_empty = ($this->property_info['value'] == '');
	    }
	    return $value_is_empty;
	}

	public function displayAsSelect()
	{
		return isset($this->property_info['display_as_select_boxes']) ? $this->property_info['display_as_select_boxes'] : null;
	}

	function getSQLFieldType()
	{
		return 'TEXT NULL';
	}
	
	/**
	 * @return boolean
	 */
	public function getDisplayAsSelectBoxes()
	{
		return $this->displayAsSelectBoxes;
	}
}

