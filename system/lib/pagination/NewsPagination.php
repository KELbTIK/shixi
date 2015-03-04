<?php

class SJB_NewsPagination extends SJB_Pagination
{
	public function __construct($categoryId, $catagoryName)
	{
		$this->item = 'news';
		$this->countActionsButtons = 2;
		$this->uniqueUrlParams = array(
			'action'        => array('value' => 'edit'),
			'category_sid'  => array('value' => $categoryId),
		);
		$actionsForSelect = array(
			'activate'      => array('name' => 'Activate'),
			'deactivate'    => array('name' => 'Deactivate'),
			'archive'       => array('name' => 'Archive'),
			'delete'        => array('name' => 'Delete')
		);
		$this->setActionsForSelect($actionsForSelect);

		$fields = array(
			'id'                => array('name' => 'ID'),
			'title'             => array('name' => 'Title'),
			'date'              => array('name' => 'Publication Date'),
			'expiration_date'   => array('name' => 'Expiration Date'),
			'active'            => array('name' => 'Status', 'isVisible' => $this->isVisibleStatusField($catagoryName)),
			'link'              => array('name' => 'URL'),
			'language'          => array('name' => 'Language'),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('date', 'DESC', 10);
	}

	private function isVisibleStatusField($catagoryName)
	{
		if ($catagoryName == 'Archive') {
			return false;
		}
		return true;
	}
}