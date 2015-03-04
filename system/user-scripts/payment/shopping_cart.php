<?php

class SJB_Payment_ShoppingCart extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$currentUser = SJB_UserManager::getCurrentUser();
		$action = SJB_Request::getVar('action', false);
		$error = SJB_Request::getVar('error', false);
		$applyPromoCode = SJB_Request::getVar('applyPromoCode', false);
		$action = $applyPromoCode?'applyPromoCode':$action;
		$numberOfListings = SJB_Request::getVar('number_of_listings');
		$productInfo = null;
		$errors = array();
		switch ($action) {
			case 'delete':
				$itemSID = SJB_Request::getVar('item_sid', 0, false, 'int');
				if (SJB_UserManager::isUserLoggedIn()) {
					if (SJB_Settings::getSettingByName('allow_to_post_before_checkout') == true) {
						$this->findCheckoutedListingsByProduct($itemSID, $currentUser->getSID());
					}
					SJB_ShoppingCart::deleteItemFromCartBySID($itemSID, $currentUser->getSID());
				}
				else {
					$products = SJB_Session::getValue('products');
					if (!empty($products[$itemSID])) {
						unset($products[$itemSID]);
						SJB_Session::setValue('products', $products);
					}
				}
				break;
			case 'checkout':
				if (SJB_UserManager::isUserLoggedIn()) {
					$products = SJB_Session::getValue('products');
					$products = $products?$products:array();
					$trialProduct = false;
					foreach ($products as $product) {
						if (!empty($product['product_info'])) {
							$productInfo = unserialize($product['product_info']);
							if ($currentUser->getUserGroupSID() != $productInfo['user_group_sid']) {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/shopping-cart/?error=user_group");
							}
							elseif (in_array($productInfo['sid'], $currentUser->getTrialProductSIDByUserSID())) {
								$trialProduct = true;
							}
							else {
								$product = new SJB_Product($productInfo, $productInfo['product_type']);
								$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
								$product->setNumberOfListings($number_of_listings);
								$productInfo['price'] = $product->getPrice();
								SJB_ShoppingCart::addToShoppingCart($productInfo, $currentUser->getSID());
							}
						}
					}
					SJB_Session::unsetValue('products');
					if ($trialProduct) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/shopping-cart/?error=trial_product");
					}
					elseif ($products) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/shopping-cart/");
					}
					
					$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
					if (empty($products)) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/my-account/");
					}
					
					$isRecurring = false;
					$subTotal = 0;
					foreach ($products as $key => $product) {
						$productInfo = unserialize($product['product_info']);
						if (!empty($productInfo['recurring'])) {
							$isRecurring = true;
						}
						if (!empty($productInfo['pricing_type']) == 'volume_based' && isset($numberOfListings[$productInfo['sid']][$product['sid']])) {
							$productInfo['number_of_listings'] = $numberOfListings[$productInfo['sid']][$product['sid']];
							$productObj = new SJB_Product($productInfo, $productInfo['product_type']);
							$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
							$productObj->setNumberOfListings($number_of_listings);
							$productInfo['price'] = $productObj->getPrice();
							if (!empty($productInfo['code_info'])) {
								SJB_PromotionsManager::applyPromoCodeToProduct($productInfo, $productInfo['code_info']);
							}
							SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
						}
						$subTotal += $productInfo['price'];
						$products[$key] = $productInfo;
						$products[$key]['item_sid'] = $product['sid'];
						$products[$key]['product_info'] = serialize($productInfo);
					}
					$index = 1;
					$items = array();
					$codeInfo = array();
					if ($isRecurring) {
						$tp->assign('confirmation', 1);
						$tp->assign('sub_total_price', $subTotal);
					} else {
						foreach ($products as $product) {
							$product_info = unserialize($product['product_info']);
							SJB_PromotionsManager::preparePromoCodeInfoByProductPromoCodeInfo($product, $product['code_info']);
							$qty = !empty($product_info['number_of_listings'])?$product_info['number_of_listings']:null;
							$items['products'][$index] = $product_info['sid'];
							if ($qty > 0)
								$items['price'][$index] = round($product['price']/ $qty, 2);
							else
								$items['price'][$index] = round($product['price'], 2);
							$items['amount'][$index] = $product['price'];
							$items['qty'][$index] = $qty;

							if (isset($product['custom_item'])) {
								$items['custom_item'][$index] = $product['custom_item'];
							} else {
								$items['custom_item'][$index] = "";
							}

							if (isset($product['custom_info'])) {
								$items['custom_info'][$index] = $product['custom_info'];
							} else {
								$items['custom_info'][$index]['shoppingCartRecord'] = $product['item_sid'];
							}

							if ($product_info['product_type'] == 'banners' && !empty($product_info['banner_info'])) {
								$items['custom_info'][$index]['banner_info'] = $product_info['banner_info'];
							}
							$index++;
							SJB_PromotionsManager::preparePromoCodeInfoByProductPromoCodeInfo($product_info, $codeInfo);
						}
						$subUserInfo = $currentUser->getSubuserInfo();
						$userSID = isset($subUserInfo['sid']) ? $subUserInfo['sid'] : $currentUser->getSID();
						$invoiceSID = SJB_InvoiceManager::generateInvoice($items, $userSID, $subTotal, SJB_System::getSystemSettings('SITE_URL') . "/create-contract/");
						SJB_PromotionsManager::addCodeToHistory($codeInfo, $invoiceSID, $userSID);
						if ($subTotal <= 0) {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/create-contract/?invoice_sid=' . $invoiceSID);
						} else {
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/payment-page/?invoice_sid=" . $invoiceSID);
						}
					}

				}
				break;
			case 'applyPromoCode':
				$promotionCode = SJB_Request::getVar('promotion_code', false);
				if ($promotionCode) {
					if (SJB_UserManager::isUserLoggedIn())
						$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
					else {
						$products = SJB_Session::getValue('products');
						$products = $products?$products:array();
						krsort($products);
					}
					$allowShoppingItems = array();
					$productSIDs = array();
					foreach ($products as $product) {
						$productInfo = unserialize($product['product_info']);
						if (!isset($productInfo['code_info'])) {
							if (isset($productInfo['custom_info'])) {
								$allowShoppingItems[] = $product['sid'];
								$productSIDs[] = $productInfo['custom_info']['productSid'];
							} else {
								$allowShoppingItems[] = $product['sid'];
								$productSIDs[] = $productInfo['sid'];
							}
						} else {
							$appliedPromoCode = $productInfo['code_info'];
						}
					}
					if ($codeInfo = SJB_PromotionsManager::checkCode($promotionCode, $productSIDs)) {
						$productSIDs = $codeInfo['product_sid']?explode(',', $codeInfo['product_sid']):false;
						$appliedProducts = array();
						$codeValid = false;
						foreach ($products as $key => $product) {
							$productInfo = unserialize($product['product_info']);
							if ($productInfo['sid'] != '-1') {
								$productSid = $productInfo['sid'];
							} else {
								$productSid = $productInfo['custom_info']['productSid'];
							}
							if (($productSIDs && in_array($productSid, $productSIDs)) && $allowShoppingItems && in_array($product['sid'], $allowShoppingItems)) {
								$currentUsesCount = SJB_PromotionsManager::getUsesCodeBySID($codeInfo['sid']);
								if (($codeInfo['maximum_uses'] != 0 && $codeInfo['maximum_uses'] > $currentUsesCount) || $codeInfo['maximum_uses'] == 0) {
									$codeValid = true;
									SJB_PromotionsManager::applyPromoCodeToProduct($productInfo, $codeInfo);
									$appliedProducts[] = $productInfo;
									if (SJB_UserManager::isUserLoggedIn()) {
										SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
									} else {
										$products[$key]['product_info'] = serialize($productInfo);
										SJB_Session::setValue('products', $products);
									}
								}
							}
						}
						if (!$codeValid) {
							$errors['NOT_VALID'] = 'Invalid promotion code';
							unset($promotionCode);
						}
						$tp->assign('applied_products', $appliedProducts);
						$tp->assign('code_info', $codeInfo);
					} else {
						$errors['NOT_VALID'] = 'Invalid promotion code';
					}
					if (isset($promotionCode) && isset($appliedPromoCode)) {
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/shopping-cart/');
					}
				} else {
					$errors['EMPTY_VALUE'] = 'Promotion code';
				}
				break;
			case 'deletePromoCode':
				if (SJB_UserManager::isUserLoggedIn()) {
					$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
				}
				else {
					$products = SJB_Session::getValue('products');
					$products = $products?$products:array();
					krsort($products);
				}
				foreach ($products as $key => $product) {
					$productInfo = unserialize($product['product_info']);
					SJB_PromotionsManager::removePromoCodeFromProduct($productInfo);
					if (SJB_UserManager::isUserLoggedIn()) {
						$numberOfListings = is_array($numberOfListings) ? array_pop($numberOfListings) : false;
						if (is_array($numberOfListings)) {
							foreach($numberOfListings as $listingSid => $listingsCount) {
								if ($listingSid == $product['sid']) {
									$productInfo['number_of_listings'] = $listingsCount;
								}
							}
						}
						SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
					} else {
						$products[$key]['product_info'] = serialize($productInfo);
						SJB_Session::setValue('products', $products);
					}
				}
				break;
		}
		if (SJB_UserManager::isUserLoggedIn()) {
			$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());

			// To display products in shopping cart after user has been registered from shopping cart page
			if (empty($products)) {
				$products = SJB_Session::getValue('products');
				$products = $products ? $products : array();
			}
		} else {
			$products = SJB_Session::getValue('products');
			$products = $products ? $products : array();
			krsort($products);
		}
		$allowShoppingItems = array();
		foreach ($products as $product) {
			$productInfo = unserialize($product['product_info']);
			if (!empty($productInfo['code_info'])) {
				$promotionCode = $productInfo['code_info']['code'];
				$promotionCodeInfo = $productInfo['code_info'];
			} else {
				$allowShoppingItems[] = $product ['sid'];
			}
		}
		$promotionCode = isset($promotionCode) ? $promotionCode : '';
		$totalPrice = 0;
		$discountTotalAmount = 0;
		$numberOfListings = SJB_Request::getVar('number_of_listings', false);
		foreach ($products as $key => $product) {
			$productInfo = unserialize($product['product_info']);
			if ($allowShoppingItems && in_array($product['sid'], $allowShoppingItems)) {
				$this->applyPromoCodesToProduct($promotionCode, $productInfo);
				if (SJB_UserManager::isUserLoggedIn()) {
					SJB_ShoppingCart::updateItemBySID($product['sid'], $productInfo);
				} else {
					$products[$key]['product_info'] = serialize($productInfo);
				}
			}
			if ($numberOfListings && array_key_exists('number_of_listings', $productInfo) && array_key_exists($productInfo['sid'], $numberOfListings)) {
				$productInfo['number_of_listings']  = $numberOfListings[$productInfo['sid']][$product['sid']];
			}
			$productObj = new SJB_Product($productInfo, $productInfo['product_type']);
			$productExtraInfo = unserialize($productInfo['serialized_extra_info']);
			if (!empty($productInfo['expiration_period']) && !is_numeric($productInfo['expiration_period'])) {
				$productInfo['primaryPrice'] = $productExtraInfo['price'];
				$productInfo['period'] = ucwords($productInfo['expiration_period']);
			}
			elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'volume_based') {
				$volumeBasedPricing = $productInfo['volume_based_pricing'];
				$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
				$productObj->setNumberOfListings($number_of_listings);
				$productInfo['price'] = $productObj->getPrice();
				$productInfo['primaryPrice'] = $productObj->getPrice();
				$this->applyPromoCodesToProduct($promotionCode, $productInfo);
				$minListings = min($volumeBasedPricing['listings_range_from']);
				$maxListings = max($volumeBasedPricing['listings_range_to']);
				$countListings = array();
				for ($i = $minListings; $i <= $maxListings; $i++) {
					$countListings[$i]['number_of_listings'] = $i;
					for ($j = 1; $j <= count($volumeBasedPricing['listings_range_from']); $j++) {
						if ($i >= $volumeBasedPricing['listings_range_from'][$j] && $i <= $volumeBasedPricing['listings_range_to'][$j]) {
							$countListings[$i]['price'] = $volumeBasedPricing['price_per_unit'][$j]*$i;
							$countListings[$i]['primaryPrice'] = $volumeBasedPricing['price_per_unit'][$j] * $i;
							if (!empty($productInfo['code_info']['type'])) {
								switch ($productInfo['code_info']['type']) {
									case 'percentage':
										$countListings[$i]['price'] = round($countListings[$i]['price'] - ($countListings[$i]['primaryPrice'] / 100) * $productInfo['code_info']['discount'], 2);
										$countListings[$i]['percentPromoAmount'] = round($countListings[$i]['primaryPrice'] - $countListings[$i]['price'], 2);
										$countListings[$i]['percentPromoCode'] = $productInfo['code_info']['code'];
										break;
									case 'fixed':
										$countListings[$i]['price']= round($countListings[$i]['price'] - $productInfo['code_info']['discount'], 2);
										break;
								}
							}
						}
					}
				}
				$productInfo['count_listings'] = $countListings;
			}
			elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
				$productInfo['primaryPrice'] = $productObj->getPrice();
				$this->applyPromoCodesToProduct($promotionCode, $productInfo);
				unset($productInfo['volume_based_pricing']);
			}
			if (isset($productInfo['code_info'])) {
				if ($productInfo['code_info']['type'] != 'fixed' && isset($productInfo['pricing_type'])  && $productInfo['pricing_type'] == 'volume_based') {
					$discountTotalAmount += (float)$productInfo['count_listings'][$productInfo['number_of_listings']]['percentPromoAmount'];
				} else {
					$discountTotalAmount += (float)$productInfo['code_info']['promoAmount'];
				}
			}
			if (empty($productInfo['volume_based_pricing'])){
				$productInfo['primaryPrice'] = $productExtraInfo['price'];
				$this->applyPromoCodesToProduct($promotionCode, $productInfo);
				$totalPrice += (float)$productInfo['price'];
			}
			$products[$key] = $productInfo;
			$products[$key]['item_sid'] = $product['sid'];
		}
		if ($currentUser){
			$taxInfo = SJB_TaxesManager::getTaxInfoByUserSidAndPrice($currentUser->getSID(), $totalPrice);
			$tp->assign('tax', $taxInfo);
		}
		$userGroupID = $productInfo ? SJB_UserGroupDBManager::getUserGroupIDBySID($productInfo['user_group_sid']) : false;
		$tp->assign('promotionCodeAlreadyUsed', $promotionCode && empty($errors));
		if (isset($promotionCodeInfo)) {
			$tp->assign('promotionCodeInfo', $promotionCodeInfo);
		}
		$tp->assign('error', $error);
		$tp->assign('errors', $errors);
		$tp->assign('total_price', $totalPrice);
		$tp->assign('discountTotalAmount', $discountTotalAmount);
		$tp->assign('products', $products);
		$tp->assign('userGroupID', $userGroupID);
		$tp->assign('account_activated', SJB_Request::getVar('account_activated', ''));
		$tp->display('shopping_cart.tpl');
	}

	/**
	 * @param $itemSID
	 * @param $userSID
	 */
	public function findCheckoutedListingsByProduct($itemSID, $userSID)
	{
		$shopCartProduct = SJB_DB::query("SELECT `product_info` FROM `shopping_cart` WHERE `sid` = ?n", $itemSID);
		if (!empty($shopCartProduct)) {
			$productInfo = unserialize($shopCartProduct[0]['product_info']);
			$countCheckoutedListings = SJB_ListingDBManager::getNumberOfCheckoutedListingsByProductSID($productInfo['sid'], $userSID);
			if ($countCheckoutedListings != 0) {
				$serializedProductSIDForShopCart = '"sid";s:' . strlen($productInfo['sid']) . ':"' . $productInfo['sid'] . '";';
				$countOfOtherShopCartProducts = SJB_DB::queryValue("SELECT COUNT(`sid`) FROM `shopping_cart` WHERE `sid` != ?n AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSIDForShopCart})' ORDER BY `sid` ASC", $itemSID, $userSID);
				if ($productInfo['product_type'] == 'mixed_product' || isset($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
					$limitCheckoutedListingsToDelete = $countCheckoutedListings - ($countOfOtherShopCartProducts * $productInfo['number_of_listings']);
					if ($limitCheckoutedListingsToDelete > 0) {
						$this->deleteCheckoutedListingsByProduct($userSID, $productInfo['sid'], $limitCheckoutedListingsToDelete);
					}
				}
				if (isset($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'volume_based') {
					$maxAvailableListings = end($productInfo['volume_based_pricing']['listings_range_to']);
					$shopCartProductsToUpdate = SJB_DB::query("SELECT `sid`,`product_info` FROM `shopping_cart` WHERE `sid` != ?n AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSIDForShopCart})' ORDER BY `sid` ASC", $itemSID, $userSID);
					$limitCheckoutedListingsToDelete = $countCheckoutedListings - ($countOfOtherShopCartProducts * $maxAvailableListings);
					if ($limitCheckoutedListingsToDelete >= 0) {
						foreach ($shopCartProductsToUpdate as $shopCartProductToUpdate) {
							$shopCartProductInfo = unserialize($shopCartProductToUpdate['product_info']);
							$shopCartProductInfo['number_of_listings'] = $maxAvailableListings;
							SJB_ShoppingCart::updateItemBySID($shopCartProductToUpdate['sid'], $shopCartProductInfo);
						}
						if ($limitCheckoutedListingsToDelete > 0) {
							$this->deleteCheckoutedListingsByProduct($userSID, $productInfo['sid'], $limitCheckoutedListingsToDelete);
						}
					} else {
						foreach ($shopCartProductsToUpdate as $shopCartProductToUpdate) {
							$shopCartProductInfo = unserialize($shopCartProductToUpdate['product_info']);
							if ($limitCheckoutedListingsToDelete > end($shopCartProductInfo['volume_based_pricing']['listings_range_to'])) {
								$limitCheckoutedListingsToDelete -= end($shopCartProductInfo['volume_based_pricing']['listings_range_to']);
								$shopCartProductInfo['number_of_listings'] = $maxAvailableListings;
							} else {
								$shopCartProductInfo['number_of_listings'] = $limitCheckoutedListingsToDelete;
							}
							SJB_ShoppingCart::updateItemBySID($shopCartProductToUpdate['sid'], $shopCartProductInfo);
						}
					}
				}
			}
		}
	}

	/**
	 * @param $userSID
	 * @param $productSID
	 * @param $limitCheckoutedListingsToDelete
	 */
	public function deleteCheckoutedListingsByProduct($userSID, $productSID, $limitCheckoutedListingsToDelete)
	{
		$serializedProductSID = SJB_ProductsManager::generateQueryBySID($productSID);
		$listingsToDelete = SJB_DB::query("SELECT `sid` FROM `listings` WHERE `checkouted` = 0 AND `complete` = 1 AND `contract_id` = 0 AND `user_sid` = ?n AND `product_info` REGEXP '({$serializedProductSID})' ORDER BY `sid` DESC LIMIT ?n", $userSID, $limitCheckoutedListingsToDelete);
		$criteriaSaver = new SJB_ListingCriteriaSaver('MyListings');
		$foundListingsSIDs = $criteriaSaver->getObjectSIDs();
		foreach ($listingsToDelete as $listing) {
			SJB_ListingManager::deleteListingBySID($listing['sid']);
			if ($foundListingsSIDs != null) {
				$key = array_search($listing['sid'], $foundListingsSIDs);
				unset($foundListingsSIDs[$key]);
			}
		}
		if ($foundListingsSIDs != null) {
			$criteriaSaver->setSessionForObjectSIDs($foundListingsSIDs);
		}
	}

	private function applyPromoCodesToProduct ($promotionCode, &$productInfo)
	{
		$allowShoppingItems = array();
		if (!isset($productInfo['code_info'])) {
			if (isset($productInfo['custom_info'])) {
				$allowShoppingItems[] = $productInfo['custom_info']['productSid'];
			} else {
				$allowShoppingItems[] = $productInfo['sid'];
			}
		}
		if ($codeInfo = SJB_PromotionsManager::checkCode($promotionCode, $allowShoppingItems)) {
			$productSIDs = $codeInfo['product_sid'] ? explode(',', $codeInfo['product_sid']) : false;
			if ($productInfo['sid'] != '-1') {
				$productSid = $productInfo['sid'];
			} else {
				$productSid = $productInfo['custom_info']['productSid'];
			}
			if (($productSIDs && in_array($productSid, $productSIDs))) {
				$currentUsesCount = SJB_PromotionsManager::getUsesCodeBySID($codeInfo['sid']);
				if (($codeInfo['maximum_uses'] != 0 && $codeInfo['maximum_uses'] > $currentUsesCount) || $codeInfo['maximum_uses'] == 0) {
					SJB_PromotionsManager::applyPromoCodeToProduct($productInfo, $codeInfo);
				}
			}
		}
	}
}