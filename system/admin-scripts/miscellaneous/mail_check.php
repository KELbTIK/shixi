<?php

class SJB_Admin_Miscellaneous_MailCheck extends SJB_Function
{

	public function isAccessible()
	{
		return true;
	}

	public function execute()
	{
		$error = array();

		// Getting variables from settings page

		$mailSettings = array(
			'smtp' => SJB_Request::getVar('smtp'),
			'smtp_host' => SJB_Request::getVar('smtp_host'),
			'smtp_port' => SJB_Request::getVar('smtp_port'),
			'smtp_sender' => SJB_Request::getVar('smtp_sender'),
			'smtp_username' => SJB_Request::getVar('smtp_username'),
			'smtp_password' => SJB_Request::getVar('smtp_password'),
			'smtp_security' => SJB_Request::getVar('smtp_security'),
			'sendmail_path' => SJB_Request::getVar('sendmail_path'),
			'system_email' => SJB_Request::getVar('system_email'),
			'FromName' => SJB_Request::getVar('FromName')
		);

		// Validation of System Email Field
		if ($mailSettings['system_email']) {
			if (!preg_match('/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,}$/', $mailSettings['system_email'])) {
				$error['NOT_VALID'][] = 'system_email';
			}
		} else {
			$error['EMPTY_VALUE'][] = 'system_email';
		}

		// Check if SMTP fields have empty values.

		if ($mailSettings['smtp'] == 1) {
			foreach ($mailSettings as $key => $value) {
				if ($value == '') {
					if ($key == 'sendmail_path' || $key == 'system_email') {
						continue;
					}
					$error['EMPTY_VALUE'][] = $key;
				}
			}
			if (!preg_match('/^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,}$/', $mailSettings['smtp_sender']) && !empty($mailSettings['smtp_sender'])) {
				$error['NOT_VALID'][] = 'smtp_sender';
			}
		}

		if ($mailSettings['smtp'] == 0) {

			if ($mailSettings['sendmail_path'] == '') {
				$error['EMPTY_VALUE'][] = 'sendmail_path';
			}
			if (!preg_match('/^([\/][\w\d]*)+$/', $mailSettings['sendmail_path'])) {
				$error['NOT_VALID'][] = 'sendmail_path';
			}
		}

		if ($error) {
			echo json_encode(array('status' => 'fieldError', 'message' => $error, 'type' => 'error'));
		} else {
			try {
				$email = new SJB_Email(SJB_Settings::getSettingByName('test_email'));
				$email->setSubject('testing');
				$email->setText('testing');
				$mail = $email->prepareMail($mailSettings);

				if ($mailSettings['smtp'] == 1) {
					$sent =  $mail->SmtpConnect();
				} else {
					$sent = $mail->Send();
				}
				echo json_encode(array('status' => $sent, 'message' => 'checked', 'type' => 'message'));
			} catch (Exception $e) {
				echo json_encode(array('status' => false, 'message' => 'failed', 'type' => 'error'));
			}
		}
	}
}
