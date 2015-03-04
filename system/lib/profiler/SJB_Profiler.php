<?php

class SJB_Profiler
{
	private $functions;
	private $queries;
	private $timeBegin;
	private $profilerEnable;
	/** @var SJB_Profiler */
	private static $instance = null;

	private function __construct()
	{
		$this->profilerEnable = !SJB_Settings::isLoaded() ? null : SJB_Settings::getSettingByName('profiler') == '1';
	}

	/**
	 * @static
	 * @return SJB_Profiler
	 */
	public static function getInstance()
	{
		if (self::$instance === null || self::$instance->isProfilerEnable() === null) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}

	public function isProfilerEnable()
	{
		return $this->profilerEnable;
	}

	public function getFunctions()
	{
		return $this->functions;
	}

	public function getQueries()
	{
		return $this->queries;
	}

	public function gatherFunctionInfo($module, $function, $spendTime)
	{
		$this->functions[] = array(
			'module_name' => $module,
			'function_name' => $function,
			'time' => $spendTime
		);
	}

	public function gatherQueryInfo($debug, $sql, $time, $function, $module)
	{
		$this->queries[] = array(
			'sql' => $sql,
			'time' => $time,
			'function_name' => $function,
			'module_name' => $module,
			'debug' => $debug,
		);
	}

	public function setStartTime($timeBegin)
	{
		$this->timeBegin = $timeBegin;
	}

	public function getTimeElapsed()
	{
		$time = microtime(true) - $this->timeBegin;
		return number_format($time, 8);
	}

	public function getMemoryUsage()
	{
		return round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB';
	}
}