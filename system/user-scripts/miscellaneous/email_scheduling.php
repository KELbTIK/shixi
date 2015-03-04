<?php

class SJB_Miscellaneous_EmailScheduling extends SJB_Function
{
	public function execute()
	{
		set_time_limit(0);

		$notifiedEmails = array();
		$emailScheduling = SJB_Settings::getSettingByName('email_scheduling');
		$numberEmails = SJB_Settings::getSettingByName('number_emails');
		$emailsSend = SJB_Settings::getSettingByName('send_emails');
		$limit = $numberEmails - $emailsSend;
		$limit = ($limit > 0) ? $limit : 20;
		$letters = SJB_DB::query('SELECT * FROM `email_scheduling` ORDER BY `id` ASC LIMIT 0, ?n', $limit);

		if ($emailScheduling && $numberEmails || count($letters)) {
			foreach ($letters as $letter) {
				$params = $letter;
				unset($params['id']);

				$email = new SJB_Email($params['email']);
				$email->setSubject($params['subject']);
				$email->setText($params['text']);
				$email->setFile($params['file']);
				if ($email->send(true)) {
					SJB_DB::query('DELETE FROM `email_scheduling` WHERE `id` = ?n', $letter['id']);
					array_push($notifiedEmails, $params['email']);
				}
			}
		}

		$tp = SJB_System::getTemplateProcessor();

		$tp->assign('notified_emails', $notifiedEmails);
		$schedulerLog = $tp->fetch('email_scheduler_log.tpl');

		SJB_HelperFunctions::writeCronLogFile('email_scheduler.log', $schedulerLog);
	}
}