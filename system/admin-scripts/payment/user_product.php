<?php

class SJB_Admin_Payment_UserProduct extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$userSid = SJB_Request::getVar('user_sid', null);
			$userGroupID = SJB_UserGroupManager::getUserGroupIDByUserSID($userSid);
			$this->setPermissionLabel('manage_' . strtolower($userGroupID));
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$userSID = SJB_Request::getVar('user_sid', false);
		$page = SJB_Request::getVar('page', '');
		$action = SJB_Request::getVar('action', false);
		$user = SJB_UserManager::getUserInfoBySID($userSID);
		$contractID = SJB_Request::getVar("contract_id", 0);
		$viewContractInfo = true;
		if ($user) {
			switch ($page) {
				case 'add_product':
					if ($action == 'add_product') {
						$productSID = SJB_Request::getVar('product_sid', false);
						$listingNumber = SJB_Request::getVar('number_of_listings_' . $productSID, null);
						if ($productSID) {
							$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
							$listingNumber = $listingNumber?$listingNumber:(!empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:null);
							$contract = new SJB_Contract(array('product_sid' => $productSID, 'numberOfListings' => $listingNumber, 'is_recurring' => 0));
							$contract->setUserSID($userSID);
							$contract->saveInDB();
							$tp->assign('contract_added', 1);
						}
						else
							$errors['UNDEFINED_PRODUCT_SID'] = 1;
					}

					$products = SJB_ProductsManager::getUserGroupProducts($user['user_group_sid']);
					foreach ($products as $key => $product) {
						if (!empty($product['pricing_type']) && $product['pricing_type'] == 'volume_based' && !empty($product['volume_based_pricing'])) {
							$volumeBasedPricing = $product['volume_based_pricing'];
							$minListings = min($volumeBasedPricing['listings_range_from']);
							$maxListings = max($volumeBasedPricing['listings_range_to']);
							$countListings = array();
							for ($i = $minListings; $i <= $maxListings; $i++)
								$countListings[] = $i;
							$products[$key]['count_listings'] = $countListings;
						}
					}
					$tp->assign('user_sid', $userSID);
					$tp->assign('products', $products);
					$tp->display('add_user_product.tpl');
					break;

				case 'user_products':
					if ($action == 'remove')
						SJB_ContractManager::deleteContract($contractID, $userSID);
					elseif ($action == 'activate') 
						SJB_ContractManager::activateContract($contractID, $userSID);
					$contracts = SJB_ContractManager::getAllContractsInfoByUserSID($userSID);
					$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
					$acl = SJB_Acl::getInstance();
					foreach ($contracts as $key => $contractInfo) {
						$contractInfo['extra_info'] = unserialize($contractInfo['serialized_extra_info']);
						$contractInfo['availableView'] = array();
						$contractInfo['availableContactViews'] = array();
						$contractInfo['listingAmount'] = array();
						foreach ($listingTypes as $listingType) {
							$listingTypeID = $listingType['id'];
							if ($acl->isAllowed('view_' . $listingTypeID . '_details', $contractInfo['id'], 'contract')) {
								$contractInfo['availableViews'][$listingTypeID]['name'] = $listingType['name'];
								$permissionParam = $acl->getPermissionParams('view_' . $listingTypeID . '_details', $contractInfo['id'], 'contract');
								$contractInfo['availableViews'][$listingTypeID]['numOfViews'] = SJB_ContractManager::getNumbeOfPagesViewed($userSID, array($contractInfo['id']), false, $listingType['sid']);
								if (empty($permissionParam)) {
									$contractInfo['availableViews'][$listingTypeID]['count'] = 'unlimited';
									$contractInfo['availableViews'][$listingTypeID]['viewsLeft'] = 'unlimited';
								}
								else {
									$contractInfo['availableViews'][$listingTypeID]['count'] = $permissionParam;
									$contractInfo['availableViews'][$listingTypeID]['viewsLeft'] = $permissionParam - $contractInfo['availableViews'][$listingTypeID]['numOfViews'];
								}
							}
							if ($acl->isAllowed('post_' . $listingTypeID, $contractInfo['id'], 'contract')) {
								$contractInfo['listingAmount'][$listingTypeID]['name'] = $listingType['name'];
								$permissionParam = $acl->getPermissionParams('post_' . $listingTypeID, $contractInfo['id'], 'contract');
								$contractInfo['listingAmount'][$listingTypeID]['numPostings'] = SJB_ContractManager::getListingsNumberByContractSIDsListingType(array($contractInfo['id']), $listingType['id']);
								if (empty($permissionParam)) {
									$contractInfo['listingAmount'][$listingTypeID]['count'] = 'unlimited';
									$contractInfo['listingAmount'][$listingTypeID]['listingsLeft'] = 'unlimited';
								}
								else {
									$contractInfo['listingAmount'][$listingTypeID]['count'] = $permissionParam;
									$contractInfo['listingAmount'][$listingTypeID]['listingsLeft'] = $contractInfo['listingAmount'][$listingTypeID]['count'] - $contractInfo['listingAmount'][$listingTypeID]['numPostings'];
								}
							}
							if ($acl->isAllowed('view_' . $listingTypeID . '_contact_info', $contractInfo['id'], 'contract')) {
								$permissionParam = $acl->getPermissionParams('view_' . $listingTypeID . '_contact_info', $contractInfo['id'], 'contract');
								$contractInfo['availableContactViews'][$listingTypeID]['name'] = $listingType['name'];
								$contractInfo['availableContactViews'][$listingTypeID]['numOfViews'] = SJB_ContractManager::getNumbeOfPagesViewed($userSID, array($contractInfo['id']), 'view_' . $listingTypeID . '_contact_info');
								if (empty($permissionParam)) {
									$contractInfo['availableContactViews'][$listingTypeID]['count'] = 'unlimited';
									$contractInfo['availableContactViews'][$listingTypeID]['viewsLeft'] = 'unlimited';
								}
								else {
									$contractInfo['availableContactViews'][$listingTypeID]['count'] = $permissionParam;
									$contractInfo['availableContactViews'][$listingTypeID]['viewsLeft'] = $contractInfo['availableContactViews'][$listingTypeID]['count'] - $contractInfo['availableContactViews'][$listingTypeID]['numOfViews'];
								}
							}
						}
	
						$contracts[$key] = $contractInfo;
						$contracts[$key]['product'] = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
					}
					$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
					$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']);
					SJB_System::setGlobalTemplateVariable('wikiExtraParam', $userGroupInfo['id']);
					$tp->assign("user_group_info", $userGroupInfo);
					$tp->assign('contracts', $contracts);
					$tp->assign('user_sid', $userSID);
					$tp->display('user_products.tpl');
					break;
	
				case 'user_product':
					$errors = array();
					if ($action == 'change') {
						$contractSIDs = SJB_Request::getVar('contract_sids', array());
						$deletedContracts = false;
						foreach ($contractSIDs as $contractSID => $val) {
							if (SJB_ContractManager::deleteContract($contractSID, $userSID)) 
								$deletedContracts = true;
						}
						if ($deletedContracts) {
							$tp->assign('deleted', 'yes');
							$viewContractInfo = false;
						}
						else
							$tp->assign('deleted', 'no');
					}
					if ($action == 'changeExpirationDate') {
						$expiredDates = SJB_Request::getVar('expired_date', array());
						foreach ($expiredDates as $contractID => $expired_date) {
							if ($expired_date && $expired_date != 'Never Expire'){
								if (!SJB_I18N::getInstance()->isValidDate($expired_date))
										$errors['WRONG_DATE_FORMAT'] = 1;
								else {
									$expired_date = SJB_I18N::getInstance()->getInput('date', $expired_date);
									SJB_DB::query("UPDATE `contracts` SET `expired_date` = ?s WHERE `id`=?n", $expired_date, $contractID);
								}
							}
							else
								SJB_DB::query("UPDATE `contracts` SET `expired_date` = NULL WHERE `id`=?n", $contractID);
						}
					}
					$i18n = SJB_ObjectMother::createI18N();
					$contractsInfo = array();
					if ($viewContractInfo) {
						$contracts = SJB_ContractManager::getAllContractsInfoByUserSID($userSID);
						foreach ($contracts as $key => $contract) {
							$contractsInfo[$key] = $contract;
							$contractsInfo[$key]['extra_info'] = unserialize($contract['serialized_extra_info']);
							$contractsInfo[$key]['countListings'] = $contract['number_of_postings'];
							$contractsInfo[$key]['product'] = SJB_ProductsManager::getProductInfoBySID($contract['product_sid']);
							$contractsInfo[$key]['expired_date'] = empty($contract['expired_date']) ? '' : $i18n->getDate($contract['expired_date']);
							$contractsInfo[$key]['creation_date'] = $i18n->getDate($contract['creation_date']);
						}
					}
					$tp->assign('errors', $errors);
					$tp->assign('contractsInfo', $contractsInfo);
					$tp->assign('countContracts', count($contractsInfo));
					$tp->assign('user_sid', $userSID);
					$tp->assign('user', $user);
					$tp->display('user_product.tpl');
					break;
			}
		}
		else {
			$errors['USER_DOES_NOT_EXIST'] = 1;
			$tp->assign('errors', $errors);
			$tp->display('../users/error.tpl');
		}
	}
}