<?php

class SJB_GuestAlerts_Unsubscribe extends SJB_Function
{
	public function execute()
	{
		$key = SJB_Request::getVar('key', '', 'GET');
		$tp = SJB_System::getTemplateProcessor();
		$error = '';
		try {
			$guestAlert = SJB_GuestAlertManager::getGuestAlertByKey($key);
			$guestAlert->setStatusUnSubscribed();
			$guestAlert->update();
			$tp->assign('email', $guestAlert->getAlertEmail());
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}

		$tp->assign('error', $error);
		$tp->display('unsubscribe.tpl');
	}
}
