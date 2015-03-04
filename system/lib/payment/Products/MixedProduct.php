<?php

class SJB_MixedProduct extends SJB_ProductDetails 
{
	public static function getDetails()
	{
		$details = parent::getDetails();
		$additionalDetails = array(
			array(
				'id'			=> 'listing_type_sid',
				'caption'		=> 'Listing Type',
				'type'			=> 'list',
				'list_values'	=> SJB_ListingTypeManager::getListingAllTypesForListType(),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array(
				'id'			=> 'listing_duration',
				'caption'		=> 'Listing Duration (days)',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array(
				'id'			=> 'featured',
				'caption'		=> 'Featured',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),
			array(
				'id'			=> 'featured_period',
				'caption'		=> 'Featured period',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 4,
			),
			array(
				'id'			=> 'priority',
				'caption'		=> 'Priority',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 5,
			),
			array(
				'id'			=> 'priority_period',
				'caption'		=> 'Priority period',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 6,
			),
			array(
				'id'			=> 'number_of_pictures',
				'caption'		=> 'Number of pictures',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 7,
			),
			array(
				'id'			=> 'video',
				'caption'		=> 'Video',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 8,
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
				'id'			=> 'number_of_listings',
				'caption'		=> 'Number of Listings',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'comment'		=> 'Leave empty or 0 for unlimited posting',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array(
				'id'			=> 'renewal_price',
				'caption'		=> 'Renewal Price (per listing)',
				'type'			=> 'float',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),	
			array(
				'id'			=> 'upgrade_to_featured_listing_price',
				'caption'		=> 'Upgrade to Featured Listing Price',
				'type'			=> 'float',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 7,
			),
			array(
				'id'			=> 'upgrade_to_priority_listing_price',
				'caption'		=> 'Upgrade to Priority Listing Price',
				'type'			=> 'float',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 8,
			),	
			array(
				'id'			=> 'expiration_period',
				'caption'		=> 'Product Expiration',
				'type'			=> 'integer',
				'validators' => array(
					'SJB_PlusValidator',
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'comment'		=> 'Set empty or zero for never expire',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 1,
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
			'listing_properties' => array(
				'name' => 'Listing Properties',
				'fields' => array('listing_type_sid', 'listing_duration', 'featured', 'featured_period', 'priority', 'priority_period', 'number_of_pictures', 'video')
			),
			'listings_access_settings' => array(
				'name' => 'Listings Access Settings',
				'fields' => array()
			),
			'pricing' => array(
				'name' => 'Pricing',
				'fields' => array('pricing_type', 'price', 'number_of_listings', 'renewal_price', 'price_per_unit', 'volume_based_pricing', 'upgrade_to_featured_listing_price', 'upgrade_to_priority_listing_price')
			),
			'product_expiration' => array(
				'name' => 'Product Expiration',
				'fields' => array('expiration_period')
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
		$serialized_extra_info = unserialize($product->getPropertyValue('serialized_extra_info'));
		$listingTypeSid = $serialized_extra_info['listing_type_sid'];
		$listingTypeId = strtolower(SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSid));
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
	    	if ($name == 'post_'.$listingTypeId) {
	    		$value = 'allow';
	    		$params = $serialized_extra_info['number_of_listings'];
	    	}
	    	elseif ($name == 'add_featured_listings' && (!empty($serialized_extra_info['featured']) || !empty($serialized_extra_info['upgrade_to_featured_listing_price'])))
	    		$value = 'allow';
	    	elseif ($name == 'add_priority_listings' && (!empty($serialized_extra_info['priority']) || !empty($serialized_extra_info['upgrade_to_priority_listing_price'])))
	    		$value = 'allow';
	    	if (empty($value)  && isset($groupPermissions[$name])) {
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
	
	public function getExpirationPeriod($product)
	{
		return $product->getPropertyValue('expiration_period');
	}
	
	public function getPrice($product)
	{
		return $product->getPropertyValue('price');
	}
	
	public function setNumberOfListings($numberOfListings)
	{
		$this->number_of_listings = $numberOfListings;
	}
	
	public function isValid($product)
	{
		$errors = array();
		$featuredPeriod = $product->getPropertyValue('featured_period');
		$priorityPeriod = $product->getPropertyValue('priority_period');
		$listingDuration = $product->getPropertyValue('listing_duration');
		if (!empty($listingDuration) && ($priorityPeriod > $listingDuration || $featuredPeriod > $listingDuration)) {
			$errors['EXCEED_LISTING_DURATION'] = 1;
		}
		return $errors;
	}
}