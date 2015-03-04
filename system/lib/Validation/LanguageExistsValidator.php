<?php


class SJB_LanguageExistsValidator
{
	/**
	 * @var I18NDataSource
	 */
	public $langDataSource;

	/**
	 * @param I18NDataSource $langDataSource
	 */
	function setLanguageDataSource(I18NDataSource $langDataSource)
	{
		$this->langDataSource = $langDataSource;
	}

	/**
	 * @param string $value
	 * @return bool
	 */
	function isValid($value)
	{
		if (SJB_Request::getVar('fast',null)) {
			$language = $this->langDataSource->getLanguageData($value);

			if ($language instanceof LangData) {
				return true;
			}

			return false;
		}

		$languages = $this->langDataSource->getLanguagesData();
		for ($i = 0; $i < count($languages); $i++)
		{
			$language = $languages[$i];
			if ((string)$value === (string)$language->getID())
				return true;
		}
		return false;
	}
}

