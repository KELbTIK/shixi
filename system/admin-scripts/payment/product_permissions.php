<?php

class SJB_Admin_Payment_ProductPermissions extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_products');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$userGroupSid = SJB_Request::getVar('user_group_sid', false);
		$productType = SJB_Request::getVar('product_type', false);
		$permissions_type = SJB_Request::getVar('permissions_type', 'additional');
		$addedPermissions = false;
		$role = SJB_Request::getVar('role', false);
		$type = 'product';
		$acl = SJB_Acl::getInstance(true);
		$resources = $acl->getResources($type);

		$product = new SJB_Product(array(), $productType);
		$additionalPermissions = $product->getAdditionalPermissions();
		$accessPermissions = $product->getAccessPermissions();

		$perms = SJB_DB::query('select * from `permissions` where `type` = ?s and `role` = ?s', $type, $role);
		$countGeneralPermissions = 0;
		foreach ($resources as $key => $resource) {
			switch ($permissions_type) {
				case 'additional':
					if (!in_array($key, $additionalPermissions)) {
						unset($resources[$key]);
						continue;
					}
					break;
				case 'access':
					if (!in_array($key, $accessPermissions)) {
						unset($resources[$key]);
						continue;
					}
					break;
			}
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
			if (!empty($addedPermissions[$key])) {
				$params = isset($addedPermissions[$key . '_params'])?$addedPermissions[$key . '_params']:'';
	    		$params1 = isset($addedPermissions[$key . '_params1'])?$addedPermissions[$key . '_params1']:'';
	    		$message = isset($addedPermissions[$key . '_message'])?$addedPermissions[$key . '_message']:'';
				if ($addedPermissions[$key] == 'deny' && $params1) 
					$params = $params1;
				
				$resources[$key]['value'] = $addedPermissions[$key];
				$resources[$key]['params'] = $params;
				$resources[$key]['message'] = $message;
			}
			if (isset($resources[$key]['group']) && $resources[$key]['group'] == 'general')
				$countGeneralPermissions++;
		}

		$tp->assign('countGeneralPermissions', $countGeneralPermissions);
		$tp->assign('resources', $resources);
		$tp->assign('listingTypes', SJB_ListingTypeManager::getAllListingTypesInfo());
		$tp->assign('role', $role);
		$tp->assign('type', $type);
		$tp->assign('user_group_sid', $userGroupSid);
		$tp->display('product_permissions.tpl');
	}
}