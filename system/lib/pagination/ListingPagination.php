<?php

class SJB_ListingPagination extends SJB_Pagination
{
	public function __construct($listingTypeInfo)
	{
		if ($listingTypeInfo['id'] == 'Job') {
			$fieldUserName = 'Username (Company Name)';
		} elseif ($listingTypeInfo['id'] == 'Resume') {
			$fieldUserName = 'Username (Name)';
		} else {
			$fieldUserName = 'Username';
		}

		$this->item = mb_strtolower($listingTypeInfo['name'] . 's', 'utf8');
		$this->countActionsButtons = 3;
		$this->popUp = true;

		$actionsForSelect = array(
			'activate'              => array('name' => 'Activate'),
			'deactivate'            => array('name' => 'Deactivate'),
			'delete'                => array('name' => 'Delete'),
			'approve'               => array('name' => 'Approve', 'isVisible' => $listingTypeInfo['waitApprove']),
			'reject'                => array('name' => 'Reject',  'isVisible' => $listingTypeInfo['waitApprove']),
			'modify_date_button'    => array('name' => 'Modify Expiration Date'),
		);
		$this->setActionsForSelect($actionsForSelect);

		$fields = array(
			'id'                => array('name' => 'ID'),
			'Title'             => array('name' => 'Title'),
			'product'           => array('name' => 'Product', 'isSort' => false),
			'activation_date'   => array('name' => 'Activation Date'),
			'expiration_date'   => array('name' => 'Expiration Date'),
			'username'          => array('name' => $fieldUserName),
			'views'             => array('name' => 'Views'),
			'active'            => array('name' => 'Status'),
			'status'            => array('name' => 'Approval Status', 'isVisible' => $listingTypeInfo['waitApprove']),
		);
		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('activation_date', 'DESC', 10);
	}
}