<?php

class SJB_ContractSQL
{
	
	public static function selectInfoByID($id)
	{
		$result = SJB_DB::query("SELECT * FROM contracts WHERE id=?n", $id);
		return array_pop($result);
	}
	
	public static function selectInfoByUserSID($user_sid)
	{
		return SJB_DB::query("SELECT * FROM contracts WHERE user_sid=?n ORDER BY `id` DESC", $user_sid);
	}
	
	public static function insert($contract_info)
	{
		$contract_id = $contract_info['contract_id'];
		if (!empty($contract_id)) {
			if (!empty($contract_info['expired_date'])) {
				return SJB_DB::query("UPDATE `contracts` SET `product_sid` = ?n, `creation_date` = ?s, `expired_date` = ?s, `price` = ?s, `status` = ?s WHERE `id` = ?n",
					$contract_info['product_sid'], $contract_info['creation_date'], $contract_info['expired_date'], $contract_info['price'], $contract_info['status'], $contract_id);
			} else {
				return SJB_DB::query("UPDATE `contracts` SET `product_sid` = ?n, `creation_date` = ?s, `price` = ?s, `status` = ?s WHERE `id` = ?n",
					$contract_info['product_sid'],  $contract_info['creation_date'], $contract_info['price'], $contract_info['status'], $contract_id);
			}
		}
		else {
			if (!empty($contract_info['expired_date'])) {
				return SJB_DB::query("INSERT INTO `contracts`(`user_sid`, `product_sid`, `creation_date`, `expired_date`, `price`, `recurring_id`, `gateway_id`, `invoice_id`, `status`) VALUES(?n, ?n, ?s, ?s, ?s, ?s, ?s, ?s, ?s)",
					$contract_info['user_sid'], $contract_info['product_sid'],  $contract_info['creation_date'], $contract_info['expired_date'], $contract_info['price'], $contract_info['recurring_id'], $contract_info['gateway_id'], $contract_info['invoice_id'], $contract_info['status']);
			} else {
				return SJB_DB::query("INSERT INTO `contracts`(`user_sid`, `product_sid`, `creation_date`, `price`, `recurring_id`, `gateway_id`, `invoice_id`, `status`) VALUES(?n, ?n, ?s, ?s, ?s, ?s, ?s, ?s)",
					$contract_info['user_sid'], $contract_info['product_sid'],  $contract_info['creation_date'], $contract_info['price'], $contract_info['recurring_id'], $contract_info['gateway_id'], $contract_info['invoice_id'], $contract_info['status']);
			}
		}
	}
	
	public static function updateContractExtraInfoByProductSID($contract)
	{
		$productSID = $contract->product_sid;
		$numberOfListings = $contract->number_of_listings;
		$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
		if ($numberOfListings && !empty($productExtraInfo['volume_based_pricing'])) {
			$volumeBasedPricing = $productExtraInfo['volume_based_pricing'];
			unset($productExtraInfo['volume_based_pricing']);
			if (!empty($volumeBasedPricing['listings_range_from'])) {
				for ($i = 1; $i <= count($volumeBasedPricing['listings_range_from']); $i++) {
					if ($numberOfListings >= $volumeBasedPricing['listings_range_from'][$i] && $numberOfListings <= $volumeBasedPricing['listings_range_to'][$i]){
						$productExtraInfo['listings_range_from'] = $volumeBasedPricing['listings_range_from'][$i];
						$productExtraInfo['listings_range_to'] = $volumeBasedPricing['listings_range_to'][$i];
						$productExtraInfo['price_per_unit'] = $volumeBasedPricing['price_per_unit'][$i];
						$productExtraInfo['renewal_price'] = $volumeBasedPricing['renewal_price_per_listing'][$i];
						$productExtraInfo['number_of_listings'] = $numberOfListings;
						break;
					}
				}
			}
		}
		SJB_DB::query("UPDATE `contracts` SET `serialized_extra_info` = ?s WHERE `id` = ?n", serialize($productExtraInfo), $contract->id);
	}
	
	public static function delete($contract_id)
	{
		return SJB_DB::query("DELETE FROM `contracts` WHERE `id`=?s", $contract_id);
	}
	
	public static function deletePageViews($user_sid)
	{
		return SJB_DB::query("DELETE FROM `page_view` WHERE `id_user`=?n", $user_sid);
	}
	
	public static function updateAllContractsExtraInfoByProductSID($contractsSIDs, $productSID)
	{
		$productExtraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
		$currentPermissions = array();

	    SJB_DB::query("delete from `permissions` where `type` = 'contract' and `role` in ({$contractsSIDs})");
		foreach (explode(',', $contractsSIDs) as $contractSID) {
			$contractInfo = self::selectInfoByID($contractSID);
			if ($contractInfo['status'] == 'active')
				SJB_DB::query("INSERT INTO `permissions` (`type`, `role`, `name`, `value`, `params`, `message`)
								SELECT 'contract', ?s, `name`, `value`, `params`, `message`
								FROM `permissions`
								WHERE `type` = 'product' AND `role` = ?s", $contractSID, $productSID);

            $extraInfo = !empty($contractInfo['serialized_extra_info'])?unserialize($contractInfo['serialized_extra_info']):array();
            $contractExtraInfo = $productExtraInfo;
            if (!empty($extraInfo['pricing_type']) && $extraInfo['pricing_type'] == 'volume_based') {
            	$numberOfListings = $extraInfo['number_of_listings'];
				$numberOfPostings[$contractSID] = $numberOfListings;
            	$volumeBasedPricing = !empty($productExtraInfo['volume_based_pricing'])?$productExtraInfo['volume_based_pricing']:array();
            	if (!empty($volumeBasedPricing['listings_range_from'])) {
					for ($i = 1; $i <= count($volumeBasedPricing['listings_range_from']); $i++) {
						if ($numberOfListings >= $volumeBasedPricing['listings_range_from'][$i] && $numberOfListings <= $volumeBasedPricing['listings_range_to'][$i]){
							$contractExtraInfo['listings_range_from'] = $volumeBasedPricing['listings_range_from'][$i];
							$contractExtraInfo['listings_range_to'] = $volumeBasedPricing['listings_range_to'][$i];
							$contractExtraInfo['price_per_unit'] = $volumeBasedPricing['price_per_unit'][$i];
							$contractExtraInfo['renewal_price'] = $volumeBasedPricing['renewal_price_per_listing'][$i];
							$contractExtraInfo['number_of_listings'] = $numberOfListings;
							break;
						}
					}
					unset($contractExtraInfo['volume_based_pricing']);
				}
            }
            SJB_DB::query("UPDATE `contracts` SET `serialized_extra_info` = ?s WHERE `id` = ?n", serialize($contractExtraInfo), $contractSID);
		}
		if (isset($productExtraInfo['pricing_type'])) 
			$currentPermissions =  SJB_DB::query("SELECT * FROM `permissions` WHERE `type` = 'contract' AND `role` in ({$contractsSIDs})");
			
        foreach ($currentPermissions as $key => $permission) {
       		$currentPermissions[$permission['name']] = $permission;
       		unset($currentPermissions[$key]);
       	}

		if ($currentPermissions && !empty($numberOfPostings) || isset($productExtraInfo['number_of_listings'])) {
			foreach (explode(',', $contractsSIDs) as $contractSID) {
				$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
			    foreach ($listingTypes as $listingType) {
	        		if (isset($currentPermissions['post_'.strtolower($listingType['id'])])) {
	        			$permission = $currentPermissions['post_'.strtolower($listingType['id'])];
	        			if ($permission['value'] == 'allow') {
	        				SJB_DB::query("UPDATE `permissions` SET `params` = ?n WHERE `type` = 'contract' AND  `role` = ?s AND `name` = ?s", !empty($numberOfPostings) ? $numberOfPostings[$contractSID] : $productExtraInfo['number_of_listings'], $contractSID, 'post_'.strtolower($listingType['id']));
	        			}
	        		}
	        	}
			}
		}
	}
	
	public static function incrementPostingsNumber($contractSID)
	{
		return SJB_DB::query("UPDATE `contracts` SET `number_of_postings` = `number_of_postings` + 1 WHERE `id` = ?n", $contractSID);
	}

	public static function updatePostingsNumber($contractSID, $postingsNumber)
	{
		return SJB_DB::query("UPDATE `contracts` SET `number_of_postings` = {$postingsNumber} WHERE `id` = ?n", $contractSID);
	}
}

