<?php

class SJB_Cache
{
	private static $instance = null;
	const TAG_LISTINGS = 'listings';
	const TAG_USERS = 'users';
	const TAG_FIELDS = 'fields';
	const TAG_LISTING_TYPES = 'listing_types';

	/**
	 * @static
	 * @return Zend_Cache_Core
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			$caching = SJB_Settings::getValue('enableCache');
			$caching = !empty($caching);
			$cacheHours = SJB_Settings::getValue('cacheHours');
			if (empty($cacheHours))
				$cacheHours = 0;
			$cacheMinutes = SJB_Settings::getValue('cacheMinutes');
			if (empty($cacheMinutes))
				$cacheMinutes = 0;
			$lifetime = intval($cacheHours) * 3600 + intval($cacheMinutes) * 60;

			$frontendOptions = array(
			   	'lifetime' => $lifetime,
			   	'automatic_serialization' => true,
				'caching' => $caching
			);

			$backendOptions = array(
			    'cache_dir' => SJB_BASE_DIR . 'system'.DIRECTORY_SEPARATOR.'cache' // директория, в которой размещаются файлы кэша
			);

			// получение объекта Zend_Cache_Core
			self::$instance = Zend_Cache::factory('Core',
			                             'File',
			                             $frontendOptions,
			                             $backendOptions);
		}
		return self::$instance;
	}
}
