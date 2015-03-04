<?php

class SJB_Admin_Users_Mailing extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('create_and_send_mass_mailings');
		return parent::isAccessible();
	}

	public function execute()
	{
		set_time_limit(0);
		ini_set('memory_limit', -1);

		$tp = SJB_System::getTemplateProcessor();
		$user_groups_info = SJB_UserGroupManager::getAllUserGroupsInfo();
		$user_group_info = reset($user_groups_info);
		$user_group_sid = $user_group_info['sid'];
		$fields_info = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group_sid);
		$fields = array();
		$tp->assign('test_message', SJB_Request::getVar('test_message', false));
		$tp->assign('undeliveredMailingsForTest', SJB_Request::getVar('undeliveredMailingsForTest', false));

		foreach ($fields_info as $key => $val) {
			if ($val['id'] == 'Location') {
				foreach ($val['fields'] as $field) {
					if ($field['id'] == 'Country') {
						$fields['country'] = SJB_CountriesManager::getAllCountriesCodesAndNames();
					} elseif ($field['id'] == 'State') {
						$fields['state'] = SJB_StatesManager::getStatesNamesByCountry();
					}
				}
			}
		}

		$tp->assign('fields', $fields);

		$errors = array();
		$errorId = SJB_Request::getVar('error', null, 'GET');
		if ($errorId) {
			$errors[$errorId] = 1;
		}
		
		if (isset($_REQUEST['submit']) && $_FILES['file_mail']['name'] && $_FILES['file_mail']['error']) {
			$errorId = SJB_UploadFileManager::getErrorId($_FILES['file_mail']['error']);
			
			if ($_REQUEST['submit'] != 'save') {
				$mailID = SJB_Request::getVar('mail_id', 0);
				$parameter = ($mailID) ? '?edit=' . $mailID : '';
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/mailing/' . $parameter . '&error=' . $errorId);
			}
			
			$errors[$errorId] = 1;
		}
		else if (isset($_REQUEST['submit'])) {
			SJB_DB::query("DELETE FROM uploaded_files WHERE id = 'file_mail'");
			$upload_manager = new SJB_UploadFileManager();
			$upload_manager->setFileGroup('files');
			$upload_manager->setUploadedFileID('file_mail');
			$upload_manager->uploadFile('file_mail');

			$file_name = '';

			if (!isset($_REQUEST['delete_file']) && isset($_REQUEST['old_file']) && !$upload_manager->getUploadedFileName('file_mail'))
				$file_name = $_REQUEST['old_file'];
			elseif ($upload_manager->getUploadedFileName('file_mail'))
				$file_name = "files/files/" . $upload_manager->getUploadedSavedFileName('file_mail');

			$language = SJB_Request::getVar('language', 'any');
			$users = SJB_Request::getVar('users', 'any');
			$without_cv = SJB_Request::getVar('without_cv', false);
			$country = SJB_Request::getVar('country', '');
			$state = SJB_Request::getVar('state', '');
			$city = SJB_Request::getVar('city', '');
			$products = SJB_Request::getVar('products', array());
			$user_status = SJB_Request::getVar('user_status', '');
			$registration_date = SJB_Request::getVar('registration_date', array());

			$param = serialize(
				array(
					'language' => $language,
					'users' => $users,
					'without_cv' => $without_cv,
					'products' => $products,
					'country' => $country,
					'state' => $state,
					'city' => $city,
					'status' => $user_status,
					'registration' => $registration_date,
				));

			$email = '';

			$mailSubject = SJB_Request::getVar('subject', '');
			$mailText = stripcslashes(SJB_Request::getVar('text', ''));
			$mailID = SJB_Request::getVar('mail_id', 0);

			if ($mailID) {
				SJB_DB::query('UPDATE `mailing` SET
					`subject` 	= ?s,
					`text` 		= ?s,
					`email` 	= ?s,
					`file` 		= ?s,
					`param` 	= ?s
				WHERE `id` 	= ?s',
					$mailSubject, $mailText, $email, $file_name, $param, $mailID);
			}
			else {
				$query = "INSERT INTO mailing ( email , subject , text , file, param) VALUES ( ?s, ?s, ?s, ?s, ?s)";
				SJB_DB::query($query, $email, $mailSubject, $mailText, $file_name, $param);
			}

			if ($_REQUEST['submit'] == 'save') {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/mailing/');
			} else {
				$parameter = ($mailID) ? '?edit=' . $mailID : '';
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/mailing/' . $parameter);
			}
		}

		if (SJB_Request::getVar('delete')) {
			$mailings = SJB_Request::getVar('mailing');
			if (is_array($mailings)) {
				foreach ($mailings as $id => $value) {
					SJB_DB::query('DELETE FROM `mailing` WHERE `id` = ?n', $id);
					SJB_DB::query('DELETE FROM `mailing_info` WHERE `mailing_id` = ?n', $id);
				}
			} else {
				$idToDelete = SJB_Request::getInt('delete', 0);
				SJB_DB::query('DELETE FROM `mailing` WHERE `id` = ?n', $idToDelete);
				SJB_DB::query('DELETE FROM `mailing_info` WHERE `mailing_id` = ?n', $idToDelete);
			}
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/mailing/');
		}


		if (isset($_REQUEST['edit'])) {
			$idToEdit = SJB_Request::getInt('edit', 0);
			$mail_arr = SJB_DB::query('SELECT * FROM mailing WHERE id = ?n', $idToEdit);

			$tp->assign("mail_id", $mail_arr[0]['id']);
			$tp->assign("subject", $mail_arr[0]['subject']);
			$tp->assign("text", $mail_arr[0]['text']);
			$tp->assign("file", $mail_arr[0]['file']);
			$tp->assign("file_url", $mail_arr[0]['file']);
			$tp->assign("param", unserialize($mail_arr[0]['param']));
		}

		// get products by UserGroup ID
		if (SJB_Request::isAjax()) {
			$userGroupID = SJB_Request::getVar('usergr', 0);

			if ($userGroupID > 0) {
				$products = SJB_ProductsManager::getProductsInfoByUserGroupSID($userGroupID);
			}
			else {
				$products = SJB_ProductsManager::getAllProductsInfo();
			}

			$tp->assign("products", $products);
			$tp->display("mailing_products.tpl");

			exit();
		}

		$mail_list = SJB_DB::query('SELECT * FROM mailing');
		foreach ($mail_list as $key => $var) {
			$param = unserialize($mail_list[$key]['param']);

			$where = '';
			$join = '';

			$numSentEmails = SJB_DB::queryValue('SELECT count(*) FROM `mailing_info` WHERE `mailing_id` = ?n AND `status`=0', $var['id']);

			if ($param["language"] != 'any')
				$where .= " and language='{$param['language']}'";
			if ($param["users"] != '0')
				$where .= ' and u.user_group_sid=' . $param['users'];
			if ($param["without_cv"]) {
				$join = "left join listings l on l.user_sid = u.sid";
				$where .= " and l.sid is null";
			}
			// user status
			if (!empty($param['status'])) {
				$where .= ' and `u`.`active`=' . (int)$param['status'];
			}

			// registration date
			if (!empty($param['registration']) && is_array($param['registration'])) {
				$i18n = SJB_I18N::getInstance();

				if (!empty($param['registration']['not_less']))
					$where .= ' AND `u`.`registration_date` > \'' . $i18n->getInput('date', $param['registration']['not_less']) . '\'';
				if (!empty($param['registration']['not_more']))
					$where .= ' AND `u`.`registration_date` < \'' . $i18n->getInput('date', $param['registration']['not_more']) . '\'';
			}

			// products
			if (!empty ($param['products'])) {
				$join .= "
            LEFT JOIN contracts ON u.sid = contracts.user_sid
            LEFT JOIN products ON products.sid = contracts.product_sid
        ";

				$whereProduct = array();

				foreach ($param['products'] as $theProduct) {
					$theProduct = (int)$theProduct;

					if (!empty ($theProduct))
						$whereProduct[] .= "products.sid = '{$theProduct}'";
					else
						$whereProduct[] .= 'products.sid IS NULL';
				}

				if (!empty($whereProduct))
					$where .= ' AND (' . implode(' OR ', $whereProduct) . ')';
			} /// products

			if (!empty ($param['country']) || !empty ($param['state'])) {

				if (!empty ($param['country'])) {
					$where_country = array();
					foreach ($param['country'] as $the_country) {
						if (!empty ($the_country))
							$where_country[] .= "`u`.`Location_Country` = '{$the_country}'";
						else
							$where_country[] .= "`u`.`Location_Country` IS NULL";
					}

					if (!empty($where_country))
						$where .= ' AND (' . implode(' OR ', $where_country) . ')';
				}

				if (!empty ($param['state'])) {
					$where_state = array();
					foreach ($param['state'] as $the_state) {
						if (!empty ($the_state))
							$where_state[] .= "`u`.`Location_State` = '{$the_state}'";
						else
							$where_state[] .= "`u`.`Location_State` IS NULL";
					}
				}

				if (!empty($where_state))
					$where .= ' AND (' . implode(' OR ', $where_state) . ')';

				if (!empty($param['city']))
					$where .= " AND `u`.`Location_City` = '{$param['city']}'";
			}

			$mail_list[$key]['not_send'] = $numSentEmails;
			$mail_list[$key]['mail_arr'] = SJB_DB::query("
        SELECT u.sid as sid, u.username, u.user_group_sid, u.language
        FROM users u
            {$join}
            WHERE u.sendmail = 0
            {$where}
            GROUP BY `u`.`sid`");

			$mail_list[$key]['count'] = count($mail_list[$key]['mail_arr']);
		}

		/*
		 * test sending
		 */
		$testMailingID = SJB_Request::getVar('test_send', 0);

		if ($testMailingID) {
			if ($this->isTestEmailValid()) {
				$testSendResult = false;
				$oMailing = new SJB_Mailing($testMailingID);
				$mailings = SJB_Request::getVar('mailing');
				if (is_array($mailings)) {
					foreach ($mailings as $id => $value) {
						$oMailing->setMailingID($id);
						$oMailing->setMailingList($mail_list);
						if ($oMailing->testSend()) {
							$testSendResult = true;
						}
					}
				} else {
					$oMailing->setMailingList($mail_list);
					$testSendResult = $oMailing->testSend();
				}
				if ($testSendResult) {
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/mailing/?test_message=1');
				} else {
					$email = urlencode(SJB_Request::getString('email', false));
					SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/mailing/?undeliveredMailingsForTest={$email}");
				}
			} else {
				$tp->assign('testEmailNotValid', true);
			}
		}


		// general sending
		$sendToMailingID = SJB_Request::getVar('sending', 0);
		$sendResult = false;
		if ($sendToMailingID) {
			$oMailing = new SJB_Mailing($sendToMailingID);
			$mailings = SJB_Request::getVar('mailing');
			$undeliveredMailingsInfo = array();
			if (is_array($mailings)) {
				foreach ($mailings as $id => $value) {
					$oMailing->setMailingID($id);
					$oMailing->setMailingList($mail_list);
					$countOfSendMailings = $oMailing->send();
					if ($countOfSendMailings <> 0) {
						$sendResult = true;
					}
					$undeliveredMailingsInfo = array_merge($oMailing->getUndeliveredMailingsInfo(), $undeliveredMailingsInfo);
				}
			} else {
				$oMailing->setMailingList($mail_list);
				$countOfSendMailings = $oMailing->send();
				if ($countOfSendMailings <> 0) {
					$sendResult = true;
				}
				$undeliveredMailingsInfo = $oMailing->getUndeliveredMailingsInfo();
			}
				if ($sendResult) {
				$tp->assign('send_result', $sendResult);
			}
			if (count($undeliveredMailingsInfo)) {
				$tp->assign("UndeliveredMailings", $oMailing->getUndeliveredMailingsInfo());
			}
		}

		// send mailing to undelivered
		$sendToUndeliveredMailingID = SJB_Request::getVar('sendToUndeliveredEmails', 0);

		if (!empty($sendToUndeliveredMailingID)) {
			$oMailing = new SJB_Mailing($sendToUndeliveredMailingID);
			$oMailing->setMailingList($mail_list);
			$oMailing->sendToUndelivered();
			if ($oMailing->getUndeliveredMailingsInfo()) {
				$tp->assign("UndeliveredMailings", $oMailing->getUndeliveredMailingsInfo());
			}
		}

		$groups = SJB_DB::query("SELECT * FROM `user_groups`");
		$products = SJB_ProductsManager::getAllProductsInfo();
		$testEmail = SJB_Settings::getSettingByName('test_email');

		$tp->assign('test_email', $testEmail);
		$tp->assign("products", $products);
		$tp->assign("groups", $groups);
		$tp->assign("mail_list", $mail_list);
		$tp->assign('errors', $errors);
		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		$tp->display("mailing.tpl");
	}

	private function isTestEmailValid()
	{
		return preg_match("^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,}\$^", SJB_Request::getVar('email', ''));
	}
}

class SJB_Mailing
{
	private $_mailingID;

	/**
	 * array of all mailings from `mailing` table
	 * @var array
	 */
	private $_aMailings = array();

	/**
	 *
	 * @param int $mailingID
	 */
	public function __construct($mailingID)
	{
		$this->_mailingID = (int) $mailingID;
	}

	/**
	 *
	 * @param array $aMailings
	 */
	public function setMailingList(array $aMailings)
	{
		$this->_aMailings = $aMailings;
	}

	public function getMailingID()
	{
		return $this->_mailingID;
	}

	public function setMailingID($mailingID)
	{
		$this->_mailingID = (int) $mailingID;
	}

	/**
	 * check if mailing exists in list
	 * of mailings
	 *
	 * @return int | boolean false
	 */
	public function checkIfMailingExists()
	{
		if ($this->_mailingID) {
			foreach ($this->_aMailings as $mailIndex => $mailItem) {
				if ($mailItem['id'] == $this->_mailingID)
					return $mailIndex;
			}
		}

		return false;

	} // 	public function checkIfMailingExists( $aMailings )

	/**
	 * send mailings
	 * @return boolean
	 */
	public function send()
	{
		$mailIndex = $this->checkIfMailingExists();

		if ($mailIndex !== false) {
			SJB_DB::query("DELETE FROM `mailing_info` WHERE `mailing_id` = ?n", $this->_mailingID);

			foreach ($this->_aMailings[$mailIndex]['mail_arr'] as $val) {
				$email = SJB_DB::queryValue('SELECT `email` FROM `users` WHERE `sid`=?n', $val['sid']);
				if (!empty($email)) {
					$query = 'INSERT INTO `mailing_info` (`mailing_id`, `email`, `username`, `status`) VALUES (?n, ?s, ?s, 0)';
					SJB_DB::query($query, $this->_mailingID, $email, $val['username']);
				}
			}
			return $this->sendToUndelivered();

		}

		return false;
	}

	/**
	 * send test mailing
	 * @return boolean
	 */
	public function testSend()
	{
		$mailIndex = $this->checkIfMailingExists();

		if ($mailIndex === false)
			return false;

		$text = $this->_aMailings[$mailIndex]['text'];
		return self::sendMailing($text, SJB_Request::getVar('email',''), $this->_aMailings[$mailIndex]['subject'], $this->_aMailings[$mailIndex]['file']);
	}

	/**
	 * send mailings to undelivered
	 * @return int count of success sends
	 */
	public function sendToUndelivered()
	{
		$countOfSuccessSends = 0;
		$mailIndex = $this->checkIfMailingExists();

		if ($mailIndex === false)
			return false;

		$aMailing = $this->_aMailings[$mailIndex];

		$text = $aMailing['text'];
		$img = self::fileImg($text);
		$file = $aMailing['file'];
		$subject = $aMailing['subject'];

		$aNeedToSend = $this->getUndeliveredMailingsInfo();

		if (!is_array($aNeedToSend))
			return false;

		foreach ($aNeedToSend as $mailInfo) {
			$sendMailingResult = self::sendMailing($text, $mailInfo['email'], $subject, $file, $img);
			SJB_DB::query('UPDATE `mailing_info` SET `status` = ?n WHERE `id` = ?n', $sendMailingResult, $mailInfo['emailId']);
			if ($sendMailingResult) {
				$countOfSuccessSends += 1;
			}
		}

		return $countOfSuccessSends;
	}

	/**
	 * get undelivered mailings info list
	 * @return array
	 */
	public function getUndeliveredMailingsInfo()
	{
		return $result = SJB_DB::query("
			SELECT u.sid as sid, u.username, u.user_group_sid, u.language, mi.email, mi.id as emailId
			FROM users u
			INNER JOIN `mailing_info` mi ON mi.`email` = u.`email` AND mi.`username` = u.`username`
			WHERE mi.`status` = 0 AND `mi`.`mailing_id` = ?n AND `mi`.`email` <> '' GROUP BY `u`.`sid`", $this->_mailingID);
	}

	public static function sendMailing($text, $emailAddress, $subject, $file = '')
	{
		$email = SJB_EmailTemplateEditor::getEmail($emailAddress, 34, array('subject' => $subject, 'message' => $text));
		if ($file)
			$email->setFile('../' . $file);
		return $email->send();
	}

	public static function fileImg(&$text)
	{
		$dir = $_SERVER['DOCUMENT_ROOT'];
		$url = dirname(SJB_System::getSystemSettings('SITE_URL'));
		$sRegExp = "/(src|background)=\"(.*)\"/Ui";
		preg_match_all($sRegExp, $text, $matches);
		$result = array();
		$i = 0;
		foreach ($matches[2] as $img_url)
		{
			if (!preg_match('#^[A-z]+://#', $img_url)) {
				$result[$i]['url'] = $img_url;
				if (strstr($img_url, $url) !== false)
					$result[$i]['dir'] = str_replace($url, $dir, $img_url);
				else
					$result[$i]['dir'] = $dir . $img_url;
				$result[$i]['name'] = 'cid:' . str_replace('/', '', strrchr($result[$i]['dir'], '/'));
			}
			$i++;
		}
		foreach ($result as $res)
			$text = str_replace($res['url'], urldecode($res['dir']), $text);
		return $result;
	}
}
