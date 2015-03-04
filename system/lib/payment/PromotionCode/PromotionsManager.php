<?php

class SJB_PromotionsManager extends SJB_ObjectManager
{
	const STATISTIC_EVENT = 'promotionUsed';

	public static function getAllPromotions()
	{
		$result = SJB_DB::query('SELECT `sid` FROM `promotions`');
		$promotions = array();
		foreach ($result as $promotion) 
			$promotions[] = self::getObjectBySID($promotion['sid']);
		
		return $promotions;
	}
	
	public static function getAllPromotionsInfo()
	{
		SJB_DB::query("UPDATE `promotions` SET `active` = 0 WHERE `active` = 1 AND `end_date` < NOW() - INTERVAL 1 DAY");
		$result = SJB_DB::query('SELECT `sid`, if(`end_date` < NOW() - INTERVAL 1 DAY, 2, `active`) as `active` FROM `promotions` ORDER BY `sid` DESC');
		$promotions = array();
		foreach ($result as $key => $promotion) {
			$promotions[$key] = SJB_ObjectDBManager::getObjectInfo('promotions', $promotion['sid']);
			$promotions[$key]['active'] = $promotion['active'];
		}
		
		return $promotions;
	}
	
	public static function savePromotionCode($promotionCode)
	{
		parent::saveObject('promotions', $promotionCode);
	}
	
	public static function getObjectBySID($sid)
	{
		$promotionCodeInfo = SJB_ObjectDBManager::getObjectInfo('promotions', $sid);
		
		if (!is_null($promotionCodeInfo)) {
			$promotionCode = new SJB_Promotions($promotionCodeInfo);
			$promotionCode->setSID($promotionCodeInfo['sid']);
			return $promotionCode;
		}
		return null;
	}
	
	public static function getCodeInfoBySID($sid)
	{
		$code_info = SJB_ObjectDBManager::getObjectInfo('promotions', $sid);
		return $code_info;
	}

	public static function deleteCodeBySID($sid)
	{
		parent::deleteObject('promotions', $sid);
	}
	
	public static function activateCodeBySID($sid)
	{
		return SJB_DB::query("UPDATE `promotions` SET `active` = 1 WHERE `sid` = ?n", $sid);
	}
	
	public static function deactivateCodeBySID($sid)
	{
		return SJB_DB::query("UPDATE `promotions` SET `active` = 0 WHERE `sid` = ?n", $sid);
	}
	
	public static function getCodeSIDByName($codeName)
	{
		$result = SJB_DB::queryValue("SELECT `sid` FROM `promotions` WHERE `code` = ?s LIMIT 1", $codeName);
		return $result ? $result['sid'] : false;
	}
	
	public static function getCodeInfoByName($codeName)
	{
		$result = SJB_DB::query("SELECT * FROM `promotions` WHERE `code` = ?s LIMIT 1", $codeName);
		$result = array_pop($result);
		return $result ? $result : array();
	}

	public static function checkCode($codeName, $productSIDs)
	{
		if (empty($codeName)) 
			return false;
			
		$where = " AND (`product_sid` = '' ";
		foreach ($productSIDs as $productSID) {
			$where .= " OR FIND_IN_SET({$productSID}, `product_sid`) ";
		}
		$where .= ")";
		$result = SJB_DB::query("SELECT * FROM `promotions` WHERE `active` = 1 AND (`start_date` IS NULL OR `start_date` <= NOW()) AND (`end_date`IS NULL OR `end_date` > NOW() - INTERVAL 1 DAY) AND `code` = ?s {$where}  LIMIT 1", $codeName);
		return $result?array_pop($result):false;
	}

	/**
	 * @param array $promotionCode
	 * @param int $invoiceSID
	 * @param int $userSID
	 */
	public static function addCodeToHistory($promotionCode = array(), $invoiceSID, $userSID)
	{
		if (!empty($promotionCode)) {
			$codeInfo = self::getCodeInfoBySID($promotionCode['sid']);

			$products = SJB_Array::get($promotionCode, 'products');
			$productsSIDs = implode(',', $products);

			$amounts = SJB_Array::get($promotionCode, 'amount');
			$amount = array_sum($amounts);

			$query = 'INSERT INTO `promotions_history` SET `user_sid` = ?n, `code_sid` = ?n, `invoice_sid` = ?n, `product_sid` = ?s,
						`date` = NOW(), `code_info` = ?s, `amount` = ?f
						ON DUPLICATE KEY UPDATE `product_sid` = ?s, `date` = NOW(), `code_info` = ?s, `amount` = ?f';

			SJB_DB::queryExec($query, $userSID, $codeInfo['sid'], $invoiceSID, $productsSIDs, serialize($codeInfo), $amount, $productsSIDs, serialize($codeInfo), $amount);
		}
	}

	public static function updatePaymentSID($codeHistorySID, $paymentSID)
	{
		SJB_DB::query("UPDATE `promotions_history` SET `payment_id` = ?n WHERE `id` = ?n", $paymentSID, $codeHistorySID);
	}
	
	public static function getAllHistory()
	{
		return SJB_DB::query("SELECT * FROM `promotions_history` ORDER BY code , `date` DESC");
	}

	public static function getCodeByPaymentSID($paymentSID)
	{
		$result = SJB_DB::query("SELECT `code` FROM `promotions_history` WHERE `payment_id` = ?n LIMIT 1", $paymentSID);
		return $result[0]['code'];
	}
	
	public static function getDiscountByID($id)
	{
		$info = self::getCodeInfoByName($id);
		return isset($info['discount'])?$info['discount']:1;
	}
	
	public static function issetActiveCodes()
	{
		$currentDate = date("Y-m-d");
		$result = SJB_DB::queryValue("SELECT count(*) FROM `promotions` WHERE `active`=1 AND `start_date` <= ?s AND `end_date` >= ?s", $currentDate);
		return $result > 0;
	}

	/**
	 * @param $sid
	 * @return bool|int
	 */
	public static function  getUsesCodeBySID($sid)
	{
		$result = SJB_DB::queryValue("SELECT count(`sid`) FROM `promotions_history` WHERE `code_sid` = ?n and `paid` = 1", $sid);
		return $result ? $result : 0;
	}

	/**
	 * @param array $productInfo
	 * @param array $promoCodeInfo
	 */
	public static function applyPromoCodeToProduct(&$productInfo, $promoCodeInfo)
	{
		if (!empty($promoCodeInfo['type'])) {
			$productPrice = isset($productInfo['primaryPrice']) ? $productInfo['primaryPrice'] : $productInfo['price'];
			$promotionalAmount = self::getPromotionalAmount($promoCodeInfo, $productPrice);
			$promotionalPrice = round($productPrice - $promotionalAmount, 2);

			$productInfo['price'] = $promotionalPrice < 0 ? 0 : $promotionalPrice;

			$promoCodeInfo['promoAmount'] = $promotionalAmount;

			$productInfo['code_info'] = $promoCodeInfo;
		}
	}

	/**
	 * @param $productInfo
	 */
	public static function removePromoCodeFromProduct(&$productInfo)
	{
		unset($productInfo['code_info']);
		$extraInfo = unserialize($productInfo['serialized_extra_info']);
		$productInfo['price'] = $extraInfo['price'];
	}

	/**
	 * @param array $promoCodeInfo
	 * @param float $productPrice
	 * @return float|int
	 */
	public static function getPromotionalAmount($promoCodeInfo, $productPrice)
	{
		$promotionalAmount = 0;
		switch ($promoCodeInfo['type']) {
			case 'percentage':
				$promotionalAmount = self::getPercentagePromotionAmount($promoCodeInfo, $productPrice);
				break;
			case 'fixed':
				$promotionalAmount = self::getFixedPromotionAmount($promoCodeInfo);
				break;
		}
		return $promotionalAmount;
	}

	/**
	 * @param array $promotionInfo
	 * @param float $productPrice
	 * @return float
	 */
	public static function getPercentagePromotionAmount($promotionInfo, $productPrice)
	{
		return round(($productPrice / 100) * $promotionInfo['discount'], 2);
	}

	/**
	 * @param array $promotionInfo
	 * @return mixed
	 */
	public static function getFixedPromotionAmount($promotionInfo)
	{
		return $promotionInfo['discount'];
	}

	/**
	 * @param $productInfo
	 * @param $codeInfo
	 */
	public static function preparePromoCodeInfoByProductPromoCodeInfo($productInfo, &$codeInfo)
	{
		if (!empty($productInfo['code_info'])) {
			$codeInfo['products'][] = $productInfo['sid'] == -1 ? $productInfo['custom_item'] : $productInfo['sid'];
			$codeInfo['amount'][]   = $productInfo['code_info']['promoAmount'];
			$codeInfo['sid']        = $productInfo['code_info']['sid'];
		}
	}

	/**
	 * @param int $promotionSID
	 * @param int $page
	 * @param int $itemsPerPage
	 * @return array|bool|int
	 */
	public static function getHistoryBySID($promotionSID, $page, $itemsPerPage)
	{
		$limit	= ($page - 1) * $itemsPerPage;
		$items = $itemsPerPage;
		return SJB_DB::query('SELECT * FROM `promotions_history` WHERE `code_sid` = ?n AND `paid` = 1 LIMIT ?n, ?n', $promotionSID, $limit, $items);
	}

	/**
	 * @param array $promotions
	 */
	public static function preparePromotionsInfoForLog(&$promotions)
	{
		foreach($promotions as &$promotionData) {
			$userInfo = SJB_UserManager::getUserInfoBySID(SJB_Array::get($promotionData, 'user_sid'));
			if ($userInfo) {
				$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID(SJB_Array::get($userInfo, 'user_group_sid'));
				$promotionData['user'] = array(
					'username' => $userInfo['username'],
					'userGroupID' => $userGroupInfo['id'],
				);
			}
			$productsNames = array();
			$productsSIDs = explode(',', SJB_Array::get($promotionData, 'product_sid'));
			if ($productsSIDs) {
				foreach ($productsSIDs as $productSID) {
					if (is_numeric($productSID)) {
						$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
						if (!empty($productInfo)) {
							array_push($productsNames, SJB_Array::get($productInfo, 'name'));
						}
					} else {
						array_push($productsNames, $productSID);
					}
				}
			}
			$promotionData['products'] = $productsNames;
			$savedPromotionCodeInfo = unserialize($promotionData['code_info']);
			$promotionData['type'] = SJB_Array::get($savedPromotionCodeInfo, 'type');
			$promotionData['code'] = SJB_Array::get($savedPromotionCodeInfo, 'code');
			unset($promotionData['code_info']);
		}
	}

	/**
	 * @param $invoiceSID
	 * @return bool|int|mixed
	 */
	public static function getCodeSidByInvoiceSID($invoiceSID)
	{
		return SJB_DB::queryValue('SELECT `code_sid` FROM `promotions_history` WHERE `invoice_sid` = ?n', $invoiceSID);
	}

	/**
	 * @param int $invoiceSID
	 */
	public static function markPromotionAsPaidByInvoiceSID($invoiceSID)
	{
		$result = SJB_DB::query('UPDATE `promotions_history` SET `paid` = 1 WHERE `invoice_sid` = ?n', $invoiceSID);
		if ($result) {
			self::addEventToStatistic($invoiceSID);
		}
	}

	/**
	 * @param int $invoiceSID
	 */
	public static function addEventToStatistic($invoiceSID)
	{
		$promotionsInfo = SJB_DB::query('SELECT `code_sid` as `sid`, `amount`, `user_sid` FROM `promotions_history` WHERE `invoice_sid` = ?n', $invoiceSID);
		foreach ($promotionsInfo as $promotionInfo) {
			SJB_Statistics::addStatistics(self::STATISTIC_EVENT, '', $promotionInfo['sid'], false, 0, 0, $promotionInfo['user_sid'], $promotionInfo['amount']);
		}
	}

	/**
	 * @param int $promotionSID
	 * @return bool|int|mixed
	 */
	public static function getHistoryCountBySID($promotionSID)
	{
		return SJB_DB::queryValue('SELECT count(`sid`) FROM `promotions_history` WHERE `code_sid` = ?n AND `paid` = 1', $promotionSID);
	}

	/**
	 * @param $invoiceSid
	 * @return bool
	 */
	public static function isPromoCodeExpired($invoiceSid)
	{
		$codeId = SJB_PromotionsManager::getCodeSidByInvoiceSID($invoiceSid);
		$codeInfo = SJB_PromotionsManager::getCodeInfoBySID($codeId);
		if (isset($codeInfo)) {
			$currentUsesCount = SJB_PromotionsManager::getUsesCodeBySID($codeInfo['sid']);
			return $codeInfo['maximum_uses'] != 0 && $codeInfo['maximum_uses'] <= $currentUsesCount;
		}
		return false;
	}
}