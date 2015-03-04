<?php

class SJB_Admin_Payment_ManageInvoices extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_invoices');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$template = SJB_Request::getVar('template', 'manage_invoices.tpl');
		$searchTemplate = SJB_Request::getVar('search_template', 'invoice_search_form.tpl');
		$action = SJB_Request::getVar('action_name');
		if (!empty($action)) {
			$invoicesSIDs = SJB_Request::getVar('invoices', array());
			$_REQUEST['restore'] = 1;
			switch ($action) {
				case  'paid':
					foreach (array_keys($invoicesSIDs) as $invoiceSID) {
						$invoice = SJB_InvoiceManager::getObjectBySID($invoiceSID);
						$userSID = $invoice->getPropertyValue('user_sid');
						if (SJB_UserManager::isUserExistsByUserSid($userSID)) {
							$items = $invoice->getPropertyValue('items');
							$productSIDs = $items['products'];
							foreach ($productSIDs as $key => $productSID) {
								if ($productSID != -1) {
									if (SJB_ProductsManager::isProductExists($productSID)) {
										$productInfo = $invoice->getItemValue($key);
										$listingNumber = $productInfo['qty'];
										$contract = new SJB_Contract(array('product_sid' => $productSID, 'numberOfListings' => $listingNumber, 'is_recurring' => $invoice->isRecurring()));
										$contract->setUserSID($userSID);
										$contract->setPrice($items['amount'][$key]);
										if ($contract->saveInDB()) {
											SJB_ListingManager::activateListingsAfterPaid($userSID, $productSID, $contract->getID(), $listingNumber);
											SJB_ShoppingCart::deleteItemFromCartBySID($productInfo['shoppingCartRecord'], $userSID);
											$bannerInfo = $productInfo['banner_info'];
											if ($productInfo['product_type'] == 'banners' && !empty($bannerInfo)) {
												$bannersObj = new SJB_Banners();
												$bannersObj->addBanner($bannerInfo['title'], $bannerInfo['link'], $bannerInfo['bannerFilePath'], $bannerInfo['sx'], $bannerInfo['sy'], $bannerInfo['type'], 0, $bannerInfo['banner_group_sid'], $bannerInfo, $userSID, $contract->getID());
												$bannerGroup = $bannersObj->getBannerGroupBySID($bannerInfo['banner_group_sid']);
												SJB_AdminNotifications::sendAdminBannerAddedLetter($userSID, $bannerGroup);
											}
											if ($contract->isFeaturedProfile()) {
												SJB_UserManager::makeFeaturedBySID($userSID);
											}
											if (SJB_UserNotificationsManager::isUserNotifiedOnSubscriptionActivation($userSID)) {
												SJB_Notifications::sendSubscriptionActivationLetter($userSID, $productInfo);
											}
										}
									}
								} else {
									$type = SJB_Array::getPath($items,'custom_info/'. $key .'/type');
									switch ($type) {
										case 'featuredListing':
											$listingId = SJB_Array::getPath($items,'custom_info/' . $key . '/listing_id');
											SJB_ListingManager::makeFeaturedBySID($listingId);
											break;
										case 'priorityListing':
											$listingId = SJB_Array::getPath($items,'custom_info/' . $key . '/listing_id');
											SJB_ListingManager::makePriorityBySID($listingId);
											break;
										case 'activateListing':
											$listingsIds = explode(",", SJB_Array::getPath($items,'custom_info/' . $key . '/listings_ids'));
											foreach ($listingsIds as $listingId) {
												SJB_ListingManager::activateListingBySID($listingId);
											}
											break;
									}
								}
							}
							SJB_Statistics::addStatisticsFromInvoice($invoice);
						}
						$total = $invoice->getPropertyValue('total');
						if ($total > 0) {
							$gatewayID = $invoice->getPropertyValue('payment_method');
							$gatewayID = isset($gatewayID) ? $gatewayID : 'cash_payment';
							$transactionId = md5($invoiceSID . $gatewayID);
							$transactionInfo = array(
								'transaction_id'=> $transactionId,
								'invoice_sid' => $invoiceSID,
								'amount' => $total,
								'payment_method'=> $gatewayID,
								'user_sid' => $invoice->getPropertyValue('user_sid')
							);
							$transaction = new SJB_Transaction($transactionInfo);
							SJB_TransactionManager::saveTransaction($transaction);
						}
						SJB_InvoiceManager::markPaidInvoiceBySID($invoiceSID);
						SJB_PromotionsManager::markPromotionAsPaidByInvoiceSID($invoiceSID);
					}
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/manage-invoices/");
					break;
				case  'unpaid':
					foreach (array_keys($invoicesSIDs) as $invoiceSID)
						SJB_InvoiceManager::markUnPaidInvoiceBySID($invoiceSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/');
					break;
				case 'delete':
					foreach (array_keys($invoicesSIDs) as $invoiceSID)
						SJB_InvoiceManager::deleteInvoiceBySID($invoiceSID);
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/');
					break;
				default:
					unset($_REQUEST['restore']);
					break;
			}
		}

		/***************************************************************/
		$_REQUEST['action'] = 'search';
		$invoice = new SJB_Invoice(array());
		$invoice->addProperty(array(
				'id' => 'username',
				'type' => 'string',
				'value' => '',
				'is_system' => true,
			)
		);

		$aliases = new SJB_PropertyAliases();
		$aliases->addAlias(array(
				'id' => 'username',
				'real_id' => 'user_sid',
				'transform_function' => 'SJB_UserDBManager::getUserSIDsLikeSearchString',
			)
		);

		$searchFormBuilder = new SJB_SearchFormBuilder($invoice);
		$criteriaSaver = new SJB_InvoiceCriteriaSaver();
		if (isset($_REQUEST['restore']))
			$_REQUEST = array_merge($_REQUEST, $criteriaSaver->getCriteria());
		$criteria = $searchFormBuilder->extractCriteriaFromRequestData($_REQUEST, $invoice);
		$searchFormBuilder->setCriteria($criteria);
		$searchFormBuilder->registerTags($tp);
		$tp->display($searchTemplate);

		/********************** S O R T I N G *********************/
		$paginator = new SJB_InvoicePagination();
		$innerJoin = false;
		if ($paginator->sortingField == 'username') {
			$innerJoin = array('users' => array('sort_field' => array( 36 => array('FirstName', 'LastName'), 41 => 'CompanyName'), 'join_field' => 'sid', 'join_field2' => 'user_sid', 'main_table' => 'invoices','join' => 'LEFT JOIN'));
		}
		$searcher = new SJB_InvoiceSearcher(array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage), $paginator->sortingField, $paginator->sortingOrder, $innerJoin);

		$foundInvoices = array();
		$foundInvoicesInfo = array();

		if (SJB_Request::getVar('action', '') == 'search') {
			$foundInvoices = $searcher->getObjectsByCriteria($criteria, $aliases);
			if (empty($foundInvoices) && $paginator->currentPage != 1) {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/manage-invoices/?page=1');
			}
			$criteriaSaver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());
		} elseif (isset($_REQUEST['restore'])) {
			$foundInvoices = $criteriaSaver->getObjectsFromSession();
		}
		foreach ($foundInvoices as $id => $invoice) {
			$subUserSID = $invoice->getPropertyValue('subuser_sid');
			if ($subUserSID) {
				$subUserInfo = SJB_UserManager::getUserInfoBySID($subUserSID);
				$parentInfo = SJB_UserManager::getUserInfoBySID($subUserInfo['parent_sid']);
				$username = $parentInfo['CompanyName'];
			}
			else {
				$userSID = $invoice->getPropertyValue('user_sid');
				$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
				if (SJB_UserGroupManager::getUserGroupIDBySID($userInfo['user_group_sid']) == 'Employer'){
					$username = $userInfo['CompanyName'];
				}
				else if (SJB_UserGroupManager::getUserGroupIDBySID($userInfo['user_group_sid']) == 'JobSeeker') {
					$username = $userInfo['FirstName']. ' ' . $userInfo['LastName'];
				} else {
					$username = $userInfo['username'];
				}
			}

			$invoice->addProperty(array(
				'id' => 'sid',
				'type' => 'string',
				'value' => $invoice->getSID(),
			));

			$invoice->addProperty(array(
				'id' => 'username',
				'type' => 'string',
				'value' => $username,
			));

			$foundInvoices[$id] = $invoice;
			$foundInvoicesInfo[$invoice->getSID()] = SJB_InvoiceManager::getInvoiceInfoBySID($invoice->getSID());
			$foundInvoicesInfo[$invoice->getSID()]['userExists'] = !empty($username) ? 1 : 0;
		}

		/****************************************************************/
		$paginator->setItemsCount($searcher->getAffectedRows());

		$form_collection = new SJB_FormCollection($foundInvoices);
		$form_collection->registerTags($tp);

		$tp->assign('paginationInfo', $paginator->getPaginationInfo());
		$tp->assign("found_invoices", $foundInvoicesInfo);
		$tp->display($template);
	}
}
