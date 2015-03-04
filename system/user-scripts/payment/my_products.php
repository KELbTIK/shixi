<?php

class SJB_Payment_MyProducts extends SJB_Function
{
	public $errors;

	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('subuser_manage_subscription');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		if (!SJB_UserManager::isUserLoggedIn()) {
			$errors['NOT_LOGGED_IN'] = true;
			$tp->assign("ERRORS", $errors);
			$tp->display("../classifieds/error.tpl");
			return;
		}
		if (SJB_Request::getVar('subscriptionComplete') == 'false') {
			$this->errors['SUBSCRIPTION_IS_FAIL'] = 1;
		}
		$currentUser = SJB_UserManager::getCurrentUser();

		$contractsInfo = SJB_ContractManager::getAllContractsInfoByUserSID($currentUser->getSID());
		$cancelRecurringContract = SJB_Request::getVar('cancelRecurringContract', false);
		if ($cancelRecurringContract)
			$tp->assign('cancelRecurringContractId', $cancelRecurringContract);

		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		$contractSIDs = array();
		foreach ($contractsInfo as $key => $contractInfo) {
			$contractInfo['extra_info'] = unserialize($contractInfo['serialized_extra_info']);
			$contractInfo['avalaibleViews'] = array();
			$contractInfo['avalaibleContactViews'] = array();
			$contractInfo['listingAmount'] = array();
			foreach ($listingTypes as $listingType) {
				$listingTypeID = $listingType['id'];
				if ($this->acl->isAllowed('view_' . $listingTypeID . '_details', $contractInfo['id'], 'contract')) {
					$contractInfo['avalaibleViews'][$listingTypeID]['name'] = $listingType['name'];
					$permissionParam = $this->acl->getPermissionParams('view_' . $listingTypeID . '_details', $contractInfo['id'], 'contract');
					$contractInfo['avalaibleViews'][$listingTypeID]['numOfViews'] = SJB_ContractManager::getNumbeOfPagesViewed($currentUser->getSID(), array($contractInfo['id']), false, $listingType['sid']);
					if (empty($permissionParam)) {
						$contractInfo['avalaibleViews'][$listingTypeID]['count'] = 'unlimited';
						$contractInfo['avalaibleViews'][$listingTypeID]['viewsLeft'] = 'unlimited';
					}
					else {
						$contractInfo['avalaibleViews'][$listingTypeID]['count'] = $permissionParam;
						$contractInfo['avalaibleViews'][$listingTypeID]['viewsLeft'] = $permissionParam - $contractInfo['avalaibleViews'][$listingTypeID]['numOfViews'];
					}
				}
				if ($this->acl->isAllowed('post_' . $listingTypeID, $contractInfo['id'], 'contract')) {
					$contractInfo['listingAmount'][$listingTypeID]['name'] = $listingType['name'];
					$permissionParam = $this->acl->getPermissionParams('post_' . $listingTypeID, $contractInfo['id'], 'contract');
					$contractInfo['listingAmount'][$listingTypeID]['numPostings'] = $contractInfo['number_of_postings'];
					if (empty($permissionParam)) {
						$contractInfo['listingAmount'][$listingTypeID]['count'] = 'unlimited';
						$contractInfo['listingAmount'][$listingTypeID]['listingsLeft'] = 'unlimited';
					}
					else {
						$contractInfo['listingAmount'][$listingTypeID]['count'] = $permissionParam;
						$contractInfo['listingAmount'][$listingTypeID]['listingsLeft'] = max($contractInfo['listingAmount'][$listingTypeID]['count'] - $contractInfo['listingAmount'][$listingTypeID]['numPostings'], 0);
					}
				}
				if ($this->acl->isAllowed('view_' . $listingTypeID . '_contact_info', $contractInfo['id'], 'contract')) {
					$permissionParam = $this->acl->getPermissionParams('view_' . $listingTypeID . '_contact_info', $contractInfo['id'], 'contract');
					$contractInfo['avalaibleContactViews'][$listingTypeID]['name'] = $listingType['name'];
					$contractInfo['avalaibleContactViews'][$listingTypeID]['numOfViews'] = SJB_ContractManager::getNumbeOfPagesViewed($currentUser->getSID(), array($contractInfo['id']), 'view_' . $listingTypeID . '_contact_info');
					if (empty($permissionParam)) {
						$contractInfo['avalaibleContactViews'][$listingTypeID]['count'] = 'unlimited';
						$contractInfo['avalaibleContactViews'][$listingTypeID]['viewsLeft'] = 'unlimited';
					}
					else {
						$contractInfo['avalaibleContactViews'][$listingTypeID]['count'] = $permissionParam;
						$contractInfo['avalaibleContactViews'][$listingTypeID]['viewsLeft'] = $contractInfo['avalaibleContactViews'][$listingTypeID]['count'] - $contractInfo['avalaibleContactViews'][$listingTypeID]['numOfViews'];
					}
				}
			}

			$contractsInfo[$key] = $contractInfo;
			$contractsInfo[$key]['product_info'] = SJB_ProductsManager::getProductInfoBySID($contractInfo['extra_info']['product_sid']);
			$contractSIDs[] = $contractInfo['id'];
		}

		$statistics = array();
		foreach ($listingTypes as $listingType) {
			$listingTypeID = $listingType['id'];
			if ($this->acl->isAllowed('view_' . $listingTypeID . '_details')) {
				$statistics['avalaibleViews'][$listingTypeID]['name'] = $listingType['name'];
				$permissionParam = $this->acl->getPermissionParams('view_' . $listingTypeID . '_details');
				$statistics['avalaibleViews'][$listingTypeID]['numOfViews'] = SJB_ContractManager::getNumbeOfPagesViewed($currentUser->getSID(), $contractSIDs, false, $listingType['sid']);
				if (empty($permissionParam)) {
					$statistics['avalaibleViews'][$listingTypeID]['count'] = 'unlimited';
					$statistics['avalaibleViews'][$listingTypeID]['viewsLeft'] = 'unlimited';
				}
				else {
					$statistics['avalaibleViews'][$listingTypeID]['count'] = $permissionParam;
					$statistics['avalaibleViews'][$listingTypeID]['viewsLeft'] = $statistics['avalaibleViews'][$listingTypeID]['count'] - $statistics['avalaibleViews'][$listingTypeID]['numOfViews'];
				}
			}
			if ($this->acl->isAllowed('post_' . $listingTypeID)) {
				$statistics['listingAmount'][$listingTypeID]['name'] = $listingType['name'];
				$permissionParam = $this->acl->getPermissionParams('post_' . $listingTypeID);
				$statistics['listingAmount'][$listingTypeID]['numPostings'] = SJB_ContractManager::getListingsNumberByContractSIDsListingType($contractSIDs, $listingTypeID);
				if (empty($permissionParam)) {
					$statistics['listingAmount'][$listingTypeID]['count'] = 'unlimited';
					$statistics['listingAmount'][$listingTypeID]['listingsLeft'] = 'unlimited';
				}
				else {
					$statistics['listingAmount'][$listingTypeID]['count'] = $permissionParam;
					$statistics['listingAmount'][$listingTypeID]['listingsLeft'] = $statistics['listingAmount'][$listingTypeID]['count'] - $statistics['listingAmount'][$listingTypeID]['numPostings'];
				}
			}
			if ($this->acl->isAllowed('view_' . $listingTypeID . '_contact_info')) {
				$permissionParam = $this->acl->getPermissionParams('view_' . $listingTypeID . '_contact_info');
				$statistics['avalaibleContactViews'][$listingTypeID]['name'] = $listingType['name'];
				$statistics['avalaibleContactViews'][$listingTypeID]['numOfViews'] = SJB_ContractManager::getNumbeOfPagesViewed($currentUser->getSID(), $contractSIDs, 'view_' . $listingTypeID . '_contact_info');
				if (empty($permissionParam)) {
					$statistics['avalaibleContactViews'][$listingTypeID]['count'] = 'unlimited';
					$statistics['avalaibleContactViews'][$listingTypeID]['viewsLeft'] = 'unlimited';
				}
				else {
					$statistics['avalaibleContactViews'][$listingTypeID]['count'] = $this->acl->getPermissionParams('view_' . $listingTypeID . '_contact_info');
					$statistics['avalaibleContactViews'][$listingTypeID]['viewsLeft'] = $statistics['avalaibleContactViews'][$listingTypeID]['count'] - $statistics['avalaibleContactViews'][$listingTypeID]['numOfViews'];
				}

			}
		}
		$productsFailedList = urldecode(SJB_Request::getVar('failedProducts'));
		if ($productsFailedList <> '') {
			$productsFailedArray = explode(',', $productsFailedList);
			if (!empty($productsFailedArray)){
				$tp->assign('productsFailed', $productsFailedArray);
			}
		}

		$tp->assign('statistics', $statistics);
		$tp->assign("contracts_info", $contractsInfo);
		$tp->assign('errors', $this->errors);
		$tp->display("my_products.tpl");
	}
}
