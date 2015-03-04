<?php

class SJB_SendListingInfoController
{
	var $submitted_data;

	/**
	 * @var null|SJB_Listing
	 */
	private $listing = null;

	function SJB_SendListingInfoController($input_data)
	{
		$listingId = isset($input_data['listing_id']) ? $input_data['listing_id'] : null;
		if (!empty($listingId)) {
			if (!is_numeric($listingId)) {
				throw new Exception("Invalid listing ID is specified");
			}
			$this->listing = SJB_ListingManager::getObjectBySID($listingId);
			if (!empty($this->listing)) {
				$this->listing = SJB_ListingManager::createTemplateStructureForListing($this->listing);
				$this->submitted_data = $input_data;
			}
		}
		return false;
	}

	function isListingSpecified()
	{
		return !empty($this->listing);
	}

	function isDataSubmitted()
	{
		return isset($this->submitted_data['is_data_submitted']);
	}

	function getData()
	{
		return array('listing' => $this->listing, 'submitted_data' => $this->submitted_data);
	}

	/**
	 * @return int|null
	 */
	function getListingID()
	{
		if (!empty($this->listing))
			return $this->listing['id'];
		return null;
	}

}
