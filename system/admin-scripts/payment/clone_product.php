<?php

class SJB_Admin_Payment_CloneProduct extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('manage_products');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', false);
		$sid = SJB_Request::getVar('sid', 0);
		$errors = array();
		$productErrors = array();

		$productInfo = SJB_ProductsManager::getProductInfoBySID($sid);

		if ($productInfo) {
			if ($productInfo['product_type'] == 'featured_user') {
				$_REQUEST['user_group_sid'] = SJB_UserGroupManager::getUserGroupSIDByID('Employer');
			}
			$productInfo = array_merge($productInfo, $_REQUEST);
			$product = new SJB_Product($productInfo, $productInfo['product_type']);
			$pages = $product->getProductPages();

			$editProductForm = new SJB_Form($product);
			$editProductForm->registerTags($tp);

			$form_submitted = SJB_Request::getVar('action', '') == 'save';
			if ($form_submitted && in_array($productInfo['product_type'], array('access_listings', 'featured_user', 'banners', 'custom_product'))) {
				$periodName = $product->getPropertyValue('period_name');
				if ($periodName == 'unlimited') 
					$product->makePropertyNotRequired('period');
			}
			$activeError = array();
			if ($form_submitted && $productInfo['active'] = 1){
				if ( !empty($productInfo['availability_to']) && SJB_I18N::getInstance()->getInput('date', $productInfo['availability_to']) <= date('Y-m-d'))
					$activeError['INVALID_ACTIVATION'] = 'The product cannot be activated. Please change the availability date.';
			}
			if ($form_submitted) {
				$productErrors = $product->isValid($product);
				$activeError = array_merge($activeError, $productErrors);
			}
			
			if ($form_submitted && $editProductForm->isDataValid($errors) && !$activeError) {
				$product->addProperty(
					array('id' => 'product_type',
						'type' => 'string',
						'value' => $productInfo['product_type'],
						'is_system' => true,
					)
				);
				$product->saveProduct($product, $_REQUEST);
				$product->savePermissions($_REQUEST);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/edit-product/?sid=' . $product->getSID());
			}
			
			$errors = array_merge($errors, $activeError);

			$formFieldsInfo = $editProductForm->getFormFieldsInfo();
			$formFields = array();
			foreach ($pages as $pageID => $page) {
				foreach ($formFieldsInfo as $formFieldInfo)
					if (in_array($formFieldInfo['id'], $page['fields']))
						$formFields[$pageID][] = $formFieldInfo;
				if (!isset($formFields[$pageID]))
					$formFields[$pageID] = array();
			}

			$tp->assign('form_fields', $formFields);
			$tp->assign('product_info', $productInfo);
			$tp->assign('product_type', $productInfo['product_type']);
			$tp->assign('pages', $pages);
			$tp->assign('pageTab', SJB_Request::getVar('page', false));
			$tp->assign("errors", $errors);
			$tp->display('clone_product.tpl');
		}
	}
}