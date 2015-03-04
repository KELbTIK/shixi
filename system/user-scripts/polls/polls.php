<?php
class SJB_Polls_Polls extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$pollSID = SJB_Request::getVar('sid', 0, null, 'int');
		$cu = SJB_UserManager::getCurrentUser();
		$i18n = SJB_I18N::getInstance();
		$lang = $i18n->getLanguageData($i18n->getCurrentLanguage());
		$langId = $lang['id'];

		if (!$pollSID) {
			$pollSID = SJB_PollsManager::getPollForDisplay($cu->user_group_sid, $langId);
		}

		if ($pollSID) {
			if (SJB_PollsManager::isActive($pollSID, $cu->user_group_sid, $langId)) {
				$action = SJB_Request::getVar('action', false);
				$IP = $_SERVER['REMOTE_ADDR'];
				$isVoted = SJB_PollsManager::isVoted($pollSID, $IP);
				switch ($action) {
					case 'save':
						$value = SJB_Request::getVar('poll', false);
						if ($value && $pollSID && !$isVoted) {
							SJB_PollsManager::addPollResult($pollSID, $value, $IP);
							$isVoted = true;
						}
						break;
				}
				$poll_info = SJB_PollsManager::getPollInfoBySID($pollSID);

				$poll = new SJB_UserPollsManager($poll_info);
				$poll->setSID($poll_info['sid']);

				$edit_form = new SJB_Form($poll);
				$edit_form->registerTags($tp);

				$form_fields = $edit_form->getFormFieldsInfo();
				$tp->assign('display_results', $poll_info['display_results']);
				$tp->assign('question', trim(strip_tags($poll_info['question'])));
				$tp->assign('isVoted', $isVoted);
				$tp->assign('form_fields', $form_fields);
				$tp->assign('sid', $pollSID);
				$tp->display('polls.tpl');
			}
		}
	}
}