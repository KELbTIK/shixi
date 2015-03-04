<?php

class SJB_LocationManager
{
	public static function saveLocation($location)
	{
		$location_sid = $location->getSID();
		$countryInfo = SJB_CountriesManager::getCountryInfoBySID($location->country_sid);
		$locationField =  $location->name ." ". $location->city ." ". $location->state ." ". $location->state_code;
		if ($countryInfo) 
			$locationField .=  " ". $countryInfo['country_name'] ." ". $countryInfo['country_code'];
		$locationField = explode(' ', $locationField);
		foreach ($locationField as $key => $field) {
			$field = trim($field);
			$len = strlen($field);
			if ($len < 4) {
				for ($i = $len; $i < 4; $i++) 
					$field .= '_'; 
				$locationField[$key] = $field;
			}
		}
		$locationField = implode(' ', $locationField);
		if (is_null($location_sid)) {
			return SJB_DB::query("INSERT INTO `locations` (`name`, `longitude`, `latitude`, `city`, `state`, `state_code`, `country_sid`, `location`) VALUES (?s, ?f, ?f, ?s, ?s, ?s, ?n, ?s)"
			, $location->name, $location->longitude, $location->latitude, $location->city, $location->state, $location->state_code, $location->country_sid, $locationField, false);
		}
		
		return SJB_DB::query("UPDATE `locations` SET `name` = ?s, `longitude` = ?f, `latitude` = ?f, `city` = ?s, `state` = ?s, `state_code` = ?s, `country_sid` = ?n, `location` = ?s WHERE `sid` = ?n" 
		, $location->name, $location->longitude, $location->latitude, $location->city, $location->state, $location->state_code, $location->country_sid, $locationField, $location->getSID());
	}

	public static function getLocationsInfo()
	{
		return SJB_DB::query("SELECT * FROM locations ORDER BY sid");
	}

	public static function deleteLocationBySID($location_sid)
	{
		return SJB_DB::query("DELETE FROM locations WHERE sid = ?n", $location_sid);
	}

	public static function addLocation($name, $longitude, $latitude, $city = null, $state = null, $state_code = null, $country_sid = null, $countryInfo = null)
	{
		$locationField =  $name ." ". $city ." ". $state ." ". $state_code;
		if ($countryInfo) 
			$locationField .=  " ". $countryInfo['country_name'] ." ". $countryInfo['country_code'];
		$locationField = explode(' ', $locationField);
		foreach ($locationField as $key => $field) {
			$field = trim($field);
			$len = strlen($field);
			if ($len < 4) {
				for ($i = $len; $i < 4; $i++) 
					$field .= '_'; 
				$locationField[$key] = $field;
			}
		}
		$locationField = implode(' ', $locationField);
		if (!self::doesLocationExistForImport($name, $country_sid, $state, $city)) {
			if (SJB_DB::query("INSERT INTO `locations` SET `name` = ?s, `longitude` = ?f, `latitude` = ?f, `city` = ?s, `state` = ?s, `state_code` = ?s, `country_sid` = ?n, `location` = ?s ",	$name, $longitude, $latitude, $city, $state, $state_code, $country_sid, $locationField)) {
				return 1;
			}
		} else {
			SJB_DB::query("UPDATE `locations` SET `longitude` = ?f, `latitude` = ?f, `city` = ?s, `state` = ?s, `state_code` = ?s, `country_sid` = ?n, `location` = ?s WHERE `name` = ?s", $longitude, $latitude, $city, $state, $state_code, $country_sid, $locationField, $name);
		}
		return 0;
	}

	public static function getLocationsInfoWithLimit($offset, $count, $where = '', $sorting_field, $sorting_order, $params = array())
	{
		return SJB_DB::query("SELECT l.*, c.`country_name` FROM locations l LEFT JOIN `countries` c ON c.`sid` = l.`country_sid` {$where} ORDER BY {$sorting_field} {$sorting_order} LIMIT $offset, $count", SJB_DB::explodeQueryArgs($params));
	}
	
	public static function getLocationNumber($search = '', $params = array())
	{
		return SJB_DB::queryValue("SELECT count(*) FROM locations l {$search}", SJB_DB::explodeQueryArgs($params));
	}

	public static function deleteAllLocations()
	{
		return SJB_DB::query("TRUNCATE TABLE locations");
	}

	public static function doesLocationExist($location_name)
	{
		if (empty($location_name)) {
			return false;
		}
		$exists = SJB_DB::query("SELECT `sid` FROM `locations` WHERE `name` = ?s  LIMIT 1", $location_name);
		return !empty($exists);
	}

	public static function doesLocationExistForImport($location_name, $country_sid, $state = '', $city = '')
	{
		if (empty($location_name) or empty($country_sid)) {
			return true; // To not allow import empty values
		}
		$exists = SJB_DB::query("SELECT `sid` FROM `locations` WHERE `name` = ?s AND
				(`country_sid` = ?s OR `country_sid` IS NULL) AND (`state` = ?s OR `state` IS NULL) AND
				(`city` = ?s OR `city` IS NULL) LIMIT 1", $location_name, $country_sid, $state, $city);
		return !empty($exists);
	}

	public static function getLocationInfoBySID($location_sid)
	{
		$location_info = SJB_DB::query("SELECT * FROM locations WHERE sid = ?n", $location_sid);
		if (empty($location_info)) {
			return null;
		}
		return array_pop($location_info);
	}
	
	public static function findPlacesWithinDistance($location, $distance)
	{
		$radius = 6371.01;
		$boundingCoordinates = $location->boundingCoordinates($distance, $radius);
		$meridian180WithinDistance = $boundingCoordinates[0]->getLongitudeInRadians() > $boundingCoordinates[1]->getLongitudeInRadians();
		$angularRadius = $distance / $radius;
		$sql = " ((`latitude` >= {$boundingCoordinates[0]->getLatitudeInDegrees()} AND `latitude` <= {$boundingCoordinates[1]->getLatitudeInDegrees()}) AND (`longitude` >= {$boundingCoordinates[0]->getLongitudeInDegrees()}".
			($meridian180WithinDistance ? " OR" : " AND") . " `longitude` <= {$boundingCoordinates[1]->getLongitudeInDegrees()}) AND 
			acos(sin({$location->getLatitudeInRadians()}) * sin(RADIANS(`latitude`)) + cos({$location->getLatitudeInRadians()}) * cos(RADIANS(`latitude`)) * cos(RADIANS(`longitude`) - {$location->getLongitudeInRadians()})) <= {$angularRadius}) ";
		return $sql;
	}

	public static function getDistanceBetweenPointsInKm($latitude1, $longitude1, $latitude2, $longitude2)
	{
		$earthRadius = 6371.01;// Radius of the earth in km
		$dLat     = deg2rad($latitude2 - $latitude1);
		$dLon     = deg2rad($longitude2 - $longitude1);
		$factor   = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon / 2) * sin($dLon / 2);
		$distance = 2 * atan2(sqrt($factor), sqrt(1 - $factor));
		return $earthRadius * $distance;
	}

	public static function locationFormat($params)
	{
		$i18N       = SJB_I18N::getInstance();
		$domain     = $i18N->getDefaultDomain();
		$format     = $params['format'];
		$location   = $params['location'];
		$state      = $location['State'] ? $i18N->gettext($domain, $location['State']) : '';
		$stateCode  = $location['State_Code'] ? $i18N->gettext($domain, $location['State_Code']) : '';
		$country    = $location['Country'] ? $i18N->gettext($domain, $location['Country']) : '';
		switch ($format) {
			case 'extraLong' :
				$result = array($country, $state, $location['City'], $location['ZipCode']);
				break;
			case 'long' :
				$result = array($location['City'], $stateCode, $country);
				break;
			case 'middle' :
				$result = array($location['City'], $stateCode, $location['ZipCode']);
				break;
			case 'short' :
			default :
				$result = array($location['City'], $stateCode);
		}
		$location = preg_replace('/(^[\,\s]+)|(\s,)|([\,\s]+$)/im', '', implode(', ', $result));
		return $location ? htmlspecialchars($location) : '';
	}
}

