<?php

class SJB_Miscellaneous_GetUserStates extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$countrySID = SJB_Request::getVar('country_sid', false);
		$stateSID = SJB_Request::getVar('state_sid', false);
		$parentID = SJB_Request::getVar('parentID', false);
		$caption = SJB_Request::getVar('caption', 'State');
		$type = SJB_Request::getVar('type', false);
		$displayAs = SJB_Request::getVar('display_as', 'state_name');
		$displayAs = ($displayAs == 'state_name' || $displayAs == 'state_code') ? $displayAs : 'state_name';
		$result = array();
		if ($countrySID)
			$result = SJB_StatesManager::getStatesNamesByCountry($countrySID, true, $displayAs);
		$tp->assign("caption", $caption);
		$tp->assign("value", $stateSID);
		$tp->assign("list_values", $result);
		$tp->assign("parentID", $parentID);
		if (!empty($countrySID)) {
			$tp->assign("enabled", true);
		}
		if ($type == 'search') {
			$tp->assign("id", $parentID.'_State');
			$tp->display("../field_types/search/list.tpl");
		}
		else {
			$tp->assign("id", 'State');
			$tp->display("../field_types/input/list.tpl");
		}
	}
}
