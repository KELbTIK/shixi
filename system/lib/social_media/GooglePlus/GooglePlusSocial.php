<?php

class SJB_GooglePlusSocial extends SJB_SocialMedia
{

	public static function getConnectSettings()
	{
		return array(
			array(
				'id'			=> 'oauth2_client_id',
				'caption'		=> 'Client ID for Web Application',
				'type'			=> 'string',
				'length'		=> '25',
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> 'To get these credentials you need to register an application in <a href="https://cloud.google.com/console" target="_blank">Google API Console</a>.<br/><br/>Follow the <a href="http://wiki.smartjobboard.com/display/sjb42/Google+Plus#GooglePlus-GettingGoogle+Credentials" target="_blank">User Manual instructions</a> on how to do this.'
			),
			array(
				'id' 			=> 'client_secret',
				'caption' 		=> 'Client Secret for Web Application',
				'type'			=> 'string',
				'length'		=> '25',
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> ''
			),
			array(
				'id' 			=> 'developer_key',
				'caption' 		=> 'API Key for Browser Applications',
				'type'			=> 'string',
				'length'		=> '25',
				'is_required'	=> true,
				'order'			=> null,
				'comment'		=> ''
			),
			array(
				'id'			=> 'google_plus_userGroup',
				'caption'		=> 'User Group',
				'type'			=> 'multilist',
				'list_values'	=> SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions(),
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> 1,
				'comment'		=> 'Please select user groups which will use Google+ login/registration',
			),
			array(
				'id'			=> 'enable_job_sharing_for_users_googleplus',
				'caption'		=> 'Enable job sharing for users',
				'type'			=> 'boolean',
				'is_required'	=> false,
				'is_system'		=> true,
				'order'			=> null,
				'comment'		=> 'Users allowed to post jobs on Social Networks will be able to post jobs on $networkName.',
			),
		);
	}
}
