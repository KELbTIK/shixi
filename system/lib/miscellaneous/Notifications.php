<?php

class SJB_Notifications
{
	const SEND_SUBUSER_REG_LTR_SID 					= 39;
	const SEND_BANNER_REJECTED_LTR_SID 				= 43;
	const SEND_USER_PASS_CHANGE_LTR_SID 			= 36;
	const SEND_APPLY_NOW_SID 						= 33;
	const SEND_TELL_FRND_LTR_SID 					= 40;
	const USER_AUTO_REPLY_SID 						= 41;
	const SEND_USER_SOCIAL_REG_LETTER_SID 			= 6;
	const SEND_SUBSCRIPTION_REACTIVATION_LTR_SID 	= 45;
	const SEND_SUBSCRIPTION_ACTIVATION_LTR_SID 		= 24;
	const SEND_USER_NEW_LISTINGS_FND_LTR_SID 		= 27;
	const GUEST_ALERT_CONFIRMATION_EMAIL_SID		= 200;
	const GUEST_ALERT_WELCOME_EMAIL_SID				= 201;
	const SEND_INVOICE_SID   					    = 300;

	/**
	 * @param SJB_User $user
	 * @param array $request
	 * @param array $permissions
	 */
	public static function sendSubuserRegistrationLetter(SJB_User $user, $request, $permissions)
	{
		$email = $request['email'];
		if (is_array($email)) {
			$email = array_pop($email);
		}

		$user = SJB_UserManager::createTemplateStructureForUser($user);

		$data = array(
			'request' => $request,
			'user' => $user,
			'permissions' => $permissions
		);

		$email = SJB_EmailTemplateEditor::getEmail($email, self::SEND_SUBUSER_REG_LTR_SID, $data);
		return $email->send('Subuser Registration');
	}

	/**
	 * @static
	 * @param SJB_SubAdminProp $user
	 * @param $request
	 * @param array $permissions
	 * @return mixed
	 */
	public static function sendSubAdminRegistrationLetter(SJB_SubAdminProp $subAdmin, $request, $permissions)
	{
		$emailTplSID =  SJB_Settings::getSettingByName('notify_admin_on_subadmin_registration');

		$email = $request['email'];
		if (is_array($email)) {
			$email = array_pop($email);
		}

		$user['username'] = $subAdmin->getPropertyValue('username');
		$user['email'] = $email;
		$user['password'] = SJB_Array::getPath($request, 'password/original');

		$data = array(
			'request' => $request,
			'subadmin' => $user,
			'permissions' => $permissions,
			'admin_email' => SJB_Settings::getSettingByName('notification_email'),
		);

		$email = SJB_EmailTemplateEditor::getEmail($email, $emailTplSID, $data);
		return $email->send('SubAdmin Registration');
	}

	public static function sendUserActivationLetter($user_sid, $returnToShoppingCart = false)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID = $user->getUserGroupSID();
		$emailTplSID =  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'user_activation_email');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$user_infoEx = SJB_UserManager::getUserInfoBySID($user_sid);
		$user_info['activation_key'] = SJB_Array::get($user_infoEx, 'activation_key');
		$user_info['returnToShoppingCart'] = $returnToShoppingCart;
		$data = array('user' => $user_info);

		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);
		return $email->send('User Activation');
	}

	public static function sendUserApprovedLetter($user_sid)
	{
		$user 			= SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	= SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'user_approval_email');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$data = array('user' => $user_info);

		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);
		return $email->send('User Approved');
	}

	public static function sendUserRejectedLetter($user_sid)
	{
		$user 			= SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'user_rejected_email');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$data = array('user' => $user_info);
		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);

		return $email->send('User Rejected');
	}

	/**
	 * @param $bannerInfo
	 * @param $user_sid
	 * @param $reject
	 * @return bool
	 */
	public static function sendBannerRejectedLetter($bannerInfo, $user_sid, $reject)
	{
		$admin_email = SJB_Settings::getSettingByName('notification_email');
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$user_info = SJB_UserManager::createTemplateStructureForUser($user);

		if (!empty($bannerInfo['contract_sid']) && $user_info) {
			$contract = SJB_ContractManager::getInfo($bannerInfo['contract_sid']);
			$productInfo  = SJB_ProductsManager::getProductInfoBySID($contract['product_sid']);
			$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productInfo['sid']);
			$productInfo = array_merge($productInfo, $productExtraInfo);
			$product = SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo);

			$data = array(
				'user' => $user_info,
				'banner' => $bannerInfo,
				'reason' => $reject,
				'admin_email' => $admin_email,
				'product' => $product
			);

			$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], self::SEND_BANNER_REJECTED_LTR_SID, $data);

			return $email->send('Banner Rejected');
		}

		return null;
	}

	public static function sendUserPasswordChangeLetter($user_sid)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$data = array('user' => $user_info);
		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], self::SEND_USER_PASS_CHANGE_LTR_SID, $data);
		return $email->send('User Password Change');
	}

	/**
	 * @static
	 * @param $user_sid
	 * @param array $listing_info
	 * @return null
	 */
	public static function sendUserListingExpiredLetter($listing_info)
	{
		$userGroupSID 	= SJB_Array::getPath($listing_info, 'user/user_group_sid');
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'notify_on_listing_expiration');
		$user_info 		= SJB_Array::get($listing_info, 'user');
		$data 			= array('user' => $user_info, 'listing' => $listing_info);
		$email 			= SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);

		return $email->send('User Listing Expired');
	}

	public static function sendUserContractExpiredLetter($userInfo, $contractInfo, $productInfo)
	{
		$user 			= SJB_UserManager::getObjectBySID($userInfo['sid']);
		if (!$user)
			return false;

		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'notify_on_contract_expiration');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$productInfo = array_merge($productInfo, SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo));
		$data = array(
			'user' => $user_info,
			'product' => $productInfo,
			'contract' => $contractInfo
		);
		$email = SJB_EmailTemplateEditor::getEmail($userInfo['email'], $emailTplSID, $data);
		return $email->send('User Contract Expired');
	}

	/**
	 * @param SJB_Listing $listing
	 * @param $user_sid
	 * @return mixed
	 */
	public static function sendUserListingActivatedLetter(SJB_Listing $listing, $user_sid)
	{
		$user 			= SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	= SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID,'notify_on_listing_activation');

		$user_info = SJB_UserManager::createTemplateStructureForUser($user);
		$listing_info = SJB_ListingManager::createTemplateStructureForListing($listing);
		$data = array(
			'listing' => $listing_info,
			'user' => $user_info
		);
		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);
		return $email->send('User Listing Activated');
	}

	public static function sendUserListingApproveOrRejectLetter($listing_sid, $user_sid, $mode = 'approve')
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID = $user->getUserGroupSID();
		switch ($mode) {
			case 'reject':
				$emailTplSID = SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_on_listing_reject');
				break;
			case 'approve':
			default:
				$emailTplSID = SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_on_listing_approve');
				break;
		}

		$user_info 		= SJB_UserManager::createTemplateStructureForUser($user);
		$listing 		= SJB_ListingManager::getObjectBySID($listing_sid);
		$listing_info	= SJB_ListingManager::createTemplateStructureForListing($listing);
		$listingTypeId	= SJB_ListingTypeManager::getListingTypeIDBySID($listing_info['type']['id']);
		$data = array(
			'user' => $user_info,
			'listing' => $listing_info,
			'listingTypeId' => $listingTypeId,
		);
		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);
		return $email->send('User Listing Approve Or Reject');
	}

	public static function sendUserNewListingsFoundLetter($listingsSIDs, $user_sid, $saved_search_info, $listingTypeSID)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$user_info = SJB_UserManager::createTemplateStructureForUser($user);

		$listings = array();
		foreach ($listingsSIDs as $listingSID) {
			$listing = SJB_ListingManager::getObjectBySID($listingSID);
			$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
			array_push($listings, $listing);
		}
		$data 			= array('listings' => $listings, 'user' => $user_info, 'saved_search' => $saved_search_info);
		$emailTplSID 	= self::SEND_USER_NEW_LISTINGS_FND_LTR_SID;

		if ($listingTypeSID) {
			$emailTplSID =  SJB_ListingTypeManager::getListingTypeEmailTemplate($listingTypeSID);
		}

		$email = SJB_EmailTemplateEditor::getEmail($user_info['email'], $emailTplSID, $data);

		return $email->send('User New Listings Found');
	}

	public static function sendApplyNow($info, $file = '', $data_resume = array(), $current_user_sid = false, $notRegisterUserData = false, $score = false)
	{
		if ($current_user_sid) {
			$user_info = SJB_UserManager::getUserInfoBySID($current_user_sid);
			$sender_email_address = $user_info['email'];
		} else {
			$sender_email_address = $notRegisterUserData['email'];
		}

		$application_email = SJB_Applications::getApplicationEmailbyListingId($info['listing']['id']);
		$email_address = !empty($application_email) ? $application_email : $info['listing']['user']['email'];
		$questionnaire = !empty($info['submitted_data']['questionnaire'])?unserialize($info['submitted_data']['questionnaire']):'';
		$questionnaireInfo = array();
		if ($questionnaire) {
			$listingInfo = SJB_ListingManager::getListingInfoBySID($info['listing']['id']);
			$questSID = isset($listingInfo['screening_questionnaire'])?$listingInfo['screening_questionnaire']:0;
			$questionnaireInfo = SJB_ScreeningQuestionnaires::getInfoBySID($questSID);
			$passing_score = 0;
			switch ($questionnaireInfo['passing_score']) {
				case 'acceptable':
					$passing_score = 1;
					break;
				case 'good':
					$passing_score = 2;
					break;
				case 'very_good':
					$passing_score = 3;
					break;
				case 'excellent':
					$passing_score = 4;
					break;
			}
			if ($score >= $passing_score) {
				$questionnaireInfo['passing_score'] = 'Passed';
			}
			else {
				$questionnaireInfo['passing_score'] = 'Not passed';
			}
		}

		if (!empty($info['listing']['subuser']['sid'])) {
			$subUserInfo = SJB_UserManager::getUserInfoBySID($info['listing']['subuser']['sid']);
			if (!empty($subUserInfo)) {
				$email_address = $subUserInfo['email'];
			}
		}
		$data = array(
			'user'					=> SJB_Array::getPath($info, 'listing/user'),
			'listing' 				=> $info['listing'],
			'applicant_request' 	=> $info['submitted_data'],
			'data_resume' 			=> $data_resume,
			'questionnaire' 		=> $questionnaire,
			'score' 				=> $score,
			'questionnaire_info' 	=> $questionnaireInfo);

		$email = SJB_EmailTemplateEditor::getEmail($email_address, self::SEND_APPLY_NOW_SID, $data);

		$email->setReplyTo($sender_email_address);
		if ($file != '') {
			$email->setFile($file);
		}
		return $email->send('Apply Now');
	}

	public static function sendTellFriendLetter($info)
	{
		$email_address = $info['submitted_data']['friend_email'];

		$data = array(
			'listing' => $info['listing'],
			'submitted_data' => $info['submitted_data']
		);

		$email = SJB_EmailTemplateEditor::getEmail( $email_address, self::SEND_TELL_FRND_LTR_SID, $data);
		return $email->send('Tell a Friend');
	}

	public static function sendNewPrivateMessageLetter($user_id, $sender_id, $message, $cc = false)
	{
		$user 			= SJB_UserManager::getObjectBySID($user_id);
		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_on_private_message');
		$userInfo = SJB_UserManager::createTemplateStructureForUser($user);
		$sender = SJB_UserManager::getObjectBySID($sender_id);
		$sender = SJB_UserManager::createTemplateStructureForUser($sender);

		$data = array(
			'recipient' => $userInfo,
			'sender' => $sender,
			'message' => $message
		);

		$email = SJB_EmailTemplateEditor::getEmail( $userInfo['email'], $emailTplSID, $data);
		if (!empty($cc)) {
			$cc = SJB_UserManager::getUserInfoBySID($cc);
			if (!empty($cc)) {
				$email->addCC($cc['email']);
			}
		}
		return $email->send('Send private message');
	}

	public static function sendSubscriptionActivationLetter($userSID, $productInfo, $reactivation = false)
	{
		if ($reactivation) {
			$emailTplSID = self::SEND_SUBSCRIPTION_REACTIVATION_LTR_SID;
		}
		else {
			$emailTplSID = SJB_Array::get($productInfo, 'welcome_email');
			if (!$emailTplSID) {
				$emailTplSID = self::SEND_SUBSCRIPTION_ACTIVATION_LTR_SID;
			}
		}

		$user = SJB_UserManager::getObjectBySID($userSID);
		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productInfo['sid']);
		$productInfo = array_merge($productInfo, $productExtraInfo);
		$fields = SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo);
		$product = array_merge($fields, $productExtraInfo);
		$data = array(
			'user' => $user,
			'product' => $product,
			'reactivation' => $reactivation
		);

		$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
		return $email->send('Subscription Activation');
	}

	public static function sendRemindSubscriptionExpirationLetter($userSID, $contractInfo, $days)
	{
		$user 			= SJB_UserManager::getObjectBySID($userSID);
		$userGroupSID 	= $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_subscription_expire_date');
		$product		= array();
		$productInfo 	= SJB_ProductsManager::getProductInfoBySID(SJB_Array::get($contractInfo, 'product_sid'));

		if ($productInfo) {
			$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productInfo['sid']);
			$productInfo = array_merge($productInfo, $productExtraInfo);
			$fields = SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo);
			$product = array_merge($fields, $productExtraInfo);
		}

		$user_info	= SJB_UserManager::createTemplateStructureForUser($user);
		$data 		= array(
			'type' 			=> 'contract',
			'user'			=> $user_info,
			'days' 			=> $days,
			'contractInfo' 	=> $contractInfo,
			'product' 		=> $product,
		);
		$email = SJB_EmailTemplateEditor::getEmail( $user_info['email'], $emailTplSID, $data);
		return $email->send('Remind Subscription Expiration');
	}

	public static function sendRemindListingExpirationLetter($userSID, $listingSID, $days)
	{
		$user = SJB_UserManager::getObjectBySID($userSID);
		$userGroupSID = $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_listing_expire_date');

		$user_info   = SJB_UserManager::createTemplateStructureForUser($user);
		$listing = SJB_ListingManager::getObjectBySID($listingSID);
		$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
		$data = array(
			'type' => 'listing',
			'user' => $user_info,
			'listing' => $listing,
			'days' => $days
		);
		$email = SJB_EmailTemplateEditor::getEmail( $user_info['email'], $emailTplSID, $data);
		return $email->send('Remind Listing Expiration');
	}

	public static function userAutoReply($listing_info, $user_sid, $questionnaire, $notRegisteredUserData = array())
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$user = SJB_UserManager::createTemplateStructureForUser($user);

		if (empty($user)) {
			$user = $notRegisteredUserData;
		}

		$listing = SJB_ListingManager::getObjectBySID($listing_info['sid']);
		$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
		$data = array(
			'user' => $user,
			'listing' => $listing,
			'text' => $questionnaire
		);
		$email = SJB_EmailTemplateEditor::getEmail( $user['email'], self::USER_AUTO_REPLY_SID, $data);
		return $email->send('Auto Reply');
	}

	public static function sendUserWelcomeLetter($user_sid)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$userGroupSID = $user->getUserGroupSID();
		$emailTplSID 	=  SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'welcome_email');

		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$data = array('user' => $user);
		$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
		return $email->send('Welcome email');
	}

	public static function sendUserApplicationApproveOrRejectLetter($application_id, $mode = 'approved')
	{
		$application_info = SJB_Applications::getBySID($application_id);
		$user = SJB_UserManager::getObjectBySID($application_info['jobseeker_id']);
		$userGroupSID = $user->getUserGroupSID();

		switch ($mode) {
			case 'rejected':
				$emailTemplate = 'notify_on_application_reject';
				break;
			case 'approved':
			default:
				$emailTemplate = 'notify_on_application_approve';
				break;
		}
		$emailTplSID = SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, $emailTemplate);
		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$listing = SJB_ListingManager::getObjectBySID($application_info['listing_id']);
		$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
		$data = array(
			'user' => $user,
			'listing' => $listing,
		);
		$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
		return $email->send('User Application ' . $mode);
	}

	public static function notifyOnUserListingDeactivated($listingId)
	{
		$listing = SJB_ListingManager::getObjectBySID($listingId);
		$userSID = $listing->getUserSID();

		if (SJB_UserNotificationsManager::isUserNotifiedOnListingDeactivation($userSID)) {
			$listing 		= SJB_ListingManager::createTemplateStructureForListing($listing);
			$user 			= SJB_UserManager::getObjectBySID($userSID);
			$userGroupSID 	= $user->getUserGroupSID();
			$emailTplSID 	= SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_user_on_listing_deactivation');

			$user = SJB_UserManager::createTemplateStructureForUser($user);
			$data = array(
				'user'  => $user,
				'listing' => $listing,
			);

			$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
			return $email->send('User Listing Deactivated');
		}
		return null;
	}

	public static function notifyOnUserListingDeleted($listingId)
	{
		$listing = SJB_ListingManager::getObjectBySID($listingId);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		$userSID = $listing->getUserSID();
		if (SJB_UserNotificationsManager::isUserNotifiedOnListingDeletion($userSID) && (!isset($listingInfo['preview']) || $listingInfo['preview'] != 1)) {
			$listing 		= SJB_ListingManager::createTemplateStructureForListing($listing);
			$user 			= SJB_Array::get($listing, 'user');
			$userGroupSID 	= SJB_Array::get($user, 'user_group_sid');
			$emailTplSID 	= SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_user_on_listing_deletion');

			$data = array(
				'user'  => $user,
				'listing' => $listing,
			);

			$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
			return $email->send('User Listing Deleted');
		}
		return null;
	}

	/**
	 * @param SJB_User $user
	 * @return boolean
	 */
	public static function notifyOnUserDeleted(SJB_User $user)
	{
		if (SJB_UserNotificationsManager::isUserNotifiedOnProfileDeletion($user->getSID())) {
			$userGroupSID = $user->getUserGroupSID();
			$emailTplSID = SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_user_on_deletion');

			$user = SJB_UserManager::createTemplateStructureForUser($user);
			$data = array(
				'user'  => $user,
			);
			$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
			return $email->send('User Deleted');
		}
		return false;
	}

	public static function notifyOnUserDeactivated($username)
	{
		$userSID = SJB_UserManager::getUserSIDbyUsername($username);

		if (SJB_UserNotificationsManager::isUserNotifiedOnProfileDeactivation($userSID)) {
			$user 			= SJB_UserManager::getObjectBySID($userSID);
			$userGroupSID 	= $user->getUserGroupSID();
			$emailTplSID 	= SJB_UserGroupManager::getEmailTemplateSIDByUserGroupAndField($userGroupSID, 'notify_user_on_deactivation');

			$user = SJB_UserManager::createTemplateStructureForUser($user);
			$data = array(
				'user'  => $user,
			);

			$email = SJB_EmailTemplateEditor::getEmail($user['email'], $emailTplSID, $data);
			return $email->send('User Deactivated');
		}
		return null;
	}

	public static function sendUserSocialRegistrationLetter(SJB_User $user, $network)
	{
		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$userEmail = SJB_Array::get($user, 'email');
		if (is_array($userEmail)) {
			$userEmail = array_pop($userEmail);
			$user['email'] = $userEmail;
		}
		$data = array(
			'user' => $user,
			'network' => $network,
		);
		$email = SJB_EmailTemplateEditor::getEmail($userEmail, self::SEND_USER_SOCIAL_REG_LETTER_SID, $data);
		return $email->send('Social Registration');
	}


	public static function sendInvoiceToCustomer($invoice_sid, $user_sid)
	{
		$invoice_info = SJB_InvoiceManager::getInvoiceInfoBySID($invoice_sid);
		$invoice_structure = SJB_InvoiceManager::createInvoiceTemplate($invoice_info);
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$userInfo = SJB_UserManager::createTemplateStructureForUser($user);
		$parentSID = SJB_Array::get($userInfo, 'parent_sid');
		if ($parentSID > 0) {
			$user = SJB_UserManager::getObjectBySID($parentSID);
			$userInfo = SJB_UserManager::createTemplateStructureForUser($user);
		}
		$data = array('user' => $userInfo, 'invoice' => $invoice_structure);
		$email = SJB_EmailTemplateEditor::getEmail($userInfo['email'], self::SEND_INVOICE_SID, $data);
		return $email->send('Send Invoice to Customer');
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 * @return mixed
	 */
	public static function sendConfirmationEmailForGuest(SJB_GuestAlert $guestAlert)
	{
		$data = array('key' => $guestAlert->getVerificationKeyForEmail());
		$email = SJB_EmailTemplateEditor::getEmail($guestAlert->getAlertEmail(), self::GUEST_ALERT_CONFIRMATION_EMAIL_SID, $data);
		return $email->send('Guest Alert Confirmation');
	}

	/**
	 * @param SJB_GuestAlert $guestAlert
	 * @return mixed
	 */
	public static function sendGuestAlertWelcomeEmail(SJB_GuestAlert $guestAlert)
	{
		$data = array('key' => $guestAlert->getVerificationKeyForEmail());
		$email = SJB_EmailTemplateEditor::getEmail($guestAlert->getAlertEmail(), self::GUEST_ALERT_WELCOME_EMAIL_SID, $data);
		return $email->send('Guest Alert Welcome');
	}

	/**
	 * @param array $listingsSIDs
	 * @param array $guestAlertInfo
	 * @param int $listingTypeSID
	 * @return array|bool|null
	 */
	public static function sendGuestAlertNewListingsFoundLetter(array $listingsSIDs, array $guestAlertInfo, $listingTypeSID)
	{
		$emailTplSID = SJB_ListingTypeManager::getListingTypeEmailTemplateForGuestAlert($listingTypeSID);

		$listings = array();
		foreach ($listingsSIDs as $listingSID) {
			$listing = SJB_ListingManager::getObjectBySID($listingSID);
			if ($listing instanceof SJB_Listing) {
				$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
				array_push($listings, $listing);
			}
		}

		try {
			$guestAlert = SJB_GuestAlertManager::getObjectBySID($guestAlertInfo['sid']);
		}
		catch (Exception $e) {}

		$data = array('listings' => $listings, 'key' => $guestAlert->getVerificationKeyForEmail());
		$email = SJB_EmailTemplateEditor::getEmail($guestAlertInfo['email'], $emailTplSID, $data);
		return $email->send('Guest Alert New Listings Found');
	}
}