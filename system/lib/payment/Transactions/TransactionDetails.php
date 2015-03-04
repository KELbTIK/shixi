<?php

class SJB_TransactionDetails extends SJB_ObjectDetails
{
    public static function getDetails()
    {
        return array
            (
                array
                (
                    'id'		=> 'invoice_sid',
                    'caption'	=> 'Invoice #',
                    'type'		=> 'string',
                    'length'	=> '20',
                    'table_name'=> 'transactions',
                    'is_required'=> true,
                    'is_system'	=> true,
                ),
		        array
		        (
			        'id'		=> 'user_sid',
			        'caption'	=> 'User sid',
			        'type'		=> 'id',
			        'length'	=> '20',
			        'table_name'=> 'transactions',
			        'is_required'=> true,
			        'is_system'	=> true,
		        ),
		        array(
			        'id'        => 'transaction_id',
			        'caption'	=> 'Transaction ID',
			        'type'		=> 'text',
			        'length'	=> '20',
			        'table_name'=> 'transactions',
			        'is_required'=> true,
			        'is_system'	=> true,
		        ),
		        array
	            (
	                'id'		=> 'date',
	                'caption'	=> 'Date',
	                'type'		=> 'date',
	                'length'	=> '20',
	                'table_name'=> 'transactions',
	                'is_required'=> true,
	                'is_system'	=> true,
	            ),

		        array
		        (
			        'id'		=> 'amount',
			        'caption'	=> 'Amount',
			        'type'		=> 'float',
			        'length'	=> '20',
			        'table_name'=> 'transactions',
			        'is_required'=> false,
			        'is_system'	=> true,
		        ),
	        array
	        (
		        'id'		=> 'payment_method',
		        'caption'	=> 'Payment method',
		        'type'		=> 'list',
		        'table_name'=> 'transactions',
		        'is_required'=> false,
		        'is_system'	=> true,
		        'list_values' => SJB_PaymentGatewayManager::getActivePaymentGatewaysList(),
	        ),

            );
    }
}
