<?php

class SJB_PromotionsDetails extends SJB_ObjectDetails
{
	public static function getDetails()
	{
		$currency = SJB_CurrencyManager::getDefaultCurrency();
		return array(
			array
			(
				'id'		=> 'code',
				'caption'	=> 'Promotion Code',
				'type'		=> 'unique_string',
				'length'	=> '20',
				'table_name'=> 'promotions',
				'validators' => array(
					'SJB_IdValidator',
					'SJB_UniqueSystemValidator'
				),
				'is_required'=> true,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'discount',
				'caption'	=> 'Discount', 
				'type'		=> 'float',
				'length'	=> '10',
				'table_name'=> 'promotions',
				'validators' => array(
					'SJB_PlusValidator'
				),
				'is_required'=> true,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'type',
				'caption'	=> 'Type', 
				'type'		=> 'list',
				'length'	=> '10',
				'table_name'=> 'promotions',
				'is_required'=> true,
				'is_system'	=> true,
				'list_values' => array(
					array(
						'id'	=> 'percentage',
						'caption'	=> '%',
					),
					array(
						'id'	=> 'fixed',
						'caption' => $currency['currency_sign'],
					),
				),
			),
			array
			(
				'id'		=> 'product_sid',
				'caption'	=> 'Applies to', 
				'type'		=> 'multilist',
				'table_name'=> 'promotions',
				'is_required'=> false,
				'is_system'	=> true,
				'list_values' => self::getProductList()
			),
			array
			(
				'id'		=> 'maximum_uses',
				'caption'	=> 'Maximum Uses',
				'type'		=> 'integer',
				'table_name'=> 'promotions',
				'comment' 	=> 'Leave empty or zero for unlimited uses',
				'is_required'=> false,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'start_date',
				'caption'	=> 'Start Date', 
				'type'		=> 'date',
				'length'	=> '20',
				'table_name'=> 'promotions',
				'comment' 	=> 'Leave blank to disable start date restrictions',
				'is_required'=> false,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'end_date',
				'caption'	=> 'End Date', 
				'type'		=> 'date',
				'length'	=> '20',
				'table_name'=> 'promotions',
				'comment'	=> 'Leave blank for none',
				'is_required'=> false,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'active',
				'caption'	=> 'Active', 
				'type'		=> 'boolean',
				'table_name'=> 'promotions',
				'is_required'=> false,
				'is_system'	=> true,
			),
		);
	}
	
	public static function getProductList()
	{
		$products = SJB_ProductsManager::getAllProductsInfo(true);
		$productList = array();
		foreach ($products as $key => $product) {
			$productList[$key]['id'] = $product['sid'];
			$productList[$key]['caption'] = $product['name'];
		}
		return $productList;
	}
}