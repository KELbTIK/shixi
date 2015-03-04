<?php
return array
(
	'display_name' => 'Comments',
	'description' => 'Comments',
	'startup_script'	=>	array (),
	'functions' => array
	(
		'to' => array
		(
			'display_name'	=> 'Comments to Listing',
			'script'		=> 'listing_comments.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array('listing_id')
		),
		'comment' => array
		(
			'display_name'	=> 'Comments to Listing',
			'script'		=> 'comment.php',
			'type'			=> 'user',
			'access_type'	=> array('user')
		),
		'listing_comments' => array
		(
			'display_name'	=> 'Listing Comments',
			'script'		=> 'listing_comments.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
