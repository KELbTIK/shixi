<?php

class SJB_StructureExplorer
{
	private $eventHandler;

	public function __construct($callback)
	{
		$this->eventHandler = $callback;
	}

	function explore(&$data)
	{
		$this->_explore($data, null, $data);
	}
	
	function _explore(&$data, $key, &$parentData)
	{
		if ($this->canRaise($data, $key, $parentData))
			$this->raiseEvent($data, $key, $parentData);
		if (is_array($data)) {
			foreach(array_keys($data) as $key)
				$this->_explore($data[$key], $key, $data);
		}
	}
	
	function canRaise($value, $key, $parentData)
	{
		return (gettype($value) === "string" && (strpos($value, ">" ) !== false || strpos($value, "\"" ) !== false));
	}

	function raiseEvent(&$value, $key, &$parentData)
	{
		$value = call_user_func($this->eventHandler, $value, $key, $parentData);
	}
}
