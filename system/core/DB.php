<?php

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class SJB_DB
{
	private static $argsAsArray = false;
	private static $function;
	private static $module;
	private static $showErrors = true;
	private static $errors = array();
	private static $args;
	
	public static function init($_host, $_user, $_pass, $dbname)
	{
		if (!isset($_host)) {
			$_host = SJB_System::getSystemSettings('DBHOST');
		}
		if (!isset($_user)) {
			$_user = SJB_System::getSystemSettings('DBUSER');
		}
		if (!isset($_pass)) {
			$_pass = SJB_System::getSystemSettings('DBPASSWORD');
		}
		if (!isset($dbname)) {
			$dbname = SJB_System::getSystemSettings('DBNAME');
		}
		
		$loader  = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('Zend');
		$db = new Adapter(
			array(
				'driver'   => self::getDBAdapter(),
				'host'     => $_host,
				'database' => $dbname,
				'username' => $_user,
				'password' => $_pass,
				'options'  => array('buffer_results' => true)
			)
		);
		
		try {
			$connection = $db->driver->getConnection();
			$connection->connect();
			$sql = new Sql($db);
			Zend_Registry::set('db', $db); 
			Zend_Registry::set('sql', $sql);
		} catch (Exception $ex) {
			die("Could not connect to database");
		}
		
		$db->createStatement("SET NAMES '" . SJB_System::getSystemSettings('MYSQL_CHARSET') . "'")->execute();
	}

	public static function setFunctionInfo($functionName, $moduleName)
	{
		self::$function = $functionName;
		self::$module = $moduleName;
	}

	/**
	 * @return null|string
	 */
	public static function getDBAdapter()
	{
		$adapter = SJB_System::getSystemSettings('DBADAPTER');
		
		if (!$adapter) {
			$extensions = get_loaded_extensions();
			if (in_array('pdo_mysql', $extensions)) {
				$adapter = 'Pdo_Mysql';
			}
			else if (in_array('mysqli', $extensions)) {
				$adapter = 'Mysqli';
			}
		}
		
		return $adapter;
	}

	/**
	 * @param  array $args
	 * @return bool|Zend\Db\Adapter\Driver\Pdo\Result|Zend\Db\Adapter\Driver\Mysqli\Result
	 */
	private static function getQueryResult($args)
	{
		if (self::$argsAsArray) {
			self::$argsAsArray = false;
			$processedArgs = array();
			foreach ($args as $arg) {
				if (is_array($arg)) {
					foreach ($arg as $value) {
						$processedArgs[] = $value;
					}
				} else {
					$processedArgs[] = $arg;
				}
			}
		} else {
			$processedArgs = $args;
		}
		
		$error  = '';
		$sql    = call_user_func_array(array('SJB_DB', 'sql'), $processedArgs);
		$params = self::$args;
		
		if (SJB_Profiler::getInstance()->isProfilerEnable()) {
			$queryResult = self::mysqlQueryProfiled($sql, $params, $processedArgs);
			$error  = "Query {$sql} : \n" . $queryResult['error'];
			$result = $queryResult['query'];
		} else {
			$db = Zend_Registry::get('db');
			try {
				$result = $db->createStatement($sql)->execute($params);
				Zend_Registry::set('affectedRows', $result->count());
			} catch (Exception $e) {
				$result = false;
				$error  = "Query {$sql} : \n" . $e->getMessage();
			}
		}
		
		if ($result === false) {
			self::debugMysqlError($error);
		}
		
		return $result;
	}

	/**
	 * @param  string $sql
	 * @param  array $params
	 * @param  array $requestArgs
	 * @return array
	 */
	private static function mysqlQueryProfiled($sql, array $params, array $requestArgs)
	{
		$db        = Zend_Registry::get('db');
		$debug     = debug_backtrace();
		$timeBegin = microtime(true);
		$result    = array();
		try {
			$result['query'] = $db->createStatement($sql)->execute($params);
			$result['error'] = false;
			Zend_Registry::set('affectedRows', $result['query']->count());
		} catch (Exception $e) {
			$result['query'] = false;
			$result['error'] = $e->getMessage();
		}
		
		$time      = microtime(true) - $timeBegin;
		$sqlString = call_user_func_array(array('SJB_DB', 'getProfileSql'), $requestArgs);
		SJB_Profiler::getInstance()->gatherQueryInfo($debug, $sqlString, $time, self::$function, self::$module);

		return $result;
	}

	/**
	 * @return bool
	 */
	public static function queryExec()
	{
		$args   = func_get_args();
		$result = self::getQueryResult($args);
		return $result != false;
	}

	/**
	 * @return null|array
	 */
	public static function queryValue()
	{
		$args   = func_get_args();
		$result = self::getQueryResult($args);
		if (is_object($result)) {
			if ($result->isQueryResult()) {
				$results = new SJB\Db\ResultSet();
				$rows    = $results->initialize($result)->current();
				return $rows ? array_pop($rows) : null;
			} else {
				$value = Zend_Registry::get('db')->getDriver()->getLastGeneratedValue();
				return $value > 0 ? $value : true;
			}
		}
		
		return null;
	}

	/**
	 * @return null|array
	 */
	public static function query()
	{
		$args   = func_get_args();
		$result = self::getQueryResult($args);
		if (is_object($result)) {
			if ($result->isQueryResult()) {
				$results = new SJB\Db\ResultSet();
				$rows    = $results->initialize($result)->toArray();
				return $rows;
			} else {
				$value = Zend_Registry::get('db')->getDriver()->getLastGeneratedValue();
				return $value > 0 ? $value : true;
			}
		}
		
		return array();
	}

	/**
	 * @param string $error
	 */
	private static function debugMysqlError($error)
	{
		if (self::$showErrors && SJB_Settings::isLoaded() && SJB_Settings::getSettingByName('error_control_mode') == 'debug') {
			SJB_HelperFunctions::d($error);
		}
		
		self::$errors[] = $error;
	}

	public static function hideMysqlErrors()
	{
		self::$showErrors = false;
	}

	/**
	 * @return bool
	 */
	public static function isErrorExist()
	{
		return !empty(self::$errors);
	}

	/**
	 * @return array
	 */
	public static function getMysqlError()
	{
		return self::$errors;
	}

	public static function cleanMysqlErrors()
	{
		self::$errors = array();
	}

	public static function getAffectedRows()
	{
		return Zend_Registry::get('affectedRows');
	}
	
	public static function quote($string)
	{
		$platform = Zend_Registry::get('db')->getPlatform();
		return substr($platform->quoteValue($string), 1, -1);
	}

	public static function explodeQueryArgs(array $args)
	{
		if (!empty($args)) {
			self::$argsAsArray = true;
		}
		return $args;
	}

	function getColumnValues($table, $column)
	{
		$rows = SJB_DB::query("SELECT DISTINCT(`$column`) from `$table`");
		if ($rows)
			return array_values($rows);
		return array();
	}

	function getHash($table, $keycolumn, $valuecolumn)
	{
		$result = array();
		$rows = SJB_DB::query("SELECT `$keycolumn`, `$valuecolumn` FROM `$table`");
		if ($rows) {
			foreach ($rows as $row)
				$result[$row[$keycolumn]] = $row[$valuecolumn];
		}
		return $result;
	}

	public static function table_exists($table_name)
	{
		if (isset($GLOBALS['SJB_DB_table_exists_tables'])) {
			$tables = $GLOBALS['SJB_DB_table_exists_tables'];
		} else {
			$cache  = SJB_Cache::getInstance();
			$tables = $cache->load(md5("SHOW TABLES"));
			if (empty($tables)) {
				$rows = SJB_DB::query("SHOW TABLES");
				foreach ($rows as $table) {
					$tables[] = current($table);
				}
				$cache->save($tables, md5("SHOW TABLES"));
			}
			$GLOBALS['SJB_DB_table_exists_tables'] = $tables;
		}
		
		return in_array($table_name, $tables);
	}

	private static function sql() # 1 - string, ....args..
	{
		self::$args = array();
		global $sqlArgs;
		$sqlArgs = func_get_args();
		return preg_replace_callback('~(\?[nsbfwlt])|\^~u', array('SJB_DB', 'sqlCallback'), array_shift($sqlArgs));
	}

	private static function sqlCallback($m)
	{
		global $sqlArgs;
		@$arg = array_shift($sqlArgs);
		switch($m[0]) {
			case '?n': // number
				return intval($arg);
			case '?s': // string
				if ($arg === null) {
					 return 'null';
				}
				
				self::$args[] = $arg;
				return '?';
			case '?b': // binary (0x462347238)
				self::$args[] = '0x' . bin2hex($arg);
				return '?';
			case '?f': // float
				self::$args[] = floatval(str_replace(',', '.', $arg));
				return '?';
			case '?w': // without
				return $arg;
			case '?t': // time
				self::$args[] = date("Y-m-d H:i:s", $arg);
				return '?';
			case '?l': // list
				if (is_array($arg)) {
					$query = '';
					foreach ($arg as $value) {
						$query .= empty($query) ? '?' : ',?';
						self::$args[] = $value;
					}
					return $query;
				}
				
				self::$args[] = $arg;
				return '?';
			default:
				return $m[0];
				break;
		}
	}

	private static function getProfileSql()
	{
		global $sqlProfileArgs;
		$sqlProfileArgs = func_get_args();
		return preg_replace_callback('~(\?[nsbfwlt])|\^~u', array('SJB_DB', 'sqlProfileCallback'), array_shift($sqlProfileArgs));
	}

	private static function sqlProfileCallback($m)
	{
		global $sqlProfileArgs;
		@$arg = array_shift($sqlProfileArgs);
		switch($m[0]) {
			case '?n': // number
				return intval($arg);
			case '?s': // string
				return "'" . SJB_DB::quote($arg) . "'";
			case '?b': // binary (0x462347238)
				return '0x' . bin2hex($arg);
			case '?f': // float
				return floatval(str_replace(',', '.', $arg));
			case '?w': // without
				return $arg;
			case '?t': // time
				return "'" . date("Y-m-d H:i:s", $arg) . "'";
			case '?l': // list
				$str = '';
				if (is_array($arg)) {
					foreach($arg as $value) {
						$str .= (empty($str) ? "'" : ", '") . SJB_DB::quote($value) . "'";
					}
					return $str;
				}
				return "'" . SJB_DB::quote($arg) . "'";
			default:
				return $m[0];
				break;
		}
	}
}