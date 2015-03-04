<?php

class SJB_SubAdminDetails extends SJB_ObjectDetails
{

	var $properties;
	var $details;

	function __construct($user_info)
	{
		$details_info = self::getDetails();

		foreach ($details_info as $detail_info)
		{
			$detail_info['value'] = '';
			if (isset($user_info[$detail_info['id']]))
				$detail_info['value'] = $user_info[$detail_info['id']];

			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}

	public static function getDetails()
	{
		$details = array(
			array(
				'id' => 'username',
				'caption' => 'Username',
				'type' => 'unique_string',
				'table_name' => 'subadmins',
				'validators' => array(
					'SJB_IdValidator',
            		'SJB_UniqueSystemValidator'
				),
				'length' => '20',
				'is_required' => true,
				'is_system' => true,
				'order' => 0,
			),
			array(
				'id' => 'email',
				'caption' => 'Email',
				'type' => 'unique_email',
				'table_name' => 'subadmins',
				'length' => '20',
				'is_required' => true,
				'is_system' => true,
				'order' => 1,
			),
			array(
				'id' => 'password',
				'caption' => 'Password',
				'type' => 'password',
				'length' => '20',
				'is_required' => true,
				'is_system' => true,
				'order' => 2,
			)
		);

		return $details;
	}

}
