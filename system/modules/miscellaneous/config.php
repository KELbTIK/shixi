<?php

return array
(
	'display_name' => 'Miscellaneous',
	'description' => 'Miscellaneous routines',

	'startup_script'	=>	array (),

	'functions' => array
	(
		'geographic_data' => array
		(
			'display_name'	=> 'Geographic Data',
			'script'		=> 'geographic_data.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'currency' => array
		(
			'display_name'	=> 'Manage Currencies',
			'script'		=> 'currency.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'import_geographic_data' => array
		(
			'display_name'	=> 'Geographic Data',
			'script'		=> 'import_geographic_data.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'edit_location' => array
		(
			'display_name'	=> 'Edit Location',
			'script'		=> 'edit_location.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'add_location' => array
		(
			'display_name'	=> 'Add Location',
			'script'		=> 'add_location.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'settings' => array
		(
			'display_name'	=> 'Settings',
			'script'		=> 'settings.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'filters' => array
		(
			'display_name'	=> 'filters',
			'script'		=> 'filters.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
				
		'task_scheduler' => array
		(
			'display_name'	=> 'Settings',
			'script'		=> 'task_scheduler.php',
			'raw_output'		=> true,
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		
		'task_scheduler_settings' => array
		(
			'display_name'	=> 'Task Scheduler Settings',
			'script'		=> 'task_scheduler_settings.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'ajax' => array
		(
			'display_name'	=> 'Ajax',
			'script'		=> 'ajax.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'adminpswd' => array
		(
			'display_name'	=> 'Admin Password',
			'script'		=> 'adminpswd.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'contact_form' => array
		(
			'display_name'	=> 'Contact Form',
			'script'		=> 'contact_form.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		
		'upgradeFieldToMonetary' => array
        (
            'display_name'    => 'Contact Form',
            'script'        => 'upgradeFieldToMonetary.php',
            'type'            => 'user',
            'access_type'    => array('user'),
        ),
        
		'plugins' => array
		(
			'display_name'	=> 'Plugins',
			'script'		=> 'plugins.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'backup' => array
		(
			'display_name'	=> 'Backup',
			'script'		=> 'backup.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'blog_page' => array
		(
			'display_name'	=> 'Blog Page',
			'script'		=> 'blog_page.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),

		'flag_listing_settings' => array
		(
			'display_name'	=> 'Flag Listing Settings',
			'script'		=> 'flag_listing_settings.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'sitemap_generator' => array
		(
			'display_name'	=> 'sitemap generator',
			'script'		=> 'sitemap_generator.php',
			'raw_output'		=> true,
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'captcha_handle' => array
		(
			'display_name'  => 'Captcha Handle',
			'script'        => 'captchaHandle.php',
			'raw_output'    => true,
			'type'          => 'user',
			'access_type'   => array('user', 'admin'),
		),
		'captcha' => array
		(
			'display_name'	=> 'captcha',
			'script'		=> 'captcha.php',
			'raw_output'		=> true,
			'type'			=> 'user',
			'access_type'	=> array('user', 'admin'),
		),
		'reloadCustomCaptcha' => array
		(
			'display_name'	=> 'reloadCustomCaptcha',
			'script'		=> 'reloadCustomCaptcha.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'maintenance_mode' => array
		(
			'display_name'	=> 'Maintenance Mode',
			'script'		=> 'maintenance_mode.php',
			'raw_output'		=> true,
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
        'email_scheduling' => array
		(
			'display_name'	=> 'Email Scheduling',
			'script'		=> 'email_scheduling.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'		=> true,
		),
		'autocomplete' => array
		(
			'display_name'	=> 'Autocomplete',
			'script'		=> 'autocomplete.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),		
		'partnersite' => array
		(
			'display_name'	=> 'partnersite',
			'script'		=> 'partnersite.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'access_denied' => array
		(
			'display_name'	=> 'access_denied',
			'script'		=> 'access_denied.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		
		'email_log' => array
		(
			'display_name'	=> 'Email Log',
			'script'		=> 'email_log.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'function_is_not_accessible' => array
		(
			'display_name'	=> 'Function is not accessible',
			'script'		=> 'error.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'function_is_not_accessible_for_subadmin' => array
		(
			'display_name'	=> 'Function is not accessible',
			'script'		=> 'subadmin-error.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'get_image_marker' => array(
			'display_name'	=> 'Get Image Marker',
			'script'		=> 'get_image_marker.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'ajax_file_upload_handler' => array
		(
			'display_name'	=> 'Ajax File Upload Handler',
			'script'		=> 'ajax_file_upload_handler.php',
			'type'			=> 'user',
			'access_type'	=> array('user', 'admin'),
			'raw_output'	=> true,
		),
		'kcfinder' => array
		(
			'display_name'	=> 'KCFinder',
			'script'		=> 'kcfinder.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'update_to_new_version' => array
		(
			'display_name'	=> 'Update to new version of SJB',
			'script'		=> 'update_to_new_version.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'update_check' => array
		(
			'display_name'	=> 'Check for SJB update',
			'script'		=> 'update_check.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'update_diff' => array(
			'display_name'	=> 'Viw files difference',
			'script'		=> 'update_diff.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'countries' => array(
			'display_name'	=> 'Countries',
			'script'		=> 'countries.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'states' => array(
			'display_name'	=> 'States',
			'script'		=> 'states.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'get_states' => array
		(
			'display_name'	=> 'Get States Names',
			'script'		=> 'get_states.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'get_user_states' => array
		(
			'display_name'	=> 'Get States Names',
			'script'		=> 'get_states.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> true,
		),
		'profiler' => array
		(
			'display_name' => 'Profiler',
			'script' => 'profiler.php',
			'type' => 'user',
			'access_type'	=> array('user'),
		),
		'admin_profiler' => array
		(
			'display_name' => 'Profiler',
			'script' => 'profiler.php',
			'type' => 'user',
			'access_type'	=> array('admin'),
		),
		'mail_check' => array
		(
			'display_name'	=> 'Mail Check',
			'script'		=> 'mail_check.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'opengraph_meta' => array
		(
			'display_name' => 'OpenGraph Meta',
			'script' => 'opengraph_meta.php',
			'type' => 'user',
			'access_type'	=> array('user'),
		),
	),
);
