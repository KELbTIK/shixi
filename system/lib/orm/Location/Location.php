<?php

class SJB_Location
{
	public $sid;
	public $name;
	public $longitude;
	public $latitude;
	public $city;
	public $state;
	public $state_code;
	public $country_sid;
	
	public function __construct($location_info = null)
	{
		$this->name = isset($location_info['name']) ? $location_info['name'] : null;
		$this->longitude = isset($location_info['longitude']) ? $location_info['longitude'] : null;
		$this->latitude = isset($location_info['latitude']) ? $location_info['latitude'] : null;
		$this->city = isset($location_info['city']) ?$location_info['city'] : null;
		$this->state = isset($location_info['state']) ? $location_info['state'] : null;
		$this->state_code = isset($location_info['state_code']) ? $location_info['state_code'] : null;
		$this->country_sid = isset($location_info['country_sid']) ? $location_info['country_sid'] : null;
	}
	
	function getInfo()
	{
		return array(
			'name'        => $this->name,
			'longitude'   => $this->longitude,
			'latitude'    => $this->latitude,
			'city'        => $this->city,
			'state'       => $this->state,
			'state_code'  => $this->state_code,
			'country_sid' => $this->country_sid
		);
	}
	
	function isDataValid(&$errors)
	{
		
		$errors = array();
		
		if ($this->name == '') {
			$errors['Zip Code'] = 'EMPTY_VALUE';
		}
		$count = SJB_DB::queryValue("SELECT count(*) FROM `locations` WHERE `name` = ?s AND `country_sid` = ?s AND `state` = ?s AND `city` = ?s AND sid <> ?n ", $this->name, $this->country_sid, $this->state, $this->city, $this->sid);
		if ($count) {
			$errors['Zip Code'] =  'NOT_UNIQUE_VALUE';
		}
		
		if ($this->longitude == '') {
			$errors['Longitude'] = 'EMPTY_VALUE';
		}
		elseif (!is_numeric($this->longitude)) {
			$errors['Longitude'] = 'NOT_FLOAT_VALUE';
		}
		
		if ($this->latitude == '') {
			$errors['Latitude'] = 'EMPTY_VALUE';
		}
		elseif (!is_numeric($this->latitude)) {
			$errors['Latitude'] = 'NOT_FLOAT_VALUE';
		}

		if ($this->country_sid == '') {
			$errors['Country'] = 'EMPTY_VALUE';
		}

		return count($errors) == 0;
	}
	
	function setSID($location_sid)
	{
		$this->sid = $location_sid;
	}
	
	function getSID()
	{
		return $this->sid;
	}
}
