<?php

class SJB_PaymentHandler
{
	/**
	 * @var null|int
	 */
	private $invoiceSID = null;
	private $product = null;
	private $recurringID = '';
	private $gatewayID = '';
	
	public function __construct($invoiceSID, $gatewayID)
	{
		$this->invoiceSID = $invoiceSID;
		$this->gatewayID = $gatewayID;
	}
	
	public function setProduct($product)
	{
		$this->product = $product;
	}
	
	public function setRecurringID($recurringID)
	{
		$this->recurringID = $recurringID;
	}
	
	public function createContract($userSID, $invoiceID, $reactivation, $status = 'active')
	{
		$listingNumber = !empty($this->product['qty'])?$this->product['qty']:null;
		if ($this->recurringID) {
			$contract = new SJB_Contract(array(
				'product_sid' => $this->product['sid'],
				'recurring_id' => $this->recurringID,
				'gateway_id' => $this->gatewayID,
				'invoice_id' => $invoiceID,
				'numberOfListings' => $listingNumber
			));
			$contractSID = SJB_ContractManager::getContractSIDByRecurringId($this->recurringID);
			SJB_ContractManager::deleteAllContractsByRecurringId($this->recurringID);
		}
		else {
			$contract = new SJB_Contract(array(
				'product_sid' => $this->product['sid'],
				'gateway_id' => $this->gatewayID,
				'invoice_id' => $invoiceID,
				'numberOfListings' => $listingNumber
			));
			if ($invoiceID)
				SJB_ContractManager::deletePendingContractByInvoiceID($invoiceID, $userSID, $this->product['sid']);
		}
		$contract->setUserSID($userSID);
		$contract->setPrice($this->product['amount']);
		$contract->setStatus($status);
		if ($contract->saveInDB()) {
			SJB_ShoppingCart::deleteItemFromCartBySID($this->product['shoppingCartRecord'], $userSID);
			$bannerInfo = $this->product['banner_info'];
			if ($this->product['product_type'] == 'banners' && !empty($bannerInfo)) {
				$bannersObj = new SJB_Banners();
				if (isset($contractSID)) {
					$bannerID = $bannersObj->getBannerIDByContract($contractSID);
					if ($bannerID)
						$bannersObj->updateBannerContract($contract->getID(), $bannerID);
				}
				else {
					$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $userSID, $contract->getID());
					$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
					SJB_AdminNotifications::sendAdminBannerAddedLetter($userSID, $bannerGroup);
				}
			}
			if ($contract->isFeaturedProfile())
				SJB_UserManager::makeFeaturedBySID($userSID);
			SJB_Statistics::addStatistics('payment', 'product', $this->product['sid'], false, 0, 0, $userSID, $this->product['amount']);
			
            if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($userSID)) {
                SJB_Notifications::sendSubscriptionActivationLetter($userSID, $this->product, $reactivation);
            }
		}
	}
	
	public function deleteContract($invoiceID, $productSID, $userSID)
	{
		$contractID = SJB_ContractManager::getContractIDByInvoiceID($invoiceID, $productSID, $userSID);	
		if ($contractID) {
			SJB_ContractManager::deleteContract($contractID, $userSID);
			SJB_Statistics::deleteStatistics('payment', 'product', $this->product['sid'], $userSID, $this->product['amount']);
		}
	}
	
	public function activateListing(SJB_Invoice $invoice)
	{
		$listingsIds = explode(",", $this->product['listings_ids']);
		SJB_ListingManager::activateListingBySID($listingsIds);
		SJB_Statistics::addStatisticsFromInvoice($invoice);
	}
	
	public function deactivateListing($userSID, $price)
	{
		$listings_ids = explode(",", $this->product['listings_ids']);
		SJB_ListingManager::deactivateListingBySID($listings_ids);
		SJB_Statistics::deleteStatistics('payment', 'activateListing', $this->invoiceSID, $userSID, $price);
	}
	
	public function makeFeatured(SJB_Invoice $invoice)
	{
		$listingId = $this->product['listing_id'];
		SJB_ListingManager::makeFeaturedBySID($listingId);
		SJB_Statistics::addStatisticsFromInvoice($invoice);
	}
	
	public function unmakeFeatured($userSID, $price)
	{
		$listing_id =$this->product['listing_id'];
		SJB_ListingManager::unmakeFeaturedBySID($listing_id);
		SJB_Statistics::deleteStatistics('payment', 'featuredListing', $this->invoiceSID, $userSID, $price);
	}
	
	public function makePriority(SJB_Invoice $invoice)
	{
		$listingId = $this->product['listing_id'];
		SJB_ListingManager::makePriorityBySID($listingId);
		SJB_Statistics::addStatisticsFromInvoice($invoice);
	}
	
	public function unmakePriority($userSID, $price)
	{
		$listing_id = $this->product['listing_id'];
		SJB_ListingManager::unmakePriorityBySID($listing_id);
		SJB_Statistics::deleteStatistics('payment', 'priorityListing', $this->invoiceSID, $userSID, $price);
	}
}
