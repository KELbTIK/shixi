<?php

class SJB_AdminPasswordDetails extends SJB_ObjectDetails
{

	public static function getDetails()
	{
		return array(
			array (
				'id'			=> 'username',
				'caption'		=> 'Username',
				'type'			=> 'unique_string',
				'length'		=> '20',
				'validators' 	=> array(
					'SJB_UniqueSystemValidator'
				),
				'table_name'	=> 'administrator',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 0,
			),
			array (
				'id'			=> 'password',
				'caption'		=> 'Current Password',
				'type'			=> 'password',
				'length'		=> '20',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 1,
			),
			array (
				'id'			=> 'new_password',
				'caption'		=> 'New Password',
				'type'			=> 'password',
				'length'		=> '20',
				'is_required'	=> true,
				'is_system'		=> true,
				'order'			=> 2,
			),
		);
	}

}
