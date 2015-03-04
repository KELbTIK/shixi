<?php

class SJB_PostListingsProduct extends SJB_ProductDetails 
{
	private $number_of_listings = null;
	
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
				'length'		=> '20',
				'validators' => array(
					'SJB_PlusValidator',
				),
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
				'id'			=> 'pricing_type',
				'caption'		=> 'Pricing Type',
				'type'			=> 'string',
				'length'		=> '20',
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
				'id'			=> 'volume_based_pricing',
				'caption'		=> 'Volume Based Pricing',
				'type'			=> 'complex',
				'fields'		=> array(
						array(
							'id'			=> 'listings_range_from',
							'caption'		=> 'Listings Range',
							'type'			=> 'integer',
							'length'		=> '20',
							'validators' => array(
								'SJB_PlusValidator',
							),
							'is_required'	=> false,
							'is_system'		=> true,
							'order'			=> 1,
						),			
						array(
							'id'			=> 'listings_range_to',
							'caption'		=> 'Listings Range',
							'type'			=> 'integer',
							'length'		=> '20',
							'validators' => array(
								'SJB_PlusValidator',
							),
							'is_required'	=> false,
							'is_system'		=> true,
							'order'			=> 2,
						),
						array(
							'id'			=> 'price_per_unit',
							'caption'		=> 'Price per unit',
							'type'			=> 'float',
							'validators' => array(
								'SJB_PlusValidator',
							),
							'length'		=> '20',
							'is_required'	=> false,
							'is_system'		=> true,
							'order'			=> 3,
						),
						array(
							'id'			=> 'renewal_price_per_listing',
							'caption'		=> 'Renewal Price (per listing)',
							'type'			=> 'float',
							'validators' => array(
								'SJB_PlusValidator',
							),
							'length'		=> '20',
							'is_required'	=> false,
							'is_system'		=> true,
							'order'			=> 4,
						),
				),
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 6,
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
		$serialized_extra_info = unserialize($product->getPropertyValue('serialized_extra_info'));
		$pricingType = $serialized_extra_info['pricing_type'];
		$listingTypeSid = $serialized_extra_info['listing_type_sid'];
		$listingTypeId = strtolower(SJB_ListingTypeManager::getListingTypeIDBySID($listingTypeSid));
		$userGroupSID = $product->getPropertyValue('user_group_sid');
		$groupPermissions = SJB_DB::query('select * from `permissions` where `type` = ?s and `role` = ?s', 'group', $userGroupSID);
		SJB_Acl::clearPermissions($type, $role);
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
	    		if ($pricingType == 'fixed') 
	    			$params = $product->getPropertyValue('number_of_listings');
	    	}
	    	elseif ($name == 'add_featured_listings' && (!empty($serialized_extra_info['featured']) || !empty($serialized_extra_info['upgrade_to_featured_listing_price'])))
	    		$value = 'allow';
	    	elseif ($name == 'add_priority_listings' && (!empty($serialized_extra_info['priority']) || !empty($serialized_extra_info['upgrade_to_priority_listing_price'])))
	    		$value = 'allow';
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
		$pricingType = $product->getPropertyValue('pricing_type');
		switch ($pricingType) {
			case 'volume_based':
				$volumeBasedPricing = $product->getPropertyValue('volume_based_pricing');
				$i = 1;
				$property = array();
				foreach ($volumeBasedPricing['listings_range_from'] as $key => $values) {
					$property['listings_range_from'][$i] = $values;
					$property['listings_range_to'][$i] = isset($volumeBasedPricing['listings_range_to'][$key])?$volumeBasedPricing['listings_range_to'][$key]:'';
					$property['price_per_unit'][$i] = isset($volumeBasedPricing['price_per_unit'][$key])?$volumeBasedPricing['price_per_unit'][$key]:'';
					$property['renewal_price_per_listing'][$i]= isset($volumeBasedPricing['renewal_price_per_listing'][$key])?$volumeBasedPricing['renewal_price_per_listing'][$key]:'';
					$i++;
				}
				$product->setPropertyValue('volume_based_pricing', $property);
				break;
		}
		return $product;
	}
	
	public function getExpirationPeriod($product)
	{
		return $product->getPropertyValue('expiration_period');
	}
	
	public function getPrice($product)
	{
		$pricingType = $product->getPropertyValue('pricing_type');
		$price = 0;
		switch ($pricingType) {
			case 'fixed':
					$price = $product->getPropertyValue('price');
				break;
			case 'volume_based':
					$volumeBasedPricing = $product->getPropertyValue('volume_based_pricing');
					$numberOfListings = $this->number_of_listings;
					if (!empty($volumeBasedPricing['listings_range_from'])) {
						for ($i = 1; $i <= count($volumeBasedPricing['listings_range_from']); $i++) {
							if ($numberOfListings >= $volumeBasedPricing['listings_range_from'][$i] && $numberOfListings <= $volumeBasedPricing['listings_range_to'][$i]){
								$price = $volumeBasedPricing['price_per_unit'][$i];
								break;
							}
						}
					}
					$price = $price*$numberOfListings;
				break;
		}
		return $price;
	}
	
	public function setNumberOfListings($numberOfListings)
	{
		$this->number_of_listings = $numberOfListings;
	}
	
	public function isValid($product)
	{
		$errors = array();
		$pricingType = $product->getPropertyValue('pricing_type');
		if ($pricingType == 'volume_based') {
			$volumeBasedPricing = $product->getPropertyValue('volume_based_pricing');
			if (!empty($volumeBasedPricing['listings_range_from'])) {
				for ($i = 1; $i <= count($volumeBasedPricing['listings_range_from']); $i++) {
					if (empty($volumeBasedPricing['listings_range_from'][$i]) || empty($volumeBasedPricing['listings_range_to'][$i]))
						$errors['QTY_FIELDS_IS_EMPTY'] = 1;
					elseif ($volumeBasedPricing['listings_range_from'][$i] > $volumeBasedPricing['listings_range_to'][$i]) 
						$errors['QTY_FIELDS_RANGE_ERROR'] = 1;
				}
			}
		}
		$featuredPeriod = $product->getPropertyValue('featured_period');
		$priorityPeriod = $product->getPropertyValue('priority_period');
		$listingDuration = $product->getPropertyValue('listing_duration');
		if (!empty($listingDuration) && ($priorityPeriod > $listingDuration || $featuredPeriod > $listingDuration)) {
			$errors['EXCEED_LISTING_DURATION'] = 1;
		}
		return $errors;
	}
}