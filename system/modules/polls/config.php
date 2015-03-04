<?php

return array
(
	'display_name' => 'Polls',
	'description' => '',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'manage_polls' => array(
				'display_name'	=> 'Manage Polls',
				'script'		=> 'manage_polls.php',
				'type'			=> 'admin',
				'access_type'	=> array('admin'),
				'raw_output'	=> false,
				),
		'polls' => array(
				'display_name'	=> 'Polls',
				'script'		=> 'polls.php',
				'type'			=> 'user',
				'access_type'	=> array('user'),
				),
		'poll_results' => array(
				'display_name'	=> 'View Poll Results',
				'script'		=> 'poll_results.php',
				'type'			=> 'user',
				'access_type'	=> array('user'),
				),
	)
);
