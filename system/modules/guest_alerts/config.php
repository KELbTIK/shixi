<?php

return array
(
	'display_name' 	=> 'Guest Alerts',
	'description' 	=> 'Guest Email Alerts',

	'functions' => array
	(
		'manage'	=> array
		(
			'display_name'	=> 'Manage Guest Alerts',
			'script'		=> 'manage.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'export'	=> array
		(
			'display_name'	=> 'Export Guest Alerts',
			'script'		=> 'export.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'create'	=> array
		(
			'display_name'	=> 'Add Guest Email Alert',
			'script'		=> 'create.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'replace'	=> array
		(
			'display_name'	=> 'Replace Guest Email Alert',
			'script'		=> 'replace.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'confirm'	=> array
		(
			'display_name'	=> 'Guest Email Alert Confirmation',
			'script'		=> 'confirm.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'unsubscribe'	=> array
		(
			'display_name'	=> 'Unsubscribe from Guest Email Alert',
			'script'		=> 'unsubscribe.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
