<?php

class SJB_Listing extends SJB_Object
{
	var $listing_type_sid 	  = 0;
	var $product_info = null;
	var $user_sid;	
	var $active;	
	var $featured;
	var $priority;
	var $activation_date;
	var $number_of_views;
	public $contractID;

	/**
	 * @var SJB_ListingDetails
	 */
	public $details = null;
	
	function SJB_Listing($listingInfo = array(), $listing_type_sid = 0, $pageID = 0)
	{
		$this->setListingTypeSID($listing_type_sid);
		$this->db_table_name = 'listings';
		
		$this->details = new SJB_ListingDetails($listingInfo, $this->listing_type_sid, $pageID);

		$this->contractID      = isset($listingInfo['contract_id'])     ? $listingInfo['contract_id'] : false;
		$this->active          = isset($listingInfo['active'])          ? $listingInfo['active']   : false;
		$this->user_sid        = isset($listingInfo['user_sid'])        ? $listingInfo['user_sid'] : 0;
		$this->featured        = isset($listingInfo['featured'])        ? $listingInfo['featured'] : false;
		$this->priority        = isset($listingInfo['priority'])        ? $listingInfo['priority'] : false;
		$this->activation_date = isset($listingInfo['activation_date']) ? date_format(date_create($listingInfo['activation_date']), 'Y-m-d H:i:s') : null;
		$this->number_of_views = isset($listingInfo['views'])           ? $listingInfo['views'] : null;
		$this->data_source     = isset($listingInfo['data_source'])     ? $listingInfo['data_source'] : null;
		
	}
	
	function getActivationDate()
	{
		return $this->activation_date;
	}
	
	function getNumberOfViews()
	{
		return $this->number_of_views;
	}
	
	function setListingTypeSID($listing_type_sid)
	{
		$this->listing_type_sid = $listing_type_sid;
	}
	
	function getListingTypeSID()
	{
		return $this->listing_type_sid;
	}
	
	function setUserSID($user_sid)
	{
		$this->user_sid = $user_sid;
	}
	
	function getUserSID()
	{
		return $this->user_sid;
	}
	
	function setProductInfo($productInfo)
	{
		$this->product_info = $productInfo;
	}
	
	function getProductInfo()
	{
		return $this->product_info;
	}

	function isActive()
	{
		return $this->active;
	}

	function getKeywords()
	{
		$properties = $this->details->getProperties();
		$keywords = '';
		foreach ($properties as $property) {
			$keywords .= $property->getKeywordValue() . ' ';
		}

		$keywords = trim(preg_replace("/\s+/u", ' ', $keywords));

		return $keywords;
	}

	function addActiveProperty($active = 0)
	{
		return $this->details->addActiveProperty($active);
	}

	function addUsernameProperty($username = null)		{ return $this->details->addUsernameProperty($username); }
	function addCompanyNameProperty($CompanyName = null){ return $this->details->addCompanyNameProperty($CompanyName); }
	function addPriorityProperty()						{ return $this->details->addPriorityProperty(); }
	function addIDProperty($id = null)					{ return $this->details->addIDProperty($id); }
	function addListingTypeIDProperty($type_id = null)	{ return $this->details->addListingTypeIDProperty($type_id); }
	function addKeywordsProperty($keywords = null)		{ return $this->details->addKeywordsProperty($keywords); }
	function addPicturesProperty()						{ return $this->details->addPicturesProperty(); }
	function addEmailFrequencyProperty()				{ return $this->details->addEmailFrequencyProperty(); }
	function addRejectReasonProperty()					{ return $this->details->addRejectReasonProperty(); }
	function addPostedWithinProperty()					{ return $this->details->addPostedWithinProperty(); }
    function addActivationDateProperty($activation_date = null)	{ return $this->details->addActivationDateProperty($activation_date); }
    function addFeaturedProperty($featured = false)		{ return $this->details->addFeaturedProperty($featured); }
    function addFeaturedLastShowedProperty($lastShowed = null)	{ return $this->details->addFeaturedLastShowedProperty($lastShowed); }
	function addExpirationDateProperty($expiration_date = null)	{ return $this->details->addExpirationDateProperty($expiration_date); }
	function addNumberOfViewsProperty($number_of_views = null) { return $this->details->addNumberOfViewsProperty($number_of_views); }
	function addApplicationsProperty($apps = null) 		{ return $this->details->addApplicationsProperty($apps); }
	function addSubuserProperty($sid = 0)				{ return $this->details->addSubuserProperty($sid); }
	function addDataSourceProperty($listing_feed_sid = 0){ return $this->details->addDataSourceProperty($listing_feed_sid); }
	function addExternalIdproperty($ext_id = 0)			{ return $this->details->addExternalIdproperty($ext_id); }
	function addCompleteProperty()						{ return $this->details->addCompleteProperty(); }
	function addProductProperty($listingTypeSid)		{ return $this->details->addProductProperty($listingTypeSid); }
	
	function isFeatured()
	{
		return $this->featured;
	}
	
	function isPriority()
	{
		return $this->priority;
	}
	
	function isPropertySetOnAllListings($listings, $sorting_field)
	{
		foreach ($listings as $key => $val){
			$listing = &$listings[$key];
			$isPropertySet = $listing->propertyIsSet($sorting_field);
			if (!$isPropertySet)
				return false;
		}
		return true;
	}
	
	function getPropertyList()
	{
		$result = array();
		$property_list = array_keys($this->getProperties());
		
		foreach ($property_list as $property_name) {
			$result[$property_name] = $property_name;
		}
		return $result;
	}
}
