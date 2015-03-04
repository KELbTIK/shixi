<?php

return array
(
	'display_name' => 'Private messages',
	'description' => 'Private messages',

	'functions' => array
	(	
		'main' => array
		(
			'display_name'	=> 'Private messages',
			'script'		=> 'private_messages.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'inbox' => array
		(
			'display_name'	=> 'Private messages - inbox',
			'script'		=> 'private_messages_inbox.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'outbox' => array
		(
			'display_name'	=> 'Private messages - outbox',
			'script'		=> 'private_messages_outbox.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),	
		'send' => array
		(
			'display_name'	=> 'Send private message',
			'script'		=> 'private_messages_send.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'read' => array
		(
			'display_name'	=> 'Send private message',
			'script'		=> 'private_messages_read.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'reply' => array
		(
			'display_name'	=> 'Send private message',
			'script'		=> 'private_messages_reply.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'aj_send' => array
		(
			'display_name'	=> 'Send private message',
			'script'		=> 'private_messages_aj_send.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'pm_main' => array
		(
			'display_name'	=> 'Private message',
			'script'		=> 'pm.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'pm_inbox' => array
		(
			'display_name'	=> 'Private message - inbox',
			'script'		=> 'pm_inbox.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'pm_outbox' => array
		(
			'display_name'	=> 'Private message - outbox',
			'script'		=> 'pm_outbox.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'pm_read' => array
		(
			'display_name'	=> 'Private message - read message',
			'script'		=> 'pm_read.php',
			'type'			=> 'admin',
			'access_type'	=> array('admin'),
		),
		'contacts' => array
		(
			'display_name'	=> 'Contacts',
			'script'		=> 'private_messages_contacts.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
		'contact' => array
		(
			'display_name'	=> 'Contact Page',
			'script'		=> 'private_messages_contact.php',
			'type'			=> 'user',
			'access_type'	=> array('user'),
		),
	),
);
