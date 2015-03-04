<?php


return array
(
	'display_name' => 'I18N',
	'description' => '',
	'startup_script' =>	array (),
	'functions' => array
	(
		'add_language' => array
		(
			'display_name'	=> 'Add Language',
			'script'		=> 'add_language.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'edit_language' => array
		(
			'display_name'	=> 'Edit Language',
			'script'		=> 'update_language.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		), 

		'manage_languages' => array
		(
			'display_name'	=> 'Languages',
			'script'		=> 'languages.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		), 

		'manage_phrases' => array
		(
			'display_name'	=> 'Manage Phrases',
			'script'		=> 'manage_phrases.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'add_phrase' => array
		(
			'display_name'	=> 'Add Phrase',
			'script'		=> 'add_phrase.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),	
			
		'edit_phrase' => array
		(
			'display_name'	=> 'Edit Phrase',
			'script'		=> 'update_phrase.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'auto_translate' => array
		(
			'raw_output'	=> true,
			'display_name'	=> 'Auto Translate',
			'script'		=> 'auto_translate.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		/*'import_translations' => array
		(
			'display_name'	=> 'Import Translations',
			'script'		=> 'import_translations.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'export_translations' => array
		(
			'display_name'	=> 'Export Translations',
			'script'		=> 'export_translations.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),*/
		
		
		'import_language' => array
		(
			'display_name'	=> 'Import Language',
			'script'		=> 'import_language.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		
		'export_language' => array
		(
			'display_name'	=> 'Export Language',
			'script'		=> 'export_language.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)	
);
