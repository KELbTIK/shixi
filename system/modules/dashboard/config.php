<?php

return array
(
	'display_name' => 'Dashboard',
	'description' => '',
	'classes' => 'classes/',
	'startup_script'	=>	array (
		),
	'functions' => array
	(
		'view' => array
		(
			'display_name'	=> 'Dashboard',
			'script'		=> 'index.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
