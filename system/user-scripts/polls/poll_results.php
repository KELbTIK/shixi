<?php

class SJB_Polls_PollResults extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$sid = false;
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$sid = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}
		$cu = SJB_UserManager::getCurrentUser();
		if (!isset($cu->user_group_sid)) {
			$userGroupSID = 0;
		} else {
			$userGroupSID = $cu->user_group_sid;
		}
		$i18n = SJB_I18N::getInstance();
		$lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$langId = $lang['id'];

		if ($sid && SJB_PollsManager::isActive($sid, $userGroupSID, $langId)) {
			$countVotes = SJB_PollsManager::getCountVotesBySID($sid);
			$pollResults = SJB_PollsManager::getPollResultsBySID($sid);
			$result = array();
			$i = 0;
			$colors = array('613978', 'aad434', 'f55c00', 'f9c635', 'f97c9e', '870000', '0ec300', '6f6f6f', '0400a5', '6eeffb', '000000', 'ff00ff');
			foreach ($pollResults as $poll) {
				$result[$i]['vote'] = $countVotes > 0 ? round((100 / $countVotes) * $poll['count'], 2) : 0;
				$result[$i]['value'] = $poll['question'];
				$result[$i]['color'] = $colors[$i];
				$i++;
			}
			$pollInfo = SJB_PollsManager::getPollInfoBySID($sid);
			$tp->assign('pollInfo', $pollInfo);
			$tp->assign('result', $result);
			$tp->assign('width', (count($pollResults) * 40) + (count($pollResults) - 1) * 3);
			$tp->assign('show_total_votes', isset($pollInfo['show_total_votes']) ? $pollInfo['show_total_votes'] : 0);
			$tp->assign('count_vote', $countVotes);
		}
		else {
			$pollInfo = SJB_PollsManager::getPollInfoBySID($sid);
			if ($pollInfo['language'] != $langId)
				$errors[] = 'This poll is not available for this language'; 
		}
		$tp->assign('errors', $errors);
		$tp->display('poll_results.tpl');
	}
}
