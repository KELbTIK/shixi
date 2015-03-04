<?php

class SJB_Admin_Menu_ShowLeftMenu extends SJB_Function
{
	private $pageID;
	private $handledHighlightGroups = array();

	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$this->pageID = SJB_PageManager::getPageParentURI(SJB_Navigator::getURI(), SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'), false);
		if (empty($this->pageID) || $this->pageID == '/') {
			$this->pageID = $GLOBALS['uri'];
		}

		if (SJB_SubAdmin::getSubAdminSID()) {
			$tp->assign('left_admin_menu', $this->mark_active_itemsPermissionWith($GLOBALS['LEFT_ADMIN_MENU'], SJB_SubAdminAcl::getInstance(), SJB_SubAdmin::getSubAdminSID()));
			$tp->assign('subadmin', SJB_SubAdmin::getSubAdminInfo());
		}
		else {
			$tp->assign('left_admin_menu', $this->mark_active_items($GLOBALS['LEFT_ADMIN_MENU']));
		}

		$tp->display('admin_left_menu.tpl');
	}

	private function mark_active_items($arr)
	{
		foreach ($arr as $key => $items) {
			$arr[$key]['active'] = false;
			foreach ($items as $item_key => $item) {
				$arr[$key][$item_key]['active'] = false;
				$item['highlight'][] = $item['reference'];

				$this->removeSimilarHighlight($arr, $item, $key, $item_key);
			}
			$arr[$key]['id'] = str_replace(' ', '_', $key);
		}
		return $arr;
	}

	private function mark_active_itemsPermissionWith(&$arr, SJB_SubAdminAcl $acl, $subAdminSID)
	{
		if (empty ($arr))
			return array();

		foreach ($arr as $key => $items) {
			$arr[$key]['active'] = false;

			foreach ($items as $item_key => $item) {
				$allowed = false;

				if (is_array($item['perm_label'])) {
					foreach ($item['perm_label'] as $permLabel) {
						if ($acl->isAllowed($permLabel, $subAdminSID, 'subadmin')) {
							$allowed = true;
							break;
						}
					}
				}
				else {
					// check permission for subadmins
					if ($acl->isAllowed($item['perm_label'], $subAdminSID, 'subadmin')) {
						$allowed = true;
					}
				}

				if (!$allowed) {
					// remove menu from menu list
					unset($arr[$key][$item_key]);
					continue;
				}

				$arr[$key][$item_key]['active'] = false;
				$item['highlight'][] = $item['reference'];

				$this->removeSimilarHighlight($arr, $item, $key, $item_key);
			}
			$arr[$key]['id'] = str_replace(' ', '_', $key);

			if (empty($arr[$key]) || count($arr[$key]) == 2) {
				unset($arr[$key]);
			}
		}
		return $arr;
	}

	/**
	 * @param array $menu
	 * @param $item
	 * @param $menuKey
	 * @param $itemKey
	 */
	private function removeSimilarHighlight(array &$menu, $item, $menuKey, $itemKey)
	{
		if (in_array(SJB_System::getSystemSettings('SITE_URL') . $this->pageID, $item['highlight'])) {
			$highlight = '';
			$highlightPrefix = '/manage-';
			$userSid = SJB_Request::getVar('user_sid', 0);
			switch ($menuKey) {
				case 'Listing Configuration':
					$fieldSID = SJB_Request::getVar('field_sid', false);
					if ($fieldSID) {
						$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($fieldSID);
						//remove unwanted highlights
						if (($fieldInfo['listing_type_sid'] == 0 && $item['title'] == 'Listing Types') || ($fieldInfo['listing_type_sid'] != 0 && $item['title'] == 'Common Fields')) {
							$highlight = $this->pageID;
						}
					}
					break;
				case 'Listing Management':
					if (preg_match('/manage-resume|job|[a-zA-Z0-9]+-listings/', $item['reference']) && !preg_match('/import|export|flagged-listings/', $item['reference'])) {
						if (!$listingTypeId = SJB_Request::getVar('listing_type_id', null)) {
							$listingId = SJB_Request::getVar('listing_id', null);
							$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
							$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
						}

						$highlight = strtolower($listingTypeId);
					}
					break;

				case 'Users':
					$highlightPrefix = '/manage-users/';
					if ($userSid) {
						$userGroupSid = SJB_UserManager::getUserGroupByUserSid($userSid);
						$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSid);
						if (preg_match($highlightPrefix, $item['reference'])) {
							$highlight = strtolower($userGroupInfo['id']);
						}
					}
					else if ($role = SJB_Request::getVar('role', null)) {
						$type = SJB_Request::getVar('type', null);
						// for user permissions
						if ($type == 'user') {
							$role = SJB_UserManager::getUserGroupByUserSid($role);
							$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($role);
							$highlight = strtolower($userGroupInfo['id']);
						}
						//for user group permissions
						else if (in_array($type, array('group', 'guest'))) {
							$highlightPrefix = 'user-';
							$highlight = 'group';
						}
					} else {
						//retrieve user group from uri
						if (preg_match('|./(\w*)/?|u', urldecode(SJB_Navigator::getURI()), $userGroupHub)) {
							$highlight = array_pop($userGroupHub);
						}
						//remove unwanted highlights
						if ($this->pageID == '/email-log/') {
							$highlight = $this->pageID;
						}
					}
					break;

				case 'System Configuration':
					//remove unwanted highlights
					if ($this->pageID == '/email-log/' && $userSid) {
						$highlight = $this->pageID;
					}
					break;
				default:
			}

			if (!$highlight || strpos($item['reference'], $highlightPrefix . $highlight) !== false) {
				if (!isset($this->handledHighlightGroups[$highlightPrefix])) {
					$this->handledHighlightGroups[$highlightPrefix] = true;

					$handledHighlightGroups[$highlightPrefix] = true;
					$menu[$menuKey][$itemKey]['active'] = true;
					$menu[$menuKey]['active'] = true;
				}
			}
		}
	}
}
