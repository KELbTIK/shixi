<?php

class SJB_Admin_Users_EditUserGroup extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_user_groups');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user_group_sid = SJB_Request::getVar('sid', null);
		$errors = array();

		if (!is_null($user_group_sid)) {
			$action = SJB_Request::getVar("action", false);
			$product_sid = SJB_Request::getVar("product_sid", false);
			if ($action && $product_sid !== false) {
				switch ($action) {
					case 'move_up':
						SJB_ProductsManager::moveUpProductBySID($product_sid, $user_group_sid);
						break;
					case 'move_down':
						SJB_ProductsManager::moveDownProductBySID($product_sid, $user_group_sid);
						break;
					case 'set_default_product':
						SJB_UserGroupManager::setDefaultProduct($user_group_sid, $product_sid);
						break;
				}
			}
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
			$user_group_info = array_merge($user_group_info, $_REQUEST);
			$userGroup = new SJB_UserGroup($user_group_info);
			$userGroup->setSID($user_group_sid);
			$edit_user_group_form = new SJB_Form($userGroup);
			$form_is_submitted = SJB_Request::getVar('submit');

			if ($form_is_submitted && $edit_user_group_form->isDataValid($errors)) {
				SJB_UserGroupManager::saveUserGroup($userGroup);

				if ($form_is_submitted == 'save_info') {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/user-groups/');
				}
			}

			$productSIDs = SJB_ProductsManager::getProductsInfoByUserGroupSID($user_group_sid);
			$productsInfo = array();
			$user_sids_in_group = SJB_UserManager::getUserSIDsByUserGroupSID($user_group_sid);
			$user_group_product_user_number = array();
			foreach ($productSIDs as $product) {
				$productsInfo[] = $product;
				$user_sids_in_product = SJB_UserManager::getUserSIDsByProductSID($product['sid']);
				$user_number = count(array_intersect($user_sids_in_group, $user_sids_in_product));
				$user_group_product_user_number[$product['sid']] = $user_number;
			}
			$edit_user_group_form->registerTags($tp);
			$tp->assign('object_sid', $userGroup->getSID());
			$tp->assign('notifications', $userGroup->getNotifications());
			$tp->assign('notificationGroups', $userGroup->getNotificationsGroups());
			$tp->assign('user_group_sid', $user_group_sid);
			$tp->assign('user_group_products_info', $productsInfo);
			$tp->assign('user_group_product_user_number', $user_group_product_user_number);
			$tp->assign('form_fields', $edit_user_group_form->getFormFieldsInfo());
		}
		else {
			$errors['USER_GROUP_SID_NOT_SET'] = 1;
		}

		$tp->assign('user_group_info', isset($user_group_info) ? $user_group_info : null);
		$tp->assign('errors', $errors);
		$tp->assign('object_sid', $user_group_sid);
		$tp->display('edit_user_group.tpl');

	}
}
