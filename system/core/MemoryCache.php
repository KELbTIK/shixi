<?php

/**
 * Класс сделан для хранения значений в массиве для повторного использования
 * как заглушка на будущие оптимизации
 */
class SJB_MemoryCache
{
	private static $values = array();

	public static function get($name)
	{
		if (self::has($name))
			return self::$values[$name];
		return false;
	}

	public static function set($name, $value)
	{
		self::$values[$name] = $value;
	}

	public static function has($name)
	{
		return array_key_exists($name, self::$values);
	}
}
