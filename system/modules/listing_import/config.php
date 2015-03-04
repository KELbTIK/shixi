<?php

return array
(
	'display_name' => 'Import listing',
	'description' => 'Import listing from external data source',

	'functions' => array
	(	
		'add_import'	=> array
		(
			'display_name'	=> 'Add new import',
			'script'		=> 'add_import.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		/*
		'add_step_one' => array
		(
			'display_name'	=> 'Add new import',
			'script'		=> 'add_step_one.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'add_step_two' => array
		(
			'display_name'	=> 'Manage field import',
			'script'		=> 'add_step_two.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'add_step_three' => array
		(
			'display_name'	=> 'Save new import',
			'script'		=> 'add_step_three.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
*/
		'show_import' => array
		(
			'display_name'	=> 'Show import',
			'script'		=> 'show_import.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'edit_import' => array
		(
			'display_name'	=> 'Edit data soupce',
			'script'		=> 'edit_import.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'run_import' => array
		(
			'display_name'	=> 'Run import from data source',
			'script'		=> 'run_import.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		)
		,
		
		'delete_import' => array
		(
			'display_name'	=> 'Delete data source',
			'script'		=> 'delete_import.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'user_fields' => array
		(
			'display_name'	=> 'User Fields',
			'script'		=> 'user_fields.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		)
	),
);
