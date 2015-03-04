<?php

class SJB_Array
{
	
	/**
	 * Get array element by key.
	 * Return null if key not exists
	 *
	 * @param array|null $array
	 * @param string|null $key
	 * @param mixed|null $default
	 * @return mixed
	 */
	public static function get($array = null, $key = null, $default = null)
	{
		if ($key === null) {
			return $array;
		}
		if (isset($array[$key])) {
			return $array[$key];
		}
		if (!is_null($default)) {
			return $default;
		}
		return null;
	}


	/**
	 * Get value from array by path.
	 * Example path to value: elem1/elem2/.../elemN
	 *
	 * @static
	 * @param array $array
	 * @param string $path
	 * @return mixed
	 */
	public static function getPath($array, $path)
	{
		$found = true;
		$path  = trim($path, ' /');
		$path  = explode("/", $path);
		$count = count($path);

		for ($x=0; ($x < $count and $found); $x++){
			$key = $path[$x];

			if (is_array($array) && array_key_exists($key, $array)){
				$array = $array[$key];
			} else {
				$found = false;
				$array = null;
			}
	    }
	    return $array;
	}


	/**
	 * Set value in array by array path.
	 * Path must be a string with '/' delimiters of path. Example: 'elem1/subelem11/subsubelem'
	 * Method returns changed array
	 *
	 * @static
	 * @param array $array
	 * @param string $stringPath
	 * @param mixed $value
	 * @return array
	 */
	public static function setPathValue($array, $stringPath, $value)
	{
		$path  = trim($stringPath, ' /');
		$path  = explode("/", $path);
		$count = count($path);

		$firstElemPath = array_shift($path);

		// if $array is null - create array here
		if (is_null($array)) {
			$array = array();
		}

		// if end of path - just set value and return
		if ($count == 1) {
			$array[$firstElemPath] = $value;
			return $array;
		}

		// if not exist - create element
		if (!array_key_exists($firstElemPath, $array)) {
			$array[$firstElemPath] = array();
		}

		// check current value and convert it to array
		if (!is_array($array[$firstElemPath])) {
			$array[$firstElemPath] = array($array[$firstElemPath]);
		}

		// OK. now recursive fill the element with value
		$stringPath = implode('/', $path);
		$array[$firstElemPath] = self::setPathValue($array[$firstElemPath], $stringPath, $value);

		return $array;
	}


	/**
	 * Unset node in array given by path string.
	 * If wrong path given (not exists) - array not will be changed.
	 *
	 *
	 * @static
	 * @param array $array
	 * @param string $stringPath
	 * @return array
	 */
	public static function unsetValueByPath($array, $stringPath)
	{
		$path  = trim($stringPath, ' /');
		$path  = explode("/", $path);
		$count = count($path);

		$firstElemPath = array_shift($path);

		// if end of path - just set value and return
		if ($count == 1) {
			unset($array[$firstElemPath]);
			return $array;
		}

		// if not exist - do not change
		if (!array_key_exists($firstElemPath, $array)) {
			return $array;
		}

		// if current element is not array, and it's not last path node it's mean "wrong path given"
		// just break removing and return
		if (!is_array($array[$firstElemPath]) && $count > 1) {
			return $array;
		}

		// OK. now recursive walk in array
		$stringPath = implode('/', $path);
		$array[$firstElemPath] = self::unsetValueByPath($array[$firstElemPath], $stringPath);

		return $array;
	}

}