<?php

class SJB_Miscellaneous_OpengraphMeta extends SJB_Function
{
	public function execute()
	{
		$listingSID = SJB_Request::getVar('listing_id', false);
		if (!empty($listingSID)) {
			$metaOpenGraph = SJB_ListingManager::setMetaOpenGraph($listingSID);
			echo $metaOpenGraph;
		}
	}
}