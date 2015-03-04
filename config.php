<?php

$protocol = 'http://';
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
	$protocol = 'https://';
return array(
	'HTTPHOST' => 'localhost',
	'BASEURL' => '',
	'DBHOST' => 'localhost',
	'DBNAME' => 'shixi',
	'DBUSER' => 'root',
	'DBPASSWORD' => '',
	'DBADAPTER' => 'Pdo_Mysql',
	'MYSQL_CHARSET' => 'utf8',
	'SITE_URL' => $protocol . $_SERVER['HTTP_HOST'] . '',
	'USER_SITE_URL' => $protocol . $_SERVER['HTTP_HOST'] . '',
	'ADMIN_SITE_URL' => $protocol . $_SERVER['HTTP_HOST'] . '/admin',
);