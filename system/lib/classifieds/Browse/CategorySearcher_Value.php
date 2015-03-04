<?php

class SJB_CategorySearcher_Value extends SJB_AbstractCategorySearcher
{
	function SJB_CategorySearcher_Value($field)
	{
		parent::SJB_AbstractCategorySearcher($field);
	}

	function& _decorateItems(&$items)
	{
		return $items;
	}
}
