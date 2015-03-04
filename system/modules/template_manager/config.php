<?php

return array
(
	'display_name' => 'Template manager',
	'description' => 'Managing tamplates',
	'classes' => 'classes/',
	'functions' => array
	(
		'upload_logo' => array
		(
			'upload_logo'		=> 'Upload logo',
			'script'		=> 'upload_logo.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_css' => array
		(
			'edit_css'		=> 'Edit css files',
			'script'		=> 'edit_css.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_templates' => array
		(
			'edit_templates'=> 'Display Modules',
			'script'		=> 'edit_templates.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_page_templates' => array
		(
			'display_name'	=> 'Edit page templates',
			'script'		=> 'edit_page_templates.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),		
		'module_list' => array
		(
			'display_name'	=> 'Display Modules',
			'script'		=> 'module_list.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'template_list' => array
		(
			'display_name'	=> '',
			'script'		=> 'template_list.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_template' => array
		(
			'display_name'	=> '',
			'script'		=> 'edit_template.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'theme_editor' => array
		(
			'display_name'	=> 'Theme Editor',
			'script'		=> 'edit_theme.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'page_template_list' => array
		(
			'display_name'	=> 'Page template list',
			'script'		=> 'page_template_list.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'add_template' => array
		(
			'display_name'	=> 'Add template',
			'script'		=> 'add_template.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'edit_email_templates' => array
		(
			'display_name'	=> 'Display Modules',
			'script'		=> 'edit_email_templates.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'delete_uploaded_file' => array
		(
			'display_name'	=> 'Delete Uploaded File',
			'script'		=> 'email_templates_delete_uploaded_file.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
	)
);
