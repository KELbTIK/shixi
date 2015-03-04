<?php

return array
(
	'display_name' => 'Statistics',
	'description' => 'Statistics routines',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'general_statistics' => array
		(
			'display_name'	=> 'General Statistics',
			'script'		=> 'general_statistics.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array ('action', 'template')
		),
		'listings_statistics' => array
		(
			'display_name'	=> 'Listings Statistics',
			'script'		=> 'listings_statistics.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array ('action', 'template')
		),
		'applications_and_views' => array
		(
			'display_name'	=> 'Applications and Views',
			'script'		=> 'applications_and_views.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array ('action', 'template')
		),
		'sales' => array
		(
			'display_name'	=> 'Sales',
			'script'		=> 'sales.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array ('action', 'template')
		),
		'guest_alerts'	=> array
		(
			'display_name'	=> 'Guest Alerts Statistics',
			'script'		=> 'guest_alerts.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array ('action', 'template')
		),
		'promotions'	=> array
		(
			'display_name'	=> 'Promotions Statistics',
			'script'		=> 'promotions.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array ('action', 'template')
		),

		'my_reports' => array
		(
			'display_name' => 'My Reports',
			'script'       => 'my_reports.php',
			'type'         => 'user',
			'access_type'  => array('user'),
			'params'       => array ('display_template', 'action')
		),
	),
);
