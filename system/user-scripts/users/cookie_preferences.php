<?php

/**
* To comply with the e-Privacy Directive we need to ask user's consent to set cookies.
*/
class SJB_Users_CookiePreferences extends SJB_Function
{
	private static $isSettingsDisabled = false;

	public function __construct(SJB_Acl $acl, $params, $roleID)
	{
		parent::__construct($acl, $params, $roleID);
	}

	public function execute()
	{
		if (!SJB_Settings::getValue('cookieLaw')) {
			return;
		}

		$template    = SJB_Request::getVar('template', 'cookie_preferences.tpl');
		$tp          = SJB_System::getTemplateProcessor();
		$mobileTheme = false;

		if (class_exists('MobilePlugin') && MobilePlugin::isMobileThemeOn()) {
			$this->processingMobileVersion($tp);
			$mobileTheme = true;
		}

		$showCookiePreferences = false;
		$cookiePreferences     = SJB_Request::getVar('cookiePreferences', false, 'COOKIE');
		if (!$cookiePreferences) {
			$showCookiePreferences        = true;
			$_COOKIE['cookiePreferences'] = 'Advertising';
			setcookie('cookiePreferences', 'Advertising', time() + 30 * 24 * 3600, '/');
		}
		else if (($cookiePreferences != 'Advertising' || $mobileTheme)
					&& !SJB_Session::getValue('cookiePreferencesAlreadyShown')) {
			$showCookiePreferences = true;
		}

		$tp->assign('action', SJB_Array::get($this->params, 'action', 'view'));
		$tp->assign('showCookiePreferences', $showCookiePreferences);
		$tp->display($template);

		if (!SJB_Session::getValue('cookiePreferencesAlreadyShown')) {
			SJB_Session::setValue('cookiePreferencesAlreadyShown', true);
		}
	}

	private function processingMobileVersion($tp)
	{
		if (!SJB_Session::getValue('cookiePreferencesAlreadyShown')) {
			$_COOKIE['cookiePreferences'] = 'Advertising';
			setcookie('cookiePreferences', 'Advertising', time() + 30 * 24 * 3600, '/');
		}

		$refererUri = SJB_Request::getVar('HTTP_REFERER', null, 'SERVER');

		if ($refererUri) {
			$refererUri = parse_url($refererUri);
		}

		if ($refererUri && SJB_System::getURI() != '/' . basename($refererUri['path']) . '/') {
			SJB_Session::setValue('cookiePreferencesMobileReferer', SJB_Request::getVar('HTTP_REFERER', SJB_System::getSystemSettings('SITE_URL'), 'SERVER'));
		}

		if (SJB_Request::getVar('cookiePreferencesSave', false, 'POST')) {
			if (!SJB_Request::getVar('Functional', false, 'POST')) {
				$_COOKIE['cookiePreferences'] = 'System';
				setcookie('cookiePreferences', 'System', time() + 30 * 24 * 3600, '/');
			} else if (!SJB_Request::getVar('Advertising', false, 'POST')) {
				$_COOKIE['cookiePreferences'] = 'Functional';
				setcookie('cookiePreferences', 'Functional', time() + 30 * 24 * 3600, '/');
			} else {
				$_COOKIE['cookiePreferences'] = 'Advertising';
				setcookie('cookiePreferences', 'Advertising', time() + 30 * 24 * 3600, '/');
			}
		}

		$tp->assign('mobileVersion', true);
	}

	public static function isAccessibleAdvertising()
	{
		return SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')
			|| !SJB_Settings::getValue('cookieLaw')
			|| SJB_Request::getVar('cookiePreferences', null, 'COOKIE') == 'Advertising';
	}

	public static function isAccessibleFunctional()
	{
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')
				|| !SJB_Settings::getValue('cookieLaw')
				|| SJB_Request::getVar('cookiePreferences', null, 'COOKIE') != 'System') {
			return true;
		}

		self::disableCookieRequiredSettings();
		return false;
	}

	public static function disableCookieRequiredSettings()
	{
		if (!self::$isSettingsDisabled) {
			SJB_Settings::changeValue('fb_likeResume', 0);
			SJB_Settings::changeValue('fb_likeJob', 0);
			SJB_Settings::changeValue('linkedin_resumeAutoFillSync', 0);
			SJB_Settings::changeValue('view_on_map', false);
			self::$isSettingsDisabled = true;
		}
	}

	public static function isPluginDisabled($name)
	{
		if (self::isAccessibleAdvertising()) {
			return false;
		}

		return in_array($name, array('GoogleAnalyticsPlugin'))
			|| (!self::isAccessibleFunctional() && in_array($name, array('IndeedPlugin', 'ShareThisPlugin')));
	}

	public static function isModuleDisabled($name)
	{
		return !self::isAccessibleFunctional() && in_array($name, array('company_insider_widget', 'profile_widget', 'member_profile_widget'));
	}

	public static function isFieldDisabled($fieldType)
	{
		return !self::isAccessibleFunctional() && in_array($fieldType,  array('youtube'));
	}

}
