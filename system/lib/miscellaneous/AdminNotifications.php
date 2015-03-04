<?php

class SJB_AdminNotifications
{
	const EMAIL_TEMPLATE_SID_SND_CONTACT_FORM_MSG = 30;
	const EMAIL_TEMPLATE_SID_SND_ADMIN_BANNER_ADDED_LTR = 25;

	public static function isSubAdminNotifiedOnListingAdded()
	{
		return SJB_SubAdminManager::getIfSubAdminsNotifiedOn('get_notifications_on_listing_added');
	}

	public static function isSubAdminNotifiedOnDeletingUserProfile($groupId)
	{
		return SJB_SubAdminManager::getIfSubAdminsNotifiedOn('get_notifications_on_deleting_' . strtolower($groupId) . '_profile');
	}

	/**
	 * @static
	 * @param SJB_Listing $listing
	 * @return bool|null
	 */
	public static function sendAdminListingAddedLetter(SJB_Listing $listing)
	{
		$emailTplSID =  SJB_Settings::getSettingByName('notify_on_listing_added');
		if ($emailTplSID) {
			$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
			$user = SJB_Array::get($listing, 'user');

			$params = array(
				'listing' => $listing,
				'user'	=> $user,
			);

			$admin_email = SJB_Settings::getSettingByName('notification_email');
			$email = SJB_EmailTemplateEditor::getEmail($admin_email, $emailTplSID , $params);
			if ($email) {
				// notify subadmins
				$subAdminsToNotify = self::isSubAdminNotifiedOnListingAdded();
				if (is_array($subAdminsToNotify)) {
					foreach ($subAdminsToNotify as $subAdminEmail) {
						$email->addCC($subAdminEmail);
					}
				}
				return $email->send();
			}

		}
		return null;
	}

	/**
	 * @param int $userSID
	 * @param string $reason
	 * @return bool|null
	 */
	public static function sendAdminDeletingUserProfile(SJB_User $user, $reason = '')
	{
		$emailTplSID =  SJB_Settings::getSettingByName('notify_admin_on_deleting_user_profile');
		if ($emailTplSID) {
			$user = SJB_UserManager::createTemplateStructureForUser($user);
			$params = array(
				'user'	=> $user,
				'reason' => $reason,
			);
			$admin_email = SJB_Settings::getSettingByName('notification_email');
			$email = SJB_EmailTemplateEditor::getEmail($admin_email, $emailTplSID , $params);

			if ($email) {
				$subAdminsToNotify = self::isSubAdminNotifiedOnDeletingUserProfile($user['group']['id']);

				if (is_array($subAdminsToNotify)) {
					foreach ($subAdminsToNotify as $subAdminEmail) {
						$email->addCC($subAdminEmail);
					}
				}

				return $email->send();
			}
		}
		return null;
	}
	
	public static function isSubAdminNotifiedOnUserRegistration($groupId)
	{
		return SJB_SubAdminManager::getIfSubAdminsNotifiedOn('get_notifications_on_' . strtolower($groupId) . '_registration');
	}

	/**
	 * @static
	 * @param SJB_User $user
	 * @return bool|null
	 */
	public static function sendAdminUserRegistrationLetter(SJB_User $user)
	{
		$emailTplSID =  SJB_Settings::getSettingByName('notify_on_user_registration');
		if ($emailTplSID) {
			$user = SJB_UserManager::createTemplateStructureForUser($user);
			$params = array(
				'user'	=> $user,
			);
			$admin_email = SJB_Settings::getSettingByName('notification_email');
			$email = SJB_EmailTemplateEditor::getEmail($admin_email, $emailTplSID , $params);

			if ($email) {
				// notify subadmins
				$subAdminsToNotify = self::isSubAdminNotifiedOnUserRegistration($user['group']['id']);
				if (is_array($subAdminsToNotify)) {
					foreach ($subAdminsToNotify as $subAdminEmail) {
						$email->addCC($subAdminEmail);
					}
				}

				return $email->send();
			}
		}
		return null;
	}
	
	public static function isSubAdminNotifiedOnListingExpiration()
	{
		return SJB_SubAdminManager::getIfSubAdminsNotifiedOn('get_notifications_on_listing_expiration');
	}
	
	public static function isSubAdminNotifiedOnListingFlagged()
	{
		return SJB_SubAdminManager::getIfSubAdminsNotifiedOn('get_notifications_on_listing_flagged');
	}

	/**
	 * @static
	 * @param array $listing
	 * @return null
	 */
	public static function sendAdminListingExpiredLetter($listing)
	{
		$emailTplSID =  SJB_Settings::getSettingByName('notify_on_listing_expiration');
		if ($emailTplSID) {
			$user = SJB_Array::get($listing, 'user');
			$params = array(
				'listing' => $listing,
				'user'	=> $user,
			);
			$admin_email = SJB_Settings::getSettingByName('notification_email');
			$email = SJB_EmailTemplateEditor::getEmail($admin_email, $emailTplSID , $params);

			if ($email) {
				// notify subadmins
				$subAdminsToNotify = self::isSubAdminNotifiedOnListingExpiration();
				if (is_array($subAdminsToNotify)) {
					foreach ($subAdminsToNotify as $subAdminEmail) {
						$email->addCC($subAdminEmail);
					}
				}

				return $email->send();
			}
		}
		return null;
	}
	
	public static function isSubAdminNotifiedOnUserContractExpiration($groupId)
	{
		return SJB_SubAdminManager::getIfSubAdminsNotifiedOn('get_notifications_on_' . strtolower($groupId) . '_subscription_expiration');
	}
	
	public static function sendAdminUserContractExpiredLetter($userSID, $contractInfo, $productInfo)
	{
		$emailTplSID =  SJB_Settings::getSettingByName('notify_on_user_contract_expiration');
		if ($emailTplSID) {
			$user = SJB_UserManager::getObjectBySID($userSID);
			$user = SJB_UserManager::createTemplateStructureForUser($user);
			$productInfo = array_merge($productInfo, SJB_ProductsManager::createTemplateStructureForProductForEmailTpl($productInfo));
			$params = array(
				'user' => $user,
				'contract' => $contractInfo,
				'product' => $productInfo,
			);
			$admin_email = SJB_Settings::getSettingByName('notification_email');
			$email = SJB_EmailTemplateEditor::getEmail($admin_email, $emailTplSID , $params);

			if ($email) {
				// notify subadmins
				$subAdminsToNotify = self::isSubAdminNotifiedOnUserContractExpiration($user['group']['id']);
				if (is_array($subAdminsToNotify)) {
					foreach ($subAdminsToNotify as $subAdminEmail) {
						$email->addCC($subAdminEmail);
					}
				}
				return $email->send();
			}
		}
		return null;
	}
	
	public static function sendContactFormMessage($name, $sEmail, $comments)
	{
		$params = array('name' => $name, 'email' => $sEmail, 'comments' => $comments);
		$admin_email = SJB_Settings::getSettingByName('notification_email');
		$email = SJB_EmailTemplateEditor::getEmail($admin_email, self::EMAIL_TEMPLATE_SID_SND_CONTACT_FORM_MSG, $params);
		if ($email) {
			$email->setReplyTo($sEmail);
			return $email->send();
		}
		return null;
	}

	/**
	 * @param SJB_Listing $listing
	 * @return bool|null
	 */
	public static function sendAdminListingFlaggedLetter(SJB_Listing $listing)
	{
		$emailTplSID = SJB_Settings::getSettingByName('notify_admin_on_listing_flagged');

		if ($emailTplSID) {
			$listing = SJB_ListingManager::createTemplateStructureForListing($listing);
			$user = SJB_Array::get($listing, 'user');

			$params = array(
				'listing' => $listing,
				'user'	=> $user,
			);

			$admin_email = SJB_Settings::getSettingByName('notification_email');
			$email = SJB_EmailTemplateEditor::getEmail($admin_email, $emailTplSID , $params);

			if ($email) {
				// notify subadmins
				$subAdminsToNotify = self::isSubAdminNotifiedOnListingFlagged();
				if (is_array($subAdminsToNotify)) {
					foreach ($subAdminsToNotify as $subAdminEmail) {
						$email->addCC($subAdminEmail);
					}
				}

				return $email->send();
			}
		}
		return null;
	}
	
	public static function sendAdminBannerAddedLetter($userSID, $bannerGroup)
	{
		$user = SJB_UserManager::getObjectBySID($userSID);
		$user = SJB_UserManager::createTemplateStructureForUser($user);
		$params = array(
			'user'	=> $user,
			'bannerGroup' => $bannerGroup,
		);
		$admin_email = SJB_Settings::getSettingByName('notification_email');
		$email = SJB_EmailTemplateEditor::getEmail($admin_email, self::EMAIL_TEMPLATE_SID_SND_ADMIN_BANNER_ADDED_LTR, $params);
		if ($email) {
			return $email->send();
		}
		return null;
	}
}

