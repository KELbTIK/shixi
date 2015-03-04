<?php

class SJB_Contracts_CreateContract extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$invoice_sid = SJB_Request::getVar('invoice_sid', null, false, 'int');
		$invoice = SJB_InvoiceManager::getObjectBySID($invoice_sid);
		$user = null;
		$errors = null;
		$userHasContract = false;
		if (!is_null($invoice)) {
			$status = $invoice->getStatus();
			if ($status == SJB_Invoice::INVOICE_STATUS_VERIFIED) {
				$userSID = $invoice->getPropertyValue('user_sid');
				$items = $invoice->getPropertyValue('items');
				$products = $items['products'];
				$user = SJB_UserManager::getObjectBySID($userSID);
				$userHasContract = $user->hasContract();
				$paymentStatus   = false;
				foreach ($products as $key => $productSID) {
					if ($productSID != -1) {
						$product_info = $invoice->getItemValue($key);
						$products[$key] = $product_info;
						if (!empty($product_info['listing_type_sid'])) {
							$listingTypeID = SJB_ListingTypeDBManager::getListingTypeIDBySID($product_info['listing_type_sid']);
							$listingTypeName = SJB_ListingTypeManager::getListingTypeNameBySID($product_info['listing_type_sid']);
							if (!in_array($listingTypeID, array('Job', 'Resume'))) {
								$listingTypeName .= ' Listing';
							}
							$listingTypes[] = array('ID' => $listingTypeID, 'name' => $listingTypeName);
						}
						$listingNumber = $product_info['qty'];
						$contract = new SJB_Contract(array('product_sid' => $productSID, 'numberOfListings' => $listingNumber, 'is_recurring' => $invoice->isRecurring()));
						$contract->setUserSID($userSID);
						$contract->setPrice($items['amount'][$key]);
						if ($contract->saveInDB()) {
							SJB_ListingManager::activateListingsAfterPaid($userSID, $productSID, $contract->getID(), $listingNumber);
							SJB_ShoppingCart::deleteItemFromCartBySID($product_info['shoppingCartRecord'], $userSID);
							$bannerInfo = $product_info['banner_info'];
							$paymentStatus = true;
							if ($product_info['product_type'] == 'banners' && !empty($bannerInfo)) {
								$bannersObj = new SJB_Banners();
								$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $userSID, $contract->getID());
								$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
								SJB_AdminNotifications::sendAdminBannerAddedLetter($userSID, $bannerGroup);
							}
							if ($contract->isFeaturedProfile())
								SJB_UserManager::makeFeaturedBySID($userSID);
							if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($userSID)) {
								SJB_Notifications::sendSubscriptionActivationLetter($userSID, $product_info);
							}
						}
					} else {
						if (isset($items['custom_info'][$key]['type'])) {
							$products[$key] = $this->updateListing($items['custom_info'][$key]['type'], $key, $items, $userSID);
						} else {
							$products[$key] = array('name' => $items['custom_item'][$key]);
						}
						
						$paymentStatus = true;
					}
				}
				if ($paymentStatus) {
					$invoice->setStatus(SJB_Invoice::INVOICE_STATUS_PAID);
					SJB_InvoiceManager::saveInvoice($invoice);
					SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoice->getSID());
				}
				if (isset($listingTypes)) {
					$tp->assign('listingTypes', $listingTypes);
				}
				$tp->assign('products', $products);
			} else {
				$errors['INVOICE_IS_NOT_VERIFIED'] = 1;
			}
		} else {
			$errors['INVALID_INVOICE_ID'] = 1;
		}
		if(!$errors) {
			$subTotal = $invoice->getPropertyValue('sub_total');
			if (empty($subTotal)) {
				SJB_Statistics::addStatisticsFromInvoice($invoice);
			}
			
			$isUserJustRegistered = SJB_UserManager::isCurrentUserJustRegistered();
			if(isset($items['products']) && count($items['products']) == 1 && $isUserJustRegistered && !$userHasContract) {
				$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($user->getUserGroupSID());
				$pageId = !empty($userGroupInfo['after_registration_redirect_to']) ? $userGroupInfo['after_registration_redirect_to'] : '';
				$redirectUrl = SJB_UserGroupManager::getRedirectUrlByPageID($pageId);
				SJB_HelperFunctions::redirect($redirectUrl);
			}
		}
		$tp->assign('errors', $errors);
		$tp->display('create_contract.tpl');
	}

	private function updateListing($type, $key, $items, $userSid)
	{
		$listingSid  = $items['custom_info'][$key]['listing_id'];
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingSid);
		$products = array(
			'name'       => $items['custom_item'][$key],
			'type'       => $type,
			'listingSid' => $listingSid
		);
		if ($listingInfo) {
			if ($type == 'featuredListing') {
				if (!$listingInfo['featured']) {
					SJB_ListingManager::makeFeaturedBySID($listingSid);
				} else {
					$products['error'] = 'LISTING_ALREADY_FEATURED';
				}
			}
			else if ($type == 'priorityListing') {
				if (!$listingInfo['priority']) {
					SJB_ListingManager::makePriorityBySID($listingSid);
				} else {
					$products['error'] = 'LISTING_ALREADY_PRIORITY';
				}
			}
			else if ($type == 'activateListing') {
				if (!$listingInfo['active']) {
					SJB_ListingManager::activateListingBySID($listingSid);
				} else {
					$products['error'] = 'LISTING_ALREADY_ACTIVE';
				}
			}
		} else {
			$products['error'] = 'INVALID_LISTING_ID';
		}
		
		SJB_ShoppingCart::deleteCustomItemFromCart($userSid, $items['custom_item'][$key]);
		
		return $products;
	}
}
