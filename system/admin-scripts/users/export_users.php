<?php

class SJB_Admin_Users_ExportUsers extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('export_users');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$tp          = SJB_System::getTemplateProcessor();
		$userGroupID = SJB_Request::getVar('user_group_id', 0);
		
		$user              = SJB_UsersExportController::createUser($userGroupID);
		$searchFormBuilder = new SJB_SearchFormBuilder($user);
		$criteria          = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $user);
		$searchFormBuilder->registerTags($tp);
		$searchFormBuilder->setCriteria($criteria);
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$exportProperties = SJB_Request::getVar('export_properties', array());
			if (empty($exportProperties)) {
				SJB_FlashMessages::getInstance()->addWarning('EMPTY_EXPORT_PROPERTIES');
			} else {
				$innerJoin = false;
				if (isset($_REQUEST['product']['multi_like']) && $_REQUEST['product']['multi_like'] != '') {
					$products = $_REQUEST['product']['multi_like'];
					if (is_array($products)) {
						$products = implode(',', $products);
					}
					$whereParam = implode(',', explode(',', SJB_DB::quote($products)));
					$innerJoin = array(
						'contracts' => array(
							'join_field'   => 'user_sid',
							'join_field2'  => 'sid',
							'join'         => 'INNER JOIN',
							'where'        => "AND FIND_IN_SET(`contracts`.`product_sid`, '{$whereParam}')"
						)
					);
					unset($criteria['system']['product']);
				}
				
				$searcher      = new SJB_UserSearcher(false, 'parent_sid', 'ASC', $innerJoin);
				$searchAliases = SJB_UsersExportController::getSearchPropertyAliases();
				$foundUsersSid = $searcher->getObjectsSIDsByCriteria($criteria, $searchAliases);
				if (!empty($foundUsersSid)) {
					$result = SJB_UsersExportController::createExportDirectories();
					
					if ($result === true) {
						$exportProperties['extUserID'] = 1;
						$exportProperties['parent_sid'] = 1;
						$exportAliases = SJB_UsersExportController::getExportPropertyAliases();
						$exportData    = SJB_UsersExportController::getExportData($foundUsersSid, $exportProperties, $exportAliases);
						
						$fileName = 'users.xls';
						SJB_UsersExportController::makeExportFile($exportData, $fileName);
						
						if (!file_exists(SJB_System::getSystemSettings('EXPORT_FILES_DIRECTORY') . "/{$fileName}")) {
							SJB_FlashMessages::getInstance()->addWarning('CANT_CREATE_EXPORT_FILES');
						} else {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . "/users/archive-and-send-export-data/");
						}
					}
				} else {
					SJB_FlashMessages::getInstance()->addWarning('EMPTY_EXPORT_DATA');
				}
			}
		}
		
		$userSystemProperties = SJB_UserManager::getAllUserSystemProperties();
		$userGroups           = SJB_UserGroupManager::getAllUserGroupsInfo();
		$userCommonProperties = array();
		foreach ($userGroups as $userGroup) {
			$userGroupProperties = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($userGroup['sid']);
			$userCommonProperties[$userGroup['id']] = $userGroupProperties;
		}
		
		$tp->assign('userSystemProperties', $userSystemProperties);
		$tp->assign('userCommonProperties', $userCommonProperties);
		$tp->assign('selected_user_group_id', $userGroupID);
		$tp->display('export_users.tpl');
	}
}
