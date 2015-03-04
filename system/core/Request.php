<?php

/**
 * @version		$Id: $
 */

class SJB_Request
{
	const METHOD_POST	= 'POST';
	const METHOD_GET	= 'GET';
	const METHOD_PUT	= 'PUT';

	public static function getMethod()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}
	
	public static function get($hash = 'default')
	{
		$input = array();
		switch ($hash) {
			case 'GET':
				$input = &$_GET;
				break;
			case 'POST':
				$input = &$_POST;
				break;
			case 'FILES':
				$input = &$_FILES;
				break;
			case 'COOKIE':
				$input = &$_COOKIE;
				break;
			case 'ENV':
				$input = &$_ENV;
				break;
			case 'SERVER':
				$input = &$_SERVER;
				break;
			default:
				$input = &$_REQUEST;
				break;
		}
		return $input;
	}

	public static function getVar($name, $default = null, $hash = 'default', $type = 'none')
	{
		$input = SJB_Request::get($hash);
		if (isset($input[$name])) {
			$var = $input[$name];
			if ($type !== 'none')
				settype($var, $type);
			return $var;
		}
		return $default;
	}

	public static function getInt($name, $default = 0, $hash = 'default')
	{
		return SJB_Request::getVar($name, $default, $hash, 'int');
	}

	public static function getFloat($name, $default = 0.0, $hash = 'default')
	{
		return SJB_Request::getVar($name, $default, $hash, 'float');
	}

	public static function getBool($name, $default = false, $hash = 'default')
	{
		return SJB_Request::getVar($name, $default, $hash, 'bool');
	}

	public static function getString($name, $default = '', $hash = 'default')
	{
		return (string) SJB_Request::getVar($name, $default, $hash, 'string');
	}
	
	/**
	 * Check request type.
	 * For AJAX request return true
	 * @return boolean
	 */
	public static function isAjax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

	/**
	 * Get HTTP_USER_AGENT value
	 * @static
	 * @return string
	 */
	public static function getUserAgent()
	{
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}

	/**
	 * Instance of SJB_Request
	 * @var SJB_Request
	 */
	private static $instance = null;
	
	/**
	 * Get instance of SJB_Request
	 * @param string $uri
	 * @return SJB_Request
	 */
	public static function getInstance($uri = null)
	{
		if (self::$instance === null)
			self::$instance = new SJB_Request($uri);
		return self::$instance;
	}

	/**
	 * Request factory
	 *
	 * @param string $uri
	 * @return SJB_Request
	 */
	public static function factory($uri = null)
	{
		return new SJB_Request($uri);
	}

	public static $method = 'GET';
	
	public static $remoteAddr = '0.0.0.0';
	
	public static $userAgent = null;
	
	/**
	 * SJB_PageConfig object
	 * @var SJB_PageConfig
	 */
	public $page_config;

	/**
	 * Headers to send
	 * @var array
	 */
	private $headers = array();
	
	/**
	 * URI of current request
	 * @var string
	 */
	public $uri;

	private function __construct($uri = null)
	{
		// fill request properties
		if (isset($_SERVER['REQUEST_METHOD']))
			self::$method = self::getVar('REQUEST_METHOD', '', 'SERVER'); // $_SERVER['REQUEST_METHOD'];

		if (isset($_SERVER['REMOTE_ADDR']))
			self::$remoteAddr = self::getVar('REMOTE_ADDR', '', 'SERVER'); // $_SERVER['REMOTE_ADDR'];

		if (isset($_SERVER['HTTP_USER_AGENT']))
			self::$userAgent = self::getVar('HTTP_USER_AGENT', '', 'SERVER');

		// default header
		$this->headers['Content-type'] = 'text/html;charset=utf-8';

		$this->uri = $uri;
		if ($uri === null || empty($uri)) {
			$this->uri = SJB_Navigator::getUri();
		}
		$errors = array();
		if (SJB_UserManager::checkBan($errors) && SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE'))
			$this->uri = "/user-banned/";

		// maintenance mode
		if (SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != SJB_System::getSystemSettings('ADMIN_ACCESS_TYPE')) {
			$oMaintenance = new SJB_MaintenanceMode(self::$remoteAddr);
			if (!$oMaintenance->getAllowed())
				$this->uri = '/maintenance-mode/';
		}

		$this->page_config = SJB_PageConfig::getPageConfig ($this->uri);
	}
	
	/**
	 * Set header to responce
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setHeader($name, $value)
	{
		$this->headers[$name] = $value;
	}

	/**
	 * Execute request
	 *
	 */
	public function execute()
	{
		// send headers
		foreach ($this->headers as $name => $value) {
			$header = $name . ':' . $value;
			header($header, true);
		}

		if ($this->page_config->PageExists()) {
			echo SJB_System::getPage($this->page_config);
		} else {
			if (SJB_System::doesParentUserPageExist($this->uri)) {
				$uri = SJB_System::getUserPageParentURI($this->uri);
				$_REQUEST['passed_parameters_via_uri'] = substr($this->uri, strlen($uri));
			} else { // the 404 error case!
				header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found'); // no such page in configuration
				$uri = '/404/';
			}
			$this->page_config = SJB_PageConfig::getPageConfig($uri);
			echo SJB_System::getPage($this->page_config);
		}
	}

	/**
	 * @param string $template page template name
	 */
	public function setPageTemplate($template)
	{
		$this->page_config->setPageTemplate($template);
	}

	/**
	 * @return bool
	 */
	public static function isBot()
	{
		if (self::getVar('SERVER_ADDR', gethostbyname(self::getVar('SERVER_NAME', '', 'SERVER')), 'SERVER') == self::$remoteAddr) {
			return false;
		}
		$bots = SJB_System::getSystemSettings('LISTING_VIEW_IGNORING_BOTS');
		$agent = strtolower(self::$userAgent);
		foreach ($bots as $bot) {
			if (strstr($agent, $bot)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * determines used protocol and return it
	 * @return string
	 */
	public static function getProtocol()
	{
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			return 'https';
		}
		return 'http';
	}
}
