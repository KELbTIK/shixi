<?php

class SJB_ExportIterator extends ArrayIterator
{
	private $array                = array();
	private $additionalParameters = array();
	private $callbackFunction     = null;
	
	public function __construct()
	{
		reset($this->array);
	}

	public function rewind()
	{
		reset($this->array);
	}

	/**
	 * @return mixed
	 */
	public function current()
	{
		$exportData = array();
		
		$value = current($this->array);
		if (is_numeric($value)) {
			$this->additionalParameters['sid'] = $value;
		} else {
			$this->additionalParameters['info'] = $value;
			$this->additionalParameters['key'] = $this->key();
		}
		
		if ($this->callbackFunction) {
			list($className, $functionName) = explode('::', $this->callbackFunction);
			$exportData = call_user_func(array($className, $functionName), $this->additionalParameters);
		} else {
			$exportData['info'] = $value;
			$exportData['key']  = $this->key();
		}
		
		return $exportData;
	}

	/**
	 * @return int|string
	 */
	public function key()
	{
		return key($this->array);
	}

	public function next()
	{
		next($this->array);
	}

	/**
	 * @return bool
	 */
	public function valid()
	{
		$currentItem = current($this->array);
		return !empty($currentItem);
	}

	/**
	 * @param array $array
	 */
	public function setArray(array $array)
	{
		$this->array = $array;
	}

	/**
	 * @param array $additionalParameters
	 */
	public function setAdditionalParameters(array $additionalParameters)
	{
		$this->additionalParameters = $additionalParameters;
	}

	/**
	 * @param string $callbackFunction
	 */
	public function setCallbackFunction($callbackFunction)
	{
		$this->callbackFunction = $callbackFunction;
	}
}
