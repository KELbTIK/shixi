<?php

class SJB_FacebookSocial extends SJB_SocialMedia
{

	function SJB_FacebookSocial($info = array())
	{
		$this->db_table_name = 'facebook_feeds';
		$this->details = new SJB_FacebookSocialDetails($info);
		$this->common_fields = SJB_SocialMediaDetails::getCommonFields();
	}

	public function saveFeed($feed, $action = '')
	{
		$networkSocialMedia = new SJB_FacebookSocialMedia();
		$accessToken = $networkSocialMedia->getAccessToken();
		$user = $networkSocialMedia->getAccountInfo($feed->getSID());
		$accountName = !empty($user['account_name']) ? $user['account_name'] : '';
		
		$feed->addProperty(
			array(
				'id'        => 'access_token',
				'type'      => 'text',
				'value'     => $accessToken,
				'is_system' => true
			)
		);
		
		$feed->addProperty(
			array(
				'id'        => 'account_name',
				'type'      => 'text',
				'value'     => $accountName,
				'is_system' => true
			)
		);
		
		$feed->addProperty(
			array(
				'id'        => 'expiration_date',
				'type'      => 'date',
				'value'     => SJB_I18N::getInstance()->getDate(date('Y-m-d', time() + 60 * 24 * 60 * 60)),
				'is_system' => true
			)
		);
		
		parent::saveFeed($feed);
	}

	public static function getConnectSettings()
	{
		return array(
				array(
					'id'			=> 'fb_appID',
					'caption'		=> 'Facebook App ID',
					'type'			=> 'string',
					'length'		=> '255',
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> -1,
					'comment'		=> 'To get these credentials you need to create an application in <a href="https://developers.facebook.com/" target="_blank">Facebook Developers Console</a>.<br /><br />Follow the <a target="_blank" href="http://wiki.smartjobboard.com/display/sjb42/Facebook#Facebook-GettingFacebookCredentials">User Manual instructions</a> on how to do this.'
				),
				array(
					'id'			=> 'fb_appSecret',
					'caption'		=> 'Facebook App Secret',
					'type'			=> 'string',
					'length'		=> '255',
					'is_required'	=> true,
					'is_system'		=> true,
					'order'			=> -0,
				),
				array(
					'id'			=> 'facebook_userGroup',
					'caption'		=> 'User Group',
					'type'			=> 'multilist',
					'list_values'	=> SJB_UserGroupManager::getAllUserGroupsIDsAndCaptions(),
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1,
					'comment'		=> 'Please select user groups which will use Facebook connect',
				),
				array(
					'id'			=> 'facebook_resumeAutoFillSync',
					'caption'		=> 'Allow Resume auto filling/synchronizing for Job Seekers',
					'type'			=> 'boolean',
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1,
				),
				array(
					'id'			=> 'fb_likeJob',
					'caption'		=> 'Display "Like" FaceBook icon for Jobs',
					'type'			=> 'boolean',
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1,
				),
				array(
					'id'			=> 'fb_likeResume',
					'caption'		=> 'Display "Like" FaceBook icon for Resumes',
					'type'			=> 'boolean',
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> 1,
				),
			);
	}

}
