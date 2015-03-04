<?php

class SJB_Admin_Classifieds_ListingActions extends SJB_Function
{
	public function isAccessible()
	{
		$listingTypeId = SJB_Request::getVar('listingTypeId', null);
		$listingType = !in_array($listingTypeId, array('Resume', 'Job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel('manage_' . strtolower($listingType));
		return parent::isAccessible();
	}

	public function execute()
	{
		$restore = 'restore=';

		if (isset($_REQUEST['action_name'], $_REQUEST['listings'])) {
			$listings_ids = $_REQUEST['listings'];

			switch (strtolower($_REQUEST['action_name'])) {
				case 'activate':
					$activatedListings = array();
					foreach ($listings_ids as $listingId => $value) {
						if (SJB_ListingManager::activateListingBySID($listingId, false)) {
							$activatedListings[] = $listingId;
						}
						$listing = SJB_ListingManager::getObjectBySID($listingId);
						if (SJB_UserNotificationsManager::isUserNotifiedOnListingActivation($listing->getUserSID())) {
							SJB_Notifications::sendUserListingActivatedLetter($listing, $listing->getUserSID());
						}
					}
					SJB_BrowseDBManager::addListings($activatedListings);
					break;

				case 'deactivate':
					$this->executeAction($listings_ids, 'deactivate');
					break;

				case 'delete':
					$this->executeAction($listings_ids, 'delete');
					break;

				case 'datemodify':
					if (isset($_REQUEST['date_to_change'])) {
						$dateToUpdate = $_REQUEST['date_to_change'];
						$date = SJB_I18N::getInstance()->getInput('date', $dateToUpdate);
						foreach ($listings_ids as $listing_id => $value) {
							$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
							$result = SJB_DB::query('UPDATE `listings` SET `expiration_date` = ?s WHERE `sid` = ?n', $date, $listingInfo['sid']);
						}
					}
					break;

				case 'approve':
					$this->executeAction($listings_ids, 'approve');
					foreach ($listings_ids as $listing_id => $value) {
						$user_sid = SJB_ListingManager::getUserSIDByListingSID($listing_id);
						if (SJB_UserNotificationsManager::isUserNotifiedOnListingApprove($user_sid)) {
							SJB_Notifications::sendUserListingApproveOrRejectLetter($listing_id, $user_sid, 'approve');
						}
					}
					break;

				case 'reject':
					$this->executeAction($listings_ids, 'reject');
					foreach ($listings_ids as $listing_id => $value) {
						$user_sid = SJB_ListingManager::getUserSIDByListingSID($listing_id);
						if (SJB_UserNotificationsManager::isUserNotifiedOnListingReject($user_sid))
							SJB_Notifications::sendUserListingApproveOrRejectLetter($listing_id, $user_sid, 'reject');
					}
					break;

				default:
					$restore = '';
					break;
			}
		}
		$listingTypeId = SJB_Request::getVar('listingTypeId', null);
		$listingType = $listingTypeId !='Job' && $listingTypeId !='Resume' ? $listingTypeId . '-listings' : $listingTypeId . 's';
		SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-' . strtolower($listingType) . '/?action=search&' . $restore);
	}

	/**
	 * @param array  $listingsIds Used listing sids
	 * @param string $action      Actions performed with the listings(delete, deactivate)
	 */
	protected function executeAction(array $listingsIds, $action)
	{
		if (empty($listingsIds)) {
			return;
		}

		$processListingsIds = array();
		foreach ($listingsIds as $key => $value) {
			$processListingsIds[] = $key;
		}
		
		switch($action) {
			case 'delete':
				SJB_ListingManager::deleteListingBySID($processListingsIds);
				return;
			case 'deactivate':
				SJB_ListingManager::deactivateListingBySID($processListingsIds);
				return;
			case 'reject':
				SJB_ListingManager::setListingApprovalStatus($processListingsIds, 'rejected');
				return;
			case 'approve':
				SJB_ListingManager::setListingApprovalStatus($processListingsIds, 'approved');
				return;
		}
	}
}