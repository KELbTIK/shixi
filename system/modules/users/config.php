<?php


return array
(
	'display_name' => 'User Management',
	'description' => '',
	'classes' => 'classes/',
	'startup_script'	=>	array (
		'user'	=> 'init_current_user_structure',
		),
	'functions' => array
	(
		'acl' => array
		(
			'display_name'	=> 'Permissions',
			'script'		=> 'acl.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'params'		=> array('type', 'role'),
		),
		'registration' => array
		(
			'display_name'	=> 'Show register block',
			'script'		=> 'registration.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array ('user_group_id'),
		),
		'login_as_user' => array
		(
			'display_name'	=> 'Login as user',
			'script'		=> 'login_as_user.php',
			'type'			=> 'user',
			'access_type'	=> array('admin'),
			'params'		=> array (),
		),
		
		'user_groups' => array
		(
			'display_name'	=> 'Show Users Groups',
			'script'		=> 'user_groups.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'add_user_group' => array
		(
			'display_name'	=> 'Add a New User Group',
			'script'		=> 'add_user_group.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'edit_user_group' => array
		(
			'display_name'	=> 'Edit User Group',
			'script'		=> 'edit_user_group.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'delete_user_group' => array
		(
			'display_name'	=> 'Delete User Group',
			'script'		=> 'delete_user_group.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'edit_user_profile' => array
		(
			'display_name'	=> 'Edit User Profile Fields',
			'script'		=> 'edit_user_profile_fields.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'edit_user_profile_field' => array
		(
			'display_name'	=> 'Edit User Profile Field',
			'script'		=> 'edit_user_profile_field.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'edit_list' => array
		(
			'display_name'	=> 'Edit List',
			'script'		=> 'edit_list.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'edit_list_item' => array
		(
			'display_name'	=> 'Edit List Item',
			'script'		=> 'edit_list_item.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'edit_tree' => array
		(
			'display_name'	=> 'Edit Tree',
			'script'		=> 'edit_tree.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'get_tree' => array
		(
			'display_name'	=> 'Get tree values',
			'script'		=> 'get_tree.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),

		'add_user_profile_field' => array
		(
			'display_name'	=> 'Add User Profile Field',
			'script'		=> 'add_user_profile_field.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'delete_user_profile_field' => array
		(
			'display_name'	=> 'Delete User Profile Field',
			'script'		=> 'delete_user_profile_field.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		
		'users' => array
		(
			'display_name'	=> 'Show Users',
			'script'		=> 'users.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'mailing' => array
		(
			'display_name'	=> 'Mailing',
			'script'		=> 'mailing.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'edit_user' => array
		(
			'display_name'	=> 'Edit User',
			'script'		=> 'edit_user.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'login' => array
		(
			'display_name'	=> 'Login',
			'script'		=> 'login.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array('template'),
		),
		
		'logout' => array
		(
			'display_name'	=> 'Login',
			'script'		=> 'logout.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),

		'activate_account' => array
		(
			'display_name'	=> 'Account activation',
			'script'		=> 'activate_account.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),

		'send_activation_letter' => array
		(
			'display_name'	=> 'Sending Activation Letter',
			'script'		=> 'send_activation_letter.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output'	=> true,
		),

		'password_recovery' => array
		(
			'display_name'	=> 'Password Recovery',
			'script'		=> 'password_recovery.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),

		'change_password' => array
		(
			'display_name'	=> 'Change Password',
			'script'		=> 'change_password.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),

		'edit_profile' => array
		(
			'display_name'	=> 'Edit Profile',
			'script'		=> 'edit_profile.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'raw_output'	=> false,
		),
		
		'user_notifications' => array
		(
			'display_name'	=> 'User Notificaitons',
			'script'		=> 'user_notifications.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),

		'delete_uploaded_file' => array
		(
			'display_name'	=> 'Delete Uploaded File',
			'script'		=> 'delete_uploaded_file.php',
			'type'			=> 'user',
			'access_type'	=> array('user', 'admin'),
			
		),

		'init_current_user_structure' => array
		(
			'display_name'	=> 'Init Current User Structure',
			'script'		=> 'init_current_user_structure.php',
			'type'			=> 'user',
			'access_type'	=> array('user', 'admin'),
			
		),
		
		'add_user' => array
		(
			'display_name'	=> 'Add User',
			'script'		=> 'add_user.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'featured_profiles' => array
		(
			'display_name'	=> 'Featured Companies',
			'script'		=> 'featured_profiles.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array ('template', 'items_count', 'number_of_cols'),
		),
		
		'employers_list' => array
		(
			'display_name'	=> 'Employers List',
			'script'		=> 'employers_list.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array ('user_group_id'),
		),
		
		'banned_ips' => array
		(
			'display_name'	=> 'Banned IPs',
			'script'		=> 'banned_ips.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'user_banned' => array
		(
			'display_name'	=> 'You IP address has been banned',
			'script'		=> 'user_banned.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		
		'sub_accounts' => array
		(
			'display_name'	=> 'Sub accounts',
			'script'		=> 'sub_accounts.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array ('action_name'),
		),
		
		'export_users' => array
		(
			'display_name'	=> 'Export Users',
			'script'		=> 'export_users.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		
		'archive_and_send_export_data' => array
		(
			'display_name'	=> 'Archive And Send Export Data',
			'script'		=> 'archive_and_send_export_data.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output' 	=> true,
		),
		
		'choose_user' => array
		(
			'display_name'	=> 'Choose User',
			'script'		=> 'choose_user.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
			'raw_output' 	=> true,
		),
		
		'edit_location_fields' => array
		(
			'display_name'	=> 'Edit Location Fields',
			'script'		=> 'edit_location_fields.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'instruction_user_profile_field' => array
		(
			'display_name'	=> 'Instruction on user profile field',
			'script'		=> 'instruction_user_profile_field.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),

		'cookie_preferences' => array
		(
			'display_name'	=> 'Cookie Preferences',
			'script'		=> 'cookie_preferences.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
			'params'		=> array('action')
		),
	)
	
);