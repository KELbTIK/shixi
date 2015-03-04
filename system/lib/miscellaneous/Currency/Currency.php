<?php

class SJB_CurrencyManager extends SJB_Object
{
    function SJB_CurrencyManager($currencyInfo)
    {
   		 $this->details = new SJB_CurrencyDetails($currencyInfo);
    }  
    
    public static function getCurrencyList($currencyId = false)
    {
    	if ($currencyId)
    		$currency_info = SJB_DB::query("SELECT `sid`, `course`, `currency_code`,  `currency_sign` FROM `currency` WHERE `sid`!=?n", $currencyId);
    	else
    		$currency_info = SJB_DB::query("SELECT * FROM `currency`");
	   	return $currency_info;
    } 
    
    public static function getActiveCurrencyList()
    {
	   	return SJB_DB::query("SELECT * FROM `currency` WHERE `active`='1'");
    } 
    
    public static function getDefaultCurrency()
    {
    	$currentCurrency = SJB_DB::query("SELECT * FROM `currency` WHERE `main`=1 LIMIT 1");
    	if ($currentCurrency)
    	    $currentCurrency = array_pop($currentCurrency);
    	return $currentCurrency;
    }
    
    public static function getCurrencyByCurrCode($curr_code)
    {
    	$currency = SJB_DB::query("SELECT * FROM `currency` WHERE `currency_code`=?s LIMIT 1", $curr_code);
    	if ($currency)
    	    $currency = array_pop($currency);
    	return $currency;
    }
    
    public static function getCurrencyBySID($curr_sid)
    {
    	$currency = SJB_DB::query("SELECT * FROM `currency` WHERE `sid`=?s LIMIT 1", $curr_sid);
    	if ($currency)
    	    $currency = array_pop($currency);
    	return $currency;
    }
}

class SJB_CurrencyDBManager extends SJB_ObjectDBManager
{
	public static function saveCurrency($currency)
	{
		parent::saveObject("currency", $currency);
	}

	public static function getCurrencyInfoBySID($currency_sid)
	{
    	return parent::getObjectInfo("currency", $currency_sid);
	}

	public static function deleteCurrencyBySID($currency_sid)
	{
		return parent::deleteObjectInfoFromDB('currency', $currency_sid);
	}

	public static function makeDefaultCurrencyBySID($currency_sid)
	{
		if (SJB_CurrencyDBManager::getCurrencyInfoBySID($currency_sid)) {
			SJB_DB::query("UPDATE `currency` SET `main`=0");
			SJB_DB::query("UPDATE `currency` SET `main`=1, `course`=1 WHERE `sid`=?n", $currency_sid);
		}
	}

	public static function updateStatusCurrencyBySID($currency_sid, $status)
	{
		SJB_DB::query("UPDATE `currency` SET `active`=?n WHERE `sid`=?n", $status, $currency_sid);
	}
}

class SJB_CurrencyDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;
	
	function SJB_CurrencyDetails($currencyInfo)
	{
		$details_info = SJB_CurrencyDetails::getDetails();
    	foreach ($details_info as $detail_info) {
			if (isset($currencyInfo[$detail_info['id']]))
				$detail_info['value'] = $currencyInfo[$detail_info['id']];
			else 
				$detail_info['value'] = '';
			
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
    	$details = array(
				array(
					'id'		=> 'name',
					'caption'	=> 'Currency Name', 
					'type'		=> 'unique_string',
					'table_name' => 'currency',
					'validators' => array(
						'SJB_IdValidator',
	            		'SJB_UniqueSystemValidator'
					),
					'length'	=> '20',
					'is_required'=> true,
					'is_system'=> true,
				),
				array(
					'id'		=> 'currency_code',
					'caption'	=> 'Currency Code', 
					'type'		=> 'unique_string',
					'table_name' => 'currency',
					'validators' => array(
						'SJB_IdValidator',
	            		'SJB_UniqueSystemValidator',
						'SJB_CurrencyCodeValidator'
					),
					'length'	=> '3',
					'is_required'=> true,
					'is_system'=> true,
				),
				array(
					'id'		=> 'currency_sign',
					'caption'	=> 'Currency Sign', 
					'type'		=> 'string',
					'table_name' => 'currency',
					'length'	=> '20',
					'is_required'=> true,
					'is_system'=> true,
				),
				array(
					'id'		 => 'course',
					'caption'	 => 'Exchange Rate',
					'type'		 => 'float',
					'table_name' => 'currency',
					'length'	 => '100',
					'is_required'=> false,
					'is_system'  => true,
				),
		);
		return $details;
    }
}
