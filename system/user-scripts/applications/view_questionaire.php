<?php

class SJB_Applications_ViewQuestionnaire extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		if (SJB_UserManager::isUserLoggedIn() === false) {
			$tp->assign('ERROR', 'NOT_LOGIN');
			$tp->display('../miscellaneous/error.tpl');
			return;
		}
		$appsSID = 0;

		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$appsSID = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}

		if (SJB_Applications::isUserOwnerApps(SJB_UserManager::getCurrentUserSID(), $appsSID)) {
			$apps_info = SJB_Applications::getBySID($appsSID);
			if (!empty($apps_info['questionnaire'])) {
				$questions = unserialize($apps_info['questionnaire']);
				$tp->assign('questions', $questions);
			}
			$tp->assign('apps_info', $apps_info);
			$tp->display('view_questionaire.tpl');
		}
	}
}