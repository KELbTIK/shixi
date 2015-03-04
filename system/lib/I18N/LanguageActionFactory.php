<?php

class SJB_LanguageActionFactory
{
	public static function get($action, $params)
	{
		if (SJB_System::getSystemSettings('isDemo'))
			return new SJB_LanguageAction();
		
		$i18n = SJB_I18N::getInstance();
		$lang = isset($params['languageId']) ? $params['languageId'] : null;
		switch ($action)
		{
			case 'set_default_language':
				return new SJB_SetDefaultLanguageAction($i18n, $lang);
				break;
			case 'add_language':
				return new SJB_AddLanguageAction($i18n, $params);
				break;
			case 'update_language':
				return new SJB_UpdateLanguageAction($i18n, $params);
				break;
			case 'delete_language':
				return new SJB_DeleteLanguageAction($i18n, $lang);
				break;
			case 'import_language':
				return new SJB_ImportLanguageAction($i18n, $params);
				break;
			case 'export_language':
				return new SJB_ExportLanguageAction($i18n, $lang);
				break;
			default: 
				return new SJB_LanguageAction();
		}
	}
}

