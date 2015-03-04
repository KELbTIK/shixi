<?php

class SJB_Admin_Users_Acl extends SJB_Function
{
	public function isAccessible()
	{
		switch (SJB_Request::getVar('type')) {
			case 'user':
				$userSid = SJB_Request::getVar('role', null);
				$userGroupID = SJB_UserGroupManager::getUserGroupIDByUserSID($userSid);
				SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupID);
				$this->setPermissionLabel('manage_' . strtolower($userGroupID));
				break;
			case 'group':
				$this->setPermissionLabel('manage_user_groups_permissions');
				break;
			case 'product':
				$this->setPermissionLabel('manage_products');
				break;
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$acl = SJB_Acl::getInstance();
		$type = SJB_Request::getVar('type', '');
		$role = SJB_Request::getVar('role', '');
		$tp = SJB_System::getTemplateProcessor();
		$resources = $acl->getResources();
		$form_submitted = SJB_Request::getVar('action');

		if ($form_submitted) {
			SJB_Acl::clearPermissions($type, $role);
			foreach ($resources as $name => $resource) {
				$params = SJB_Request::getVar($name . '_params');
				$message = '';
			    if (SJB_Request::getVar($name) == 'deny') {
		            $params = SJB_Request::getVar($name . '_params1');
		            if ($params == 'message')
						$message = SJB_Request::getVar($name . '_message');
		        }
				SJB_Acl::allow($name, $type, $role, SJB_Request::getVar($name, ''), $params, SJB_Request::getVar($name . '_message'));
			}

			if ($type == 'plan' && SJB_Request::getVar('update_users', 0) == 1) {
				$contracts =  SJB_ContractManager::getAllContractsByMemebershipPlanSID($role);
				foreach ($contracts as $contract_id) {
					SJB_Acl::clearPermissions('contract', $contract_id['id']);
					SJB_DB::query("insert into `permissions` (`type`, `role`, `name`, `value`, `params`, `message`)"
							. " select 'contract', ?s, `name`, `value`, `params`, `message` from `permissions` "
							. " where `type` = 'plan' and `role` = ?s", $contract_id['id'], $role);
				}
			}

			if ($form_submitted == 'save') {
				switch ($type) {
					case 'group' :
						$parameter = "/edit-user-group/?sid=" . $role;
						break;
					case 'guest' :
						$parameter = "/user-groups/";
						break;
				}

				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . $parameter);
			}
		}

		$acl = SJB_Acl::getInstance(true);
		$resources = $acl->getResources($type);
		$perms = SJB_DB::query('select * from `permissions` where `type` = ?s and `role` = ?s', $type, $role);
		foreach ($resources as $key => $resource) {
			$resources[$key]['value'] = 'inherit';
			$resources[$key]['name'] = $key;
			foreach ($perms as $perm) {
				if ($key == $perm['name']) {
					$resources[$key]['value'] = $perm['value'];
					$resources[$key]['params'] = $perm['params'];
					$resources[$key]['message'] = $perm['message'];
					break;
				}
			}
		}

		$tp->assign('resources', $resources);
		$tp->assign('type', $type);
		$tp->assign('listingTypes', SJB_ListingTypeManager::getAllListingTypesInfo());
		$tp->assign('role', $role);

		switch ($type) {
			case 'group':
				$tp->assign('userGroupInfo', SJB_UserGroupManager::getUserGroupInfoBySID($role));
				break;
			case 'user':
				$userInfo = SJB_UserManager::getUserInfoBySID($role);
				$tp->assign('userGroupInfo', SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']));
				break;
		}

		$tp->display('acl.tpl');
	}
}