<?php

$timeBegin = microtime(true);
error_reporting(-1);
ini_set('display_errors', 'on');

define ('PATH_TO_SYSTEM_CLASS','../system/core/System.php');
$DEBUG = array();
$PATH_BASE = str_replace('/admin', '', dirname(__FILE__));

require_once(PATH_TO_SYSTEM_CLASS);
define ('SJB_BASE_DIR', realpath(dirname(__FILE__ ) . "/..") . '/');
SJB_System::loadSystemSettings ('../system/admin-config/DefaultSettings.php');
SJB_System::loadSystemSettings ('../config.php');

$GLOBALS['system_settings']['USER_SITE_URL'] = $GLOBALS['system_settings']['SITE_URL'];
$GLOBALS['system_settings']['SITE_URL'] = $GLOBALS['system_settings']['ADMIN_SITE_URL'];

// load installed SJB version info
SJB_System::setGlobalTemplateVariable('version', SJB_System::getSystemSettings('version'));

SJB_System::boot();
SJB_System::init();
if (SJB_Profiler::getInstance()->isProfilerEnable()) {
	SJB_Profiler::getInstance()->setStartTime($timeBegin);
}

// bind send notification emails if listing deactivated/deleted
SJB_Event::handle('listingDeactivated', array('SJB_Notifications', 'notifyOnUserListingDeactivated'));
SJB_Event::handle('beforeListingDelete', array('SJB_Notifications', 'notifyOnUserListingDeleted'));

// bind send notification emails if user deactivated/deleted
SJB_Event::handle('onBeforeUserDelete', array('SJB_Notifications', 'notifyOnUserDeleted'));
SJB_Event::handle('onBeforeUserDeactivate', array('SJB_Notifications', 'notifyOnUserDeactivated'));

SJB_Request::getInstance()->execute();

SJB_HelperFunctions::debugInfoPrint();
