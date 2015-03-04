<?php

return array
(
	'display_name' => 'Builders manager',
	'description' => 'Managing builders',
	'classes' => 'classes/',
	'functions' => array
	(
		'form_builders' => array
		(
			'display_name'	=> 'Form Builder',
			'script'		=> 'form_builders.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'get_fields' => array
		(
			'display_name'	=> 'Get Fields',
			'script'		=> 'get_fields.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin', 'user'),
		),
		'get_listing_fields' => array
		(
			'display_name'	=> 'Get Fields',
			'script'		=> 'get_listing_fields.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'get_inactive_fields' => array
		(
			'display_name'	=> 'Get Fields',
			'script'		=> 'get_inactive_fields.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'save' => array
		(
			'display_name'	=> 'Save Fields',
			'script'		=> 'save_order.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'display_listing_builder' => array
		(
			'display_name'	=> 'Display Listing Builder',
			'script'		=> 'display_listing_builder.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'search_form_builder' => array
		(
			'display_name'	=> 'Search Listings Form Builder',
			'script'		=> 'search_form_builder.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
