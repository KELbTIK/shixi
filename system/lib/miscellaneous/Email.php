<?php

include_once('PHPMailer/class.phpmailer.php');

class SJB_Email
{
	protected $email;
	
	function __construct($recipient_email, $template = false, $data = false)
	{
		$this->email = new SJB_EmailInternal($recipient_email, $template, $data);
	}
	
	function __call($method, $params)
	{
		$result = call_user_func_array(array($this->email, $method), $params);
		if ($method == 'send' && is_array($result))  {
			$error_msg	= isset($result['error_msg'])	? $result['error_msg']	: false;
			$result		= isset($result['status'])		? $result['status']		: false;
			SJB_EmailLog::writeToLog($this->email, $result, $error_msg);
		}
		return $result;
	}
	
	function __get($property) 
	{
		return $this->email->$property;
	}
	
	function __set($property, $value)
	{
		$this->email->$property = $value;
	}
}


class SJB_EmailInternal
{
	var $mail = NULL;
	var $text = NULL;

	const EMAIL_DATA_LABEL = 'emailData';

	public function setText($text)
	{
		$this->text = $text;
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
	}

	var $subject 		 = NULL;
	var $recipient_email = NULL;

	var $reply_to = NULL;
	var $fileAttachment = null;
	
	private $cc = array();
	private $fromName = '';
	private $fromEmail = '';

	/**
	 * @var SJB_I18N
	 */
	public $i18n;

	function SJB_EmailInternal($recipient_email, $template = false, $data = array())
	{
		$this->recipient_email = $recipient_email;
		if ($template) {
			$tp = SJB_System::getTemplateProcessor();
			$this->registerTags($tp);
			foreach ($data as $key => $value) {
				if (self::EMAIL_DATA_LABEL == $key) {
					$this->parseEmailData($value, $tp);
				}
				$tp->assign($key, $value);
			}

			$at = $tp->getSystemAccessType();
			$tp->setSystemAccessType('user');
			$tp->fetch($template);
			$tp->setSystemAccessType($at);
		}
	}

	/**
	 * @param array $value
	 * @param SJB_TemplateProcessor $tp
	 */
	protected function parseEmailData(&$value, SJB_TemplateProcessor $tp)
	{
		foreach ($value as &$emailDataVal) {
			$emailDataVal = $tp->fetch('eval:' . $emailDataVal);
		}
	}

	public function addCC($cc)
	{
		array_push($this->cc, $cc);
	}

	/**
	 * @param SJB_TemplateProcessor $tp
	 */
	function registerTags(SJB_TemplateProcessor &$tp)
	{
		$tp->registerPlugin('block', 'fromName', array(&$this, 'parseLetterFromName'));
		$tp->registerPlugin('block', 'subject', array(&$this, 'parseLetterSubject'));
		$tp->registerPlugin('block', 'message', array(&$this, 'parseLetterMessage'));
	}
		
	function translate($params, $phrase_id, &$smarty, $repeat)
	{
		if ($repeat) {
			return null; // see Smarty manual
		}

		$this->i18n = SJB_I18N::getInstance();
		$mode = isset($params['mode']) ? $params['mode'] : null;
		$phrase_id = trim($phrase_id);
		$res = $this->i18n->gettext('', $phrase_id, $mode);
		return $this->replace_with_template_vars($res, $smarty);
	}

	function replace_with_template_vars($res, &$smarty)
	{
		if (preg_match_all("/{[$]([a-zA-Z0-9_]+)}/", $res, $matches)) {
			foreach($matches[1] as $varName){
				$value = $smarty->getTemplateVars($varName);
				$res = preg_replace("/{[$]".$varName."}/u",$value,$res);
			}
		}
		return $res;
	}

	public function parseLetterFromName($params, $content)
	{
		$this->fromName = $content;
	}

	function parseLetterSubject($params, $content, &$tp, &$repeat)
	{
		$this->subject = $content;
	}

	function parseLetterMessage($params, $content, &$tp, &$repeat)
	{
		$this->text = $content;
	}

	function getText()
	{
		return $this->text;
	}
	
	function setReplyTo($reply_to)
	{
		$this->reply_to = $reply_to;
	}
	
	function setFile($file)
	{
		$this->fileAttachment = $file;
	}

	function send($cron = false)
	{
		if (self::emailScheduling($cron)) {
			if (empty($this->recipient_email)) {
				return false;
			}

			try {
				$mailSettings = array(
					'smtp' => SJB_Settings::getSettingByName('smtp'),
					'smtp_host' => SJB_Settings::getSettingByName('smtp_host'),
					'smtp_port' => SJB_Settings::getSettingByName('smtp_port'),
					'smtp_sender' => SJB_Settings::getSettingByName('smtp_sender'),
					'smtp_username' => SJB_Settings::getSettingByName('smtp_username'),
					'smtp_password' => SJB_Settings::getSettingByName('smtp_password'),
					'smtp_security' => SJB_Settings::getSettingByName('smtp_security'),
					'sendmail_path' => SJB_Settings::getSettingByName('sendmail_path'),
					'system_email' => SJB_Settings::getSettingByName('system_email'),
					'FromName' => SJB_Settings::getSettingByName('FromName')
				);

				$mail = $this->prepareMail($mailSettings);
				$sent = $mail->Send();

				return array('status' => $sent);
			}
			catch (Exception $e) {
				SJB_Error::logError(E_WARNING, $e->getMessage(), $e->getFile(), $e->getLine());
			}
			return array('status' => false, 'error_msg' => $e->getMessage());
		}
		return !$cron;
	}

	public function prepareMail($mailSettings)
	{
		$mail = new PHPMailer(true);
		$mail->MsgHTML($this->text);
		$mail->From = $this->getFromEmail() ? $this->getFromEmail() : $mailSettings['system_email'];
		$mail->Sender = $mail->From;
		$mail->FromName = $this->getFromName() ? $this->getFromName() : $mailSettings['FromName'];
		$mail->Subject = $this->subject;
		$mail->AddAddress($this->recipient_email);
		$mail->CharSet = "UTF-8";

		if ($mailSettings['smtp'] == 1) {
			$mail->IsSMTP();
			$mail->Port = $mailSettings['smtp_port'];
			$mail->SMTPAuth = true;
			$mail->Host = $mailSettings['smtp_host'];
			$mail->Username = $mailSettings['smtp_username'];
			$mail->Password = $mailSettings['smtp_password'];
			$mail->Sender = $mailSettings['smtp_sender'];
			$mail->From = $mail->Sender;
			$mail->AddReplyTo($mailSettings['system_email']);
			$smtpSecurity = $mailSettings['smtp_security'];

			if ($smtpSecurity != 'none') {
				$mail->set('SMTPSecure', $smtpSecurity);
			}
		} elseif ($mailSettings['smtp'] == 0) {
			if ($mailSettings['sendmail_path'] != '') {
				$mail->isSendmail();
				$mail->Sendmail =  $mailSettings['sendmail_path'];
			}
		}

		if (!empty($this->cc)) {
			if (is_array($this->cc)) {
				foreach ($this->cc as $cc) {
					$mail->AddCC($cc);
				}
			} else {
				$mail->AddCC($this->cc);
			}
		}

		if (!empty($this->reply_to)) {
			$mail->AddReplyTo($this->reply_to);
		}

		if ($this->fileAttachment) {
			$mail->AddAttachment($this->fileAttachment);
		}
		return $mail;
	}


	function emailScheduling($cron = false)
	{
		$email_scheduling = SJB_Settings::getSettingByName('email_scheduling');
		$number_emails = SJB_Settings::getSettingByName('number_emails');

		if ($email_scheduling && $number_emails) {
			$send_emails = SJB_Settings::getSettingByName('send_emails');
			$time_sending_emails = SJB_Settings::getSettingByName('time_sending_emails');

			if (!$time_sending_emails) {
				SJB_Settings::updateSettings(array('time_sending_emails' => time()));
			} else {
				$now = time();
				$period = $now - $time_sending_emails;

				if ($period > 3600) {
					$send_emails = 0;
					SJB_Settings::updateSettings(array('time_sending_emails' => 0));
				}
			}

			if (!$send_emails) {
				SJB_Settings::updateSettings(array('send_emails' => 1));
				return true;
			} else {
				if ($send_emails < $number_emails) {
					$send_emails++;
					SJB_Settings::updateSettings(array('send_emails' => $send_emails));
					return true;
				} else if (!$cron) {
					SJB_DB::query("INSERT INTO email_scheduling (email , subject , text , file) VALUES ( ?s, ?s, ?s, ?s)",
						$this->recipient_email, $this->subject, $this->text, ltrim($this->fileAttachment, './')
					);
				}
				return false;
			}
		}
		return true;
	}

	public function setFromName($fromName)
	{
		$this->fromName = $fromName;
	}

	public function getFromName()
	{
		return $this->fromName;
	}

	public function setFromEmail($fromEmail)
	{
		$this->fromEmail = $fromEmail;
	}

	public function getFromEmail()
	{
		return $this->fromEmail;
	}

	public function setRecipientEmail($recipient_email)
	{
		$this->recipient_email = $recipient_email;
	}
}

class SJB_EmailNone extends SJB_Email
{
	/**
	 * @param string $emailName
	 * @return bool
	 */
	public function send($emailName = '')
	{
		$i18n = SJB_I18N::getInstance();
		$notificationName = $i18n->gettext('Backend', $emailName);
		$text = $i18n->gettext('Backend', 'email was not sent because template for it was not found.');
		$errorMsg = '"'.$notificationName.'" ' . $text;
		$this->email->setSubject($errorMsg);
		$this->email->setText($errorMsg);
		SJB_EmailLog::writeToLog($this->email, 'Not Sent', $errorMsg);
		return false;
	}
}

class SJB_EmailDoNotSend extends SJB_Email
{
	public function __construct()
	{
	}

	public function send()
	{
		return false;
	}
}

