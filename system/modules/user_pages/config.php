<?php

return array
(
	'display_name' => 'Site Pages',
	'description' => 'Managing site pages',
	'functions' => array
	(
		'edit_user_pages' => array
		(
			'display_name'	=> 'Editing site pages',
			'script'		=> 'edit_user_pages.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'register_page_button' => array
		(
			'display_name'	=> 'Editing site pages',
			'script'		=> 'register_page_button.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> true,
		),
		'register_page_link' => array
		(
			'display_name'	=> 'Displays link to register page',
			'script'		=> 'register_page_link.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> true,
		),
	)
);
