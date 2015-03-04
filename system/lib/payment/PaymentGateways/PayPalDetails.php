<?php


class SJB_PayPalDetails extends SJB_PaymentGatewayDetails
{
    public static function getDetails()
	{
        $common_details = parent::getDetails();

		$specific_details = array
			   (
				array
				(
					'id'		=> 'paypal_account_email',
					'caption'	=> 'PayPal account email',
					'type'		=> 'string',
					'length'	=> '20',
					'is_required'=> true,
					'is_system' => false,
				),
				array
				(
					'id'		=> 'currency_code',
					'caption'	=> 'Currency Code',
					'type'		=> 'string',
					'length'	=> '20',
					'is_required'=> true,
					'is_system' => false,
				),
				array
				(
					'id'		=> 'use_sandbox',
					'caption'	=> 'PayPal Sandbox <br /> <span class=\'note\'>check to enable PayPal Sandbox</span>',
					'type'		=> 'boolean',
					'length'	=> '20',
					'is_required'=> false,
					'is_system' => false,
				),
			   );

		return array_merge($common_details, $specific_details);
	}
}

