<?php

class SJB_Classifieds_MakePriority extends SJB_Function
{
	public function execute()
	{
		$listingSid = SJB_Request::getVar("listing_id", null, 'default', 'int');
		if ($listingSid) {
			$listing = SJB_ListingManager::getObjectBySID($listingSid);
			if (!is_null($listing) && !$listing->isPriority()) {
				$productInfo = $listing->getProductInfo();
				if ($productInfo['priority']) {
					SJB_ListingManager::makePriorityBySID($listingSid);
				} else {
					$userSid        = $listing->getUserSID();
					$productSid     = $productInfo['product_sid'];
					$subTotalPrice  = $productInfo['upgrade_to_priority_listing_price'];
					$listingTitle   = $listing->getProperty('Title')->getValue();
					$listingTypeSid = $listing->getListingTypeSID();
					$listingTypeId  = SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSid);
					
					$newProductName = "Upgrade of \"{$listingTitle}\" {$listingTypeId} to priority";
					$newProductInfo = SJB_ShoppingCart::createInfoForCustomProduct($userSid, $productSid, $listingSid, $subTotalPrice, $newProductName, 'priorityListing');
					
					if ($subTotalPrice <= 0) {
						SJB_InvoiceManager::generateInvoice($newProductInfo, $userSid, $subTotalPrice, SJB_System::getSystemSettings('SITE_URL') . '/make-priority/');
						SJB_ListingManager::makeFeaturedBySID($listingSid);
					} else {
						SJB_ShoppingCart::createCustomProduct($newProductInfo, $userSid);
						$shoppingUrl = SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/';
						SJB_HelperFunctions::redirect($shoppingUrl);
					}
				}
			}
			else if (is_null($listing)) {
				$errors['INVALID_LISTING_ID'] = 1;
			} else {
				$errors['LISTING_ALREADY_PRIORITY'] = 1;
			}
		} else {
			$errors['PARAMETERS_MISSED'] = 1;
		}
		
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign("errors", (isset($errors) ? $errors : null));
		$tp->display("make_listing_priority.tpl");
	}
}
