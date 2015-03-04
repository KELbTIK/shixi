<?php
class SJB_Miscellaneous_TaskScheduler extends SJB_Function
{
	/** @var SJB_TemplateProcessor*/
	public $tp;

	private $lang;
	private $currentDate;
	private $notifiedSavedSearchesSID;

	public function execute()
	{
		set_time_limit(0);
		$i18n = SJB_I18N::getInstance();
		$this->lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$this->currentDate = strftime($this->lang['date_format'], time());

		// Do Autobackup
		$autoBackup = new SJB_Autobackup();
		$autoBackup->doBackup();
		$this->tp = SJB_System::getTemplateProcessor();

		if ((time() - SJB_Settings::getSettingByName('task_scheduler_last_executed_time_hourly')) > 3600) {
			$this->runHourlyTaskScheduler();
			SJB_Settings::updateSetting('task_scheduler_last_executed_time_hourly', time());
		}
		if ((time() - SJB_Settings::getSettingByName('task_scheduler_last_executed_time_daily')) > 86400) {
			$this->runDailyTaskScheduler();
			SJB_Settings::updateSetting('task_scheduler_last_executed_time_daily', time());
		}
		$this->runTaskScheduler();
	}

	private function runDailyTaskScheduler()
	{
		$guestsNotifiedEmails = $this->sendGuestsAlerts();
		$this->tp->assign('notified_guests_emails', $guestsNotifiedEmails);
		$this->sendSearchedNotifications();
	}

	private function runHourlyTaskScheduler()
	{
		SJB_System::getModuleManager()->executeFunction('miscellaneous', 'email_scheduling');
	}

	private function runTaskScheduler()
	{
		// Deactivate Expired Listings & Send Notifications
		$listingsExpiredID = SJB_ListingManager::getExpiredListingsSID();
		foreach ($listingsExpiredID as $listingExpiredID) {
			SJB_ListingManager::deactivateListingBySID($listingExpiredID, true);
			$listing = SJB_ListingManager::getObjectBySID($listingExpiredID);
			$listingInfo = SJB_ListingManager::createTemplateStructureForListing($listing);
			if (SJB_UserNotificationsManager::isUserNotifiedOnListingExpiration($listing->getUserSID())) {
				SJB_Notifications::sendUserListingExpiredLetter($listingInfo);
			}

			// notify admin
			SJB_AdminNotifications::sendAdminListingExpiredLetter($listingInfo);
		}
		$listingsDeactivatedID = array();
		if (SJB_Settings::getSettingByName('automatically_delete_expired_listings')) {
			$listingsDeactivatedID = SJB_ListingManager::getDeactivatedListingsSID();
			foreach ($listingsDeactivatedID as $listingID) {
				SJB_ListingManager::deleteListingBySID($listingID);
			}
		}

		SJB_ListingManager::unFeaturedListings();
		SJB_ListingManager::unPriorityListings();
		SJB_Cache::getInstance()->clean('matchingAnyTag', array(SJB_Cache::TAG_LISTINGS));
		/////////////////////////// Send remind notifications about expiration of LISTINGS

		// 1. get user sids and days count of 'remind listing notification' setting = 1 from user_notifications table
		// 2. foreach user:
		//   - get listings with that expiration remind date
		//   - check every listing sid in DB table of sended. If sended - remove from send list
		//   - send notification with listings to user
		//   - write listings sid in DB table of sended notifications

		$notificationData = SJB_UserNotificationsManager::getUsersAndDaysOnListingExpirationRemind();

		foreach ($notificationData as $elem) {
			$userSID = $elem['user_sid'];
			$days = $elem['days'];

			$listingSIDs = SJB_ListingManager::getListingsIDByDaysLeftToExpired($userSID, $days);

			if (empty($listingSIDs)) {
				continue;
			}

			$listingsInfo = array();
			// check listings remind sended
			foreach ($listingSIDs as $key => $sid) {
				if (SJB_ListingManager::isListingNotificationSended($sid)) {
					unset($listingSIDs[$key]);
					continue;
				}
				$info = SJB_ListingManager::getListingInfoBySID($sid);

				$listingsInfo[$sid] = $info;
			}

			if (! empty($listingsInfo)) {
				// now only unsended listings we have in array
				// send listing notification
				foreach ($listingSIDs as $sid) {
					SJB_Notifications::sendRemindListingExpirationLetter($userSID, $sid, $days);
				}

				// write listing id in DB table of sended notifications
				SJB_ListingManager::saveListingIDAsSendedNotificationsTable($listingSIDs);
			}
		}

		// Send Notifications for Expired Contracts
		$contractsExpiredID = SJB_ContractManager::getExpiredContractsID();
		foreach ($contractsExpiredID as $contractExpiredID) {
			$contractInfo = SJB_ContractManager::getInfo($contractExpiredID);
			$productInfo = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
			$userInfo = SJB_UserManager::getUserInfoBySID($contractInfo['user_sid']);
			$serializedExtraInfo = unserialize($contractInfo['serialized_extra_info']);

			if (! empty($serializedExtraInfo['featured_profile']) && ! empty($userInfo['featured'])) {
				$contracts = SJB_ContractManager::getAllContractsInfoByUserSID($userInfo['sid']);
				$isFeatured = 0;
				foreach ($contracts as $contract) {
					if ($contract['id'] != $contractExpiredID) {
						$serializedExtraInfo = unserialize($contract['serialized_extra_info']);
						if (! empty($serializedExtraInfo['featured'])) {
							$isFeatured = 1;
						}
					}
				}
				if (! $isFeatured) {
					SJB_UserManager::removeFromFeaturedBySID($userInfo['sid']);
				}
			}
			if (SJB_UserNotificationsManager::isUserNotifiedOnContractExpiration($contractInfo['user_sid']))
				SJB_Notifications::sendUserContractExpiredLetter($userInfo, $contractInfo, $productInfo);
			// notify admin
			SJB_AdminNotifications::sendAdminUserContractExpiredLetter($userInfo['sid'], $contractInfo, $productInfo);

			SJB_ContractManager::deleteContract($contractExpiredID, $contractInfo['user_sid']);
		}
		//////////////////////// Send remind notifications about expiration of contracts

		// 1. get user sids and days count of 'remind subscription notification' setting = 1 from user_notifications table
		// 2. foreach user:
		//   - get contracts with that expiration remind date
		//   - check every contract sid in DB table of sended. If sended - remove from send list
		//   - send notification with contracts to user
		//   - write contract sid in DB table of sended contract notifications

		$notificationData = SJB_UserNotificationsManager::getUsersAndDaysOnSubscriptionExpirationRemind();

		foreach ($notificationData as $elem) {
			$userSID = $elem['user_sid'];
			$days = $elem['days'];

			$contractSIDs = SJB_ContractManager::getContractsIDByDaysLeftToExpired($userSID, $days);

			if (empty($contractSIDs)) {
				continue;
			}

			$contractsInfo = array();
			// check contracts sended
			foreach ($contractSIDs as $key => $sid) {
				if (SJB_ContractManager::isContractNotificationSended($sid)) {
					unset($contractSIDs[$key]);
					continue;
				}
				$info = SJB_ContractManager::getInfo($sid);
				$info['extra_info'] = !empty($info['serialized_extra_info']) ? unserialize($info['serialized_extra_info']) : '';

				$contractsInfo[$sid] = $info;
			}

			if (! empty($contractsInfo)) {
				// now only unsended contracts we have in array
				// send contract notification
				foreach ($contractSIDs as $sid) {
					SJB_Notifications::sendRemindSubscriptionExpirationLetter($userSID, $contractsInfo[$sid], $days);
				}

				// write contract id in DB table of sended contract notifications
				SJB_ContractManager::saveContractIDAsSendedNotificationsTable($contractSIDs);
			}
		}

		// delete applications with no employer and job seeker
		$emptyApplications = SJB_DB::query('SELECT `id` FROM `applications` WHERE `show_js` = 0 AND `show_emp` = 0');
		foreach ($emptyApplications as $application) {
			SJB_Applications::remove($application['id']);
		}

		// NEWS
		$expiredNews = SJB_NewsManager::getExpiredNews();
		foreach ($expiredNews as $article) {
			SJB_NewsManager::deactivateItemBySID($article['sid']);
		}

		// LISTING XML IMPORT
		SJB_XmlImport::runImport();

		// UPDATE PAGES WITH FUNCTION EQUAL BROWSE(e.g. /browse-by-city/)
		SJB_BrowseDBManager::rebuildBrowses();

		//-------------------sitemap generator--------------------//
		SJB_System::executeFunction('miscellaneous', 'sitemap_generator');

		// CLEAR `error_log` TABLE
		$errorLogLifetime = SJB_System::getSettingByName('error_log_lifetime');
		$lifeTime = strtotime("-{$errorLogLifetime} days");
		if ($lifeTime > 0) {
			SJB_DB::query('DELETE FROM `error_log` WHERE `date` < ?t', $lifeTime);
		}

		SJB_Settings::updateSetting('task_scheduler_last_executed_date', $this->currentDate);
		$this->tp->assign('expired_listings_id', $listingsExpiredID);
		$this->tp->assign('deactivated_listings_id', $listingsDeactivatedID);
		$this->tp->assign('expired_contracts_id', $contractsExpiredID);
		$this->tp->assign('notified_saved_searches_id', $this->notifiedSavedSearchesSID);

		$schedulerLog = $this->tp->fetch('task_scheduler_log.tpl');
		SJB_HelperFunctions::writeCronLogFile('task_scheduler.log', $schedulerLog);

		SJB_DB::query('INSERT INTO `task_scheduler_log`
			(`last_executed_date`, `notifieds_sent`, `expired_listings`, `expired_contracts`, `log_text`)
			VALUES ( NOW(), ?n, ?n, ?n, ?s)',
			count($this->notifiedSavedSearchesSID), count($listingsExpiredID), count($contractsExpiredID), $schedulerLog);

		SJB_System::getModuleManager()->executeFunction('social', 'linkedin');
		SJB_System::getModuleManager()->executeFunction('social', 'facebook');
		SJB_System::getModuleManager()->executeFunction('classifieds', 'linkedin');
		SJB_System::getModuleManager()->executeFunction('classifieds', 'facebook');
		SJB_System::getModuleManager()->executeFunction('classifieds', 'twitter');

		SJB_Event::dispatch('task_scheduler_run');
	}

	public function sendGuestsAlerts()
	{
		$guestEmailsNotified = array();
		$notificationsLimit = (int)SJB_Settings::getSettingByName('num_of_listings_sent_in_email_alerts');

		$listing = new SJB_Listing();
		$listing->addActivationDateProperty();
		$aliasInfoID = $listing->addIDProperty();
		$userNameAliasInfo = $listing->addUsernameProperty();
		$listingTypeIDInfo = $listing->addListingTypeIDProperty();
		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias($aliasInfoID);
		$aliases->addAlias($userNameAliasInfo);
		$aliases->addAlias($listingTypeIDInfo);

		$guestAlertsToNotify = SJB_GuestAlertManager::getGuestAlertsToNotify();

		foreach ($guestAlertsToNotify as $guestAlertInfo) {
			$dataSearch = unserialize($guestAlertInfo['data']);
			$dataSearch['active']['equal'] = 1;
			if (! empty($guestAlertInfo['last_send'])) {
				$dateArr = explode('-', $guestAlertInfo['last_send']);
				$guestAlertInfo['last_send'] = strftime($this->lang['date_format'], mktime(0, 0, 0, $dateArr[1], $dateArr[2], $dateArr[0]));
				$dataSearch['activation_date']['not_less'] = $guestAlertInfo['last_send'];
			}
			$dataSearch['activation_date']['not_more'] = $this->currentDate;
			$listingTypeSID = 0;
			if ($dataSearch['listing_type']['equal']) {
				$listingTypeID = $dataSearch['listing_type']['equal'];
				$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
				if (SJB_ListingTypeManager::getWaitApproveSettingByListingType($listingTypeSID)) {
					$dataSearch['status']['equal'] = 'approved';
				}
			}

			$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($dataSearch, $listing);
			$searcher = new SJB_ListingSearcher();
			$searcher->found_object_sids = array();
			$searcher->setLimit($notificationsLimit);
			$listingsIDsFound = $searcher->getObjectsSIDsByCriteria($criteria, $aliases);

			if (count($listingsIDsFound)) {
				$sentGuestAlertNewListingsFoundLetter = SJB_Notifications::sendGuestAlertNewListingsFoundLetter($listingsIDsFound, $guestAlertInfo, $listingTypeSID);
				if ($sentGuestAlertNewListingsFoundLetter) {
					SJB_GuestAlertStatistics::saveEventSent($listingTypeSID, $guestAlertInfo['sid']);
					SJB_GuestAlertManager::markGuestAlertAsSentBySID($guestAlertInfo['sid']);
					array_push($guestEmailsNotified, $guestAlertInfo['email']);
				}
			}
		}
		return $guestEmailsNotified;
	}

	private function sendSearchedNotifications()
	{
		$savedSearches = SJB_SavedSearches::getAutoNotifySavedSearches();
		$listing = new SJB_Listing();
		$this->notifiedSavedSearchesSID = array();
		$notificationsLimit = (int)SJB_Settings::getSettingByName('num_of_listings_sent_in_email_alerts');
		foreach ($savedSearches as $savedSearch) {
			$searcher = new SJB_ListingSearcher();
			$listing->addActivationDateProperty();
			$dataSearch = unserialize($savedSearch['data']);
			$dataSearch['active']['equal'] = 1;
			$dateArray = explode('-', $savedSearch['last_send']);
			$savedSearch['last_send'] = strftime($this->lang['date_format'], mktime(0, 0, 0, $dateArray[1], $dateArray[2], $dateArray[0]));
			$dataSearch['activation_date']['not_less'] = $savedSearch['last_send'];
			$dataSearch['activation_date']['not_more'] = $this->currentDate;
			$listingTypeSID = 0;
			if ($dataSearch['listing_type']['equal']) {
				$listingTypeID = $dataSearch['listing_type']['equal'];
				$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
				if (SJB_ListingTypeManager::getWaitApproveSettingByListingType($listingTypeSID)) {
					$dataSearch['status']['equal'] = 'approved';
				}
			}
			$idAliasInfo = $listing->addIDProperty();
			$usernameAliasInfo = $listing->addUsernameProperty();
			$listingTypeIDInfo = $listing->addListingTypeIDProperty();
			$aliases = new SJB_PropertyAliases();
			$aliases->addAlias($idAliasInfo);
			$aliases->addAlias($usernameAliasInfo);
			$aliases->addAlias($listingTypeIDInfo);

			$dataSearch['access_type'] = array(
				'accessible' => $savedSearch['user_sid'],
			);

			$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($dataSearch, $listing);
			$searcher->found_object_sids = array();
			$searcher->setLimit($notificationsLimit);
			$foundListingsIDs = $searcher->getObjectsSIDsByCriteria($criteria, $aliases);

			if (count($foundListingsIDs)) {
				$savedSearch['activation_date'] = $savedSearch['last_send'];
				if (SJB_Notifications::sendUserNewListingsFoundLetter($foundListingsIDs, $savedSearch['user_sid'], $savedSearch, $listingTypeSID)) {
					SJB_Statistics::addStatistics('sentAlert', $listingTypeSID, $savedSearch['sid']);
					SJB_DB::query('UPDATE `saved_searches` SET `last_send` = CURDATE() WHERE `sid` = ?n', $savedSearch['sid']);
				}
				$this->notifiedSavedSearchesSID[] = $savedSearch['sid'];
			}
		}
	}
}
