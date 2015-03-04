<?php

class SJB_ListingComplexField extends SJB_Object
{
	var $field_sid;
	var $field_type;
	var $order;
	
	function SJB_ListingComplexField($listing_field_info, $listing_type_sid = 0)
	{
		$this->db_table_name = 'listing_complex_fields';
		$this->details = new SJB_ListingComplexFieldDetails($listing_field_info);
		$this->setListingTypeSID($listing_type_sid);
		$this->field_type = isset($listing_field_info['type']) ? $listing_field_info['type'] : null;
		$this->order = isset($listing_field_info['order']) ? $listing_field_info['order'] : null;
	}
	
	function setListingTypeSID($listing_type_sid)
	{
		$this->listing_type_sid = $listing_type_sid;
	}
	
	function getOrder()
	{
		return $this->order;
	}
	
	function getListingTypeSID()
	{
		return false;
	}
	
	function getListingParentSID()
	{
		return $this->field_sid;
	}
	
	function getFieldType()
	{
		return $this->field_type;
	}
}

