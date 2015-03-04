<?php

class SJB_ListingEditListController extends SJB_EditListController
{
	function SJB_ListingEditListController($input_data)
	{
		parent::SJB_EditListController($input_data, new SJB_ListingFieldManager, new SJB_ListingFieldListItemManager);
	}
}
