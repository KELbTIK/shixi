<?php

/**
 * Logger class to log messages and backtraces to error log
 * 
 * @author 
 *
 */

class SJB_Logger
{
	/**
	 * Put DEBUG message to error log.
	 * Can accept string, or string format and arguments to it
	 * 
	 * For example: 
	 * SJB_Logger::debug($string);
	 * 
	 * this will save to error log file value of $string and backtrace to 
	 * code, where it was called.
	 * 
	 * And next example:
	 * $string = 'TEST';
	 * SJB_Logger::debug("This is string value: %s", $string);
	 * 
	 * this will save to error log next string:
	 * [14-Nov-2011 16:07:54] DEBUG: [This is string value: TEST]
	 * 
	 * and some backtrace to code, where it was called.
	 */
	public static function debug()
	{
		$args = func_get_args();
		$format = array_shift($args);
		$msg = empty($args)?$format:vsprintf($format, $args);
		$backtrace = SJB_Logger::getBackTrace();
		error_log(sprintf("DEBUG: [%s]\n BACKTRACE:\n [%s]", $msg, join("\n", $backtrace)), 0);
	}

	
	/**
	 * Put ERROR message to error log.
	 * Can accept string, or string format and arguments to it.
	 */
	public static function error()
	{
		$args = func_get_args();
		$format = array_shift($args);
		$msg = empty($args)?$format:vsprintf($format, $args);
		$backtrace = SJB_Logger::getBackTrace();
		error_log(sprintf("ERROR: [%s]\n BACKTRACE:\n [%s]\n", $msg, join("\n", $backtrace)), 0);
	}
	
	
	/**
	 * Gets formatted backtrace string
	 */
	public static function getBackTrace()
	{ 
		$stack = debug_backtrace();
		array_shift($stack); 
		$res = array();
		foreach($stack as $v) { 
			$a = isset($v['args']) ? implode(",", array_map('gettype', $v['args'])) : null;
			$file = isset($v['file']) ? $v['file'] : 'unknown';
			$line = isset($v['line']) ? $v['line'] : 'unknown';
			$res[] = sprintf("%s(%s) in file \"%s\" at line %s.", $v['function'], $a, $file, $line);
		} 
		return $res;
	}
}
