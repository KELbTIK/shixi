<?php

return array(
	'display_name' => 'Admin Sub-Accounts Management',
	'description' => '',
	'functions' => array (
		'manage_subadmins' => array (
			'display_name' => 'Admin Sub Accounts',
			'script' => 'manage_subadmins.php',
			'type' => 'admin',
			'access_type' => array('admin'),
		),
		'edit_subadmin' => array (
			'display_name' => 'Edit Admin Sub-Account',
			'script' => 'edit_subadmin.php',
			'type' => 'admin',
			'access_type' => array('admin'),
		),
		'delete_subadmin' => array (
			'display_name' => 'Delete Admin Sub-Account',
			'script' => 'delete_subadmin.php',
			'type' => 'admin',
			'access_type' => array('admin'),
		),
		'add_subadmin' => array (
			'display_name' => 'Add Admin Sub-Account',
			'script' => 'add_subadmin.php',
			'type' => 'admin',
			'access_type' => array('admin'),
		),
		'edit_profile' => array (
			'display_name' => 'Edit Admin Sub-Account',
			'script' => 'edit_subadmin_profile.php',
			'type' => 'admin',
			'access_type' => array('admin'),
		),
	)
);
