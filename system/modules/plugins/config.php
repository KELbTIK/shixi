<?php


return array
(
	'display_name' => 'Plugins',
	'description' => 'Plugins',
	'classes' => 'classes/',
	'functions' => array
	(
		'partnersite' => array
		(
			'display_name'	=> 'partnersite',
			'script'		=> 'indeed/partnersite.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
