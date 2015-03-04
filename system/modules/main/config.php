<?php

return array
(
	'display_name' => 'Main',
	'description' => 'Home page and all site pages',
	'classes' => 'classes/',
	'startup_script' => array (
		'admin' => 'admin_login',
	),
	'functions' => array
	(
		'admin_login' => array
		(
			'display_name'	=> 'Admin Login Page',
			'script'		=> 'admin_login.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	),
);
