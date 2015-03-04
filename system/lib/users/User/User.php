<?php

class SJB_User extends SJB_Object
{
	
	var $user_group_sid;
	var $contract_id;
	var $activation_key   = null;
	var $verification_key = null;
	private $subuserInfo  = array();
	
	/**
	 * Not updatable. for template structure only
	 * @var array
	 */
	private $userInfo = array();

	/**
	 * @var int Parent user id
	 */
	public $parent = 0;

	function SJB_User($user_info = array(), $user_group_sid = 0)
	{
		$this->setUserGroupSID($user_group_sid);
		$this->db_table_name = 'users';
		$this->details = new SJB_UserDetails($user_info, $user_group_sid);
		if (isset($user_info['contract_id'])) {
			$this->setContractID($user_info['contract_id']);
		}
		if (!$this->hasContract()) {
			$this->setContractID(false);
		}
		$this->userInfo = $user_info;
	}
	
	public function isSubuser()
	{
		return !empty($this->subuserInfo);
	}
	
	public function setSubuserInfo($subuserInfo)
	{
		$this->subuserInfo = $subuserInfo;
	}
	
	public function getSubuserInfo()
	{
		return $this->subuserInfo;
	}
	
	public function setParent($parentSID)
	{
		$this->parent = $parentSID;
	}
	
	public function getParent()
	{
		return $this->parent;
	}
	
	public function addParentProperty($value = 0)
	{
		$this->details->addProperty(
			array (
				'id'			=> 'parent_sid',
				'caption'		=> 'Parent SID',
				'type'			=> 'id',
				'is_system'		=> true,
				'value'			=> $value
			));
	}
	
	public function addUserGroupProperty($groupID = null)
	{
		$user_groups_info = SJB_UserGroupManager::getAllUserGroupsInfo();
		$list_values = array();
		foreach ($user_groups_info as $user_group) {
			$list_values[] = array('id' => $user_group['id'], 'caption' => $user_group['name']);
		}

		$this->addProperty(
			array(
				'id'			=> 'user_group',
				'type'			=> 'list',
				'value'			=> $groupID,
				'is_system' 	=> true,
				'list_values' 	=> $list_values,
			)
		);

		return array(
			'id' 				 => 'user_group',
			'real_id' 			 => 'user_group_sid',
			'transform_function' => 'SJB_UserGroupManager::getUserGroupSIDByID',
		);
	}
	
	
	public function addProductProperty($productSID = null, $userGroupSID = false )
	{
		if ($userGroupSID)
			$productsInfo = SJB_ProductsManager::getProductsInfoByUserGroupSID($userGroupSID);
		else
			$productsInfo = SJB_ProductsManager::getAllProductsInfo();
			
		$list_values = array();
		foreach ($productsInfo as $productInfo) {
			$list_values[] = array('id' => $productInfo['sid'], 'caption' => $productInfo['name']);
		}

		$this->addProperty(
			array(
				'id'			=> 'product',
				'type'			=> 'list',
				'value'			=> $productSID,
				'is_system' 	=> true,
				'list_values' 	=> $list_values,
			)
		);
	}
	
	public function addRegistrationDateProperty($registrationDate = null)
	{
		$this->addProperty(
			array(
				'id'		=> 'registration_date',
				'type'		=> 'date',
				'value'		=> $registrationDate,
				'is_system' => true,
			)
		);
	}

	/**
	 * @param array $aUserFields
	 * @param array $aListingFields
	 * @param array $aUserFieldsNotRequiredInRegistration
	 */
	public function prepareRegistrationFields($aUserFields = array(), $aListingFields = array(), $aUserFieldsNotRequiredInRegistration = array())
	{
		/** @var $oProperty SJB_ObjectProperty */
		foreach ($this->getProperties() as $oProperty) {
			$propertyID = $oProperty->getID();
			$complexRequired = false;
			if ($oProperty->getType() == 'location') {
				if ($oProperty->is_required) {
					$complexRequired = true;
				}
				else if (!empty($oProperty->type->fields)) {
					foreach ($oProperty->type->fields as $locationFieldInfo) {
						if (SJB_Array::get($locationFieldInfo, 'is_required') > 0 || SJB_Array::get($locationFieldInfo, 'hidden') > 0) {
							$complexRequired = true;
							break;
						}
					}
				}
				
				if ($complexRequired) {
					$location = $oProperty->getObjectType();
					$location->prepareLocationRegistrationFields();
				}
			}
			
			if ((!$complexRequired && !in_array($propertyID, $aUserFields)
					&& !in_array($propertyID, $aListingFields)
					&& !$oProperty->isRequired())
					|| in_array($propertyID, $aUserFieldsNotRequiredInRegistration)) {
				$this->deleteProperty($propertyID);
			}
		}
	}

	public function addExtUserIDProperty($value = null)
	{
		$this->addProperty(
			array(
				'id'		=> 'extUserID',
				'caption'	=> 'Ext User ID',
				'type'		=> 'string',
				'value'		=> $value,
				'is_system' => true,
				'order'		=> 1000,
			)
		);
	}
	
	function setUserGroupSID($user_group_sid)
	{
		$this->user_group_sid = $user_group_sid;
	}
	
	function getUserGroupSID()
	{
		return $this->user_group_sid;
	}
	
	function getContractID()
	{
		return SJB_ContractManager::getAllContractsSIDsByUserSID(($this->sid));
	}
	
	function hasContract()
	{        
        $contract_info = SJB_ContractManager::getAllContractsInfoByUserSID($this->sid);	
		return !empty($contract_info);		
	}
	
	function mayChooseContract() // пользователь всегда может переподписаться
	{
		return true;
	}
	
	function getTrialProductSIDByUserSID()
	{
		$ids = SJB_ObjectManager::getSystemPropertyValueByObjectSID('users', $this->getID(), 'trial');
		if (empty($ids)) {
			return array();
		}

		$ids = explode(',', $ids);
		$trialProducts = array();
		$products = SJB_ProductsManager::getAllProductsInfo();
		foreach ($products as $product) {
			if ($product['trial'] == 1 && $product['user_group_sid'] == $this->getUserGroupSID() && in_array($product['sid'], $ids) )
				$trialProducts[] = $product['sid'];
		}

		return $trialProducts;
	}
	
	function ifSubscribeOnceUsersProperties($productSID, $user_id)
	{
		$unserialized_extra_info = SJB_ProductsManager::getProductInfoBySID($productSID);
		$user = SJB_UserManager::getObjectBySID($user_id);
		$already_subscribed = $user->getTrialProductSIDByUserSID();
		return $unserialized_extra_info['trial'] == 1 && !in_array($productSID, $already_subscribed);
	}
	
	function updateSubscribeOnceUsersProperties($productSID, $userSID)
	{
		
		$unserialized_extra_info = SJB_ProductsManager::getProductInfoBySID($productSID);
		$user = SJB_UserManager::getObjectBySID($userSID);
		$already_subscribed = $user->getTrialProductSIDByUserSID();
		if ($unserialized_extra_info['trial'] == 1) {
			if (!in_array($productSID, $already_subscribed)) {
				$already_subscribed[] = $productSID;
				$value = $productSID;
				if (count($already_subscribed) > 1) {
					$value = implode(',', $already_subscribed);
				}
				$user->addProperty(array(
					'id'=>'trial',
					'type'=>'string',
					'value' => $value,
					'is_system' => true,
				));
				$user->deleteProperty('password');
				SJB_UserManager::saveUser($user);
				return true;
			}	
		} else {
			if (in_array($productSID, $already_subscribed)) {
				$value = array_search($productSID, $already_subscribed);
				unset($already_subscribed[$value]);
				if (count($already_subscribed) > 1){
					$value = implode(',',$already_subscribed);
				}
				else {
					$value = array_pop($already_subscribed);
				}
				$user->addProperty(array(
					'id'=>'trial',
					'type'=>'string',
					'value' => $value,
					'is_system' => true,
				));
				$user->deleteProperty('password');
				SJB_UserManager::saveUser($user);
			}		
		}		
		return false;
	}
	
	function mayChooseProduct($productSID, &$error)
	{
		$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
		
		if (isset($productInfo['trial']) && $productInfo['trial']) {
			if ($this->ifSubscribeOnceUsersProperties($productSID, $this->getID()))
				return true;
			$error = 'PRODUCT_IS_ONLY_ONCE_AVAILABLE';
			return false;
		}
		return true;
	}
	
	function setContractID($contract_id)
	{
		$this->contract_id = $contract_id;
	}

	function getUserName()
	{
		$username = $this->details->getProperty('username')->getValue();
		if (is_array($username))
			$username = array_pop($username);
		return $username;
	}

	function getUserEmail()
	{
		$userEmail = $this->details->getProperty('email')->getValue();
		if (is_array($userEmail))
			$userEmail = array_pop($userEmail);
		return $userEmail;
	}

	function isSavedInDB()
	{
		$sid = $this->getSID();
		return !empty($sid);
	}

	function getActivationKey()
	{
		return $this->activation_key;
	}

	function getVerificationKey()
	{
		return $this->verification_key;
	}

	function createActivationKey()
	{
		$this->activation_key = $this->createUniqueKey();
	}

	function createVerificationKey()
	{
		$this->verification_key = $this->createUniqueKey();
	}

	function createUniqueKey()
	{
		$symbols = array_merge(range('a','z'), range('0','9'));
		shuffle($symbols);
		return join('', $symbols);
	}

	/**
	 * Not updatable. for template structure only
	 * @return array
	 */
	public function getUserInfo()
	{
		return $this->userInfo;
	}

}


