<?php

$timeBegin = microtime(true);
error_reporting(-1);
ini_set('display_errors', 'on');

//Browse By Category Fix
$_SERVER['REQUEST_URI'] = str_replace("-or-", "%2F", $_SERVER['REQUEST_URI']);

$PATH_BASE = dirname(__FILE__);
$DEBUG     = array();
$ds	  = DIRECTORY_SEPARATOR;
$path = $PATH_BASE."{$ds}system{$ds}cache{$ds}agents_bots.txt";
if (file_exists($path)) {
	$agents    = str_replace("\r", '', file_get_contents($path));
	$agents    = explode("\n", $agents);
	$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'NoUserAgent'; // for example: JobG8 not send UserAgent
	foreach ($agents as $agent) {
		if ($agent && strstr($userAgent, $agent)) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');// no such page in configuration
			include($PATH_BASE.'/temp/403.php');
			exit;
		}
	}
}

define ('PATH_TO_SYSTEM_CLASS','system/core/System.php');
define ('SJB_BASE_DIR', dirname(__FILE__ )."/");

//         start of the script actions
require_once(PATH_TO_SYSTEM_CLASS);

SJB_System::loadSystemSettings ('system/user-config/DefaultSettings.php');
SJB_System::loadSystemSettings ('config.php');

if (is_null(SJB_System::getSystemSettings('SITE_URL'))) {
	header("Location: install.php");
	exit;
}
else {
	if (is_readable ("install.php") && SJB_System::getSystemSettings('IGNORE_INSTALLER') != 'true') {
		echo '<p>Your installation is temporarily disabled because the install.php file in the root of your'
		.' installation is still readable.<br> To proceed, please remove the file or change its mode to make'
		.' it non-readable for the Apache server process and refresh this page.</p>';
		exit;
	}
}

SJB_System::boot();
SJB_System::init();
if (SJB_Profiler::getInstance()->isProfilerEnable()) {
	SJB_Profiler::getInstance()->setStartTime($timeBegin);
}
SJB_Event::dispatch('AfterSystemBoot');

SJB_UpdateManager::updateDatabase();

	//bind send notification emails if listing deactivated/deleted
    SJB_Event::handle( 'listingDeactivated', array( 'SJB_Notifications', 'notifyOnUserListingDeactivated' ) );
    SJB_Event::handle( 'beforeListingDelete', array( 'SJB_Notifications', 'notifyOnUserListingDeleted' ) );

	//bind send notification emails if user deactivated/deleted
    SJB_Event::handle( 'onBeforeUserDelete', array( 'SJB_Notifications', 'notifyOnUserDeleted' ) );
    SJB_Event::handle( 'onBeforeUserDeactivate', array( 'SJB_Notifications', 'notifyOnUserDeactivated' ) );

	// bind session clear to task scheduler event
	SJB_Event::handle( 'task_scheduler_run', array('SJB_Session', 'clearTemporaryData'));


SJB_Request::getInstance()->execute();
SJB_Statistics::addStatistics('siteView', '', 0, true);
$isPseudoCronEnabled = intval( SJB_Settings::getSettingByName('isPseudoCronEnabled') ) === 1;

if ($isPseudoCronEnabled) {
	$isEmailSchedulerEnabled = intval(SJB_Settings::getSettingByName('email_scheduling')) === 1;
	$isOncePerHourCondition = SJB_Settings::getSettingByName('emailSchedule_lastTimeExecuted') < strtotime('1 hour ago');
	if ($isEmailSchedulerEnabled && $isOncePerHourCondition) {
		SJB_Settings::updateSetting('emailSchedule_lastTimeExecuted', time());
		SJB_System::getModuleManager()->executeFunction('miscellaneous', 'email_scheduling');
	}

    $numberOfPageViewsSinceLastTime = SJB_Settings::getValue('pseudoCron_numberOfPageViewsSinceLastTime');
	$isPageViewCondition = intval(SJB_Settings::getValue('pseudoCron_numberOfPageViewsSinceLastTime')) > SJB_Settings::getSettingByName('numberOfPageViewsToExecCronIfExceeded');
    if ($isPageViewCondition) {
		SJB_Settings::updateSetting('pseudoCron_numberOfPageViewsSinceLastTime', 0);
		list($month, $day, $year ) = explode('.', SJB_Settings::getSettingByName('task_scheduler_last_executed_date'));
		$isOnceADayCondition = strtotime("{$year}-{$month}-{$day}") < strtotime('today');
		if ($isOnceADayCondition) {
			SJB_HelperFunctions::runScriptByCurl(SJB_System::getSystemSettings('SITE_URL') . '/system/miscellaneous/task_scheduler/');
		}
	}
	else {
		SJB_Settings::updateSetting('pseudoCron_numberOfPageViewsSinceLastTime', $numberOfPageViewsSinceLastTime + 1);
	}
}

SJB_HelperFunctions::debugInfoPrint();
