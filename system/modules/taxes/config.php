<?php

return array
(
	'display_name' => 'Taxes',
	'description' => '',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'manage_taxes' => array(
			'display_name'	=> 'Manage Taxes',
			'script'		=> 'manage_taxes.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> false,
		),
	)
);
