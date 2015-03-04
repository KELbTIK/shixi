<?php

class SJB_EmailTemplateEditor
{
	const EMAIL_DO_NOT_SEND = 'DoNotSend';

	/**
	 * @var array
	 */
	private static $emailTemplateGroups = array(
		SJB_NotificationGroups::GROUP_ID_USER		=> 'User Emails',
		SJB_NotificationGroups::GROUP_ID_LISTING	=> 'Listing Emails',
		SJB_NotificationGroups::GROUP_ID_PRODUCT 	=> 'Product Emails',
		SJB_NotificationGroups::GROUP_ID_ALERTS 	=> 'Email Alerts',
		SJB_NotificationGroups::GROUP_ID_OTHER	=> 'Other Emails',
	);

	/**
	 * @return array
	 */
	public static function getEmailTemplateGroups()
	{
		return self::$emailTemplateGroups;
	}

	/**
	 * @static
	 * @param string $name
	 * @param string $group
	 * @return array|bool|int
	 */
	public static function addNewEmptyTemplate($name, $group)
	{
		return SJB_DB::query('INSERT INTO `email_templates` SET `name` = ?s, `group` = ?s', $name, $group);
	}

	/**
	 * @static
	 * @param string $group
	 * @return array|bool|int
	 */
	public static function getEmailTemplatesByGroup($group)
	{
		return SJB_DB::query('SELECT * FROM `email_templates` WHERE `group` = ?s ORDER BY `name`', $group);
	}

	/**
	 * retrieve email templates with info: id and caption
	 * @static
	 * @param string $group
	 * @return array|bool|int
	 */
	public static function getEmailTemplatesForListByGroup($group)
	{
		return SJB_DB::query('SELECT `sid` as `id`, `name` as `caption` FROM `email_templates` WHERE `group` = ?s ORDER BY `name`', $group);
	}

	/**
	 * @static
	 * @param string $group
	 * @param string $name
	 * @return bool
	 */
	public static function checkIfEmailTemplateExists($group, $name)
	{
		$result = SJB_DB::query('SELECT `sid` FROM `email_templates` WHERE `name` = ?s AND `group` = ?s', $name, $group);
		if (!empty($result) && !empty($result[0])) {
			return $result[0]['sid'];
		}
		return false;
	}

	/**
	 * retrieve email template info by sid
	 * @static
	 * @param int $sid
	 * @return bool|mixed
	 */
	public static function getEmailTemplateInfoBySID($sid)
	{
		$result = SJB_DB::query('SELECT * FROM `email_templates` WHERE `sid` = ?n', $sid);
		if (!empty($result)) {
			return array_pop($result);
		}
		return false;
	}

	public static function saveEmailTemplate($emailTemplate)
	{
		return SJB_ObjectDBManager::saveObject('email_templates', $emailTemplate);
	}

	/**
	 * @param int $sid
	 * @return null|SJB_EmailTemplate
	 */
	public static function getEmailTemplateBySID($sid)
	{
		$templateInfo = self::getEmailTemplateInfoBySID($sid);
		if (!empty($templateInfo)) {
			$emailTemplate = new SJB_EmailTemplate($templateInfo);
			$emailTemplate->setSID($templateInfo['sid']);
			return $emailTemplate;
		}
		return null;
	}

	/**
	 * @static
	 * @param int $templateSID
	 * @return array|bool|int
	 */
	public static function deleteEmailTemplateBySID($templateSID)
	{
		$emailTemplate = SJB_EmailTemplateEditor::getEmailTemplateBySID($templateSID);

		if (!empty($emailTemplate)) {
			$userDefined = $emailTemplate->getPropertyValue('user_defined');
			if ($userDefined) {
				// delete atachment
				$fileProp = $emailTemplate->getProperty('file');
				if ($fileProp)
					SJB_UploadFileManager::deleteUploadedFileByID($fileProp->value);
				return SJB_ObjectDBManager::deleteObjectInfoFromDB('email_templates', $templateSID);
			}
		}

		return false;
	}

	/**
	 * @param int $templateSID
	 * @return array|bool
	 */
	public static function prepareEmailData($templateSID)
	{
		$emailTpl = self::getEmailTemplateBySID($templateSID);
		if ($emailTpl instanceof SJB_EmailTemplate)
			return self::createTemplateStructureForEmailTpl($emailTpl);
		return false;
	}

	/**
	 * @static
	 * @param SJB_EmailTemplate $emailTpl
	 * @return array
	 */
	public static function createTemplateStructureForEmailTpl(SJB_EmailTemplate $emailTpl)
	{
		if (!$emailTpl)
			return array();

		$structure = array();

		foreach ($emailTpl->getProperties() as $property) {
			if ($property->getType() == 'list') {
				$value = $property->getValue();
				$listValues = isset($property->type->property_info['list_values']) ? $property->type->property_info['list_values'] : array();
				foreach ($listValues as $listValue) {
					if ($listValue['id'] == $value)
						$structure[$property->getID()] = $listValue['caption'];
				}
			}
			else {
				$structure[$property->getID()] = $property->getValue();
			}
		}

		$structure['id'] = $emailTpl->getID();

		return $structure;
	}

	/**
	 * retrieve email SJB_Email with defined options:
	 * - from name
	 * - from email
	 * - attached file
	 * - cc
	 * ATTENTION: reserved index for $data:
	 * - emailData
	 *
	 * @static
	 * @param string $email
	 * @param int $emailTplSID
	 * @param array $data  (message, subject, signature) are reserved indexes
	 * @return SJB_Email
	 */
	public static function getEmail($email, $emailTplSID, $data = array())
	{
		if (self::EMAIL_DO_NOT_SEND === $emailTplSID) {
			return new SJB_EmailDoNotSend();
		}

		$templateData = self::prepareEmailData($emailTplSID);

		if (!$templateData) {
			return new SJB_EmailNone($email);
		}

		$message 	= SJB_Array::get($templateData, 'text');
		$subject 	= SJB_Array::get($templateData, 'subject');
		$signature 	= SJB_Settings::getSettingByName('system_email_signature');
		$cc 		= SJB_Array::get($templateData, 'cc');
		$fromEmail	= SJB_Array::get($templateData, 'from_email');
		$fromName	= SJB_Array::get($templateData, 'from_name');
		$file		= SJB_Array::get($templateData, 'file');

		self::replaceSmartyTags($fromName);
		self::replaceSmartyTags($message);
		self::replaceSmartyTags($subject);
		self::replaceSmartyTags($signature);

		$data[SJB_EmailInternal::EMAIL_DATA_LABEL] = array(
			'fromName'  => $fromName,
			'message'   => $message,
			'subject'   => $subject,
			'signature'	=> $signature,
		);

		$email = new SJB_Email($email, '../miscellaneous/email_theme.tpl', $data);

		if ($cc)
			$email->addCC($cc);

		if ($fromEmail)
			$email->setFromEmail($fromEmail);

		// file attachment
		if (SJB_Array::get($file, 'file_id')) {
			$file_link = SJB_UploadFileManager::getUploadedFileLink(SJB_Array::get($file, 'file_id'), false, true);
			if ($file_link) {
				$email->setFile(SJB_BASE_DIR . $file_link);
			}
		}

		return $email;
	}

	public static function replaceSmartyTags(&$element)
	{
		self::replaceNotAllowedInsideSmaryTagsElements($element, '&#39;', '\'');
		self::replaceNotAllowedInsideSmaryTagsElements($element, '&quot;', '"');
		self::replaceNotAllowedInsideSmaryTagsElements($element, '&amp;', '&');
	}

	/**
	 * replace elements that are not allowed inside smarty tags
	 * forexample: {if $listing.type.id eq &#39;Resume&#39;} should be replaced with normal quotes "'"
	 * it occures after WYSIWYG replacements.
	 * @static
	 * @param string $message
	 * @param string $find
	 * @param string $replace
	 */
	public static function replaceNotAllowedInsideSmaryTagsElements(&$message, $find = '&#39;', $replace = '\'' )
	{
		$pattern 	= '/{.*(' . $find . ').*}/U';
		$result 	= preg_match_all($pattern, $message, $matches);

		if ($result) {
			$match = SJB_Array::get($matches, 0);
			if ($match) {
				$theReplacedMatches = array();
				foreach ($match as $key => $theMatch) {
					$theReplacedMatches[$key] = str_replace($find, $replace, $theMatch);
				}
				foreach ($theReplacedMatches as $key => $theReplace) {
					$message = str_replace(SJB_Array::get($match, $key), $theReplace, $message);
				}
			}
		}
	}
}
