<?php

class SJB_Admin_Miscellaneous_EmailLog extends SJB_Function
{
	const EMAIL_ERRORS = 'emailErrors';
	const EMAIL_LOG_MESSAGE = 'emailLogMessage';
	const DO_NOT_SHOW_ATTACHMENT_NOTIFICATION = 'doNotShowAttachmentNotification';

	protected $emailSIDsFaildToSend = array();
	protected $errors;

	public function isAccessible()
	{
		$this->setPermissionLabel('email_log');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp 		= SJB_System::getTemplateProcessor();
		$restore 	= SJB_Request::getVar('restore', false);
		$action 	= SJB_Request::getVar('action_name', 'view_log');
		$action 	= $action == 'search' ? 'view_log' : $action;
		$paginator  = new SJB_EmailLogPagination();
		$display_error 	= SJB_Request::getVar('display_error', false);
		$savedErrors = SJB_Session::getValue(self::EMAIL_ERRORS);
		SJB_Session::unsetValue(self::EMAIL_ERRORS);
		$this->errors = $savedErrors ? $savedErrors : array();
		$message = SJB_Session::getValue(self::EMAIL_LOG_MESSAGE);
		SJB_Session::unsetValue(self::EMAIL_LOG_MESSAGE);

		switch ($action) {
			case 'display_message':
				$sid = SJB_Request::getVar('sid', false);
				$email = SJB_EmailLogManager::getEmailInfoBySID($sid);
				$tp->assign('display_error', $display_error);
				$tp->assign('email', $email);
				$tp->display('display_log_message.tpl');
				return true;
				break;

			case 'resend':
				try {
					SJB_Settings::saveSetting(self::DO_NOT_SHOW_ATTACHMENT_NOTIFICATION, SJB_Request::getVar(self::DO_NOT_SHOW_ATTACHMENT_NOTIFICATION));
					$this->resendEmails();
					SJB_Session::setValue(self::EMAIL_LOG_MESSAGE, 'The message(s) were successfully resent.');
				} catch (Exception $e) {
					array_push($this->errors, $e->getMessage());
				}
				SJB_Session::setValue(self::EMAIL_ERRORS, $this->errors);
				$searchFields = SJB_Request::getVar('searchFields');
				SJB_HelperFunctions::redirect(SJB_HelperFunctions::getSiteUrl() . '/email-log/?sorting_field=' . $paginator->sortingField . '&sorting_order=' . $paginator->sortingOrder . '&items_per_page=' . $paginator->itemsPerPage. $searchFields);
				break;

			case 'view_log':
				$userSID = SJB_Request::getVar('user_sid', false);
				if ($userSID) {
					$template = 'user_email_log.tpl';
					$_REQUEST['username']['equal'] = $userSID;
					$userInfo = SJB_UserManager::getUserInfoBySID($userSID);
					$userGroupInfo = SJB_UserGroupManager::getUserGroupInfoBySID($userInfo['user_group_sid']);
					$tp->assign('userGroupInfo', $userGroupInfo);
					$tp->assign('display_error', $display_error);
					$tp->assign('user_info', $userInfo);
					$tp->assign('user_sid', $userSID);
				}
				$email = new SJB_EmailLog($_REQUEST);
				$search_form_builder = new SJB_SearchFormBuilder($email);
				$criteria_saver = new SJB_EmailLogCriteriaSaver();
				if ($restore)
					$_REQUEST = array_merge($_REQUEST, $criteria_saver->getCriteria());

				$criteria = $search_form_builder->extractCriteriaFromRequestData($_REQUEST, $email);
				if (!$userSID) {
					$template = 'email_log.tpl';
					$search_form_builder->setCriteria($criteria);
					$search_form_builder->registerTags($tp);

					$tp->display("email_log_search_form.tpl");
				}
				$order_info = array(
					'sorting_field' => $paginator->sortingField,
					'sorting_order' => $paginator->sortingOrder);
				$criteria_saver->setSessionForOrderInfo($order_info);
				$criteria_saver->setSessionForCurrentPage($paginator->currentPage);
				$criteria_saver->setSessionForListingsPerPage($paginator->itemsPerPage);
				$searcher = new SJB_EmailLogSearcher(array('limit' => ($paginator->currentPage - 1) * $paginator->itemsPerPage, 'num_rows' => $paginator->itemsPerPage), $paginator->sortingField, $paginator->sortingOrder);

				$found_emails = $searcher->getObjectsSIDsByCriteria($criteria);
				$criteria_saver->setSession($_REQUEST, $searcher->getFoundObjectSIDs());

				foreach ($found_emails as $id => $emailSID) {
					$emailInfo = SJB_EmailLogManager::getEmailInfoBySID($emailSID);
					$found_emails[$id] = $emailInfo;
					$found_emails[$id]['user'] = !empty($emailInfo['username']) ? SJB_UserManager::getUserInfoBySID($emailInfo['username']) : array();
					$found_emails[$id]['admin'] = array();
					if (!empty($emailInfo['admin'])) {
						if (is_numeric($emailInfo['admin']))
							$found_emails[$id]['admin'] = SJB_SubAdminManager::getSubAdminInfoBySID($emailInfo['admin']);
						else
							$found_emails[$id]['admin']['username'] = 'admin';
					}
				}

				$paginator->setItemsCount($searcher->getAffectedRows());

				$searchFields = '';
				foreach ($_REQUEST as $key => $val) {
					if (is_array($val)) {
						foreach ($val as $fieldName => $fieldValue) {
							$searchFields .= "&{$key}[{$fieldName}]={$fieldValue}";
						}
					}
				}

				$tp->assign(self::DO_NOT_SHOW_ATTACHMENT_NOTIFICATION, SJB_Settings::getSettingByName(self::DO_NOT_SHOW_ATTACHMENT_NOTIFICATION));
				$tp->assign('message', $message);
				$tp->assign('errors', $this->errors);
				$tp->assign("searchFields", $searchFields);
				$tp->assign('paginationInfo', $paginator->getPaginationInfo());
				$tp->assign("found_emails", $found_emails);
				$tp->display($template);
				break;
		}
	}

	/**
	 * @return bool
	 * @throws Exception
	 */
	protected function resendEmails()
	{
		$emailsIDsToResend = SJB_Request::getVar('emails', array());

		if (!is_array($emailsIDsToResend)) {
			throw new Exception('Not valid params to resend email');
		}

		foreach ($emailsIDsToResend as $emailSID) {
			try {
				$this->resendEmailBySID($emailSID);
			} catch (Exception $e) {
				SJB_Error::writeToLog($e->getMessage() . ' Email Log ID:' . $emailSID);
				array_push($this->emailSIDsFaildToSend, $emailSID);
			}
		}

		if (!empty($this->emailSIDsFaildToSend)) {
			throw new Exception('The message(s) were not resent. Please check the settings');
		}

		return true;
	}

	/**
	 * @param $emailSID
	 * @throws Exception
	 */
	protected function resendEmailBySID($emailSID)
	{
		$emailToSend = SJB_EmailLogManager::getObjectBySID($emailSID);
		if (!$emailToSend instanceof SJB_EmailLog) {
			throw new Exception('Not valid email log ID to resend');
		}

		$email = new SJB_Email($emailToSend->getPropertyValue('email'));
		$email->setSubject($emailToSend->getPropertyValue('subject'));
		$email->setText($emailToSend->getPropertyValue('message'));
		if (!$email->send()) {
			throw new Exception('Email log > Resend Function: The message were not resent. Please check the settings');
		}
	}
}
