<?php

define('IN_PHPBB', true);
define('IN_INSTALL', true);

$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

error_reporting(E_ALL ^ E_NOTICE);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
set_config('require_activation', USER_ACTIVATION_DISABLE);
global $config;

if ($config['require_activation'] == USER_ACTIVATION_DISABLE) {
	echo "Registration disabled";
}
else {
	echo "Registration is not disabled";
}