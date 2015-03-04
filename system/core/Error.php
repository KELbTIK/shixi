<?php

class SJB_Error
{
	private static $runtimeErrorCode = array(
		E_ERROR				=> 'E_ERROR',
		E_WARNING			=> 'E_WARNING',
		E_PARSE				=> 'E_PARSE',
		E_NOTICE			=> 'E_NOTICE',
		E_CORE_ERROR		=> 'E_CORE_ERROR',
		E_CORE_WARNING		=> 'E_CORE_WARNING',
		E_COMPILE_ERROR		=> 'E_COMPILE_ERROR',
		E_COMPILE_WARNING	=> 'E_COMPILE_WARNING',
		E_USER_ERROR		=> 'E_USER_ERROR',
		E_USER_WARNING		=> 'E_USER_WARNING',
		E_USER_NOTICE		=> 'E_USER_NOTICE',
		E_STRICT			=> 'E_STRICT',
		E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
		E_DEPRECATED       	=> 'E_DEPRECATED',
		E_USER_DEPRECATED  	=> 'E_USER_DEPRECATED',
		E_ALL				=> 'E_ALL'
	);
	
	// runtime errors
	public static $runtimeErrors = array();
	
	// catched errors array
	public $errors = array();
	
	// errors counter
	public $counter = 0;
	
	public $errorCode = array (
		// LISTING ERRORS
			'UNDEFINED_LISTING_ID'			=> 'Listing ID is not defined',
			'WRONG_LISTING_ID_SPECIFIED'	=> 'Listing does not exist',
			'LISTING_IS_NOT_ACTIVE'			=> 'Listing with specified ID is not active',
			'NOT_OWNER'						=> "You're not the owner of this posting",
			'LISTING_IS_NOT_APPROVED'		=> 'Listing with specified ID is not approved by admin',
			'WRONG_DISPLAY_TEMPLATE'		=> 'Wrong template to display listing',
		// USERS ERRORS
			'NOT_LOGIN'		=> 'User not logged',
		// TEST ERRORS
			'SOME_ERROR'	=> 'Some error happens',
			'TEST_ERROR'	=> "It's test error for error test"
		);
	
	
	
	/********************** CODE GENERATED ERRORS ******************/
	
	/**
	 * Add new error on page.
	 *
	 * @param string  $error Codestring of error, or error text
	 */
	public function add($error)
	{
		$this->errors[] = $error;
		$this->counter++;
	}
	
	/**
	 * Get array of page errors.
	 *
	 * @return array
	 */
	public function getErrors()
	{
		$errors = array();
		
		foreach ($this->errors as $error) {
			if (isset($this->errorCode[$error])) {
				$errors[] = $this->errorCode[$error];
			} else {
				$errors[] = $error;
			}
		}
		return $errors;
	}
	
	
	/********************* RUNTIME ERRORS **********************/
	
	/**
	 * Return runtime errors array.
	 * If set param $errorLevel - filter errors by self::filterErrors() before return
	 *
	 * @param string $errorLevel
	 * @return array
	 */
	public static function getRuntimeErrors($errorLevel = null)
	{
		if (empty($errorLevel)) {
			return self::$runtimeErrors;
		}
		
		$errors         = self::$runtimeErrors;
		$filteredErrors = self::filterErrors($errors, $errorLevel);
		
		return $filteredErrors;
	}
	
	
	/**
	 * Filter errors array by level, and return only matched
	 * i.e. $errorLevel:
	 * E_ERROR - returns only E_ERROR errors
	 * E_WARNING - returns E_ERROR and E_WARNING
	 * E_NOTICE - returns E_ERROR, E_WARNING and E_NOTICE
	 * E_ALL - returns incoming array
	 *
	 * @param array $errors
	 * @param string $errorLevel
	 * @return array
	 */
	public static function filterErrors($errors, $errorLevel)
	{
		if (empty($errors)) {
			return array();
		}
		$outputErrors = array();
		
		if ($errorLevel != 'E_ALL') {
			
			foreach ($errors as $error) {
				switch ($errorLevel) {
					case 'E_ERROR':
					case 'Fatal error':
						if ( !in_array($error['level'], array('E_WARNING', 'E_NOTICE', 'E_USER_WARNING', 'E_USER_NOTICE')) ) {
							$outputErrors[] = $error;
						}
						break;
					case 'E_WARNING':
					case 'Warning':
						if ( !in_array($error['level'], array('E_NOTICE','E_USER_NOTICE')) ) {
							$outputErrors[] = $error;
						}
						break;
					default:
						$outputErrors[] = $error;
						break;
				}
			}
		} else {
			$outputErrors = $errors;
		}
		
		return $outputErrors;
	}
	
	
	/**
	 * SJB error handler
	 *
	 * @param integer $level
	 * @param string $message
	 * @param string $file
	 * @param integer $line
	 */
	public static function errorHandler($intLevel, $message = NULL, $file = NULL, $line = NULL, $errContext = NULL)
	{
		// work as current setting of error_reporting
		if (!(error_reporting() & $intLevel)) {
			return;
		}

		$errorMode = SJB_System::getSettingByName('error_control_mode');
		
		if ($errorMode == 'debug') {
			// Display error
			$types = array(
				"Fatal error" => array("E_ERROR", "E_CORE_ERROR", "E_COMPILE_ERROR", "E_USER_ERROR", "E_RECOVERABLE_ERROR"),
				"Warning"     => array("E_WARNING", "E_CORE_WARNING", "E_COMPILE_WARNING", "E_USER_WARNING"),
				"Parse error" => array("E_PARSE"),
				"Notice"      => array("E_NOTICE", "E_USER_NOTICE", "E_DEPRECATED", "E_USER_DEPRECATED", "E_STRICT"),
			);
			// Textual error type.
			$type = "Unknown error";
			foreach ($types as $keyName=>$groups) {
				foreach ($groups as $group) {
					$error = defined($group)? constant($group) : 0;
					if ($intLevel == $error) {
						$type = $keyName;
						break(2);
					}
				}
			}
			// Format message.
			$prefix = ini_get('error_prepend_string');
			$suffix = ini_get('error_append_string');

			$file = $file ? "in <b>{$file}</b>" : '';
			$line = $line ? "on line <b>{$line}</b>" : '';
			echo  $prefix . "<b>{$type}</b>: {$message} {$file} {$line}<br />" . $suffix;
		}

		self::logError($intLevel, $message, $file, $line);
	}


	/**
	 * Stores the data that will be saved in the database logs
	 *
	 * @param integer $level
	 * @param string $message
	 * @param string $file
	 * @param integer $line
	 */
	public static function logError($intLevel, $message = NULL, $file = NULL, $line = NULL)
	{
		$level = self::$runtimeErrorCode[$intLevel];

		// GET BACKTRACE TO ERROR
		require_once 'Logger.php'; // Автолоадер тут не срабатывает
		$backtrace = SJB_Logger::getBackTrace();

		self::$runtimeErrors[] = array(
			'level'     => $level,
			'message'   => $message,
			'file'      => $file,
			'line'      => $line,
			'backtrace' => sprintf("BACKTRACE:\n [%s]", join("<br/>\n", $backtrace)),
		);
	}
	
	
	/**
	 * Output buffer handler. Using to catch fatal errors
	 *
	 * @param string $output
	 * @return string
	 */
	public static function fatalErrorHandler($output)
	{
		// free piece of memory
		// this was reserved in SJB_System::init() before output buffering
		unset($GLOBALS['fatal_error_reserve_buffer']);
		
		// RE to determine is there was an error.
		$prefix = ini_get('error_prepend_string');
		$suffix = ini_get('error_append_string');
		
		$reg = '{^(.*)(' .
			preg_quote($prefix, '{}') .
			"<br />\r?\n<b>(\w+ error)</b>: \s*" .
			'(.*?)' .
			' in <b>)(.*?)(</b>' .
			' on line <b>)(\d+)(</b><br />' .
			"\r?\n" .
			preg_quote($suffix, '{}') .
			')()$' .
		'}s';
        
		$matches = null;
		if (!preg_match($reg, $output, $matches)) {
			return $output;
		}
        
		list (, $content, $beforeFile, $error, $msg, $file, $beforeLine, $line, $afterLine, $tail) = $matches;
		
		$siteURL   = SJB_System::getSystemSettings('SITE_URL');
		$errorMode = SJB_System::getSettingByName('error_control_mode');
		$errorText = '';
        
		if ($errorMode == 'debug') {
			$errorText = "<b>{$error}</b>:  {$msg} in <b>{$file}</b> on line <b>{$line}</b><br />";
		}
		
		
		$outHTML = "
		<html>
			<head>
				<link rel=\"stylesheet\" href=\"{$siteURL}/templates/_system/errors/errors.css\" type=\"text/css\">
			</head>
			<body>
				<p class=\"error\">Fatal error! Your request can not be executed!</p>
				{$errorText}
			</body>
		</html>";
		
        
		SJB_Error::writeToLog(array( array('level' => 'E_ERROR', 'message' => $msg, 'file' => $file, 'line' => $line, 'backtrace' => '' )));
        
		return $outHTML;
	}
	
	
	/**
	 * Get max error level of runtime errors, and return message block
	 * associated to this error level
	 *
	 * @return string
	 */
	public static function getErrorContent()
	{
		if ( !empty(self::$runtimeErrors)) {
			$errLvl = null;
			foreach (self::$runtimeErrors as $error) {
				$currLevel = $error['level'];
				if ($currLevel == 'E_NOTICE' && $errLvl == null) {
					$errLvl = $currLevel;
				} elseif ($currLevel == 'E_WARNING' && ($errLvl == null || $errLvl == 'E_NOTICE') ) {
					$errLvl = $currLevel;
				} elseif ($currLevel == 'E_ERROR' && ($errLvl == null || $errLvl == 'E_NOTICE' || $errLvl == 'E_WARNING') ) {
					$errLvl = $currLevel;
				}
			}
			if ($errLvl == 'E_NOTICE' || $errLvl == 'E_WARNING') {
				return SJB_I18N::getInstance()->gettext(null, 'Done with errors on the page');
			}
		}
		
		return '';
	}

	/**
	 * Write errors array|string to log
	 *
	 * @param array|string $errors
	 */
	public static function writeToLog($errors)
	{
		$errorsString = '';
		$caption = '';

		if (is_array($errors)) {
			foreach ($errors as $error) {
				switch ($error['level']) {
					case 'E_NOTICE':
					case 'E_USER_NOTICE':
						$caption = 'Notice';
						break;
					case 'E_WARNING':
					case 'E_USER_WARNING':
						$caption = 'Warning';
						break;
					case 'E_ERROR':
					case 'E_USER_ERROR':
						$caption = 'Fatal error';
						break;
				}
				$file = isset($error['file']) ? "in <b>{$error['file']}</b>" : '';
				$line = isset($error['line']) ? "in <b>{$error['line']}</b>" : '';
				$backtrace = isset($error['backtrace']) ? "<p>{$error['backtrace']}</p>" : '';
				$errorsString .= "<p><b>{$caption}:</b> {$error['message']} {$file} {$line}</p> {$backtrace}\n";
			}
		} else {
			$errorsString = $errors;
		}
		
		return SJB_DB::query("INSERT INTO `error_log` SET `date` = NOW(), `errors` = ?s", $errorsString);
	}
	
	
	/**
	 * Get records from error log
	 *
	 * @param integer $recordsNum
	 * @return array
	 */
	public static function getErrorLog($recordsNum = 10)
	{
		return SJB_DB::query("SELECT * FROM `error_log` ORDER BY `sid` DESC LIMIT ?n", $recordsNum);
	}
	
}