<?php


return array
(
	'display_name' => 'Static Content',
	'description' => 'Static Content',
	'classes' => 'classes/',
	'functions' => array
	(
		'edit_static_content' => array
		(
			'display_name'	=> 'Edit Static Content',
			'script'		=> 'edit_static_content.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'show_static_content' => array
		(
			'display_name'	=> 'Show Static Content',
			'script'		=> 'show_static_content.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array('pageid'),	
		),
	)
);

