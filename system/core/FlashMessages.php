<?php

class SJB_FlashMessages
{
	const MESSAGES_HOLDER = 'flashMessagesHolder';
	const ERROR           = 'error';
	const WARNING         = 'warning';
	const NOTICE          = 'notice';
	const MESSAGE         = 'message';

	/** @var SJB_FlashMessages */
	private static $instance = null;

	private function __construct()
	{
		if (!isset($_SESSION[self::MESSAGES_HOLDER])) {
			$_SESSION[self::MESSAGES_HOLDER][self::ERROR]   = array();
			$_SESSION[self::MESSAGES_HOLDER][self::WARNING] = array();
			$_SESSION[self::MESSAGES_HOLDER][self::NOTICE]  = array();
			$_SESSION[self::MESSAGES_HOLDER][self::MESSAGE] = array();
		}
	}

	/**
	 * @return SJB_FlashMessages
	 */
	public static function getInstance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * @param string $name
	 * @param null|array $params
	 */
	public function addError($name, $params = null)
	{
		$this->_addToHolder(self::ERROR, $name, $params);
	}

	/**
	 * @param string $name
	 * @param null|array $params
	 */
	public function addWarning($name, $params = null)
	{
		$this->_addToHolder(self::WARNING, $name, $params);
	}

	/**
	 * @param string $name
	 * @param null|array $params
	 */
	public function addNotice($name, $params = null)
	{
		$this->_addToHolder(self::NOTICE, $name, $params);
	}

	/**
	 * @param string $name
	 * @param null|array $params
	 */
	public function addMessage($name, $params = null)
	{
		$this->_addToHolder(self::MESSAGE, $name, $params);
	}

	/**
	 * @param string $type
	 * @param string $name
	 * @param null|array $params
	 */
	private function _addToHolder($type, $name, $params = null)
	{
		if ($params) {
			$_SESSION[self::MESSAGES_HOLDER][$type][] = $params;
			$size = sizeof($_SESSION[self::MESSAGES_HOLDER][$type]);
			$_SESSION[self::MESSAGES_HOLDER][$type][$size - 1]['messageId'] = $name;
		} else {
			$_SESSION[self::MESSAGES_HOLDER][$type][] = $name;
		}
	}

	/**
	 * @return bool
	 */
	public function isErrors()
	{
		return !empty($_SESSION[self::MESSAGES_HOLDER][self::ERROR]);
	}

	/**
	 * @return array
	 */
	public function getErrorsAndRemove()
	{
		$errors = $_SESSION[self::MESSAGES_HOLDER][self::ERROR];
		$_SESSION[self::MESSAGES_HOLDER][self::ERROR] = array();
		return $errors;
	}

	/**
	 * @return array
	 */
	public function getContentAndRemove()
	{
		$messages = array();
		
		$messages[self::WARNING] = $_SESSION[self::MESSAGES_HOLDER][self::WARNING];
		$messages[self::NOTICE]  = $_SESSION[self::MESSAGES_HOLDER][self::NOTICE];
		$messages[self::MESSAGE] = $_SESSION[self::MESSAGES_HOLDER][self::MESSAGE];
		
		$_SESSION[self::MESSAGES_HOLDER][self::WARNING] = array();
		$_SESSION[self::MESSAGES_HOLDER][self::NOTICE]  = array();
		$_SESSION[self::MESSAGES_HOLDER][self::MESSAGE] = array();
		
		return $messages;
	}
}
