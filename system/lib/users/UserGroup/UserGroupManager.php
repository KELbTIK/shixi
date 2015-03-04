<?php

class SJB_UserGroupManager
{
	public static function getAllUserGroups()
	{
		$user_groups_info = SJB_UserGroupDBManager::getAllUserGroupsInfo();
		$user_groups = array();
		foreach ($user_groups_info as $user_group_info) {
			$user_group = new SJB_UserGroup($user_group_info);
			$user_group->setSID($user_group_info['sid']);
			$user_groups[] = $user_group;
		}
		return $user_groups;
	}
	
	public static function getAllUserGroupsInfo()
	{
		return SJB_UserGroupDBManager::getAllUserGroupsInfo();
	}
	
	public static function deleteUserGroupBySID($user_group_sid)
	{
		$user_profile_fields_info = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group_sid);
		foreach ($user_profile_fields_info as $user_profile_field_info) {
			SJB_UserProfileFieldManager::deleteUserProfileFieldBySID($user_profile_field_info['sid']);
		}
		SJB_DB::query('DELETE FROM `products` WHERE `user_group_sid` = ?n', $user_group_sid);
		return SJB_UserGroupDBManager::deleteUserGroupInfo($user_group_sid);
	}
	
	public static function getUserGroupInfoBySID($user_group_sid)
	{
		$cacheId = md5('SJB_UserGroupManager::getUserGroupInfoBySID' . $user_group_sid);
		$cache = SJB_Cache::getInstance();
		if ($cache->test($cacheId)) {
			return $cache->load($cacheId);
		}
		$groupInfo = SJB_UserGroupDBManager::getUserGroupInfoBySID($user_group_sid);
		$cache->save($groupInfo, $cacheId, array(SJB_Cache::TAG_FIELDS, SJB_Cache::TAG_USERS));
		return $groupInfo;
	}
	
	public static function saveUserGroup($user_group)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS, SJB_Cache::TAG_USERS));
		SJB_UserGroupDBManager::saveUserGroup($user_group);
	}
	
	public static function getUserGroupSIDByID($user_group_id)
	{
		return SJB_UserGroupDBManager::getUserGroupSIDByID($user_group_id);
	}
	
	public static function getUserGroupIDBySID($user_group_sid)
	{
		return SJB_UserGroupDBManager::getUserGroupIDBySID($user_group_sid);
	}

	public static function getUserGroupIDByUserSID($userSid)
	{
		return SJB_UserGroupDBManager::getUserGroupIDByUserSID($userSid);
	}

	public static function getUserGroupIDByUserName($userSid)
	{
		return SJB_UserGroupDBManager::getUserGroupIDByUserName($userSid);
	}

	public static function getUserGroupNameBySID($user_group_sid)
	{
		return SJB_UserGroupDBManager::getUserGroupNameBySID($user_group_sid);
	}

	public static function getUserGroupSIDByName($user_group_name)
	{
		return SJB_UserGroupDBManager::getUserGroupSIDByName($user_group_name);
	}

	public static function isSendActivationEmail($user_group_sid)
	{
		$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
		if (!empty($user_group_info)) {
			return $user_group_info['send_activation_email'];
		}
		return null;
	}
	
	public static function isApproveByAdmin($user_group_sid)
	{
		$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
		if (!empty($user_group_info)) {
			return !empty($user_group_info['approve_user_by_admin']);
		}
		return null;
	}
	
	public static function isUserEmailAsUsernameInUserGroup($user_group_sid)
	{
		if (SJB_MemoryCache::has('userGroupInfo' . $user_group_sid)) {
			$user_group_info = SJB_MemoryCache::get('userGroupInfo' . $user_group_sid);
		}
		else {
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
			SJB_MemoryCache::set('userGroupInfo' . $user_group_sid, $user_group_info);
		}
		return (!empty($user_group_info) && $user_group_info['user_email_as_username'] == 1);
	}
	
	public static function getAllUserGroupsIDsAndCaptions()
	{
		$cacheId = 'UserGroupManager::getAllUserGroupsIDsAndCaptions';
		if (SJB_MemoryCache::has($cacheId))
			return SJB_MemoryCache::get($cacheId);
		$user_groups_info = SJB_UserGroupManager::getAllUserGroupsInfo();
		$user_groups_ids_and_captions = array();
		foreach ($user_groups_info as $user_group_info) {
			$user_groups_ids_and_captions[] = array('id' 		=> $user_group_info['sid'],
													'key' 		=> $user_group_info['id'],
												    'caption'	=> $user_group_info['name']);
		}
		SJB_MemoryCache::set($cacheId, $user_groups_ids_and_captions);
		return $user_groups_ids_and_captions;
	}

    public static function createTemplateStructureForUserGroups()
	{
		$user_groups_info = SJB_UserGroupManager::getAllUserGroupsInfo();
		$structure = array();
		foreach ($user_groups_info as $user_group_info) {
			$structure[$user_group_info['id']] = array (
				'sid'				=> $user_group_info['sid'],
				'id'				=> $user_group_info['id'],
				'caption'			=> $user_group_info['name'],
				'user_number'		=> SJB_UserManager::getUsersNumberByGroupSID($user_group_info['sid']),
				'reg_form_template'	=> $user_group_info['reg_form_template'],
			);
		}

		return $structure;
	}
	
	public static function setDefaultProduct($groupSID, $productSID)
	{
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_FIELDS, SJB_Cache::TAG_USERS));
		SJB_DB::query('UPDATE `user_groups`	SET `default_product` = ?n WHERE `sid` = ?n', $productSID, $groupSID);
		return true;
	}
	
	/**
	 * @param integer $groupSID
	 * @return integer|false
	 */
	public static function getDefaultProduct($groupSID)
	{
		$defaultProduct = SJB_DB::queryValue('SELECT `default_product` from `user_groups` WHERE `sid` = ?n', $groupSID);
		if (empty($defaultProduct))
			return false;
		return (is_numeric($defaultProduct) && $defaultProduct != 0) ? intval($defaultProduct) : false;
	}

	public static function getEmailTemplateSIDByUserGroupAndField($userGroupSID, $field)
	{
		$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSID);
		return SJB_Array::get($userGroupInfo, $field);
	}

	public static function getRedirectUrlByPageID($pageId)
	{
		$error = '';
		if (!is_null(SJB_Session::getValue('fromAnonymousShoppingCart'))) {
			SJB_Session::unsetValue('fromAnonymousShoppingCart');
			return SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/?';
		}
		$redirectUrl = SJB_System::getSystemSettings('SITE_URL') . '/my-account/?';
		if(empty($pageId)) {
			return $redirectUrl;
		}
		if($pageId == 'posting_page') {
			$user           = SJB_UserManager::getCurrentUser();
			$userGroupId    = SJB_UserGroupManager::getUserGroupIDBySID($user->getUserGroupSID());
			$listingTypeSid = SJB_ListingTypeManager::getListingTypeByUserSID($user->getSID());
			$listingTypeId  = !empty($listingTypeSid) ? SJB_ListingTypeManager::getListingTypeIDBySID(array_pop($listingTypeSid)) : '';

			if($user->hasContract() && SJB_ListingManager::canCurrentUserAddListing($error, $listingTypeId)) {
				$redirectUrl = SJB_System::getSystemSettings('SITE_URL') . '/add-listing/?listing_type_id=' . $listingTypeId . "&";
			} elseif($user->hasContract()) {
				$redirectUrl = SJB_System::getSystemSettings('SITE_URL') . '/my-account/?';
			} else {
				$redirectUrl = SJB_System::getSystemSettings('SITE_URL') . '/' . mb_strtolower($userGroupId) . '-products/?postingProductsOnly=1&';
			}
		}
		return $redirectUrl;
	}
}
