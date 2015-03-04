<?php

return array
(
	'display_name' => 'Error',
	'description' => '',

	'startup_script'	=>	array (),
	'functions' => array
	(
		'show' => array
		(
			'display_name'	=> 'Show Errors',
			'script'		=> 'show.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		
		'view_error_log' => array
		(
			'display_name'	=> 'View ErrorLog',
			'script'		=> 'view_error_log.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
