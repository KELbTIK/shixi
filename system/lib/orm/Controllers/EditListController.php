<?php


class SJB_EditListController extends SJB_ListController
{
	var $input_data;

	function SJB_EditListController($input_data, $FieldManager, $ListItemManager)
	{
		$this->input_data = $input_data;
		parent::SJB_ListController($this->input_data, $FieldManager, $ListItemManager);
	}

	function saveItem($multiple = false)
	{
		if (!$multiple) {
			$list_item = new SJB_ListItem();
			$list_item->setFieldSID($this->field_sid);
			$list_item_value = $this->input_data['list_item_value'];
			$list_item->setValue( trim($list_item_value) );
			return $this->ListItemManager->SaveListItem($list_item);
		}
		else {
			$list_item_value = str_replace("\r", '', $this->input_data['list_multiItem_value']);
			$list_item_value = explode("\n", $list_item_value);
			$result = true;
			foreach ($list_item_value as $list_it) {
				$list_it = trim($list_it);
				if ( $list_it != "") {
			        $list_item = new SJB_ListItem();
					$list_item->setFieldSID($this->field_sid);
					$list_item->setValue( trim($list_it) );
					if (!$this->ListItemManager->SaveListItem($list_item) && $result)
						$result = false;
				}
			}
			return $result;
		}
	}

	function deleteItem($item_sid = false)
	{
		if (!$item_sid) {
    		$item_sid = isset($this->input_data['item_sid']) ? $this->input_data['item_sid'] : null;
		}
		return $this->ListItemManager->deleteListItemBySID($item_sid);
	}

	function moveUpItem()
	{
		$item_sid = isset($this->input_data['item_sid']) ? $this->input_data['item_sid'] : null;
		return $this->ListItemManager->moveUpItem($item_sid);
	}
	
	function moveDownItem()
	{
    	$item_sid = isset($this->input_data['item_sid']) ? $this->input_data['item_sid'] : null;
		return $this->ListItemManager->moveDownItem($item_sid);
	}
	
	function sortItems($field_sid, $sorting_order = 'ASC')
	{
		return $this->ListItemManager->sortItems($field_sid, $sorting_order);
	}

	function isValidValueSubmitted()
	{
		return (isset($this->input_data['list_item_value']) && $this->input_data['list_item_value'] != '');
	}
	
	function isValidMultiValueSubmitted()
	{
		return (isset($this->input_data['list_multiItem_value']) && $this->input_data['list_multiItem_value'] != '');
	}

	function getAction()
	{
		return isset($this->input_data['action']) ? $this->input_data['action'] : null;
	}
	
	function saveNewItemsOrder($items_order)
	{
		return $this->ListItemManager->saveNewItemsOrder($items_order);
	}
}
