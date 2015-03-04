<?php

class SJB_GuestAlerts_Replace extends SJB_GuestAlerts_Create
{
	public function execute()
	{
		$guestAlert = new SJB_GuestAlert($_REQUEST);
		$guestAlert->addDataProperty(serialize($this->criteriaData));
		$tp = SJB_System::getTemplateProcessor();
		try {
			$guestAlertSID = SJB_GuestAlertManager::getGuestAlertSIDByEmail($guestAlert->getAlertEmail());
			$guestAlert->setSID($guestAlertSID);
			$guestAlert->update();
		} catch (Exception $e) {
			$tp->assign('error', $e->getMessage());
		}
		$tp->display('alert_replaced.tpl');
	}
}
