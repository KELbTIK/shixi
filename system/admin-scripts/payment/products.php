<?php

class SJB_Admin_Payment_Products extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel(array('manage_users', 'manage_products'));
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$sid = SJB_Request::getVar('sid', 0);
		$errors = array();

		switch ($action) {
			case 'activate':
				SJB_ProductsManager::activateProductBySID($sid);
				break;
			case 'deactivate':
				SJB_ProductsManager::deactivateProductBySID($sid);
				break;
			case 'delete':
				if (SJB_ContractManager::getContractQuantityByProductSID($sid) || SJB_InvoiceManager::getInvoiceQuantityByProductSID($sid)) {
					$errors['PRODUCT_IS_IN_USE'] = 1;
				} else {
					SJB_ProductsManager::deleteProductBySID($sid);
				}
				break;
		}

		$products = SJB_ProductsManager::getAllProductsInfo();
		foreach ($products as $key => $productInfo) {
			$product = new SJB_Product($productInfo, $productInfo['product_type']);
			$product->setNumberOfListings(1);
			if ($productInfo['product_type'] != 'post_listings' && $productInfo['product_type'] != 'mixed_product') {
				$products[$key]['number_of_postings'] = '-';
			}
			$products[$key]['price'] = $product->getPrice();
			$products[$key]['user_group'] = SJB_UserGroupManager::getUserGroupInfoBySID($productInfo['user_group_sid']);
			$products[$key]['product_type'] = SJB_ProductsManager::getProductTypeByID($productInfo['product_type']);
			$products[$key]['subscribed_users'] = SJB_ContractManager::getContractQuantityByProductSID($productInfo['sid']);
			$products[$key]['invoices'] = SJB_InvoiceManager::getInvoiceQuantityByProductSID($productInfo['sid']);
			if (!empty($productInfo['availability_to']) && $productInfo['availability_to'] <= date('Y-m-d')) {
				$products[$key]['expired'] = 1;
			}
		}
		
		$tp->assign('errors', $errors);
		$tp->assign('products', $products);
		$tp->display('products.tpl');
	}
}
