<?php

class SJB_Contract
{
	var $contract_id 			= null;
	var $user_sid  				= null;
	var $product_sid		 	= null;
	var $creation_date 			= null;
	var $expired_date 			= null;
	var $price;
	var $id;
	private $recurring_id		= null;
	private $gateway_id			= null;
	private $invoice_id			= null;
	public $number_of_listings 	= null;
	public $featured_profile 	= false;
	public $product_type 		= null;
	public $number_of_postings  = null; 
	public $status  			= 'active'; 
	public $extra_info 			= null; 
	
	function SJB_Contract($input_info)
	{
		if ( isset($input_info['contract_id']) ) 
			$this->contract_id = $input_info['contract_id'];
		if ( isset($input_info['recurring_id']) ) 
			$this->recurring_id = $input_info['recurring_id'];
		if ( isset($input_info['gateway_id']) ) 
			$this->gateway_id = $input_info['gateway_id'];
		if ( isset($input_info['invoice_id']) ) 
			$this->invoice_id = $input_info['invoice_id'];
		if ( isset($input_info['product_sid']) ) 		
			$this->_constructorByProduct($input_info);
		 else 
			$this->_constructorByID($input_info['contract_id']);
		if (isset($input_info['user_sid']) && $input_info['user_sid'] != false)
			$this->user_sid = $input_info['user_sid'];
	}
	 
    function isExpired()
    {
    	return false;
    }
    
    function saveInDB()
    {
    	$result = SJB_ContractSQL::insert($this->_getHashedFields());
    	if ($result) {
    		if (!$this->id) {
    			$this->id = $result;
    		}
    		SJB_ContractSQL::updateContractExtraInfoByProductSID($this);
    		if ($this->status == 'active')
    			SJB_Acl::copyPermissions($this->product_sid, $this->id, $this->number_of_listings);
    		else 
    			SJB_Acl::clearPermissions('contract', $this->id);
    		$userInfo = SJB_UserManager::getUserInfoBySID($this->user_sid);
    		$user = new SJB_User($userInfo, $userInfo['user_group_sid']);
    		$user->updateSubscribeOnceUsersProperties($this->product_sid, $this->user_sid);
    	}
    	
    	return (bool)$result;
    }

	function _getHashedFields()
	{
		$fields['product_sid'] 			= $this->product_sid;
		$fields['creation_date']		= $this->creation_date? $this->creation_date: date("Y-m-d");
		$fields['expired_date']			= $this->expired_date;
		$fields['contract_id']			= $this->id;
		$fields['user_sid']				= $this->user_sid;
		$fields['recurring_id']			= $this->recurring_id;
		$fields['gateway_id']			= $this->gateway_id;
		$fields['invoice_id']			= $this->invoice_id;
		$fields['price'] 				= (float) $this->price;
		$fields['status'] 				= $this->status;
		return $fields;
	}

	function _constructorByID($id)
	{
		$contract_info = SJB_ContractSQL::selectInfoByID($id);
		if ($contract_info) {
			$this->id = $id;
			$this->contract_id	  		= $contract_info['id'];
			$this->price				= $contract_info['price'];
			$this->product_sid 			= $contract_info['product_sid'];
			$this->expired_date 		= $contract_info['expired_date'];
			$this->user_sid				= $contract_info['user_sid'];
			$this->extra_info 			= is_null($contract_info['serialized_extra_info']) ? null : unserialize($contract_info['serialized_extra_info']);
			$this->number_of_postings  	= $contract_info['number_of_postings'];
		}
		if ($contract_info['product_sid']) {
			$productSID = $contract_info['product_sid'];
			$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
			$this->product_type =  $productInfo['product_type'];
			if ($this->product_type == 'featured_user')
				$this->featured_profile = true;
		}
	}
	
	function _constructorByProduct($productInfo)
	{
		$productSID = $productInfo['product_sid'];
		$numberOfListings = isset($productInfo['numberOfListings'])?$productInfo['numberOfListings']:false;
		$this->product_sid = $productSID;
		$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
		$is_recurring = !empty($productInfo['recurring'])?$productInfo['recurring']:false;
		if ($productInfo['product_type'] == 'featured_user')
			$this->featured_profile = true;
		$this->product_type = $productInfo['product_type'];
		$product = new SJB_Product($productInfo, $productInfo['product_type']);
		if ($numberOfListings) 
			$product->setNumberOfListings($numberOfListings);
		else 
			$numberOfListings = !empty($productInfo['number_of_listings'])?$productInfo['number_of_listings']:0;
			
		$this->setNumberOfListings($numberOfListings);
		$expirationPeriod = $product->getExpirationPeriod();
		if ($expirationPeriod) {
			if ($is_recurring) // Для рекьюринг планов, делаем запас для проплаты в 1 день
				$expirationPeriod++;
			$this->expired_date = date("Y-m-d", strtotime("+" . $expirationPeriod . " day"));
		}
		$this->price = $product->getPrice();
	}
	
	function getPrice()
	{
		return $this->price;
	}
	
	function setPrice($price = 0)
	{
		$this->price = $price;
	}
	
	function setStatus($status = 'active')
	{
		$this->status = $status;
	}

	function setCreationDate($creation_date)
	{
		$this->creation_date = $creation_date? $creation_date: date("Y-m-d");
	}

	function setExpiredDate($expired_date)
	{
		$this->expired_date = $expired_date;
	}
	
	function getID()
	{
		return $this->id;
	}
	
	function setUserSID($user_sid)
	{
		$this->user_sid = $user_sid;
	}
	
	function getUserSID()
	{
		return $this->user_sid;
	}
    
    function delete()
    {
        return SJB_ContractSQL::delete($this->contract_id);   
    }
    
    public function setNumberOfListings($numberOfListings)
    {
    	$this->number_of_listings = $numberOfListings;
    }
    
	public function isFeaturedProfile()
	{
		return $this->featured_profile;
	}
	
	public function getProductType()
	{
		return $this->product_type;
	}
	
	public function incrementPostingsNumber()
	{
		return SJB_ContractSQL::incrementPostingsNumber($this->contract_id);
	}
	
	function getPostingsNumber()
	{
		return $this->number_of_postings;
	}
}
