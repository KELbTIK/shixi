<?php

class SJB_GeoLocation 
{
	private $radLat;  // latitude in radians
	private $radLon;  // longitude in radians

	private $degLat;  // latitude in degrees
	private $degLon;  // longitude in degrees

	private $MIN_LAT; 
	private $MAX_LAT;
	private $MIN_LON;
	private $MAX_LON;

	public function __construct() 
	{
		$this->MIN_LAT = deg2rad(-90); // -PI/2
		$this->MAX_LAT = deg2rad(90);   //  PI/2
		$this->MIN_LON = deg2rad(-180); // -PI
		$this->MAX_LON = deg2rad(180);  //  PI
	}

	/**
	 * @param latitude the latitude, in degrees.
	 * @param longitude the longitude, in degrees.
	 */
	public static function fromDegrees($latitude, $longitude) 
	{
		$result = new SJB_GeoLocation();
		$result->radLat = deg2rad($latitude);
		$result->radLon = deg2rad($longitude);
		$result->degLat = $latitude;
		$result->degLon = $longitude;
		return $result;
	}

	/**
	 * @param latitude the latitude, in radians.
	 * @param longitude the longitude, in radians.
	 */
	public static function fromRadians($latitude, $longitude) 
	{
		$result = new SJB_GeoLocation();
		$result->radLat = $latitude;
		$result->radLon = $longitude;
		$result->degLat = rad2deg($latitude);
		$result->degLon = rad2deg($longitude);
		return $result;
	}

	/**
	 * @return the latitude, in degrees.
	 */
	public function getLatitudeInDegrees() 
	{
		return $this->degLat;
	}

	/**
	 * @return the longitude, in degrees.
	 */
	public function getLongitudeInDegrees() 
	{
		return $this->degLon;
	}

	/**
	 * @return the latitude, in radians.
	 */
	public function getLatitudeInRadians() 
	{
		return $this->radLat;
	}

	/**
	 * @return the longitude, in radians.
	 */
	public function getLongitudeInRadians() 
	{
		return $this->radLon;
	}


	public function toString() 
	{
		return "(" + $this->degLat + "\u00B0, " + $this->degLon + "\u00B0) = (" +
		$this->radLat + " rad, " + $this->radLon + " rad)";
	}

	/**
	 * Computes the great circle distance between this GeoLocation instance
	 * and the location argument.
	 * @param radius the radius of the sphere, e.g. the average radius for a
	 * spherical approximation of the figure of the Earth is approximately
	 * 6371.01 kilometers.
	 * @return the distance, measured in the same unit as the radius
	 * argument.
	 */
	public function distanceTo($location, $radius) 
	{
		return acos(sin($this->radLat) * sin($location->radLat) +
				cos($this->radLat) * cos($location->radLat) *
				cos($this->radLon - $location->radLon)) * $radius;
	}

	/**
	 * <p>Computes the bounding coordinates of all points on the surface
	 * of a sphere that have a great circle distance to the point represented
	 * by this GeoLocation instance that is less or equal to the distance
	 * argument.</p>
	 * <p>For more information about the formulae used in this method visit
	 * <a href="http://JanMatuschek.de/LatitudeLongitudeBoundingCoordinates">
	 * http://JanMatuschek.de/LatitudeLongitudeBoundingCoordinates</a>.</p>
	 * @param distance the distance from the point represented by this
	 * GeoLocation instance. Must me measured in the same unit as the radius
	 * argument.
	 * @param radius the radius of the sphere, e.g. the average radius for a
	 * spherical approximation of the figure of the Earth is approximately
	 * 6371.01 kilometers.
	 * @return an array of two GeoLocation objects such that:<ul>
	 * <li>The latitude of any point within the specified distance is greater
	 * or equal to the latitude of the first array element and smaller or
	 * equal to the latitude of the second array element.</li>
	 * <li>If the longitude of the first array element is smaller or equal to
	 * the longitude of the second element, then
	 * the longitude of any point within the specified distance is greater
	 * or equal to the longitude of the first array element and smaller or
	 * equal to the longitude of the second array element.</li>
	 * <li>If the longitude of the first array element is greater than the
	 * longitude of the second element (this is the case if the 180th
	 * meridian is within the distance), then
	 * the longitude of any point within the specified distance is greater
	 * or equal to the longitude of the first array element
	 * <strong>or</strong> smaller or equal to the longitude of the second
	 * array element.</li>
	 * </ul>
	 */
	public function boundingCoordinates($distance, $radius) 
	{
		$radDist = $distance / $radius;

		$minLat = $this->radLat - $radDist;
		$maxLat = $this->radLat + $radDist;

		if ($minLat > $this->MIN_LAT && $maxLat < $this->MAX_LAT) {
			$deltaLon = asin(sin($radDist) / cos($this->radLat));
			$minLon = $this->radLon - $deltaLon;
			if ($minLon < $this->MIN_LON) {
				$minLon += 2 * pi();
			}
			$maxLon = $this->radLon + $deltaLon;
			if ($maxLon > $this->MAX_LON) {
				$maxLon -= 2 * pi();
			}
		} else {
			// a pole is within the distance
			$minLat = max($minLat, $this->MIN_LAT);
			$maxLat = min($maxLat, $this->MAX_LAT);
			$minLon = $this->MIN_LON;
			$maxLon = $this->MAX_LON;
		}
		return array($this->fromRadians($minLat, $minLon), $this->fromRadians($maxLat, $maxLon));;
	}
}