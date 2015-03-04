<?php

class SJB_Users_EmployersList extends SJB_Function
{
	public function execute()
	{
		$access_type = SJB_Request::getVar('access_type');
		$listing_id = SJB_Request::getVar('listing_id');
		$user_group_id = SJB_Request::getVar('user_group_id');
		$employersGroupSID = SJB_UserGroupManager::getUserGroupSIDByID($user_group_id);
		$employersSIDs = SJB_UserManager::getUserSIDsByUserGroupSID($employersGroupSID);

		$employers = array();
		foreach ($employersSIDs as $emp) {
			$currEmp = SJB_UserManager::getUserInfoBySID($emp);
			if (isset($currEmp['CompanyName']) && $currEmp['CompanyName'] != '')
				$employers[] = array('name' => $currEmp['CompanyName'], 'sid' => $emp);
		}
		sort($employers);

		$tp = SJB_System::getTemplateProcessor();
		$listing_access_list = SJB_ListingManager::getListingAccessList($listing_id, $access_type);
		$tp->assign('listing_access_list', $listing_access_list);
		$tp->assign('employers', $employers);
		$tp->display('employers_list.tpl');
	}
}