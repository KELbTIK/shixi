<?php
return array
(
	'display_name' => 'Rating',
	'description' => 'rating',
	'startup_script'	=>	array (),
	'functions' => array
	(
		'listing_rating' => array
		(
			'display_name'	=> 'Listing Rating',
			'script'		=> 'listing_rating.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
