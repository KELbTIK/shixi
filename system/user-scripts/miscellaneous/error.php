<?php

class SJB_Miscellaneous_FunctionIsNotAccessible extends SJB_Function
{
	public function execute()
	{
		$pageConfig = SJB_Request::getInstance()->page_config;
		$tp = SJB_System::getTemplateProcessor();
		
		if (isset($this->params['ERROR'])) {
			$tp->assign('ERROR', $this->params['ERROR']);
		}
		else if (SJB_UserManager::isUserLoggedIn()) {
			$tp->assign('ERROR', 'ACCESS_DENIED');
		} else {
			$tp->assign('ERROR', 'NOT_LOGIN');
		}
		
		$tp->assign('page_function', $pageConfig->function);
		$tp->display('../miscellaneous/error.tpl');
	}
}