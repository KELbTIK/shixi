<?php

class SJB_ListingFieldRequestCreator extends SJB_DBObjectRequestCreator
{
	function SJB_ListingFieldRequestCreator($listing_sid_collection)
	{
		$this->table_prefix = 'listing_fields';
		parent::SJB_DBObjectRequestCreator($listing_sid_collection);
	}
	
}
