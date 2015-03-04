<?php


class SJB_ListingType extends SJB_Object
{
	function SJB_ListingType($listing_type_info)
	{
		$this->db_table_name = 'listing_types';
		$this->details = new SJB_ListingTypeDetails($listing_type_info);
	}
}

