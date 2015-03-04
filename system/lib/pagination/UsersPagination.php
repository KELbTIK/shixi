<?php

class SJB_UsersPagination extends SJB_Pagination
{
	public function __construct($userGroupInfo, $online, $template)
	{
		if ($userGroupInfo['id'] == 'JobSeeker' || $userGroupInfo['id'] == 'Employer') {
			$this->item = mb_strtolower($userGroupInfo['name'], 'utf8') . 's';
		} else {
			$this->item = '\'' . mb_strtolower($userGroupInfo['name'], 'utf8') . '\' users';
		}

		if ($online == 1) {
			$this->uniqueUrlParams['online'] = array('value' => '1');
		}

		if ($template == 'choose_user.tpl') {
			$this->actionsForSelect = false;
		} else {
			$this->countActionsButtons = 2;
			$this->popUp = true;
			$actionsForSelect = array(
				'activate'                  => array('name' => 'Activate'),
				'deactivate'                => array('name' => 'Deactivate'),
				'approve'                   => array('name' => 'Approve', 'isVisible' => $userGroupInfo['approve_user_by_admin']),
				'reject'                    => array('name' => 'Reject',  'isVisible' => $userGroupInfo['approve_user_by_admin']),
				'send_activation_letter'    => array('name' => 'Send Activation Email'),
				'delete'                    => array('name' => 'Delete'),
				'change_product'            => array('name' => 'Change Product'),
				'ban_ip'                    => array('name' => 'Ban IP'),
				'unban_ip'                  => array('name' => 'Unban IP'),
			);
			$this->setActionsForSelect($actionsForSelect);
		}



		$fields = array(
			'sid'               => array('name' => 'ID'),
			'username'          => array('name' => 'Username'),
			'CompanyName'       => array('name' => 'Company Name', 'isVisible' => false),
			'FirstName'         => array('name' => 'First Name', 'isVisible' => false),
			'LastName'          => array('name' => 'Last Name', 'isVisible' => false),
			'email'             => array('name' => 'Email'),
			'products'          => array('name' => 'Products', 'isSort' => false),
			'registration_date' => array('name' => 'Registration Date'),
			'active'            => array('name' => 'Status'),
			'approval'          => array('name' => 'Approval Status', 'isVisible' => $userGroupInfo['approve_user_by_admin']),
		);

		if ($userGroupInfo['id'] == 'Employer') {
			$fields['CompanyName']['isVisible'] = true;
		} elseif ($userGroupInfo['id'] == 'JobSeeker') {
			$fields['FirstName']['isVisible'] = true;
			$fields['LastName']['isVisible'] = true;
		}

		$this->setSortingFieldsToPaginationInfo($fields);

		parent::__construct('sid', 'DESC', 10);
	}
}
