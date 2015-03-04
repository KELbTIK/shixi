<?php

class SJB_LinkedInSocial extends SJB_SocialMedia
{

	/**
	 * @param array $info
	 * @param bool $isGroupsExist
	 * @param bool $isAuthorized
	 */
	function __construct($info = array(), $isGroupsExist = false, $isAuthorized = false)
	{
		$this->db_table_name = 'linkedin_feeds';
		$this->details = new SJB_LinkedInSocialDetails($info, $isGroupsExist, $isAuthorized);
	}

	/**
	 * @return array
	 */
	public static function getConnectSettings() {
		return array(
			array(
				'id'            => 'li_apiKey',
				'caption'       => 'Linkedin API Key',
				'type'          => 'string',
				'length'        => '255',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 1,
				'comment'       => 'To get these credentials you need to create an application in <a href="https://www.linkedin.com/secure/developer" target="_blank">Linkedin Developer Network</a>.<br /><br />Follow the <a href="http://wiki.smartjobboard.com/display/sjb42/Linkedin#Linkedin-GettingLinkedinCredentials" target="_blank">User Manual instructions</a> on how to do this.',
			),
			array(
				'id'            => 'li_secKey',
				'caption'       => 'Linkedin Secret Key',
				'type'          => 'string',
				'length'        => '255',
				'is_required'   => true,
				'is_system'     => true,
				'order'         => 2,
			),
			array(
				'id'            => 'linkedin_userGroup',
				'caption'       => 'User Group',
				'type'          => 'multilist',
				'list_values'   => SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions(),
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 3,
				'comment'       => 'Please select user groups which will use LinkedIn login/registration',
			),
			array(
				'id'            => 'linkedin_resumeAutoFillSync',
				'caption'       => 'Allow Resume auto filling/synchronizing for Job Seekers',
				'type'          => 'boolean',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 4,
			),
			array(
				'id'            => 'li_allowShareJobs',
				'caption'       => 'Allow Job Seekers to share Jobs on LinkedIn',
				'type'          => 'boolean',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 5,
			),
			array(
				'id'            => 'li_memberProfileWidget',
				'caption'       => 'Display "Member Profile" widget in User Info block',
				'type'          => 'boolean',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 6,
			),
			array(
				'id'            => 'li_companyInsiderWidget',
				'caption'       => 'Display "Company Insider" widget on Company Profile page',
				'type'          => 'boolean',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 8,
			),
			array(
				'id'            => 'li_companyProfileWidget',
				'caption'       => 'Display "Company Profile" widget in Company Info block',
				'type'          => 'boolean',
				'is_required'   => false,
				'is_system'     => true,
				'order'         => 9,
			),
		);
	}

	/**
	 * @param SJB_LinkedInSocial $feed
	 * @param string $action
	 */
	public function saveFeed($feed, $action = '')
	{
		parent::saveFeed($feed);
		$linkedInSocialMedia = new SJB_LinkedInSocialMedia();
		$linkedInSocialMedia->saveAccountInfo($feed->getSID(), $feed->getPropertyValue('account_id'));
	}
}
