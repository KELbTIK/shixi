<?php

return array
(
	'display_name' => 'Social',
	'description' => 'Social Plugins',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'social_login' => array
		(
			'display_name'	=> 'Social Login Forms',
			'script'		=> 'social_login.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'linkedin' => array
		(
			'display_name'	=> 'Linkedin',
			'script'		=> 'linkedin.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'facebook' => array
		(
			'display_name'	=> 'Facebook',
			'script'		=> 'facebook.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'company_insider_widget' => array
		(
			'display_name'	=> 'Linkedin Company Insider Widget',
			'script'		=> 'linkedin_company_insider_widget.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'profile_widget' => array
		(
			'display_name'	=> 'Linkedin Profile Widget',
			'script'		=> 'linkedin_profile_widget.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'member_profile_widget' => array
		(
			'display_name'	=> 'Linkedin Member Profile Widget',
			'script'		=> 'linkedin_member_profile_widget.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'linkedin_share_button' => array
		(
			'display_name'	=> 'Linkedin Share Button',
			'script'		=> 'linkedin_share_button.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'linkedin_people_search' => array
		(
			'display_name'	=> 'Linkedin People Search',
			'script'		=> 'linkedin_people_search.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'linkedin_people_search_results' => array
		(
			'display_name'	=> 'Linkedin People Search',
			'script'		=> 'linkedin_people_search_results.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'registration_social' => array
		(
			'display_name'	=> 'Show register block',
			'script'		=> 'registration_social.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'link_with_linkedin' => array
		(
			'display_name'	=> 'Link Profile With Linkedin',
			'script'		=> 'link_with_linkedin.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'social_plugins' => array
		(
			'display_name'	=> 'List Of available Social Plugins',
			'script'		=> 'social_plugins.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'facebook_like_button' => array
		(
			'display_name'	=> 'Facebook Like',
			'script'		=> 'facebook_like_button.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
