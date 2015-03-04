<?php

return array
(
	'display_name' => 'Breadcrumbs',
	'description' => 'Breadcrumbs',

	//'startup_script'	=>	array (),

	'functions' => array
	(		
		'manage_breadcrumbs' => array
		(
			'display_name'	=> 'Manage Breadcrumbs',
			'script'		=> 'manage_breadcrumbs.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'show_breadcrumbs' => array
		(
			'display_name'	=> 'Breadcrumbs',
			'script'		=> 'show_breadcrumbs.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
