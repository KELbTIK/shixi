<?php

class SJB_TaxesManager extends SJB_ObjectManager
{
	public static function saveTax($taxInfo)
	{
		parent::saveObject('taxes', $taxInfo);
	}
	
	public static function getObjectBySID($sid)
	{
		$taxInfo = SJB_ObjectDBManager::getObjectInfo('taxes', $sid);
		
		if (!is_null($taxInfo)) {
			$tax = new SJB_Taxes($taxInfo);
			$tax->setSID($taxInfo['sid']);
			return $tax;
		}
		return null;
	}
	
	public static function deleteTaxBySID($sid)
	{
		parent::deleteObject('taxes', $sid);
	}
	
	public static function activateTaxBySID($sid)
	{
		return SJB_DB::query("UPDATE `taxes` SET `active` = 1 WHERE `sid` = ?n", $sid);
	}
	
	public static function deactivateTaxBySID($sid)
	{
		return SJB_DB::query("UPDATE `taxes` SET `active` = 0 WHERE `sid` = ?n", $sid);
	}

	public static function getAllTaxesInfo()
	{
		return SJB_DB::query('SELECT * FROM `taxes` ORDER BY `sid` DESC');
	}

	public static function getAllActiveTaxesInfo()
	{
		return SJB_DB::query('SELECT * FROM `taxes` where `active` = 1 ORDER BY `sid` DESC');
	}

	public static function getTaxInfoBySID($sid)
	{
		return SJB_ObjectDBManager::getObjectInfo('taxes', $sid);
	}

	public static function getTaxAmount($invoice_sum, $tax_rate, $price_includes_taxes = false)
	{
		$i18n = SJB_I18N::getInstance();
		$lang_data = $i18n->getLanguageData($i18n->getCurrentLanguage());

		if ($price_includes_taxes)
			$amount = round($invoice_sum - ($invoice_sum / ($tax_rate/ 100 + 1)), $lang_data['decimals']);
		else
			$amount = round($invoice_sum * $tax_rate / 100, $lang_data['decimals']);

		return $amount;
	}

	public static function isTaxExistByCountryAndState($country, $state, $sid = false)
	{
		$count = SJB_DB::queryValue("SELECT count(*) FROM `taxes` WHERE `Country` = ?s AND `State` = ?s AND sid != ?n ", $country, $state, $sid);
		return $count > 0 ? true : false;
	}

	public static function getTaxInfoByCountryAndState($countrySID, $stateSID)
	{
		if (SJB_Settings::getSettingByName('enable_taxes')){
			$tax_info = SJB_DB::query("SELECT `sid` ,`tax_name` ,`price_includes_tax` , `tax_rate`,
				IF(`Country`= ?s and `State`= ?s and `Country` is not null and `State` is not null, 1,
					IF(`Country`= ?s and `Country` is not null and `State` = '', 2,
						IF(`Country`= '' and `State` = '', 3, 4))) as `param`
			    FROM `taxes` WHERE `active` = 1 and (`Country`= ?s and `State`= ?s and `Country` is not null and `State` is not null
			    or `Country`= ?s and `Country` is not null and `State` = '' or `Country`= '' and `State` = '')
			    ORDER BY `param` LIMIT 1;", $countrySID, $stateSID, $countrySID, $countrySID, $stateSID, $countrySID);
			$tax_info = array_pop($tax_info);
			if (count($tax_info))
				return $tax_info;
			else
				return array();
		} else {
			return array();
		}
	}

	public static function createTemplateStructureForTax($tax_info){
		$tax = new SJB_Taxes($tax_info);
		foreach ($tax->getProperties() as $property) {
			if ($property->getType() == 'list') {
				$value = $property->getValue();
				$properties =  $property->type->property_info;

				if ($properties['id'] == 'State'){
					$properties['list_values'] = SJB_StatesManager::getStatesNamesByCountry($tax->getPropertyValue('Country'));
				}
				$listValues = isset($properties['list_values']) ? $properties['list_values'] : array();
				$caption = null;
				foreach ($listValues as $listValue) {
					if ($listValue['id'] == $value){
						$caption = $listValue['caption'];
						break;
					}
				}
				$tax_info[$property->getID()] = isset($caption) ? $caption : '';
			}

		}
		return $tax_info;
	}

	public static function getTaxInfoByUserSidAndPrice($user_sid, $price)
	{
		$user = SJB_UserManager::getObjectBySID($user_sid);
		$location = $user->getPropertyValue('Location');
		$tax_info = SJB_TaxesManager::getTaxInfoByCountryAndState(SJB_Array::get($location, 'Country'), SJB_Array::get($location, 'State'));
		$empty_tax_info = array(
			'sid' => ''	,
			'tax_name'=> '' ,
			'price_includes_tax' => 0,
			'tax_rate'=> 0,
		);
		$tax_info = array_merge($empty_tax_info, $tax_info);
		$tax_info['tax_amount'] = SJB_TaxesManager::getTaxAmount($price, $tax_info['tax_rate'], $tax_info['price_includes_tax']);
		return $tax_info;
	}

}
