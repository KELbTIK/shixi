<?php

class SJB_UserDetails extends SJB_ObjectDetails
{
	var $properties;
	var $details;
	
	function SJB_UserDetails($user_info, $user_group_sid)
	{
		$details_info = SJB_UserDetails::getDetails($user_group_sid, !empty($user_info['reference_uid']));
		foreach ($details_info as $detail_info) {
		    $detail_info['value'] = '';
			if (isset($user_info[$detail_info['id']]))
				$detail_info['value'] = $user_info[$detail_info['id']];
				
			$this->properties[$detail_info['id']] = new SJB_ObjectProperty($detail_info);
		}
	}
	
	public static function getDetails($user_group_sid, $referenceUid = false)
	{
		$details =  array (
			    array (
					'id'			=> 'featured',
					'caption'		=> 'Featured',
					'type'			=> 'boolean',
					'length'		=> '20',
					'is_required'	=> false,
					'is_system'		=> true,
					'order'			=> null,
				),
				array (
					'id'			=> 'active',
					'caption'		=> 'Status',
					'type'			=> 'list',
					'list_values'	 => array(
											array(
											'id'		=> '1',
											'caption'	=> 'Active',
											),
											array(
											'id'		=> '0',
											'caption'	=> 'Not active',
											),
										),
					'length'		=> '10',
					'is_required'	=> false,
					'is_system'	=> true,
				),
				array (
					'id'		=> 'sendmail',
					'caption'	=> "Don't send mailings",
					'type'		=> 'boolean',
					'length'	=> '1',
					'is_required'=> false,
					'is_system'=> true,
				),
				
			   );

		$user_group_info    = SJB_UserGroupManager::getUserGroupInfoBySID($user_group_sid);
		$email_confirmation = false;
		if(isset($user_group_info['email_confirmation'])) {
			$email_confirmation = $user_group_info['email_confirmation'];
		}

		if (SJB_UserGroupManager::isUserEmailAsUsernameInUserGroup($user_group_sid)
				|| (class_exists ('SJB_SocialPlugin') && $referenceUid)) {
			$userFields = array (
						 array (
							'id'		 => 'username',
							'caption'	 => 'User name', 
							'type'		 => 'string',
							'table_name' => 'users',
							'length'	 => '20',
							'is_required'=> true,
							'is_system'  => true,
							'order'		 => 0,
						),
						array (
							'id'		=> 'password',
							'caption'	=> 'Password',
							'type'		=> 'password',
							'length'	=> '20',
							'is_required'=> true,
							'is_system'=> true,
							'order'			=> 2,
						),
						array (
							'id'		=> 'email',
							'caption'	=> 'Email',
							'type'		=> 'unique_email',
							'table_name' => 'users',
							'length'	=> '20',
							'is_required'=> true,
							'is_system'=> true,
							'order'			=> 1,
							'email_confirmation' => $email_confirmation,
						)
					);
		} else {
			$userFields = array (
			   	    array (
						'id'		=> 'username',
						'caption'	=> 'User name', 
						'type'		=> 'unique_string',
						'table_name' => 'users',
						'length'	=> '20',
			   	    	'validators' => array(
							'SJB_IdValidator',
		            		'SJB_UniqueSystemValidator'
						),
						'is_required'=> true,
						'is_system'=> true,
						'order'			=> 0,
					),
					array (
						'id'		=> 'password',
						'caption'	=> 'Password',
						'type'		=> 'password',
						'length'	=> '20',
						'is_required'=> true,
						'is_system'=> true,
						'order'			=> 1,
					),
					array (
						'id'		=> 'email',
						'caption'	=> 'Email',
						'type'		=> 'unique_email',
						'table_name' => 'users',
						'length'	=> '20',
						'is_required'=> true,
						'is_system'=> true,
						'order'			=> 2,
						'email_confirmation' => $email_confirmation,
					),
				);
		}
				
		$details = array_merge($userFields, $details);	
		$extra_details = SJB_UserProfileFieldManager::getFieldsInfoByUserGroupSID($user_group_sid);
		foreach ($extra_details as $key => $extra_detail) {
			if ($extra_detail['type'] == 'monetary' || $extra_detail['type'] == 'complex')
				$extra_details[$key]['is_system'] = false;
			else 
				$extra_details[$key]['is_system'] = true;
		}
		$details = array_merge($details, $extra_details);

		if (SJB_PluginManager::isPluginActive('CaptchaPlugin')
				&& SJB_System::getSettingByName('registrationCaptcha') == 1 && SJB_System::getURI()== '/registration/') {
			$details_captcha =  array (
					array (
						'id'		=> 'captcha',
						'caption'	=> 'Enter code from image', 
						'type'		=> 'captcha',
						'length'	=> '20',
						'is_required'=> true,
						'is_system'=> false,
					),
				);
			$details = array_merge($details, $details_captcha);
		}
		return $details;
	}
}
