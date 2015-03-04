<?php

return array
(
	'display_name' => 'Flash Message',
	'description'  => '',
	
	'startup_script' => array(),
	'functions' => array
	(
		'display' => array
		(
			'display_name' => 'Show Messages',
			'script'       => 'display.php',
			'type'         => 'user',
			'access_type'  => array('admin', 'user'),
		)
	)
);
