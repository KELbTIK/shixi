<?php

class SJB_CommentDetails extends SJB_ObjectDetails
{
	public static function getDetails()
	{
		return array(
			array(
				'id'		=> 'listing_id',
				'caption'	=> 'Listing ID', 
				'type'		=> 'id',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> false,
			),
			array(
				'id'		=> 'post_id',
				'caption'	=> 'Post ID', 
				'type'		=> 'id',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> false,
			),
			array(
				'id'		=> 'user_id',
				'caption'	=> 'User ID', 
				'type'		=> 'id',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> true,
			),
			array(
				'id'		=> 'disabled',
				'caption'	=> 'Disabled', 
				'type'		=> 'boolean',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> true,
			),
			array(
				'id'		=> 'added',
				'caption'	=> 'Added', 
				'type'		=> 'date',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> true,
			),
			array(
				'id'		=> 'subject',
				'caption'	=> 'Subject', 
				'type'		=> 'text',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> true,
			),
			array(
				'id'		=> 'message',
				'caption'	=> 'Message', 
				'type'		=> 'text',
				'table_name' => 'comments',
				'is_required'=> false,
				'is_system'=> true,
			),
			array(
				'id'		=> 'email',
				'caption'	=> 'Guest e-mail', 
				'type'		=> 'string',
				'table_name' => 'comments_details',
				'is_required'=> false,
				'is_system'=> false,
			),
			array(
				'id'		=> 'name',
				'caption'	=> 'Guest name', 
				'type'		=> 'string',
				'table_name' => 'comments_details',
				'is_required'=> false,
				'is_system'=> false,
			),
		);
	}
}