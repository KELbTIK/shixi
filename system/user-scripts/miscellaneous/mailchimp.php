<?php

class SJB_Miscellaneous_Mailchimp extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$error = false;
		$submitted = SJB_Request::getVar('subscribe', null, 'GET');

		if ($submitted) {
			$name = SJB_Request::getVar('mch_name', null, 'GET');
			$email = SJB_Request::getVar('mch_email', null, 'GET');

			if ($email && $name) {
				if (MailChimpPlugin::subscribeUser('', $email, $name, $error)) {
					$tp->assign('message', 'Subscribed - look for the confirmation email!');
				}
			}
			else {
				$error = 'EMPTY_FIELD';
			}
		}

		$tp->assign('error', $error);
		$tp->display('mailchimp_subscribe.tpl');
	}

}
