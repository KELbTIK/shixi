<?php

class SJB_ListingDisplayListController extends SJB_DisplayListController
{
	function SJB_ListingDisplayListController($input_data)
	{
		parent::SJB_DisplayListController($input_data, new SJB_ListingFieldManager, new SJB_ListingFieldListItemManager);
	}

	function _getTypeInfo()
	{
		return SJB_ListingTypeManager::getListingTypeInfoBySID($this->field->getListingTypeSID());
	}

	function _getTypeSID()
	{
		return $this->field->getListingTypeSID();
	}

}
