<?php

class SJB_Iterator extends ArrayIterator
{
    private $array = array();
    private $listing_type_sid = 0;  
    private $criteria = array(); 
    private $user_logged_in = false;
    private $current_user_sid = 0;
	private $view = 'list';
	static private $index = 0;
	static private $coordinates = array();

    public function __construct() 
    {
        reset($this->array);
    }

    public function rewind() 
    {
        reset($this->array);
    }

    public function current() 
    {
    	$listing_structure = array();
    	$info = current($this->array);
    	if (is_numeric($info)) {
	   		$sid = $info;
			$cache = SJB_Cache::getInstance();
			$cacheID = md5('ListingIterator::SJB_ListingManager::getObjectBySID' . $sid);
			if ($cache->test($cacheID)) {
				$listing = $cache->load($cacheID);
			}
			else {
				$listing = SJB_ListingManager::getObjectBySID($sid);
				$cache->save($listing, $cacheID, array(SJB_Cache::TAG_LISTINGS));
			}

			$listing->addPicturesProperty();
			$cacheId = md5('SJB_ListingTypeManager::getListingTypeIDBySID' . $listing->getListingTypeSID());
			if (SJB_MemoryCache::has($cacheId)) {
				$listing_type = SJB_MemoryCache::get($cacheId);
			} else {
				$listing_type = SJB_ListingTypeManager::getListingTypeIDBySID($listing->getListingTypeSID());
				SJB_MemoryCache::set($cacheId, $listing_type);
			}

			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing);
			$listing_structure = SJB_ListingManager::newValueFromSearchCriteria($listing_structure, $this->criteria);
			if ($this->user_logged_in) {
				$listing_structure['saved_listing'] = SJB_SavedListings::getSavedListingsByUserAndListingSid($this->current_user_sid, $listing->getID());
			}
			$listing_structure['activation_date'] = date('Y-m-d H:i:s', strtotime($listing_structure['activation_date']));
			$listing_structure['expiration_date'] = date('Y-m-d H:i:s', strtotime($listing_structure['expiration_date']));
			$listing_structure['listing_url']		= SJB_System::getSystemSettings('SITE_URL')."/display-".strtolower($listing_type)."/".$listing->getSID()."/";

			if (isset($listing->details->properties['EmploymentType'])) {
				$employmentInfo		= $listing->details->properties['EmploymentType']->type->property_info;
				$employmentTypes	= array();
				$employment			= explode(",", $employmentInfo['value']);

				foreach ($employmentInfo['list_values'] as $type) {
					$empType = str_replace(" ", "", $type['caption']);
					$employmentTypes[$empType] = 0;
					
					if ( in_array($type['id'], $employment) ) 
						$employmentTypes[$empType] = 1;
				}
				$listing_structure['myEmploymentType'] = $employmentTypes;
	    	}

			// GOOGLE MAP SEARCH RESULTS CUSTOMIZATION
			if ($this->view == 'map') {
				$zipCode = $listing_structure['Location']['ZipCode'];
				// get 'latitude' and 'longitude' from zipCode field, if it not set
				$latitude  = isset($listing_structure['latitude']) ? $listing_structure['latitude'] : '';
				$longitude = isset($listing_structure['longitude']) ? $listing_structure['longitude'] : '';

				if (!empty($zipCode) && empty($latitude) && empty($longitude)) {
					$result = SJB_DB::query("SELECT * FROM `locations` WHERE `name` = ?s LIMIT 1", $zipCode);
					if ($result) {
						$current_coordinates = array($result[0]['latitude'], $result[0]['longitude']);
						if (in_array($current_coordinates, self::$coordinates)) {
							self::$index += 0.0001;
						}
						$listing_structure['latitude'] = $result[0]['latitude'] + self::$index;
						$listing_structure['longitude'] = $result[0]['longitude'] + self::$index;
						self::$coordinates = array_merge(self::$coordinates, array($current_coordinates));
					}
				} elseif (!empty($listing_structure['Location']['City']) && !empty($listing_structure['Location']['State']) && !empty($listing_structure['Location']['Country'])) {
					$address = $listing_structure['Location']['City'].', '.$listing_structure['Location']['State'].', '.$listing_structure['Location']['Country'];
					$address = urlencode($address);
					$cache = SJB_Cache::getInstance();
					$parameters = array(
						'City'    => $listing_structure['Location']['City'],
						'State'   => $listing_structure['Location']['State'],
						'Country' => $listing_structure['Location']['Country']
					);
					$hash = md5('google_map'.serialize($parameters));
					$data = $cache->load($hash);
					if (!$data) {
						try {
							$geoCod = SJB_HelperFunctions::getUrlContentByCurl("http://maps.googleapis.com/maps/api/geocode/json?address={$address}&sensor=false");
							$geoCod = json_decode($geoCod);
							if ($geoCod->status == 'OK') {
								$cache->save($geoCod, $hash);
							}
						} catch (Exception $e) {
							$backtrace = SJB_Logger::getBackTrace();
							SJB_Error::writeToLog(array(( array('level' => 'E_USER_WARNING', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'backtrace' => sprintf("BACKTRACE:\n [%s]", join("<br/>\n", $backtrace)) ))));
						}
					} else {
						$geoCod = $data;
					}
					try {
						if(!is_object($geoCod)) {
							throw new Exception("Map object nave not been Created");
						}
						if($geoCod->status != 'OK') {
							throw new Exception("Status is not OK");
						}
						$location = $geoCod->results[0]->geometry->location;
						$current_coordinates = array($location->lat, $location->lng);
						if (in_array($current_coordinates, self::$coordinates)) {
							self::$index += 0.0001;
						}
						$listing_structure['latitude'] = $location->lat + self::$index;
						$listing_structure['longitude'] = $location->lng + self::$index;
						self::$coordinates = array_merge(self::$coordinates, array($current_coordinates));
					} catch (Exception $e) {
						$backtrace = SJB_Logger::getBackTrace();
						SJB_Error::writeToLog(array(( array('level' => 'E_USER_WARNING', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'backtrace' => sprintf("BACKTRACE:\n [%s]", join("<br/>\n", $backtrace)) ))));
					}
				}
			}
    	}
    	elseif ($info) {
    		$listing_structure = $info;
    	}
        return $listing_structure;
    }

    public function key() 
    {
        return key($this->array);
    }

    public function next() 
    {
        next($this->array);
    }

    public function valid() 
    {
    	$currentItem = current($this->array);
        return !empty($currentItem);
    }
    
   	public function setListingsSids($listingSids) 
   	{
    	$this->array = $listingSids;
    }
    
   	public function setListingTypeSID($listingTypeSID) 
   	{
    	$this->listing_type_sid = $listingTypeSID;
    }
    
    public function setCriteria($criteria)
    {
    	$this->criteria = $criteria;
    }
    
    public function setUserLoggedIn($userLoggedIn)
    {
    	$this->user_logged_in = $userLoggedIn;
    }
    
    public function setCurrentUserSID($userSID)
    {
    	$this->current_user_sid = $userSID;
    }
    
    public function offsetSet($offset, $value) 
    {
        $this->array[$offset] = $value;
    }
    
    public function offsetExists($offset) 
    {
        return isset($this->array[$offset]);
    }
    
    public function offsetUnset($offset) 
    {
        unset($this->array[$offset]);
    }
    
    public function offsetGet($offset) 
    {
        return $this->array[$offset];
    }

	public function count()
	{
		return count($this->array);
	}

	public function setView($view)
	{
		$this->view = $view;
	}

}
