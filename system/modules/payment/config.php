<?php

return array
(
	'display_name' => 'Payment',
	'description' => 'Handles payment routines',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'gateways' => array(
								'display_name'	=> 'Payment Gateway Control Panel',
								'script'		=> 'gateways.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'configure_gateway' => array(
								'display_name'	=> 'Payment Gateway Control Panel',
								'script'		=> 'configure_gateway.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),

		'transaction_history' => array(
								'display_name'	=> 'Payments',
								'script'		=> 'transaction_history.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),

		'payment_log'         => array(
								'display_name'	=> 'Payment Log',
								'script'		=> 'payment_log.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'get_product_price' => array(
			'display_name'	=> 'Get product price',
			'script'		=> 'get_product_price.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> false,
		),
		'manage_invoices' =>  array(
			'display_name'	=> 'Manage Invoices',
			'script'		=> 'manage_invoices.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'add_invoice' =>  array(
			'display_name'	=> 'Create Invoice',
			'script'		=> 'add_invoice.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_invoice' =>  array(
			'display_name'	=> 'Edit Invoice',
			'script'		=> 'edit_invoice.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

//
//               USER SCRIPTS
//


		'payment_page' => array(
								'display_name'	=> 'Payment',
								'script'		=> 'payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'paypal_pro_fill_payment_card' => array(
								'display_name'	=> 'PayPal Payments Pro',
								'script'		=> 'paypal_pro_fill_payment_card.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
		),
		'callback' => array(
								'display_name'	=> 'Payment',
								'script'		=> 'callback_payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
        'notifications' => array(
								'display_name'	=> 'Payment notifications',
								'script'		=> 'notifications_payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'service_completed' => array(
								'display_name'	=> 'Payment complited',
								'script'		=> 'service_completed.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'cancel_recurring' => array(
								'display_name'	=> 'Cancel recurring',
								'script'		=> 'cancel_recurring.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),								
		'cash_payment_page' => array(
								'display_name'	=> 'Payments',
								'script'		=> 'cash_payment_page.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'products' => array(
								'display_name'	=> 'Products',
								'script'		=> 'products.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'add_product' => array(
								'display_name'	=> 'Add Product',
								'script'		=> 'add_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'edit_product' => array(
								'display_name'	=> 'Edit Product',
								'script'		=> 'edit_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'clone_product' => array(
								'display_name'	=> 'Clone Product',
								'script'		=> 'clone_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'product_permissions' => array(
								'display_name'	=> 'Get Permissions',
								'script'		=> 'product_permissions.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
		'user_products' => array(
								'display_name'	=> 'Products',
								'script'		=> 'products.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								'params'		=> array ('action', 'userGroupID')
								),
		'shopping_cart' => array(
								'display_name'	=> 'Shopping Cart',
								'script'		=> 'shopping_cart.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'user_product' => array(
								'display_name'	=> 'User Product',
								'script'		=> 'user_product.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								'params'		=> array ('action')
								),
		'show_shopping_cart' =>  array(
								'display_name'	=> 'Show Shopping Cart',
								'script'		=> 'show_shopping_cart.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'my_products' =>  array(
								'display_name'	=> 'My Products',
								'script'		=> 'my_products.php',
								'type'			=> 'user',
								'access_type'	=> array('user'),
								'raw_output'	=> false,
								),
		'manage_promotions' =>  array(
								'display_name'	=> 'Manage Promotions',
								'script'		=> 'manage_promotions.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								'params'		=> array ('action')
								),
		'promotions_log' =>  array(
								'display_name'	=> 'Promotions Log',
								'script'		=> 'promotions_log.php',
								'type'			=> 'admin',
								'access_type'	=> array('admin'),
								'raw_output'	=> false,
								),
        'payment_completed' =>  array(
                                'display_name'	=> 'Payment Completed',
                                'script'		=> 'payment_completed.php',
                                'type'			=> 'user',
                                'access_type'	=> array('user'),
                                ),
		'my_invoices' =>  array(
			'display_name'	=> 'My Invoices',
			'script'		=> 'my_invoices.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),
		'view_invoice' =>  array(
			'display_name'	=> 'View Invoice',
			'script'		=> 'view_invoice.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),
		'create-contract' => array(
			'display_name'	=> 'Create contract',
			'script'		=> 'create_contract.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),

	),
);
