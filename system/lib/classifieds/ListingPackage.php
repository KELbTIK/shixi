<?php

class SJB_ListingPackage
{
	function getFieldsInfo()
	{
		return array (
		
			'name' => array(	
				'type' => 'string',				
				'value' => '',				
				'caption' => "Name",				
				),
				
			'description' => array(	
				'type' => 'text',				
				'value' => '',
				'caption' => "Description",				
				),
				
			'price' => array(	
				'type' => 'float',
				'value' => '',
				'caption' => "Single Posting Price",				
				),
			'price_exp' => array(
				'type' => 'float',
				'value' => '',
				'caption' => "Reactivation Posting Price",
				'comment'   => "The amount users will be charged for reactivating their listing after expiration.",
				),
							
			'listing_lifetime' => array(
				'type'		=> 'integer',
				'value'		=> '',		
				'caption'	=> "Listing Lifetime(days)",				
				),
				
			'priority_listing' => array(
				'type'		=> 'boolean',				
				'value'		=> '',				
				'caption'	=> "Priority Listing",
				'comment'   => "Priority listings displayed above other listings in search results and marked with different color.",
				),
						
			'is_featured' => array(
				'type'		=> 'boolean',				
				'value'		=> '',				
				'caption'	=> "Is Featured",
				),
			
			'featured_price' => array(
				'type'		=> 'float',				
				'value'		=> '',				
				'caption'	=> "Price for Upgrade to Featured",
				),
			
			'pic_limit' => array(
				'type'		=> 'integer',				
				'value'		=> '',				
				'caption'	=> "Number of Pictures Allowed",
				),
				
			'video_allowed' => array(
				'type'		=> 'boolean',				
				'value'		=> '',				
				'caption'	=> "Is Video Allowed",
				),
		);
	}
}

