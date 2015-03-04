<?php

class SJB_ScreeningQuestionnairesListController extends SJB_EditListController
{
	function SJB_ScreeningQuestionnairesListController($input_data)
	{
		parent::SJB_EditListController($input_data, new SJB_ScreeningQuestionnairesFieldManager, new SJB_ScreeningQuestionnairesListItemManager);
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
			$list_item_value = $this->input_data['list_multiItem_value'];
			$result = true;
			foreach ($list_item_value as $key => $list_it) {
				$list_it = trim($list_it);
				if ( $list_it != "") {
			        $list_item = new SJB_ListItem();
					$list_item->setFieldSID($this->field_sid);
					$list_item->setValue( $list_it );
					$list_item->score = $this->input_data['score'][$key];
					if (!$this->ListItemManager->SaveListItem($list_item) && $result)
						$result = false;
				}
			}
			
			return $result;
		}
	}

}