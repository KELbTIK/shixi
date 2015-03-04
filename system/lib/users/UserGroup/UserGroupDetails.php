<?php

class SJB_UserGroupDetails extends SJB_ObjectDetails
{
	/**
	 * @var SJB_NotificationGroups
	 */
	protected $notificationGroups;

	/**
	 * @param array $object_info
	 */
	public function __construct($object_info)
	{
		$this->notificationGroups = new SJB_NotificationGroups();
		if (isset($object_info['id'])) {
			$this->notificationGroups->setNotificationListingType($object_info['id']);
		}
		parent::SJB_ObjectDetails($object_info);
	}

	public function getDetails()
	{
		$userGroupDetails = $this->getUserGroupDetails();
		$userGroupNotificationDetails = $this->getNotificationsDetails();
		return array_merge($userGroupDetails, $userGroupNotificationDetails);
	}

	public function getUserGroupDetails()
	{
		return array(
			array(
				'id' => 'id',
				'caption' => 'ID',
				'type' => 'unique_string',
				'length' => '20',
				'table_name' => 'user_groups',
				'validators' => array(
					'SJB_IdValidator',
            		'SJB_UniqueSystemValidator'
				),
				'is_required' => true,
				'is_system' => true,
			),
			array(
				'id' => 'name',
				'caption' => 'Group name',
				'type' => 'string',
				'length' => '20',
				'table_name' => 'user_groups',
				'is_required' => true,
				'is_system' => true,
			),
			array(
				'id' => 'reg_form_template',
				'caption' => 'Registration form template',
				'type' => 'string',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array(
				'id' => 'send_activation_email',
				'caption' => 'Send Activation Email',
				'type' => 'boolean',
				'comment' => 'Enable this setting if you want users to activate their account using the activation link sent to their email.',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array(
				'id' => 'approve_user_by_admin',
				'caption' => 'Approve Users by Admin',
				'type' => 'boolean',
				'comment' => 'Enable this setting if you want users of this group to be approved by admin, before their account will be activated.',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array(
				'id' => 'email_confirmation',
				'caption' => 'Require email confirmation',
				'type' => 'boolean',
				'comment' => 'If this box is checked, users will be asked to enter their email twice for confirmation when registering.',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array(
				'id' => 'user_menu_template',
				'caption' => 'User Menu Template',
				'type' => 'string',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array(
				'id' => 'show_mailing_flag',
				'caption' => "Show \"Don't send mailings\" check box<br/> in user profile",
				'type' => 'boolean',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array(
				'id' => 'user_email_as_username',
				'caption' => 'User email as user name',
				'type' => 'boolean',
				'comment' => 'Set this setting if you want users to use their email<br/> instead of user name when sign in',
				'length' => '',
				'is_required' => false,
				'is_system' => true,
			),
			array (
				'id'		=> 'after_registration_redirect_to',
				'caption'	=> 'After registration redirect to',
				'type'		=> 'list',
				'length'	=> '20',
				'table_name'=> 'user_groups',
				'list_values'	 => array(
					array(
						'id'		=> 'my_account',
						'caption'	=> 'My Account',
					),
					array(
						'id'		=> 'posting_page',
						'caption'	=> 'Posting page',
					),
				),
				'is_required'=> false,
				'is_system'	=> true,
			)
		);
	}

	public function getNotificationsDetails()
	{
		$notificationGroupsSet = $this->notificationGroups->getNotifications();
		$notifications = array();
		foreach ($notificationGroupsSet as $groupID => $groupNotifications) {
			foreach ($groupNotifications as &$notification)
				$this->prepareNotification($notification, $groupID);
			$notifications = array_merge($notifications, $groupNotifications);
		}

		return $notifications;
	}

	/**
	 * @param array $notification
	 * @param string $groupID
	 */
	public function prepareNotification(&$notification, $groupID)
	{
		if (in_array($notification['id'], $this->notificationGroups->getNotificationsWithIntegerType())) {
			$notification['type'] = 'integer';
		}
		else {
			$notification['type'] = 'list';
			$notification['list_values'] = SJB_EmailTemplateEditor::getEmailTemplatesForListByGroup($groupID);
		}
		$notification['is_required'] = false;
		$notification['is_system'] = false;
	}

	/**
	 * @return \SJB_NotificationGroups
	 */
	public function getNotificationGroups()
	{
		return $this->notificationGroups;
	}
}