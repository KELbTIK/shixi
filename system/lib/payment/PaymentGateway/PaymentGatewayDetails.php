<?php


class SJB_PaymentGatewayDetails extends SJB_ObjectDetails
{
	public static function getDetails()
	{
		return array
			   (
			    array
				(
					'id'		=> 'id',
					'caption'	=> 'ID',
					'type'		=> 'id',
					'length'	=> '20',
					'is_required'=> true,
					'is_system'	=> true,
				),
			    array
				(
					'id'		=> 'caption',
					'caption'	=> 'Caption',
					'type'		=> 'string',
					'length'	=> '20',
					'is_required'=> true,
					'is_system'	=> true,
				),
			    array
				(
					'id'		=> 'active',
					'caption'	=> 'Active',
					'type'		=> 'boolean',
					'length'	=> '20',
					'is_required'=> false,
					'is_system'	=> true,
				),
			   );
	}
}

