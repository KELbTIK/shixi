<?php

class SJB_Admin_Users_SendActivationLetter extends SJB_Function
{
	public function isAccessible()
	{
		$userSidList = SJB_Request::getVar('userids', null);
		$userSid = array_shift($userSidList);
		$userGroupID = SJB_UserGroupManager::getUserGroupIDByUserSID($userSid);
		$this->setPermissionLabel('manage_' . strtolower($userGroupID));
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		if (isset($_REQUEST['ajax'])) {
			$sent = 0;
			$ids = array();
			if (isset ($_REQUEST['userids'])) {
				$ids = $_REQUEST['userids'];
				foreach ($ids as $user_sid)
					if (!empty($user_sid) && SJB_Notifications::sendUserActivationLetter($user_sid))
						$sent++;
			}
			$tp->assign("countOfSuccessfulSent", $sent);
			$tp->assign("countOfUnsuccessfulSent", count($ids) - $sent);
			$tp->display("send_activation_letter.tpl");
			exit;
		}

		$user_sid = SJB_Request::getVar('usersid', null);

		$error = null;
		if (!SJB_UserManager::getObjectBySID($user_sid)) {
			$error = "USER_DOES_NOT_EXIST";
		} elseif (!SJB_Notifications::sendUserActivationLetter($user_sid)) {
			$error = "CANNOT_SEND_EMAIL";
		}

		$tp->assign("error", $error);
		$tp->display("send_activation_letter.tpl");
	}
}
