<?php

class SJB_GuestAlerts_Confirm extends SJB_Function
{
	public function execute()
	{
		$key = SJB_Request::getVar('key', '', 'GET');
		$tp = SJB_System::getTemplateProcessor();
		$error = '';
		try {
			$guestAlert = SJB_GuestAlertManager::getGuestAlertByKey($key);
			$guestAlert->setStatusActiveFromUnconfirmed();
			$guestAlert->update();
			SJB_Notifications::sendGuestAlertWelcomeEmail($guestAlert);
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}

		$tp->assign('error', $error);
		$tp->display('confirm.tpl');
	}
}
