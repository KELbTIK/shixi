<?php

class SJB_AccessListingsProduct extends SJB_ProductDetails 
{
	public static function getDetails()
	{
		$details = parent::getDetails();
		$additionalDetails = array(
			array(
				'id'			=> 'recurring',
				'caption'		=> '',
				'type'			=> 'list',
				'length'		=> '20',
				'list_values'	=> array(
					array (
						'id' => '0',
						'caption' => 'One-time payment',
					),
					array (
						'id' => '1',
						'caption' => 'Recurring Subscription',
					)
				),
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 0,
			),
			array(
				'id'			=> 'price',
				'caption'		=> 'Price',
				'type'			=> 'float',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array(
				'id'			=> 'period',
				'caption'		=> 'Period',
				'type'			=> 'integer',
				'length'		=> '20',
				'table_name'	=> 'products',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array(
				'id'			=> 'period_name',
				'caption'		=> 'Period',
				'type'			=> 'list',
				'length'		=> '20',
				'list_values'	=> array(
					array (
						'id' => 'unlimited',
						'caption' => 'Unlimited',
					),
					array (
						'id' => 'week',
						'caption' => 'Week(s)',
					),
					array (
						'id' => 'month',
						'caption' => 'Month(s)',
					),
					array (
						'id' => 'year',
						'caption' => 'Year(s)',
					),
				),
				'table_name'	=> 'products',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 3,
			),
		);
		$details = array_merge($details, $additionalDetails);
		return $details;
	}
	
	public static function getPages()
	{
		return array(
			'general' => array(
				'name' => 'General Settings',
				'fields' => array('name', 'short_description', 'detailed_description', 'user_group_sid', 'active', 'availability_from', 'availability_to', 'trial', 'welcome_email')
			),
			'listings_access_settings' => array(
				'name' => 'Listings Access Settings',
				'fields' => array()
			),
			'pricing' => array(
				'name' => 'Pricing',
				'fields' => array('recurring', 'price', 'period', 'period_name')
			),
			'additional_permissions' => array(
				'name' => 'Additional Permissions',
				'fields' => array()
			),
		);
	}
	
	public function savePermissions($request, $product)
	{
		$acl = SJB_Acl::getInstance();
		$resources = $acl->getResources();
		$type = 'product';
		$role = $product->getSID();
		SJB_Acl::clearPermissions($type, $role);
		$userGroupSID = $product->getPropertyValue('user_group_sid');
		$groupPermissions = SJB_DB::query('select * from `permissions` where `type` = ?s and `role` = ?s', 'group', $userGroupSID);
		foreach ($groupPermissions as $key => $groupPermission) {
			$groupPermissions[$groupPermission['name']] = $groupPermission;
			unset($groupPermissions[$key]);
		}
	    foreach ($resources as $name => $resource) {
	    	$params = isset($request[$name . '_params'])?$request[$name . '_params']:'';
	    	$params1 = isset($request[$name . '_params1'])?$request[$name . '_params1']:'';
	    	$value = isset($request[$name])?$request[$name]:'';
	    	$message = isset($request[$name . '_message'])?$request[$name . '_message']:'';
	    	if (empty($value) && isset($groupPermissions[$name])) {
	    		$value = 'inherit';
	    		$message = $groupPermissions[$name]['message'];
	    		$params = $groupPermissions[$name]['params'];
	    	}
	    	elseif ($value == 'deny' && $params1) {
	    		$params = $params1;
	    	}
	        SJB_Acl::allow($name, $type, $role, $value, $params, $message);
	    }
	}
	
	public function saveProduct($product, $request = array())
	{
		 return $product;
	}
	
	public function getPrice($product)
	{
		return  $product->getPropertyValue('price');
	}
	
	public function getExpirationPeriod($product)
	{
		$period = $product->getPropertyValue('period');
		if (!empty($period)) {
			$periodName = $product->getPropertyValue('period_name');
			$period = $period." ".$periodName;
			$date = strtotime("+ {$period}");
			$expiration_period = round(($date-time())/(60*60*24));
			return $expiration_period;
		}
		return null;
	}
	
	public function isValid($product)
	{
		$errors = array();
		if ($product->isRecurring()) {
			$periodName =  $product->getPropertyValue('period_name');
			if ($periodName == 'unlimited') {
				$errors['UNLIMITED_PERIOD'] = 1;
			}
		}
		return $errors;
	}
}