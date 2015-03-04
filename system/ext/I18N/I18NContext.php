<?php

class I18NContext
{
	/**
	 * @var SJB_System
	 */
	public $systemSettings;

	/**
	 * @var SJB_Settings
	 */
	public $settings;

	/**
	 * @var I18NLanguageSettings
	 */
	public $langSettings;

	/**
	 * @var bool
	 */
	private $_config = true;


	/**
	 * @param SJB_Settings $settings
	 */
	function setSettings(SJB_Settings $settings){
		$this->settings = $settings;
	}
	function setSession(&$session){
		$this->session =& $session;
	}

	/**
	 * @param I18NLanguageSettings $settings
	 */
	function setLanguageSettings(I18NLanguageSettings $settings){
		$this->langSettings = $settings;
	}

	/**
	 * @param SJB_System $settings
	 */
	function setSystemSettings(SJB_System $settings)
	{
		$this->systemSettings = $settings;
	}

	function getLang()
	{
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')) {
			return SJB_Request::getVar('langAdmin', false, 'COOKIE');
		}

		if (SJB_Settings::getValue('CURRENT_THEME') == 'mobile') {
			return $this->getDefaultLang();
		}

		if (SJB_Users_CookiePreferences::isAccessibleFunctional()) {
			return SJB_Request::getVar('langUser', false, 'COOKIE');
		}

		return $this->session->getValue('lang');
	}

	function setLang($lang)
	{
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')) {
			$_COOKIE['langAdmin'] = $lang;
			if (isset($_REQUEST['lang'])) {
				return setcookie('langAdmin', $lang, time() + 30 * 24 * 3600, '/');
			}
		}

		if (SJB_Users_CookiePreferences::isAccessibleFunctional()) {
			$_COOKIE['langUser'] = $lang;
			if (isset($_REQUEST['lang'])) {
				return setcookie('langUser', $lang, time() + 30 * 24 * 3600, '/');
			}
		}

		return $this->session->setValue('lang', $lang);
	}

	function getDefaultLang(){
		return $this->settings->getSettingByName('i18n_default_language');
	}
	function getDefaultDomain(){
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') == SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')) {
			return 'Backend';
		}
		return $this->settings->getSettingByName('i18n_default_domain');
	}
	function getHighlightedPattern(){
		return $this->systemSettings->getSystemSettings('I18NSettings_HighlightedPattern');
	}
	function getAdminSiteUrl(){
		return $this->systemSettings->getSystemSettings('ADMIN_SITE_URL');
	}
	function getDefaultMode(){
		return $this->settings->getSettingByName('i18n_display_mode_for_not_translated_phrases');
	}
	function getPathToLanguageFiles(){
		return $this->systemSettings->getSystemSettings('I18NSettings_PathToLanguageFiles');
	}
	function getFileNameTemplateForLanguageFile(){
		return $this->systemSettings->getSystemSettings('I18NSettings_FileNameTemplateForLanguageFile');
	}
	function getFileNameTemplateForLanguageExportFile(){
		return $this->systemSettings->getSystemSettings('I18NSettings_FileNameTemplateForLanguageExportFile');
	}
	function getFileNameTemplateForLanguagePagesFile(){
			return $this->systemSettings->getSystemSettings('I18NSettings_FileNameTemplateForLanguagePagesFile');
	}
	function getDecimalPoint(){
		return $this->langSettings->getDecimalPoint();
	}
	function getThousandsSeparator(){
		return $this->langSettings->getThousandsSeparator();
	}
	function getDecimals(){
		return $this->langSettings->getDecimals();
	}
	function getDateFormat(){
		return $this->langSettings->getDateFormat();
	}
	function getTheme(){
		return $this->langSettings->getTheme();
	}
	function getLanguageIDMaxLength() {
		return $this->systemSettings->getSystemSettings('LanguageIDMaxLength');
	}
	function getLanguageCaptionMaxLength() {
		return $this->systemSettings->getSystemSettings('LanguageCaptionMaxLength');
	}
	function getDateFormatValidSymbols(){
		return $this->systemSettings->getSystemSettings('DateFormatValidSymbols');
	}
	function getDateFormatMaxLength() {
		return $this->systemSettings->getSystemSettings('DateFormatMaxLength');
	}
	function getValidThousandsSeparators() {
		return $this->systemSettings->getSystemSettings('ValidThousandsSeparators');
	}
	function getValidDecimalsSeparators() {
		return $this->systemSettings->getSystemSettings('ValidDecimalsSeparators');
	}
	function getPhraseIDMaxLength(){
		return $this->systemSettings->getSystemSettings('PhraseIDMaxLength');
	}
	function getTranslationMaxLength(){
		return $this->systemSettings->getSystemSettings('TranslationMaxLength');
	}
	function setDefaultLang($lang_id){
		return $this->settings->updateSetting('i18n_default_language', $lang_id);
	}

	/**
	 * @param boolean $config
	 */
	public function setConfig($config)
	{
		$this->_config = $config;
	}

	/**
	 * @return boolean
	 */
	public function getConfig()
	{
		return $this->_config;
	}

}
