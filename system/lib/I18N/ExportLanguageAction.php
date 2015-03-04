<?php


class SJB_ExportLanguageAction
{
	/**
	 * @var SJB_I18N
	 */
	protected $i18n;


	function SJB_ExportLanguageAction($i18n, $lang_id)
	{
		$this->i18n = $i18n;
		$this->lang_id = $lang_id;
	}

	function canPerform()
	{
		return $this->i18n->languageExists($this->lang_id);
	}

	function perform()
	{
		$fileBaseName = $this->i18n->getFileNameForLangExportFile($this->lang_id);
		SJB_WrappedFunctions::header('Content-Type: application/download');
		SJB_WrappedFunctions::header('Content-disposition: attachment; filename=' . $fileBaseName);
		echo $this->i18n->exportLanguage($this->lang_id);
	}

	function getErrors()
	{
		return array('LANGUAGE_NOT_EXISTS');
	}
}

