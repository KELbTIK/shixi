<?php


class I18NSwitchLanguageAgent
{
	/**
	 * 
	 * @var SJB_I18N
	 */
	private $i18n;

	/**
	 * @var I18NContext
	 */
	public $context;

	/**
	 * @var SJB_Session
	 */
	public $session;

	/**
	 * @param SJB_Session $session
	 */
	function setSession(SJB_Session $session)
	{
		$this->session = $session;
	}

	/**
	 * @param I18NContext $context
	 */
	function setContext(I18NContext $context)
	{
		$this->context = $context;
	}

	/**
	 * @param SJB_I18N $i18n
	 */
	function setI18N(SJB_I18N $i18n)
	{
		$this->i18n = $i18n;
	}

	/**
	 * returns current active language id
	 * @return mixed
	 */
	function execute()
	{
		$existLanguage = $this->fetchExistLanguage();
		if ($existLanguage !== $this->context->getLang()) {
			$this->context->setLang($existLanguage);
			$theme = $this->context->getTheme();
			if (!empty($theme))
				$this->session->setValue('CURRENT_THEME', $theme);
		}
		return $this->context->getLang();
	}

	/**
	 * @return mixed
	 */
	function fetchExistLanguage()
	{
		$lang_priority = array (
			SJB_Request::getVar('lang', null),
			$this->context->getLang(),
			$this->context->getDefaultLang()
		);

		foreach ($lang_priority as $lang) {
			if (!$lang)
				continue;
			$this->context->langSettings->setCurrentLangID($lang);
			if ($this->i18n->languageExists($lang) && $this->i18n->isLanguageActive($lang)) {
				return $lang;
			}
		}
	}
}
