<?php

class SJB_UserManager extends SJB_ObjectManager
{
	public static function getCurrentUserInfo()
	{
		if (SJB_Authorization::isUserLoggedIn())
			return SJB_Authorization::getCurrentUserInfo();
		return null;
	}

	public static function isUserLoggedIn()
	{		
		return SJB_Authorization::isUserLoggedIn();
	}

	/**
	 * Get current user
	 *
	 * @return SJB_User|null
	 */
	public static function getCurrentUser()
	{
		$user_info = SJB_UserManager::getCurrentUserInfo();
		$user = null;
		if (!is_null($user_info)) {
			$user = new SJB_User($user_info, $user_info['user_group_sid']);
			$user->setSID($user_info['sid']);
			if (isset($user_info['subuser']))
				$user->setSubuserInfo($user_info['subuser']);
		}
		return $user;
	}

	/**
	 * Gets user object by sid
	 *
	 * @param int $user_sid
	 * @return SJB_User
	 */
	public static function getObjectBySID($user_sid)
	{
		$user_info = SJB_UserManager::getUserInfoBySID($user_sid);
		if (!is_null($user_info)) {
			$user = new SJB_User($user_info, $user_info['user_group_sid']);
			$user->setSID($user_info['sid']);
			return $user;
		}
		return null;
	}

	public static function getUserInfoBySID($user_sid)
	{
		return SJB_UserDBManager::getUserInfoBySID($user_sid);
	}

	public static function isUserActiveBySID($user_sid)
	{
		return SJB_DB::queryValue("SELECT `active` FROM `users` WHERE `sid` = ?n", $user_sid);
	}

	/**
	 * @static
	 * @param SJB_User $user
	 * @return bool
	 */
	public static function saveUser(SJB_User $user)
	{
		$newUserInDB = !$user->isSavedInDB();
		if ($newUserInDB) {
			$user->createActivationKey();
			$user->createVerificationKey();
		}

		SJB_UserDBManager::saveUser($user);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
		if ($newUserInDB) {
			SJB_Event::dispatch('onAfterUserCreated', $user);
			if (!$user->isSubuser()) {
				//add default user notifications
				$userNotifications = new SJB_UserNotificationsManager($user);
				$userNotifications->addDefaultUserNotifications();
			}
		}

		if (SJB_Authorization::isUserLoggedIn()) {
			SJB_Authorization::updateCurrentUserSession();
		}

		return true;
	}

	public static function getAllUsersInfo()
	{
		return SJB_UserDBManager::getAllUsersInfo();
	}

	public static function getUsersNumberByGroupSID($user_group_sid)
	{
		return SJB_DB::queryValue("SELECT COUNT(*) FROM ?w WHERE user_group_sid = ?n", "users", $user_group_sid);
	}

	public static function getUserSIDsByUserGroupSID($user_group_sid)
	{
		$sql_result = SJB_DB::query("SELECT `sid` FROM `users` WHERE `user_group_sid`=?n", $user_group_sid);
		return SJB_UserManager::_getUserSIDsFromRawSIDInfo($sql_result);
	}

	public static function getUserSIDsByProductSID($productSID)
	{
		$sql_result = SJB_DB::query(
			"SELECT DISTINCT users.sid  
			FROM users 
			INNER JOIN contracts ON users.sid = contracts.user_sid 
			INNER JOIN products ON products.sid = contracts.product_sid 
			WHERE products.sid=?n 
			GROUP BY users.sid", $productSID);
		
		return SJB_UserManager::_getUserSIDsFromRawSIDInfo($sql_result);
	}

	public static function _getUserSIDsFromRawSIDInfo($raw_sid_info)
	{
		$result = array();
		foreach($raw_sid_info as $found_sid_info)
			$result[] = $found_sid_info['sid'];
		return $result;
	}

	public static function deleteUserById($id)
	{
		$user = SJB_UserManager::getObjectBySID($id);
		if (empty($user)) {
			SJB_UserDBManager::deleteEmptyUsers();
			return true;
		}

		SJB_Event::dispatch('onBeforeUserDelete', $user);

        $listings = SJB_ListingDBManager::getListingsSIDByUserSID($id);
		SJB_ListingManager::deleteListingBySID($listings);

		$subusers = self::getSubusers($id);
		foreach($subusers as $subuser)
			self::deleteUserById($subuser['sid']);			

		// delete user logo file
		$pictProp = $user->getProperty('Logo');
		if ($pictProp) {
			SJB_UploadFileManager::deleteUploadedFileByID($pictProp->value);
		}

		$videoProp = $user->getProperty('video');
		if ($videoProp) {
			SJB_UploadFileManager::deleteUploadedFileByID($videoProp->value);
		}

		// delete social info
		$socialReference = SJB_SocialPlugin::getProfileSocialID($user->getSID());
		if ($socialReference) {
			SJB_SocialPlugin::deleteProfileSocialInfoByReference($socialReference);
		}
        $result = SJB_UserDBManager::deleteUserById($id) && SJB_ContractManager::deleteAllContractsByUserSID($id) && SJB_Rating::deleteRatingByUserSID($id);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
        return $result && SJB_SavedSearches::deleteUserSearchesFromDB($id);
	}

	public static function activateUserByUserName($username)
	{
		$result = SJB_UserDBManager::activateUserByUserName($username);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
		return $result;
	}
	
	public static function setApprovalStatusByUserName($username, $status, $reason = '')
	{
		if (trim($reason))
			return SJB_DB::query("UPDATE `users` SET `reason`= ?s, `approval` = ?s WHERE `username` = ?s", $reason, $status, $username);
		else
			return SJB_DB::query("UPDATE `users` SET `approval`=?s WHERE `username`=?s", $status, $username);
	}
	
	public static function deactivateUserByUserName($username)
	{
        SJB_Event::dispatch('onBeforeUserDeactivate', $username);
        SJB_UserDBManager::deactivateUserByUserName($username);
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_USERS));
	}

	public static function getUserInfoByUserName($username)
	{
		return SJB_UserDBManager::getUserInfoByUserName($username);
	}
	
	public static function getUserInfoByExtUserID($extUserID, $listingTypeID)
	{
		return SJB_UserDBManager::getUserInfoByExtUserID($extUserID, $listingTypeID);
	}

	public static function getUserInfoByUserEmail($email)
	{
		return SJB_UserDBManager::getUserInfoByUserEmail($email);
	}

	public static function login($username, $password, &$errors, $autorizeByUsername = false, $login_as_user)
	{
		$userExists = SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `username` = ?s", $username);

		if ($userExists && $autorizeByUsername)
		    return true;

		if ($userExists) {
			if (!$login_as_user)
				$userAuthorized = SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `username` = ?s AND `password` = ?s", $username, md5($password));
			else
				$userAuthorized = SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `username` = ?s AND `password` = ?s", $username, $password);

			if (!$userAuthorized) {
				$errors['INVALID_PASSWORD'] = 1;
				return false;
			}
			return true;
		}

		$errors['NO_SUCH_USER'] = 1;
		return false;
	}

	public static function getCurrentUserSID()
	{
		$user_info = SJB_UserManager::getCurrentUserInfo();
		if (!is_null($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserNameByUserSID($user_sid)
	{
		return SJB_UserDBManager::getUserNameByUserSID($user_sid);
	}
	
	public static function getExtUserIDByUserSID($user_sid)
	{
		return SJB_UserDBManager::getExtUserIDByUserSID($user_sid);
	}

	public static function getUserSIDbyUsername($username)
	{
		$user_info = SJB_UserManager::getUserInfoByUserName($username);
		if (!empty($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserSIDbyEmail($email)
	{
		$user_info = SJB_UserManager::getUserInfoByUserEmail($email);
		if (!empty($user_info))
			return $user_info['sid'];
		return null;
	}

	public static function getUserSIDsLikeUsername($username)
	{
		return SJB_UserDBManager::getUserSIDsLikeUsername($username);
	}

	public static function getUserSIDsLikeCompanyName($username)
	{
		return SJB_UserDBManager::getUserSIDsLikeCompanyName($username);
	}

	public static function getUserSIDsLikeFirstNameOrLastName($name)
	{
		return SJB_UserDBManager::getUserSidsLikeFirstNameOrLastName($name);
	}

	public static function getUserPassword($username)
	{
		return SJB_DB::queryValue("SELECT `password` FROM `users` WHERE `username` = ?s", $username);
	}

	public static function changeUserPassword($user_sid, $password)
	{
		return SJB_DB::query("UPDATE `users` SET `password` = ?s WHERE `sid` = ?s", md5($password), $user_sid);
	}

	public static function saveUserSessionKey($session_key, $user_sid)
	{
		SJB_DB::query("INSERT INTO user_sessions SET session_key = ?s, user_sid = ?n, remote_ip = ?s, user_agent = ?s, start = UNIX_TIMESTAMP()", $session_key, $user_sid, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
	}

	public static function removeUserSessionKey($session_key)
	{
		SJB_DB::query("DELETE FROM user_sessions WHERE session_key = ?s", $session_key);
	}

	public static function getUserSIDBySessionKey($session_key)
	{
		return SJB_DB::queryValue("SELECT user_sid FROM user_sessions WHERE session_key = ?s", $session_key);
	}

	public static function getUserGroupByUserSid($userSid)
	{
		return SJB_DB::queryValue("SELECT `user_group_sid` FROM `users` WHERE sid = ?n", $userSid);
	}

	/**
	 * 
	 * @param SJB_User $user
	 */
    public static function createTemplateStructureForUser($user)
	{
		if (!$user)
			return array();
		$structure = $user->getUserInfo();
		if (SJB_MemoryCache::has('userGroupInfo' . $user->getUserGroupSID())) {
			$user_group_info = SJB_MemoryCache::get('userGroupInfo' . $user->getUserGroupSID());
		}
		else {
			$user_group_info = SJB_UserGroupManager::getUserGroupInfoBySID($user->getUserGroupSID());
			SJB_MemoryCache::set('userGroupInfo' . $user->getUserGroupSID(), $user_group_info);
		}
		foreach ($user->getProperties() as $property) {
			$value = $property->getValue();
			if ($property->getType() == 'list') {
				$listValues = isset($property->type->property_info['list_values']) ? $property->type->property_info['list_values'] : array();
				foreach ($listValues as $listValue) {
					if ($listValue['id'] == $value) 
						$structure[$property->getID()] = $listValue['caption'];
				}
			}
			elseif ($property->getType() == 'location') {
				foreach($property->type->fields as $locationField) {
					if (isset($structure[$property->getID()]) && array_key_exists($locationField['id'], $structure[$property->getID()])) {
						if ($locationField['id'] == 'State') {
							$displayAs = !empty($locationField['display_as'])?$locationField['display_as']:'state_name';
							$listValues = SJB_StatesManager::getStateNamesBySid($property->value['State'], $displayAs);
						}
						else {
							$listValues = isset($locationField['list_values']) ? $locationField['list_values'] : array();
						}
		
						foreach ($listValues as $listValue) {
							if ($listValue['id'] == $value[$locationField['id']]) {
								$structure[$property->getID()][$locationField['id']] = $listValue['caption'];
								$structure[$property->getID()][$locationField['id'].'_Code'] = $listValue['Code'];
								$structure[$property->getID()][$locationField['id'].'_Name'] = $listValue['Name'];
							}
						}
					}
				}
			}
			else {
				$structure[$property->getID()] = $value;
			}
		}

		$structure['id'] = $user->getID();
		$structure['isJobg8'] = strpos($structure['username'], 'jobg8_') !== false;
		$structure['group'] = array('id' 		=> $user_group_info['id'],
									'caption'	=> $user_group_info['name']);


		$subuserInfo = $user->getSubuserInfo();
		if (!empty($subuserInfo)) {
			$structure['subuser'] = $subuserInfo;
		}

		$structure['METADATA'] = array(
			'group' => array(
				'caption' => array('type' => 'string', 'propertyID' => 'caption'),
			),
			'registration_date' => array('type' => 'date'),
		);
		$structure['METADATA'] = array_merge($structure['METADATA'], parent::getObjectMetaData($user));
		return $structure;
	}

    public static function createTemplateStructureForCurrentUser()
	{
		return SJB_UserManager::createTemplateStructureForUser(SJB_UserManager::getCurrentUser());
	}

	/**
	 * gets all groups info
	 *
	 * @return array
	 */
	public static function getGroupsInfo()
	{
		$res = array();
		//TODO: можно ускорить и сделать так же как в листингах
		$periods = array(
			"Today" => "`u`.`registration_date` >= CURDATE()",
			"This Week" => "`u`.`registration_date` >= FROM_DAYS(TO_DAYS(CURDATE()) - WEEKDAY(CURDATE()))",
			"This Month" => "`u`.`registration_date` >= FROM_DAYS(TO_DAYS(CURDATE()) - DAYOFMONTH(CURDATE()) + 1)");

		$user_groups_structure = SJB_UserGroupManager::createTemplateStructureForUserGroups();

		foreach ($user_groups_structure as $userGroup) {
			foreach ($periods as $key => $value) {
				$queryResult = SJB_DB::query("
					select	ifnull(count(u.user_group_sid), 0) as `count`,
							ifnull(sum(u.active), 0) as `active`
					from users u
					where $value and u.user_group_sid = {$userGroup['sid']}");
				$res[$userGroup["id"]]["periods"][$key] = array_shift($queryResult);
			}
			$queryResult = SJB_DB::query("
				select ifnull(count(u.user_group_sid), 0) as `count`, ifnull(sum(u.active), 0) as `active`
				from users u
				where u.user_group_sid = {$userGroup['sid']}");
			$res[$userGroup["id"]]["total"] = array_shift($queryResult);
			$res[$userGroup["id"]]["caption"] = $userGroup["caption"];
			$res[$userGroup["id"]]["approveInfo"] = self::getUsersApproveInfo($userGroup["sid"]);
		}
		return $res;
	}
	
	public static function getUsersApproveInfo($userGroupSID = false) 
	{
		if ($userGroupSID != false) {
			$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userGroupSID);
			if (empty($userGroupInfo['approve_user_by_admin']))
				return false;
			$res = SJB_DB::query("
				SELECT count(*) as `count`, `approval`, `user_group_sid` 
				FROM `users` 
				WHERE `user_group_sid` = ?n 
				GROUP BY `approval`", $userGroupSID);
			
			$statusInfo = array();
			foreach ($res as $arr)
				$statusInfo[$arr['approval']] = $arr['count'];

			$statusInfo['user_group_sid'] = $userGroupSID;
			$statusInfo['user_group_id'] = SJB_UserGroupManager::getUserGroupIDBySID($userGroupSID);
			return $statusInfo;
		}
		
		$res = SJB_DB::query("
				SELECT count(*) as `count`, `user_group_sid`, `approval` 
				FROM `users` 
				GROUP BY `user_group_sid`, `approval`");
		
		$approve = array();
		foreach ($res as $arr)
			$approve[$arr['user_group_sid']][$arr['approval']] = $arr['count'];
		return $approve;
	}

	/**
	 * gets all users info
	 *
	 * @return array
	 */
	public static function getUsersInfo()
	{
		$usersInfo = SJB_DB::query("select ifnull(count(*), 0) as `count`, ifnull(sum(users.active), 0) as `active` from users");
		return array_shift($usersInfo);
	}

	public static function getOnlineUsers()
	{
		$maxLifeTime = ini_get("session.gc_maxlifetime");
		$currentTime = time();

		// здесь получаем число используемых онлайн аккаунтов
		$result = SJB_DB::query("
			SELECT `u`.`sid`, `ug`.`id` as `type`, `ses`.*
			FROM `users` `u`, `user_groups` `ug`, `user_session_data_storage` `ses`
			WHERE `ug`.`sid` = `u`.`user_group_sid`
				AND `u`.`sid` = `ses`.`user_sid`
				AND unix_timestamp(`ses`.`last_activity`) + {$maxLifeTime} > {$currentTime}
				GROUP BY `u`.`sid`");
		
		return $result;
	}

	/**
	 * @param  int $numberOfProfiles
	 * @return array
	 */
	public static function getFeaturedProfiles($numberOfProfiles)
	{
		$logosInfo = SJB_UserProfileFieldManager::getFieldsInfoByType('logo');
		$logoFields = array();
		foreach ($logosInfo as $logoInfo) {
			if (!empty($logoInfo['id'])) {
				$logoFields[] = " `{$logoInfo['id']}` != '' ";
			}
		}
		
		$whereLogo = empty($logos) ? '' : 'AND (' . implode(' OR ', $logoFields) . ')';
		$usersInfo = SJB_DB::query("SELECT `sid` FROM `users` WHERE `featured`=1 AND `active`=1 {$whereLogo} ORDER BY RAND() LIMIT 0, ?n", $numberOfProfiles);
		
		$users = array();
		foreach ($usersInfo as $userInfo) {
			$user    = SJB_UserManager::getObjectBySID($userInfo['sid']);
			$users[] = !empty($user) ? SJB_UserManager::createTemplateStructureForUser($user) : null;
		}
		
		return $users;
	}

	public static function checkBan(&$errors, $bySavedUserIP = false)
	{
		$banIPs = SJB_IPManager::getAllBannedIPs();
		$userIP = $_SERVER['REMOTE_ADDR'];
		if ($bySavedUserIP) 
				$userIP = $bySavedUserIP;

		foreach ($banIPs as $banIP) {
			$ip = $banIP['value'];
			if (preg_match('#^' . str_replace('\*', '.*?', preg_quote($ip, '#')) . '$#i', $userIP)){
				$errors['BANNED_USER'] = 1;
				return true;
			}
		}
		return false;
	}
	
	public static function getUserNameByCompanyName($companyName) 
	{
		$userName = SJB_DB::queryValue("SELECT `username` FROM `users` WHERE `companyName`=?s", $companyName);
		return $userName ? $userName : false;
	}

	public static function getSubusers($userId)
	{
		return SJB_DB::query('SELECT `u`.* FROM `users` `u` WHERE `parent_sid` = ?n', $userId);
	}

	/**
	 * define displayName ("Message to" field ) for Private Messages
	 * @param string|integer $to
	 * @param string $displayName
	 * @return string or null
	 */
	public static function getComposeDisplayName($to, &$displayName)
	{
		if (empty($to))
			return null;

		// by user's id
		$oReceiverInfo = SJB_UserManager::getUserInfoBySID((int)$to);

		// by username
		if (is_null($oReceiverInfo))
			$oReceiverInfo = SJB_UserManager::getUserInfoByUserName($to);

		// Message to:  отображать там если есть то CompanyName
		// если нет, то FirstName LastName
		// если нет и того ни другого, то можно написать username
		if (!empty ($oReceiverInfo['CompanyName']))
			$displayName = $oReceiverInfo['CompanyName'];
		elseif (!empty ($oReceiverInfo['FirstName']))
			$displayName = $oReceiverInfo['FirstName'] . ((!empty ($oReceiverInfo['LastName'])) ? ' ' . $oReceiverInfo['LastName'] : '');
		elseif (!empty ($oReceiverInfo['username']))
			$displayName = $oReceiverInfo['username'];
	}

	public static function getAllUserSystemProperties()
	{	
		$system_properties = array('id','username','password', 'email', 'product', 'user_group','active','language','featured','ip','registration_date', 'pictures');
		return array('system' => $system_properties);
	}
	
    public static function makeFeaturedBySID($user_sid)
	{
       return SJB_DB::query("UPDATE users SET featured = 1 WHERE sid = ?n", $user_sid);
	}
	
	public static function removeFromFeaturedBySID($user_sid)
	{
		return SJB_DB::query("UPDATE users SET featured = 0 WHERE sid = ?n", $user_sid);
	}

	public static function saveRecentlyViewedListings($user_sid, $listing_sid)
	{
		$viewed_listings = SJB_DB::query("
			SELECT *
			FROM `recently_viewed_listings`
			WHERE `user_sid` = ?n
			ORDER BY `order`",
			$user_sid
		);
		$count = count($viewed_listings);
		$insert = true;

		if ($count) {
			if ($count == 10) {
				SJB_DB::query("
					DELETE FROM `recently_viewed_listings`
					WHERE `user_sid` = ?n AND `order` = 10",
					$user_sid
				);
			}

			$limit = 10;
			foreach ($viewed_listings as $viewed_listing) {
				if ($viewed_listing['listing_sid'] == $listing_sid) {
					$order = 1;
					$limit = $viewed_listing['order'];
					$insert = false;
				} else {
					if ($viewed_listing['order'] > $limit)
						continue;
					$order = $viewed_listing['order'] + 1;
				}
				SJB_DB::query("
					UPDATE `recently_viewed_listings`
					SET `order` = ?n
					WHERE `sid` = ?n",
					$order,  $viewed_listing['sid']
				);
			}
		}

		if ($insert) {
			SJB_DB::query("
				INSERT INTO `recently_viewed_listings`
				SET `user_sid` = ?n, `listing_sid` = ?n, `order` = 1",
				$user_sid, $listing_sid
			);
		}
	}

	public static function getRecentlyViewedListingsByUserSid($user_sid, $limit)
	{
		return SJB_DB::query("
			SELECT *
			FROM `recently_viewed_listings`
			WHERE `user_sid` = ?n
			ORDER BY `order`
			LIMIT 0, ?n",
			$user_sid, $limit
		);
	}

	public static function getUserIdByKeywords($keywords)
	{
		return SJB_DB::queryValue("SELECT `sid` FROM `users` WHERE `FirstName` = ?s OR `companyName` = ?s LIMIT 1", $keywords, $keywords);
	}

	/**
	 * @param int $userSID
	 * @param string $originalMd5Password
	 * @return array|bool|int
	 */
	public static function saveUserPassword($userSID, $originalMd5Password)
	{
		return SJB_DB::query('UPDATE `users` SET `password` = ?s WHERE `sid` = ?n', $originalMd5Password, $userSID);
	}

	/**
	 * Checks if current user registered in 1 hour period ago
	 * @static
	 * @return bool
	 */
	public static function isCurrentUserJustRegistered()
	{
		$userInfo = SJB_UserManager::getCurrentUserInfo();
		if(empty($userInfo)) {
			return false;
		}
		$userRegistrationTime = !empty($userInfo['registration_date']) ? $userInfo['registration_date'] : '';
		if($userRegistrationTime) {
			$userRegistrationTime = new DateTime($userRegistrationTime);
			$currentTime = new DateTime(date('Y-m-d H:i:s'));
			$userRegistrationTime->format('Y-m-d H:i:s');
			$currentTime->format('Y-m-d H:i:s');
			$interval = $userRegistrationTime->diff($currentTime);
			$interval = $interval->format('%h');

			if($interval > 0) {
				return false;
			} else{
				return true;
			}
		}
		return false;
	}
	
	public static function issetFieldByName($fieldName)
	{
		return SJB_DB::query("SHOW COLUMNS FROM `users` WHERE `Field` = ?s", $fieldName);
	}

	public static function isUserExistsByUserSid($userSid)
	{
		return SJB_DB::queryValue("SELECT count(*) FROM `users` WHERE `sid` = ?n LIMIT 1", $userSid);
	}

	public static function replaceCompanyNameWithSIDs($companyNames) 
	{
		if (empty($companyNames)) {
			return 0;
		}
		$foundSids = SJB_DB::query("SELECT `sid` FROM `users` WHERE `CompanyName` IN (?l)", $companyNames);
		$companySids = array();
		foreach ($foundSids as $sid) {
			$companySids[] = $sid['sid'];
		}
		return $companySids;
	}

}
