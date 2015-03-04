<?php


class SJB_PayPalProDetails extends SJB_PaymentGatewayDetails
{
	public static function getDetails()
	{
		$common_details = parent::getDetails();

		$specific_details = array
		(
			array
			(
				'id'		=> 'user_name',
				'caption'	=> 'PayPal Pro User name',
				'type'		=> 'string',
				'length'	=> '20',
				'is_required'=> true,
				'is_system' => false,
			),
			array
			(
				'id'		=> 'user_password',
				'caption'	=> 'PayPal Pro User password',
				'type'		=> 'string',
				'length'	=> '20',
				'is_required'=> true,
				'is_system' => false,
			),
			array
			(
				'id'		=> 'user_signature',
				'caption'	=> 'PayPal Pro User signature',
				'type'		=> 'string',
				'length'	=> '50',
				'is_required'=> true,
				'is_system' => false,
			),
			array
			(
				'id'		=> 'use_sandbox',
				'caption'	=> 'PayPal Pro Sandbox <br /> <span class=\'note\'>check to enable PayPal Sandbox</span>',
				'type'		=> 'boolean',
				'length'	=> '20',
				'is_required'=> false,
				'is_system' => false,
			),
			array
			(
				'id'		=> 'https',
				'caption'	=> 'https <br /> <span class=\'note\'>works only if SSL certificate is set up in the system</span>',
				'type'		=> 'boolean',
				'length'	=> '20',
				'is_required'=> false,
				'is_system' => false,
			),
			array
			(
				'id'		  => 'country',
				'caption'	  => 'Country <br /> <span class=\'note\'>The country your PayPal Pro account is registered in</span>',
				'type'		  => 'list',
				'list_values' => SJB_CountriesManager::getCountriesCodesAndNamesByCodes(array('US', 'CA', 'UK')),
				'is_required' => true,
				'is_system'   => false,
			),
		);

		return array_merge($common_details, $specific_details);
	}
}

