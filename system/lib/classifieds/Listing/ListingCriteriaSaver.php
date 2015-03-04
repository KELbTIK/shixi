<?php

class SJB_ListingCriteriaSaver extends SJB_CriteriaSaver
{
	var $uri;
	
	function SJB_ListingCriteriaSaver($storage_id = '')
	{
		$storage_id = 'ListingSearcher' . $storage_id;
		parent::SJB_CriteriaSaver($storage_id, new SJB_ListingManager);
		$this->uri	= &$_SESSION[$storage_id]['uri'];
		if (is_null($this->uri))
			$this->uri = SJB_Navigator::getURI();
	}
	
	function setUri($uri)
	{
		$this->uri = $uri;
	}
	
	function getUri()
	{
		return $this->uri;
	}
	
}