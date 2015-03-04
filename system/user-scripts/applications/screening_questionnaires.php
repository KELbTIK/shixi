<?php

class SJB_Applications_ScreeningQuestionnaires extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('use_screening_questionnaires');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = SJB_Request::getVar('action', 'list');
		$sid = SJB_Request::getVar('sid', null, null, 'int');
		if (SJB_Acl::getInstance()->isAllowed('use_screening_questionnaires')) {
			switch ($action) {
				case 'delete':
					if (SJB_ScreeningQuestionnaires::isUserOwnerQuestionnaire(SJB_UserManager::getCurrentUserSID(), $sid)) {
						SJB_ScreeningQuestionnaires::deleteQuestionnaireBySID($sid);
					}
					$action = 'list';
					break;
			}
			
			$tp->assign('questionnaires', SJB_ScreeningQuestionnaires::getList(SJB_UserManager::getCurrentUserSID()));
			$tp->assign('action', $action);
			$tp->display('screening_questionnaires.tpl');
		}
	}
}