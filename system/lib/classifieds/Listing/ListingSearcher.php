<?php

class SJB_ListingSearcher extends SJB_Searcher
{
	public function __construct()
	{
		parent::__construct(new SJB_ObjectInfoSearcher('listings'), new SJB_ListingManager);
	}

	/**
	 * @param $criteria
	 * @param $aliases
	 * @return int
	 */
	public function countRowsByCriteria($criteria, $aliases)
	{
		$this->criteria = $criteria;
		$this->object_info_searcher->setCriteria($criteria, $aliases);
		return $this->object_info_searcher->countRows();
	}

	/**
	 * @param array $groupByField
	 */
	public function setGroupByField($groupByField)
	{
		$this->object_info_searcher->setGroupByField($groupByField);
	}
}
