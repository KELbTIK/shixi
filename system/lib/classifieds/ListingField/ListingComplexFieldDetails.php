<?php

class SJB_ListingComplexFieldDetails extends SJB_ObjectDetails
{
	
	function SJB_ListingComplexFieldDetails($listing_field_info)
	{
		$details_info = SJB_ListingComplexFieldDetails::getDetails($listing_field_info);
		foreach ($details_info as $detail_info) {
			if (isset($listing_field_info[$detail_info['id']]))
				$detail_info['value'] = $listing_field_info[$detail_info['id']];
			else 
				$detail_info['value'] = isset($detail_info['value']) ? $detail_info['value'] : '';
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($listing_field_info)
	{
		$common_details_info = array(
				array (
					'id'		=> 'id',
					'caption'	=> 'ID', 
					'type'		=> 'unique_string',
					'validators' => array(
						'SJB_IdValidator',
						'SJB_UniqueSystemComplexValidator'
					),
					'length'	=> '20',
                    'table_name'=> 'listing_complex_fields',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array (
					'id'		=> 'caption',
					'caption'	=> 'Caption', 
					'type'		=> 'string',
					'length'	=> '20',
                    'table_name'=> 'listing_complex_fields',
					'is_required'=> true,
					'is_system'	=> true,
				),
				array (
					'id'		=> 'type',
					'caption'	=> 'Type',
					'type'		=> 'list',
					'list_values' => array(
											array(
												'id' => 'list',
												'caption' => 'List',
											),
											array(
												'id' => 'multilist',
												'caption' => 'MultiList',
											),
											array(
												'id' => 'string',
												'caption' => 'String',
											),
											array(
												'id' => 'text',
												'caption' => 'Text',
											),
											array(
												'id' => 'integer',
												'caption' => 'Integer',
											),
											array(
												'id' => 'float',
												'caption' => 'Float',
											),
											array(
												'id' => 'boolean',
												'caption' => 'Boolean',
											),
                                            array(
                                                'id' => 'date',
                                                'caption' => 'Date',
                                            ),
											/*
											дерево пока не работают
											array(
												'id' => 'tree',
												'caption' => 'Tree',
											),
											*/
											array(
												'id' => 'geo',
												'caption' => 'Geographical',
											),
											array(
												'id' => 'complexfile',
												'caption' => 'File',
											),
											array(
												'id' => 'youtube',
												'caption' => 'YouTube',
											),
											array(
												'id' => 'monetary',
												'caption' => 'Monetary',
											),											
										),
					'length'	=> '',
					'is_required'=> true,
					'is_system' => true,
				),
				array(
					'id' => 'is_required',
					'caption' => 'Required',
					'type' => 'boolean',
					'length' => '20',
					'table_name' => 'listing_complex_fields',
					'is_required' => false,
					'is_system' => true,
				),
		);

		$field_type = isset($listing_field_info['type']) ? $listing_field_info['type'] : null;
		$extra_details_info = SJB_ListingComplexFieldDetails::getDetailsByFieldType($field_type);
		$details_info = array_merge($common_details_info, $extra_details_info);
		return array_merge( $details_info, self::getInfillInstructions());
	}
	
	public static function getDetailsByFieldType($field_type)
	{
		return SJB_TypesManager::getExtraDetailsByFieldType($field_type);
	}

	/**
	 * get Infill instructions field
	 * @return array
	 */
	public static function getInfillInstructions()
	{
		return array(
			array(
					'id' => 'instructions',
					'caption' => 'Infill Instructions',
					'type' => 'text',
					'length' => '',
					'table_name' => 'listing_fields',
					'is_required' => false,
					'is_system' => true,
					'order' => 100,
				),
		);
	}
}

