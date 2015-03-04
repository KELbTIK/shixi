<?php

return array
(
	'display_name' => 'Social Media',
	'description'  => 'Social Media',

	'startup_script' => array (),

	'functions' => array
	(
		'bitly' => array
		(
			'display_name'  => 'Bitly',
			'script'        => 'bitly.php',
			'type'          => 'admin',
			'access_type'   => array('admin')
		),
		'social_media' => array
		(
			'display_name' => 'Social Media',
			'script'       => 'social_media.php',
			'type'         => 'admin',
			'access_type'  => array('admin'),
		),
		'social_posting' => array
		(
			'display_name' => 'Social Posting',
			'script'       => 'social_posting.php',
			'type'         => 'user',
			'access_type'  => array('user'),
		),
	),
);
