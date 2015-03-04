<?php

return array
(
	'display_name' => 'News',
	'description' => 'News module',

	'startup_script'	=>	array (),

	'functions' => array
	(
		
		'news_categories' => array
		(
			'display_name'	=> 'Manage News Categories',
			'script'		=> 'news_categories.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'manage_news' => array
		(
			'display_name'	=> 'Manage News',
			'script'		=> 'manage_news.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'show_news' => array
		(
			'display_name'	=> 'Show News',
			'script'		=> 'news.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		
		'article_details' => array
		(
			'display_name'	=> 'News Details',
			'script'		=> 'article_details.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		)
	)
);
