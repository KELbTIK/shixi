<?php

class SJB_Menu_UserMenu extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$user_menu_template = 'user_menu.tpl';
		if (SJB_UserManager::isUserLoggedIn()) {
			$user_info = SJB_Authorization::getCurrentUserInfo();
			if (!empty($user_info)) {
				$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_info['user_group_sid']);
				if (!empty($user_group_info['user_menu_template']) && $tp->templateExists($user_group_info['user_menu_template'])) {
					$user_menu_template = $user_group_info['user_menu_template'];
				}
				$tp->assign("user_group_info", $user_group_info);
				$tp->assign('listingTypesInfo', SJB_ListingTypeManager::getAllListingTypesInfo());
			}
		}

		$tp->assign("account_activated", SJB_Request::getVar('account_activated', ''));
		$tp->display($user_menu_template);
	}
}

