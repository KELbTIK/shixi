<?php

class SJB_ProductDetails extends SJB_ObjectDetails
{
	public	$properties;
	public	$details;
	public	$template = '';

	function __construct($productInfo = array())
	{
		$details_info = $this->getDetails();

		foreach ($details_info as $index => $property_info) 
			$sort_array[$index] = $property_info['order'];
		
		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

		foreach ($sort_array as $index => $value) 
			$sorted_details_info[$index] = $details_info[$index];
		foreach ($sorted_details_info as $detail_info) {
			$detail_info['value'] = '';
			if (isset($productInfo[$detail_info['id']]))
				$detail_info['value'] = $productInfo[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
		$details = array(
			array(
				'id'			=> 'name',
				'caption'		=> 'Name',
				'type'			=> 'unique_string',
				'length'		=> '20',
				'table_name'	=> 'products',
				'validators' => array(
            		'SJB_UniqueSystemValidator'
				),
				'unique'		=> '1',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array(
				'id'			=> 'short_description',
				'caption'		=> 'Short Description',
				'type'			=> 'text',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 2,
			),
			array(
				'id'			=> 'detailed_description',
				'caption'		=> 'Detailed Description',
				'type'			=> 'text',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 3,
			),
			array(
				'id'			=> 'user_group_sid',
				'caption'		=> 'User Group',
				'type'			=> 'list',
				'length'		=> '20',
				'table_name'	=> 'products',
				'list_values'	=> SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions(true), 
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 4,
			),
			array(
				'id'			=> 'active',
				'caption'		=> 'Active',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 5,
			),
			array(
				'id'			=> 'availability_from',
				'caption'		=> 'Availability',
				'type'			=> 'date',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 6,
			),
			array(
				'id'			=> 'availability_to',
				'caption'		=> 'AvailabilityTo',
				'type'			=> 'date',
				'length'		=> '20',
				'table_name'	=> 'products',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 7,
			),
			array(
				'id'			=> 'trial',
				'caption'		=> 'Trial Product',
				'type'			=> 'boolean',
				'length'		=> '20',
				'table_name'	=> 'products',
				'comment'		=> 'Trial Product will not be available for a user after the 1st purchase',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 8,
			),
			array(
				'id'			=> 'welcome_email',
				'caption'		=> 'Welcome Email',
				'type'			=> 'list',
				'list_values'	=> SJB_TemplateEditor::getTemplatesByGroupForListType(SJB_NotificationGroups::GROUP_ID_PRODUCT),
				'length'		=> '20',
				'table_name'	=> 'products',
				'comment'		=> 'You can create more email templates for different products from Admin Panel > Email Templates > Product Emails',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 9,
			)
		);
		return $details;
	}
	
	public function isValid($product)
	{
		return array();
	}
}