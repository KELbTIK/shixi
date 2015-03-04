<?php

class SJB_TaxesDetails extends SJB_ObjectDetails
{
	public static function getDetails()
	{
		return array(
			array
			(
				'id'		=> 'tax_name',
				'caption'	=> 'Tax Name',
				'type'		=> 'unique_string',
				'length'	=> '20',
				'table_name'=> 'taxes',
                'is_required'=> true,
				'is_system'	=> true,
				'validators' => array(
					'SJB_StringWithoutTagsValidator'
				),
			),
			array
			(
				'id'		=> 'Country',
				'caption'	=> 'Country',
				'type'		=> 'list',
				'list_values'	=> SJB_CountriesManager::getAllCountriesCodesAndNames(),
				'table_name'=> 'taxes',
				'is_required'=> false,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'State',
				'caption'	=> 'State',
				'type'		=> 'list',
				'table_name'=> 'taxes',
				'list_values'	=> array(),
				'is_required'=> false,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'tax_rate',
				'caption'	=> 'Tax Rate',
				'type'		=> 'float',
				'table_name'=> 'taxes',
				'length'	=> '10',
                'is_required'=> true,
				'is_system'	=> true,
				'minimum' => '0',
				'maximum' => '100'
			),
			array
			(
				'id'		=> 'active',
				'caption'	=> 'Active',
				'type'		=> 'boolean',
				'table_name'=> 'taxes',
				'is_required'=> false,
				'is_system'	=> true,
			),
			array
			(
				'id'		=> 'price_includes_tax',
				'caption'	=> 'Price Includes Tax',
				'type'		=> 'boolean',
				'table_name'=> 'taxes',
				'is_required'=> false,
				'is_system'	=> true,
			),
		);
	}
}
