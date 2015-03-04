<?php


return array
(
	'display_name' => 'Banners',
	'description' => 'Banners',
	'classes' => 'classes/',
	'functions' => array
	(
		
		'manage_banners' => array
		(
			'display_name'	=> 'Manage Banners',
			'script'		=> 'manage_banners.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'show_banners' => array
		(
			'display_name'	=> 'Display Banners',
			'script'		=> 'show_banners.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			// 'params' => array ('banner_id'),		
		),
		'add_banner' => array
		(
			'display_name'	=> 'Add Banner',
			'script'		=> 'add_banner.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			// 'params' => array ('banner_id'),		
		),
		'edit_banner' => array
		(
			'display_name'	=> 'Edit Banner',
			'script'		=> 'edit_banner.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params' => array ('banner_id'),		
		),
		'go_link' => array
		(
			'display_name'	=> 'Go to Link',
			'script'		=> 'banner_go_link.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params' => array ('banner_id'),		
		),
		
		
		'manage_banner_groups' => array
		(
			'display_name'	=> 'Banner Groups',
			'script'		=> 'manage_banner_groups.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'add_banner_group' => array
		(
			'display_name'	=> 'Add Banner Group',
			'script'		=> 'add_banner_group.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_banner_group' => array
		(
			'display_name'	=> 'Edit Banner Group',
			'script'		=> 'edit_banner_group.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);

