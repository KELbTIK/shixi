<?php

class SJB_PostingPagesDetails extends SJB_ObjectDetails
{
	public $properties;
	public $details;
	
	function SJB_PostingPagesDetails($page_info, $listing_type_sid)
	{
		$details_info = self::getDetails($listing_type_sid);

		foreach ($details_info as $index => $property_info) {
			$sort_array[$index] = $property_info['order'];
		}

		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

        foreach ($sort_array as $index => $value) {
			$sorted_details_info[$index] = $details_info[$index];
		}

		foreach ($sorted_details_info as $detail_info) {
		    $detail_info['value'] = '';
			if (isset($page_info[$detail_info['id']]))
				$detail_info['value'] = $page_info[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($listing_type_sid)
	{
		$details = array(
		            array(
						'id'			=> 'page_id',
						'caption'		=> 'Page ID',
						'type'			=> 'unique_string',
                     	'table_name'    => 'posting_pages',
		            	'validators' => array(
							'SJB_IdValidator',
		            		'SJB_UniqueSystemPagesValidator'
						),
						'length'		=> '20',
						'is_required'	=> true,
						'is_system'		=> true,
						'order'			=> 1,
					),	
                    array(
						'id'			=> 'page_name',
						'caption'		=> 'Page Name',
						'type'			=> 'string',
                     	'table_name'    => 'posting_pages',
						'length'		=> '20',
						'is_required'	=> true,
						'is_system'		=> true,
						'order'			=> 1,
					),	
					array(
						'id'			=> 'description',
						'caption'		=> 'Page Description', 
					 	'table_name' 	=> 'posting_pages',
						'type'			=> 'text',
						'length'		=> '20',
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> 2,
					));
		return $details;
	}
}