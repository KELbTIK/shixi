<?php

class SJB_Admin_Payment_AddProduct extends SJB_Function
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
		$productType = SJB_Request::getVar('product_type', false);
		$action = SJB_Request::getVar('action', false);
		$errors = array();
		$productErrors = array();

		if ($productType) {
			if ($productType == 'featured_user') {
				$_REQUEST['user_group_sid'] = SJB_UserGroupManager::getUserGroupSIDByID('Employer');
			}
			$product = new SJB_Product($_REQUEST, $productType);
			$pages = $product->getProductPages();
			$addProductForm = new SJB_Form($product);
			$addProductForm->registerTags($tp);
			$form_submitted = SJB_Request::getVar('action', '') == 'save';
			if ($form_submitted) {
				$productErrors = $product->isValid($product);
				if (in_array($productType, array('access_listings', 'featured_user', 'banners', 'custom_product'))) {
					$periodName = $product->getPropertyValue('period_name');
					if ($periodName == 'unlimited') 
						$product->makePropertyNotRequired('period');
				}
			}
			
			if ($form_submitted && $addProductForm->isDataValid($errors) && !$productErrors) {
				$product->addProperty(
					array('id' => 'product_type',
						'type' => 'string',
						'value' => $productType,
						'is_system' => true,
					)
				);
				$product->saveProduct($product, $_REQUEST);
				$product->savePermissions($_REQUEST);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/products/');
			}
			$errors = array_merge($errors, $productErrors);
			$formFieldsInfo = $addProductForm->getFormFieldsInfo();
			$formFields = array();
			foreach ($pages as $pageID => $page) {
				foreach ($formFieldsInfo as $formFieldInfo)
					if (in_array($formFieldInfo['id'], $page['fields']))
						$formFields[$pageID][] = $formFieldInfo;
				if (!isset($formFields[$pageID]))
					$formFields[$pageID] = array();
			}

			$tp->assign('form_fields', $formFields);
			$tp->assign('product_type', $productType);
			$tp->assign('request', $_REQUEST);
			$tp->assign('params', http_build_query($_REQUEST));
			$tp->assign('pages', $pages);
			$tp->assign('pageTab', SJB_Request::getVar('page', false));
			$tp->assign("errors", $errors);
			$tp->display('add_product.tpl');
		}
		else {
			$tp->display('select_product_type.tpl');
		}
	}
}