<?php

class SJB_Admin_Errors_ViewErrorLog extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('configure_system_settings');
		return parent::isAccessible();
	}
	
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		//how many error records get from log
		$recordsNum = SJB_Request::getVar('recordsNum', 10);

		$errorLog = SJB_Error::getErrorLog($recordsNum);

		$tp->assign('recordsNum', $recordsNum);
		$tp->assign('errorLog', $errorLog);

		$tp->display('view_error_log.tpl');
	}
}