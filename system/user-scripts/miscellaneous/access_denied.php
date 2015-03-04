<?php

class SJB_Miscellaneous_AccessDenied extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$permission = SJB_Request::getVar('permission', false);

		if ($permission) {
			$acl = SJB_Acl::getInstance();
			$tp->assign('message', $acl->getPermissionMessage($permission));
		}
		$tp->display('denied_option_message.tpl');
	}
}