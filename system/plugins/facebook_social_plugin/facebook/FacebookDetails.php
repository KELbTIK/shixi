<?php

class SJB_FacebookDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;
	var $common_fields;

	function __construct()
	{
		$details_info = self::getDetails();
		
		foreach ($details_info as $index => $property_info) {
			$sort_array[$index] = $property_info['order'];
		}

		$sort_array = SJB_HelperFunctions::array_sort($sort_array);

        foreach ($sort_array as $index => $value) {
			$sorted_details_info[$index] = $details_info[$index];
		}

		foreach ($sorted_details_info as $detail_info) {
		    $detail_info['value'] = '';
			if (isset($info[$detail_info['id']]))
				$detail_info['value'] = $info[$detail_info['id']];
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails()
	{
		$listing_info = array();
		
		foreach (SJB_UserGroupManager::getAllUserGroupsInfo() as $key => $val) {
			array_push($listing_info, array('id' => $val['sid'], 'caption' => $val['name']));
		}
		
		$system_details = array(
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
						'list_values'	=> $listing_info,
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
						'caption'		=> 'Display “Like” FaceBook icon for Jobs', 
						'type'			=> 'boolean',
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> 1,
					),
					array(
						'id'			=> 'fb_likeResume',
						'caption'		=> 'Display “Like” FaceBook icon for Resumes', 
						'type'			=> 'boolean',
						'is_required'	=> false,
						'is_system'		=> true,
						'order'			=> 1,
					),
		);

		return $system_details;
	}
	
}

