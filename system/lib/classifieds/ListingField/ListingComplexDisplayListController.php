<?php

class SJB_ListingComplexDisplayListController extends SJB_DisplayListController
{
	function SJB_ListingComplexDisplayListController($input_data)
	{
		parent::SJB_DisplayListController($input_data, new SJB_ListingComplexFieldManager, new SJB_ListingFieldListItemManager());
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
