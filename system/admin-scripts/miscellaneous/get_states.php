<?php

class SJB_Admin_Miscellaneous_GetStates extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$countrySID = SJB_Request::getVar('country_sid', false);
		$stateSID = SJB_Request::getVar('state_sid', false);
		$parentID = SJB_Request::getVar('parentID', false);
		$type = SJB_Request::getVar('type', false);
		$displayAs = SJB_Request::getVar('display_as', 'state_name');
		$displayAs = $displayAs?$displayAs:'state_name';
		$result = array();
		if ($countrySID) {
			$result = SJB_StatesManager::getStatesNamesByCountry($countrySID, false, $displayAs);
		}
		$tp->assign("caption", 'State');
		$tp->assign("value", $stateSID);
		$tp->assign("list_values", $result);
		$tp->assign("parentID", $parentID);
		if (!empty($countrySID)) {
			$tp->assign("enabled", true);
		}
		if ($type) {
			$tp->assign("requestType", $type);
		}
		if ($type == 'search') {
			$tp->assign("id", $parentID.'_State');
			$tp->display("../field_types/search/list.tpl");
		}
		elseif ($type == 'modifyZipCode' or $type == 'zipCodeSearch') {
			$tp->display("../miscellaneous/zipcode_database_states_list.tpl");
		} else {
			$tp->assign("id", 'State');
			$tp->display("../field_types/input/list.tpl");
		}
	}
}
