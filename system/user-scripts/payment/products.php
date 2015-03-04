<?php

class SJB_Payment_UserProducts extends SJB_Function
{
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
		$current_user = SJB_UserManager::getCurrentUser();
		$action = SJB_Request::getVar('action', 'productList');
		$productSID = SJB_Request::getVar('product_sid', 0, 'default', 'int');
		$template = 'products.tpl';
		$availableProducts = array();
		$errors = array();

		switch ($action) {
			case 'productList':
				if (SJB_UserManager::isUserLoggedIn()) {
					$postingProductsOnly = SJB_Request::getVar('postingProductsOnly', false);
					$availableProducts = SJB_ProductsManager::getProductsByUserGroupSID($current_user->getUserGroupSID(), $current_user->getSID());
					$trialProduncts = $current_user->getTrialProductSIDByUserSID();
					foreach ($availableProducts as $key => $availableProduct) {
						if (in_array($availableProduct['sid'], $trialProduncts)
							|| ($postingProductsOnly && ($availableProduct['product_type'] != "post_listings") && ($availableProduct['product_type'] != "mixed_product"))) {
							unset($availableProducts[$key]);
						}
					}
					if ($postingProductsOnly) {
						$tp->assign('postingProductsOnly', $postingProductsOnly);
					}
				}
				elseif ($userGroupID = SJB_Request::getVar('userGroupID', false)) {
					$userGroupSID = SJB_UserGroupManager::getUserGroupSIDByID($userGroupID);
					$availableProducts = SJB_ProductsManager::getProductsByUserGroupSID($userGroupSID, 0);
				}
				else
					$availableProducts = SJB_ProductsManager::getAllActiveProducts();

				foreach ($availableProducts as $key => $availableProductInfo) {
					if (SJB_ProductsManager::isProductTrialAndAlreadyInCart($availableProductInfo, $current_user)) {
						unset($availableProducts[$key]);
						continue;
					}
					$availableProduct = new SJB_Product($availableProductInfo, $availableProductInfo['product_type']);
					$availableProduct->setNumberOfListings(1);
					$availableProducts[$key]['price'] = $availableProduct->getPrice();
					if (isset($availableProducts[$key]['listing_type_sid'])) {
						$availableProducts[$key]['listing_type_id'] = SJB_ListingTypeDBManager::getListingTypeIDBySID($availableProducts[$key]['listing_type_sid']);
					}
				}
				SJB_Event::dispatch('RedefineTemplateName', $template, true);
				SJB_Event::dispatch('RedefineProductsDisplayInfo', $availableProducts, true);
				$tp->assign("account_activated", SJB_Request::getVar('account_activated', ''));
				$tp->assign("availableProducts", $availableProducts);
				break;

			case 'view_product_detail':
				$template = 'view_product_detail.tpl';
				if (!SJB_UserManager::isUserLoggedIn() || $current_user->mayChooseProduct($productSID, $errors)) {
					$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
					if (in_array($productInfo['product_type'], array('post_listings', 'mixed_product'))) {
						$productInfo['listingTypeID'] = SJB_ListingTypeManager::getListingTypeIDBySID($productInfo['listing_type_sid']);
					}
					$event = SJB_Request::getVar('event', false);
					if ($event) {
						if ($productInfo) {
							switch ($productInfo['product_type']) {
								case 'banners':
									$params = $_REQUEST;
									if (empty($params['title']))
										$errors[] = "Banner Title is empty.";
									if (empty($params['link']))
										$errors[] = "Banner link mismatched!";
									if (empty($_FILES['image']['name']))
										$errors[] = "No file attached!";
									elseif ($_FILES['image']['error']) {
										switch ($_FILES['image']['error']) {
											case '1':
												$errors[] = 'UPLOAD_ERR_INI_SIZE';
												break;
											case '2':
												$errors[] = 'UPLOAD_ERR_FORM_SIZE';
												break;
											case '3':
												$errors[] = 'UPLOAD_ERR_PARTIAL';
												break;
											case '4':
												$errors[] = 'UPLOAD_ERR_NO_FILE';
												break;
											default:
												$errors[] = 'NOT_UPLOAD_FILE';
												break;
										}
									}
									else {
										$imageInfo = @getimagesize($_FILES['image']['tmp_name']);
										if (!$imageInfo || ($imageInfo['2'] < 1 && $imageInfo['2'] > 3)) 
											$errors[] = 'Image format is not supported';
										elseif (!empty($productInfo['width']) && $imageInfo[0] != $productInfo['width'])
											$errors[] = "Your banner dimensions exceed the required size. Please upload an appropriate banner.";
										elseif (!empty($productInfo['height']) && $imageInfo[1] != $productInfo['height'])
											$errors[] = "Your banner dimensions exceed the required size. Please upload an appropriate banner.";
									}
									if ($errors)
										break;

									//add banner
									$title = $params['title'];
									$link = $params['link'];
									$expr = preg_match("/(http:\/\/)/", $link, $matches);
									if ($expr != true) {
										$link = "http://" . $link;
									}
									$filesDir = SJB_System::getSystemSettings('FILES_DIR');
									$ext = preg_match("|\.(\w{3})\b|u", $_FILES['image']['name'], $arr);
									$fileName = preg_replace("|\.(\w{3})\b|u", "", $_FILES['image']['name']);
									$hashName = md5((time() * $_FILES['image']['size'])) . "_" . $fileName;
									$bannerFilePath = $filesDir . "banners/" . $hashName . "." . $arr[1];
									$copy = move_uploaded_file($_FILES['image']['tmp_name'], $bannerFilePath);
									if (!$copy) {
										$errors[] = 'Cannot copy file from TMP dir to Banners Dir';
										break;
									}

									if ($_FILES['image']['type'] != 'application/x-shockwave-flash') {
										$bannerInfo = getimagesize($bannerFilePath);
										if ($productInfo['width'] != '' && $productInfo['height'] != '') {
											$sx = $productInfo['width'];
											$sy = $productInfo['height'];
										} else {
											$sx = $bannerInfo[0];
											$sy = $bannerInfo[1];
										}
										$type = $bannerInfo['mime'];

									} else {
										if ($productInfo['width'] == '' || $productInfo['height'] == '') {
											$errors[] = 'Your banner dimensions exceed the required size. Please upload an appropriate banner.';
											break;
										}
										$sx = $productInfo['width'];
										$sy = $productInfo['height'];
										$type = $_FILES['image']['type'];
									}

									$active = 0;
									$group = $productInfo['banner_group_sid'];

									$params['bannerFilePath'] = "/" . str_replace("../", "/", str_replace(SJB_BASE_DIR, '', $bannerFilePath));
									$params['openBannerIn'] = '';
									$params['bannerType'] = 'file';
									$params['code'] = '';
									$params['title'] = $title;
									$params['link'] = $link;
									$params['type'] = $type;
									$params['sx'] = $sx;
									$params['sy'] = $sy;
									$params['banner_group_sid'] = $group;
									$productInfo['banner_info'] = $params;
									break;
							}
							if (!$errors) {
								$numberOfListings = SJB_Request::getVar('number_of_listings');
								$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
								if (!empty($extraInfo['pricing_type']) && $extraInfo['pricing_type'] == 'volume_based' && $numberOfListings) {
									$productInfo['number_of_listings'] = $numberOfListings;
									$productObj = new SJB_Product($productInfo, $productInfo['product_type']);
									$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
									$productObj->setNumberOfListings($number_of_listings);
									$productInfo['price'] = $productObj->getPrice();
								}
								if (SJB_UserManager::isUserLoggedIn())
									SJB_ShoppingCart::addToShoppingCart($productInfo, $current_user->getSID());
								else {
									if (isset($_SESSION['products'])) {
										foreach ($_SESSION['products'] as $addedProduct) {
											$addedProductInfo = unserialize($addedProduct['product_info']);
											if ($addedProductInfo['user_group_sid'] != $productInfo['user_group_sid']) {
												$errors[] = 'You are trying to add products of different User Groups in your Shopping Cart. You сan add only products belonging to one User Group. If you want to add this product in the Shopping Cart please go back to the Shopping Cart and remove products of other User Groups.';
												break;
											}
										}
									}
									if (!$errors) {
										$id = time();
										$_SESSION['products'][$id]['product_info'] = serialize($productInfo);
										$_SESSION['products'][$id]['sid'] = $id;
										$_SESSION['products'][$id]['user_sid'] = 0;
									}
								}
								if (!$errors)
									SJB_HelperFunctions::redirect(SJB_System::getSystemsettings('SITE_URL') . '/shopping-cart/');
							}
						}
					}

					if (!empty($productInfo['expiration_period']) && !is_numeric($productInfo['expiration_period']))
						$productInfo['period'] = ucwords($productInfo['expiration_period']);
					elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'volume_based' && !empty($productInfo['volume_based_pricing'])) {
						$volumeBasedPricing = $productInfo['volume_based_pricing'];
						$price = array();
						$firstPrice = 0;
						if (!empty($volumeBasedPricing['listings_range_from'])) {
							for ($i = 1; $i <= count($volumeBasedPricing['listings_range_from']); $i++) {
								if ($volumeBasedPricing['listings_range_from'][$i] == $volumeBasedPricing['listings_range_to'][$i])
									$price[$i]['range']['from'] = $volumeBasedPricing['listings_range_from'][$i];
								else {
									$price[$i]['range']['from'] = $volumeBasedPricing['listings_range_from'][$i];
									$price[$i]['range']['to'] = $volumeBasedPricing['listings_range_to'][$i];
								}
								$price[$i]['price'] = $volumeBasedPricing['price_per_unit'][$i];
								if ($i > 1 && $firstPrice > $volumeBasedPricing['price_per_unit'][$i]) {
									$price[$i]['savings'] = round(100 - (100 / $firstPrice) * $volumeBasedPricing['price_per_unit'][$i]);
								}
								else
									$firstPrice = $volumeBasedPricing['price_per_unit'][$i];
							}
						}
						$productInfo['volume_based_pricing'] = $price;
						$minListings = min($volumeBasedPricing['listings_range_from']);
						$maxListings = max($volumeBasedPricing['listings_range_to']);
						$countListings = array();
						for ($i = $minListings; $i <= $maxListings; $i++)
							$countListings[] = $i;
						$productInfo['count_listings'] = $countListings;
					}
					elseif (!empty($productInfo['pricing_type']) && $productInfo['pricing_type'] == 'fixed') {
						$productInfo['fixed_period'] = 1;
					}
					if ($productInfo['product_type'] == 'banners') {
						$params = $_REQUEST;
						$bannersObj = new SJB_Banners();
						$banner_fields = $bannersObj->getBannersMeta();
						foreach ($banner_fields as $key => $banner_field) {
							$banner_fields[$banner_field['id']] = $banner_field;
							if (!empty($params[$banner_field['id']]))
								$banner_fields[$banner_field['id']]['value'] = $params[$banner_field['id']];
							unset($banner_fields[$key]);
						}
						if (!empty($params['errors']))
							$tp->assign("errors", $params['errors']);
						$tp->assign("banner_fields", $banner_fields);
					}
					
					$userGroupID = SJB_UserGroupDBManager::getUserGroupIDBySID($productInfo['user_group_sid']);
					$tp->assign('productInfo', $productInfo);
					$tp->assign('userGroupID', $userGroupID);
					$tp->assign('productSID', $productSID);
					$tp->assign('mayChooseProduct', true);
				}
				$tp->assign('errors', $errors);
				break;
		}
		$tp->display($template);
	}
}