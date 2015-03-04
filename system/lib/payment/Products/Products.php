<?php

class SJB_Product extends SJB_Object
{
	public  $product_type = 0;
	public  $pages = array();
	public  $permissions = array();

	/**
	 * @var SJB_ProductDetails
	 */
	public	$details = null;

	function __construct($productInfo = array(), $product_type)
	{
		$this->db_table_name = 'products';
		$this->setProductType($product_type);
		$this->getProductDetails($productInfo);
	}
	
	public function setProductType($product_type)
	{
		$this->product_type = $product_type;
	}
	
	public function getProductType()
	{
		return $this->product_type;
	}
	
	public function getProductPages()
	{
		return $this->pages;
	}
	
	public function getExpirationPeriod()
	{
		return $this->details->getExpirationPeriod($this);
	}
	
	public function getPrice()
	{
		return $this->details->getPrice($this);
	}

	/**
	 * @return array
	 */
	public function getAdditionalPermissions()
	{
		$permissions = array(
			'open_search_by_company_form',
			'delete_user_profile',
			'use_private_messages',
			'save_searches',
			'use_screening_questionnaires',
			'create_sub_accounts',
			'bulk_job_import',
			'post_jobs_on_social_networks',
		);
		
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as $listingType) {
			$typeId      = strtolower($listingType['id']);
			$permissions = array_merge(
				$permissions,
				array(
					"flag_{$typeId}",
					"save_{$typeId}",
					"add_{$typeId}_comments",
					"add_{$typeId}_ratings"
				)
			);
		}
		
		return $permissions;     
	}

	/**
	 * @return array
	 */
	public function getAccessPermissions()
	{
		$permissions = array(
			'apply_for_a_job',
		);
		
		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();
		foreach ($listingTypes as $listingType) {
			$typeId      = strtolower($listingType['id']);
			$permissions = array_merge(
				$permissions,
				array(
					"open_{$typeId}_search_form",
					"view_{$typeId}_search_results",
					"view_{$typeId}_details",
					"view_{$typeId}_contact_info",
					"use_{$typeId}_alerts"
				)
			);
		}
		
		return $permissions; 
	}

	public function getProductDetails($productInfo = array())
	{
		switch ($this->product_type) {
			case 'post_listings':
				$this->details = new SJB_PostListingsProduct($productInfo);
				$this->pages = $this->details->getPages();
				break;
			case 'access_listings':
				$this->details = new SJB_AccessListingsProduct($productInfo);
				$this->pages = $this->details->getPages();
				break;
			case 'mixed_product':
				$this->details = new SJB_MixedProduct($productInfo);
				$this->pages = $this->details->getPages();
				break;
			case 'featured_user':
				$this->details = new SJB_FeaturedUserProduct($productInfo);
				$this->pages = $this->details->getPages();
				break;
			case 'banners':
				$this->details = new SJB_BannersProduct($productInfo);
				$this->pages = $this->details->getPages();
				break;
			case 'custom_product':
				$this->details = new SJB_CustomProduct($productInfo);
				$this->pages = $this->details->getPages();
				break;
		}
	}
	
	public function savePermissions($request)
	{
		$this->details->savePermissions($request, $this);
	}
	
	public function saveProduct($product, $request = array())
	{
		$price = trim($product->getPropertyValue('price'));
		if (empty($price)) {
			$product->setPropertyValue('price', 0);
		}
		$product = $this->details->saveProduct($product, $request);
		SJB_ProductsManager::saveProduct($product);
		return $product;
	}
	
	public function setNumberOfListings($numberOfListings)
	{
		if (method_exists($this->details,'setNumberOfListings'))
			$this->details->setNumberOfListings($numberOfListings);
	}
	
	public function isRecurring()
	{
		return $this->getPropertyValue('recurring');
	}
	
	public function isValid($product) 
	{
		return $this->details->isValid($product);
	}

	public static function isFeaturedProfile()
	{
	}
}