<?php

class SJB_PostingPages extends SJB_Object
{
	public $listing_type_sid = 0;
	public $order;
	
	function SJB_PostingPages($page_info = array(), $listing_type_sid = 0)
	{
		$this->db_table_name = 'posting_pages';
		$this->setListingTypeSID($listing_type_sid);
		$this->db_table_name = 'posting_pages';
		$this->details = new SJB_PostingPagesDetails($page_info, $this->listing_type_sid);
		$this->order = isset($page_info['order']) ? $page_info['order'] : null;
	}
	
	function setListingTypeSID($listing_type_sid)
	{
		$this->listing_type_sid = $listing_type_sid;
	}
	
	function getListingTypeSID() 
	{
		return $this->listing_type_sid;
	}
	
	function getOrder() 
	{
		return $this->order;
	}
}