<?php

class SJB_Payment_ShowShoppingCart extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$currentUser = SJB_UserManager::getCurrentUser();
		$products = array();

		if (!empty($_SESSION['products'])) {
			$products = $_SESSION['products'];
		}
		if (SJB_UserManager::isUserLoggedIn()) {
			foreach ($products as $product) {
				if (!empty($product['product_info'])) {
					$productInfo = unserialize($product['product_info']);
					if ($currentUser->getUserGroupSID() != $productInfo['user_group_sid']) {
						SJB_Session::unsetValue('products');
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/shopping-cart/?error=user_group");
					}
					else {
						SJB_ShoppingCart::addToShoppingCart($productInfo, $currentUser->getSID());
					}
				}
			}
			SJB_Session::unsetValue('products');
			$products = SJB_ShoppingCart::getAllProductsByUserSID($currentUser->getSID());
		}
		$total_price = 0;
		foreach ($products as $product) {
			$productInfo = unserialize($product['product_info']);
			$product = new SJB_Product($productInfo, $productInfo['product_type']);
			$number_of_listings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:1;
			$product->setNumberOfListings($number_of_listings);
			$productInfo['price'] = $product->getPrice();
			$total_price += $productInfo['price'];
			if ($productInfo['pricing_type'] != 'volume_based' && $productInfo['code_info']) {
				$total_price += $productInfo['code_info']['promoAmount'];
			}
		}

		$tp->assign('products_number', count($products));
		$tp->assign('total_price', $total_price);
		$tp->assign("currency", SJB_CurrencyManager::getDefaultCurrency());
		$tp->display('show_shopping_cart.tpl');
	}
}