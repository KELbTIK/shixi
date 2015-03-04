<?php

class SJB_ContractManager
{
	public static function deleteContract($contract_id, $user_sid = false)
	{
        $contract = new SJB_Contract( array('contract_id' => $contract_id, 'user_sid' => $user_sid) );
        SJB_ContractManager::deleteContractIDFromNotificationSended($contract_id);
        if ($contract->isFeaturedProfile()) {
	        $allContracts = self::getAllContractsInfoByUserSID($user_sid);
	        $featured = 0;
	        foreach ($allContracts as $userContract) {
	        	if ($userContract['id'] != $contract_id) {
		        	$userContract = new SJB_Contract( array('contract_id' => $userContract['id'], 'user_sid' => $user_sid) );
		        	if ($userContract->isFeaturedProfile()) {
		        	 	$featured = 1; 
		        	 	break;
		        	 }
	        	}
	        }
	        if ($featured == 0) 
	        	SJB_UserManager::removeFromFeaturedBySID($user_sid);
        }
        if ($contract->product_type == 'banners') {
        	$banner = new SJB_Banners();
        	$contractBannerID = $banner->getBannerIDByContract($contract_id);
        	if ($contractBannerID)
        		$banner->deleteBanner($contractBannerID);
        }
        $permissions = SJB_Acl::getInstance();
        $permissions->clearPermissions('contract', $contract_id);
        return $contract->delete();  
    }
    
	public static function deleteAllContractsByUserSID($user_sid)
	{
		$userContracts = SJB_DB::query("SELECT `id`, `gateway_id`, `recurring_id` FROM `contracts` WHERE `user_sid` = ?n", $user_sid);
		foreach ($userContracts as $contract) {
			// 'paypal_standard' != $contract['gateway_id']  - redirect on paypal
			// cancel recurring
			if ( !empty($contract['gateway_id']) && !empty($contract['recurring_id']) && 'paypal_standard' != $contract['gateway_id'] && $gateway = SJB_PaymentGatewayManager::getObjectByID($contract['gateway_id'], true) )
			{
				$gateway->cancelSubscription($contract['recurring_id']);
			}
			SJB_ContractManager::deleteContractIDFromNotificationSended($contract['id']);
		}
		return SJB_DB::query("DELETE FROM `contracts` WHERE `user_sid`=?n", $user_sid);
    }
    
   	public static function deleteAllContractsByRecurringId($recurringId, $invoiceID = false)
	{
		$where = '';
    	if ($invoiceID)
    		$where = " AND `invoice_id` = '{$invoiceID}' ";
		$contracts = SJB_DB::query("SELECT id FROM `contracts` WHERE `recurring_id` = ?s {$where}", $recurringId);
		foreach ($contracts as $contract) {
			SJB_ContractManager::deleteContractIDFromNotificationSended($contract['id']);
		}
        return SJB_DB::query("DELETE FROM `contracts` WHERE `recurring_id` = ?s {$where}", $recurringId);
    }
    
    public static function deletePendingContractByInvoiceID($invoiceID, $userSID, $productSID)
    {
    	$contracts = SJB_DB::query("SELECT id FROM `contracts` WHERE `invoice_id` = ?s AND `user_sid` = ?s AND `product_sid` = ?n AND `status` = 'pending'", $invoiceID, $userSID, $productSID);
    	foreach ($contracts as $contract) {
			SJB_ContractManager::deleteContractIDFromNotificationSended($contract['id']);
		}
		 return SJB_DB::query("DELETE FROM `contracts` WHERE `invoice_id` = ?s AND `user_sid` = ?s AND `product_sid` = ?n AND `status` = 'pending'", $invoiceID, $userSID, $productSID);
    }
    
    public static function getContractSIDByRecurringId($recurringId, $invoiceID = false) 
    {
    	$where = '';
    	if ($invoiceID)
    		$where = " AND `invoice_id` = '{$invoiceID}' ";
    	$contract = SJB_DB::queryValue("SELECT id FROM `contracts` WHERE `recurring_id` = ?s $where", $recurringId);
		if (empty($contract))
			return false;

		return $contract;
    }
	
	public static function getExpiredContractsID()
	{
		$expired_contracts = SJB_DB::query("SELECT id FROM contracts WHERE expired_date < NOW() AND expired_date != '0000-00-00'");
		$contracts_id = array();
		foreach ($expired_contracts as $expired_contract) {			
			$contracts_id[] = $expired_contract['id'];			
		}
		return $contracts_id;
	}
	
	public static function getContractsIDByDaysLeftToExpired($user_sid, $days = 0)
	{
		$expired_contracts = SJB_DB::query("SELECT id FROM contracts WHERE expired_date < DATE_ADD( NOW(), INTERVAL ?w DAY ) AND expired_date != '0000-00-00' AND `user_sid` = ?n", $days, $user_sid);
		$contracts_id = array();
		foreach ($expired_contracts as $expired_contract) {			
			$contracts_id[] = $expired_contract['id'];			
		}
		return $contracts_id;
	}
	
	
	/**
	 * Check contract to have sended remind notifications about expiration date.
	 * Look for contract ID in 'contract_notifications_send' table,
	 *
	 * @param integer $contractID
	 * @return boolean
	 */
	public static function isContractNotificationSended($contractID)
	{
		$result = SJB_DB::query("SELECT * FROM `notifications_sended` WHERE `object_type` = 'contract' AND `object_sid` = ?n", $contractID);
		return !empty($result);
	}
	
	
	public static function deleteContractIDFromNotificationSended($contractSID)
	{
		return SJB_DB::query("DELETE FROM `notifications_sended` WHERE `object_type` = 'contract' AND `object_sid` = ?n", $contractSID);
	}
	
	/**
	 * Save contract ID in `contract_notifications_send` table.
	 *
	 * @param integer|array $contractSID
	 * @return boolean
	 */
	public static function saveContractIDAsSendedNotificationsTable($contractSID)
	{
		$result = false;
		
		if (is_integer($contractSID)) {
			$result = SJB_DB::query("INSERT INTO `notifications_sended` SET `object_sid` = ?n, `object_type` = 'contract'", $contractSID);
		} elseif ( is_array($contractSID)) {
			$insertValues = array();
			foreach ($contractSID as $value) {
				if (!is_numeric($value))
					continue;
				$insertValues[] = "('contract', $value)";
			}
			
			$insert = implode(",", $insertValues);
			$result = SJB_DB::query("INSERT INTO `notifications_sended` (`object_type`, `object_sid`) VALUES $insert");
		}

		return $result !== false;
	}
    
    public static function getInfo($contract_id)
    {
    	if ($contract_id == 0) {
    		return false;
    	}
        $contractInfo = SJB_ContractSQL::selectInfoByID($contract_id);

        if ($contractInfo && empty($contractInfo['serialized_extra_info']) ) {
        	$product= SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
        	$contractInfo['serialized_extra_info'] = $product['serialized_extra_info'];
        }

        return $contractInfo;
    }
    
    public static function getAllContractsInfoByUserSID($user_sid)
    {
    	if ($user_sid == 0) {
    		return false;
    	}
        $contractsInfo = SJB_ContractSQL::selectInfoByUserSID($user_sid);

        foreach($contractsInfo as $key => $contractInfo) {
	        if ($contractInfo && empty($contractInfo['serialized_extra_info']) ) {
	        	$product = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
	        	$contractInfo['serialized_extra_info'] = $product['serialized_extra_info'];
	        	$contractsInfo[$key] = $contractInfo;
	        }
        }
        return $contractsInfo;
    }
    
    public static function getAllContractsSIDsByUserSID($user_sid)
    {
    	if ($user_sid == 0) {
    		return false;
    	}
        $contractsInfo = SJB_ContractSQL::selectInfoByUserSID($user_sid);
		$result = array();
        foreach($contractsInfo as $contractInfo) {
			$result[] = $contractInfo['id'];
        }
        return $result;
    }
    
	public static function getExtraInfoByID($contract_id)
	{
    	$extra_info = SJB_DB::queryValue("SELECT serialized_extra_info FROM contracts WHERE id = ?n", $contract_id);
    	$contract_extra_info = false;
    	if (!empty($extra_info))
    		$contract_extra_info = unserialize($extra_info);

		return $contract_extra_info;
    }
    
	public static function getPageAccessByUserContracts($contracts_id, $pageID)
	{
	    $permission = '';
	    $pageAccess = array();
	    switch ($pageID) {
	        case '/search-resumes/':
	            $permission = 'open_resume_search_form';
	            break;
	        case '/search-results-resumes/':
	            $permission = 'view_resume_search_results';
	            break;
	        case '/display-resume/':
	            $permission = 'view_resume_details';
	            break;
	        case '/find-jobs/':
	            $permission = 'open_job_search_form';
	            break;
	        case '/search-results-jobs/':
	            $permission = 'view_job_search_results';
	            break;
	        case '/display-job/':
	            $permission = 'view_job_details';
	            break;
	        default:
	            return array();
	            break;
	    }
	    $acl = SJB_Acl::getInstance();
	    
	    if ($acl->isAllowed($permission) && in_array($acl->getPermissionParams($permission), array('', '0')))
	        return $pageAccess;
	    
	    foreach ($contracts_id as $contractId) {
            if ($acl->isAllowed($permission, $contractId, 'contract')) {
            	$params = $acl->getPermissionParams($permission, $contractId, 'contract');
                if (isset($pageAccess[$pageID]['count_views'])) {
                    if (is_numeric($params)) {
                        $pageAccess[$pageID]['count_views'] += $params;
                        $pageAccess[$pageID]['contract_id'][] = $contractId;
                    }
                }
                else {
                	if (!is_numeric($params))
                		$params = 0;
                    $pageAccess[$pageID]['count_views'] = $params;
                    $pageAccess[$pageID]['contract_id'][] = $contractId;
                }
            }	     
	    }
		return $pageAccess;
    }

	public static function getNumbeOfPagesViewed($user_id, $contractsID, $pageID = false, $listingTypeSID = false)
	{
		if (empty($contractsID)) {
			return 0;
		}
		if ($listingTypeSID) {
			return SJB_DB::queryValue("SELECT count(param) FROM `page_view` WHERE id_user = ?s AND listing_type_sid = ?n AND `contract_id` in (?l)", $user_id, $listingTypeSID, $contractsID);
		}
		return SJB_DB::queryValue("SELECT count(param) FROM `page_view` WHERE id_user = ?s AND id_pages = ?s AND `contract_id` in (?l)", $user_id, $pageID, $contractsID);
	}
    
    public static function removeSubscriptionId($subscriptionId)
    {
    	SJB_DB::query('UPDATE `contracts` SET `recurring_id` = \'\', `gateway_id` = \'\' WHERE `recurring_id` = ?s', $subscriptionId);
    }
    
    public static function isPageViewed($userSID, $pageID, $param)
    {
    	$result = SJB_DB::query("SELECT `param` FROM `page_view` WHERE `id_user` = ?s AND `id_pages` = ?s AND `param` = ?n", $userSID, $pageID, $param);
    	return $result?true:false;
    }
    
    public static function addViewPage($userSID, $pageID, $param, $contractID)
    {
    	return SJB_DB::query("INSERT INTO page_view (`id_user` ,`id_pages`, `param`, `contract_id`) VALUES ( ?s, ?s, ?s, ?s)", $userSID, $pageID, $param, $contractID);
    }
    
    public static function getAllContractsByProductSID($productSID)
    {
    	 return SJB_DB::query("SELECT `id` FROM `contracts` WHERE `product_sid` = ?n",$productSID);
    }
    
	public static function getContractQuantityByProductSID($productSID)
	{		 
		$result = SJB_DB::queryValue("SELECT COUNT( DISTINCT users.sid)
							FROM users 
							INNER JOIN contracts ON users.sid = contracts.user_sid 
							INNER JOIN products ON products.sid = contracts.product_sid 
							WHERE products.sid=?n", $productSID);
		
		return $result ? $result : 0;
	}
	
	public static function updateExpirationPeriod($contractSID)
	{
		$contractInfo = self::getInfo($contractSID);
		if ($contractInfo) {
			$productInfo = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
			$product = new SJB_Product($productInfo, $productInfo['product_type']);
			$is_recurring = !empty($productInfo['is_recurring'])?$productInfo['is_recurring']:false;
			$expirationPeriod = $product->getExpirationPeriod();
			if ($expirationPeriod) {
				if ($is_recurring) // Для рекьюринг планов, делаем запас для проплаты в 1 день
					$expirationPeriod++;
				$expired_date = date("Y-m-d", strtotime("+" . $expirationPeriod . " day"));
				SJB_DB::query("UPDATE `contracts` SET `expired_date` = ?s WHERE `id` = ?n", $expired_date, $contractSID);
			}
		}
	}
	
	public static function getListingsNumberByContractSIDsListingType($contractsSIDs, $listingTypeID)
	{
		$acl = SJB_Acl::getInstance();
		$result = 0;
		foreach ($contractsSIDs as $contractSID) {
			if ($acl->isAllowed('post_' . $listingTypeID, $contractSID, 'contract')) {
				$contractInfo = self::getInfo($contractSID);
				$result += $contractInfo['number_of_postings'];
			}
		}
		return $result;
	}
	
	public static function getContractIDByInvoiceID($invoiceID, $productSID, $userSID)
	{
		$contractID = SJB_DB::queryValue("SELECT `id` FROM `contracts` WHERE `product_sid` = ?n AND `invoice_id` = ?s AND `user_sid` = ?n",$productSID, $invoiceID, $userSID);
		return $contractID ? $contractID : false;
	}

	public static function activateContract($contract_id, $user_sid = false)
	{
		$contractInfo = self::getInfo($contract_id);
		$number_of_listings = isset($contractInfo['number_of_postings']) ? $contractInfo['number_of_postings'] : 0;
		$product_sid = isset($contractInfo['product_sid']) ? $contractInfo['product_sid'] : 0;
		SJB_Acl::copyPermissions($product_sid, $contract_id, $number_of_listings);
		SJB_DB::query("UPDATE `contracts` SET `status` = 'active' WHERE `id` = ?n", $contract_id);
	}
}
