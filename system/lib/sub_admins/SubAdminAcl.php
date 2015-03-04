<?php

/**
 * Permission class for subadmins
 */
class SJB_SubAdminAcl extends SJB_Acl
{
	/**
	  * @var SJB_SubAdminAcl
	  */
	 protected static $instance = null;

	/**
	 * @return SJB_SubAdminAcl
	 */
	public static function getInstance($reload = false)
	{
		if (null === self::$instance || $reload)
			self::$instance = new self();
		return self::$instance;
	}

	/**
	 * @return array
	 */
	public function getResources($type = 'all', $role = '')
	{
		$listingsTypes = SJB_ListingTypeDBManager::getAllListingTypesInfo();
		/* sort by resume, job then others */
		usort($listingsTypes, function($listingType) {
			return ($listingType['id'] == 'Job' || $listingType['id'] == 'Resume') ? true : false;
		});
		$listingsTypes = array_reverse($listingsTypes);

		$listingsTypesResources = array();
		foreach ($listingsTypes as $listingType) {
			$listingType['name'] = (!in_array($listingType['name'], array('Resume', 'Job'))) ? "'{$listingType['name']}' Listings" : $listingType['name'] . 's';
			$listingType['id'] = (!in_array($listingType['id'], array('Resume', 'Job'))) ? "{$listingType['id']}_listings" : $listingType['id'] . 's';
			$listingsTypesResources['manage_' . mb_strtolower($listingType['id'], 'UTF-8')] = array(
					'title' => "Manage {$listingType['name']}",
					'group' => 'listings',
					'type' => 'subadmin'
			);
				$listingsTypesResources['get_notifications_on_' . mb_strtolower($listingType['id'], 'UTF-8') . '_added'] = array(
					'title' => 'Get Notifications on ' . $listingType['name'] . ' Added',
					'group' => 'listings',
					'type' => 'subadmin',
					'parent' => 'manage_' . mb_strtolower($listingType['id'], 'UTF-8'),
					'notification' => true
				);
				$listingsTypesResources['get_notifications_on_' . mb_strtolower($listingType['id'], 'UTF-8') . '_expiration'] = array(
					'title' => 'Get Notifications on ' . $listingType['name'] . ' Expiration',
					'group' => 'listings',
					'type' => 'subadmin',
					'parent' => 'manage_' . mb_strtolower($listingType['id'], 'UTF-8'),
					'notification' => true,
				);
		}
		$resources = array(
			'manage_common_listing_fields' => array(
				'title' => 'Manage Common Listing Fields',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'manage_listing_types_and_specific_listing_fields' => array(
				'title' => 'Manage Listing Types and Specific Listing Fields',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'set_posting_pages' => array(
				'title' => 'Set Posting Pages',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'import_listings' => array(
				'title' => 'Import Listings',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'export_listings' => array(
				'title' => 'Export Listings',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'set_xml_feeds' => array(
				'title' => 'Set XML Feeds',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'set_xml_import' => array(
				'title' => 'Set XML Import',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'manage_flagged_listings' => array(
				'title' => 'Manage Flagged Listings',
				'group' => 'listings',
				'type' => 'subadmin'
			),
			'get_notifications_on_listing_flagged' => array(
				'title' => 'Get Notifications on Listing Flagged',
				'group' => 'listings',
				'type' => 'subadmin',
				'parent' => 'manage_flagged_listings',
				'notification' => true,
			),

			// USERS
			'manage_user_groups' => array(
				'title' => 'Manage User Groups',
				'group' => 'users',
				'type' => 'subadmin'
			),
			'manage_user_groups_permissions' => array(
				'title' => 'Manage User Groups Permissions',
				'group' => 'users',
				'type' => 'subadmin',
				'parent' => 'manage_user_groups',

			),
			'edit_user_groups_profile_fields' => array(
				'title' => 'Edit User Groups Profile Fields',
				'group' => 'users',
				'type'	=> 'subadmin',
				'parent' => 'manage_user_groups',
			),
			'import_users' => array(
				'title' => 'Import Users',
				'group' => 'users',
				'type' => 'subadmin'
			),
			'export_users' => array(
				'title' => 'Export Users',
				'group' => 'users',
				'type' => 'subadmin'
			),
			'create_and_send_mass_mailings' => array(
				'title' => 'Create and Send Mass Mailings',
				'group' => 'users',
				'type' => 'subadmin'
			),
			'manage_guest_email_alerts' => array(
				'title' => 'Manage Guest Email Alerts',
				'group' => 'users',
				'type' => 'subadmin'
			),
			'manage_banned_ips' => array(
				'title' => 'Manage Banned IPs',
				'group' => 'users',
				'type' => 'subadmin'
			),

			//  	Edit Templates and Themes
			'edit_form_builder' => array(
				'title' => 'Use Form Bulider',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			'edit_templates_and_themes' => array(
				'title' => 'Edit Templates and Themes',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			'manage_banners' => array(
				'title' => 'Manage Banners',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			'manage_site_pages' => array(
				'title' => 'Manage Site Pages',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			'manage_static_content' => array(
				'title' => 'Manage Static Content',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			'manage_news' => array(
				'title' => 'Manage News',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			'manage_polls' => array(
				'title' => 'Manage Polls',
				'group' => 'layout and content',
				'type' => 'subadmin'
			),
			//  SYSTEM CONFIGURATION
			'configure_system_settings' => array(
				'title' => 'Configure System Settings',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'social_media' => array(
				'title' => 'Social Media',
				'group' => 'system configuration',
				'type' => 'subadmin',
			),
			'social_media_bitly' => array(
				'title' => 'Bitly Settings',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent' => 'social_media',
			),
			'edit_zipcode_database' => array(
				'title' => 'Edit ZipCode Database',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'manage_countries' => array(
				'title' => 'Manage Countries',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'manage_states_or_regions' => array(
				'title' => 'Manage States/Regions',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'configure_breadcrumbs' => array(
				'title' => 'Configure Breadcrumbs',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'set_html_filters' => array(
				'title' => 'Set HTML filters',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'manage_currencies' => array(
				'title' => 'Manage Currencies',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'set_task_scheduler' => array(
				'title' => 'Set Task Scheduler',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'manage_plug-ins' => array(
				'title' => 'Manage Plug-ins',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'set_phpbb_plug-in' => array(
				'title' => 'Set PhpBB Plug-in',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_facebook_plug-in' => array(
				'title' => 'Set Facebook Plug-in',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_linkedin_plug-in' => array(
				'title' => 'Set Linkedin Plug-in',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_wordpress_plug-in' => array(
				'title' => 'Set WordPress Plug-in',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_twitter_plug-in' => array(
				'title' => 'Set Twitter Plug-in',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_sharethisplugin' => array(
				'title' => 'Set ShareThisPlugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_captchaplugin' => array(
				'title' => 'Set CaptchaPlugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_indeedplugin' => array(
				'title' => 'Set IndeedPlugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_jujuplugin' => array(
				'title' => 'Set JujuPlugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_simplyhiredplugin' => array(
				'title' => 'Set SimplyHiredPlugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_googleplugin' => array(
				'title' => 'Set Google Social Plugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_googleanalyticsplugin' => array(
				'title' => 'Set Google Analytics Plugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_beyondplugin' => array(
				'title' => 'Set Beyond Plugin',
				'group' => 'system configuration',
				'type' => 'subadmin',
				'parent'	=> 'manage_plug-ins',
			),
			'set_refine_search_parameters' => array(
				'title' => 'Set Refine Search Parameters',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'create_and_restore_backups' => array(
				'title' => 'Create and Restore Backups',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'edit_flag_listing_settings' => array(
				'title' => 'Edit Flag Listing Settings',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			'email_log' => array(
				'title' => 'View Email Log',
				'group' => 'system configuration',
				'type' => 'subadmin'
			),
			//  	BILLING
			'manage_invoices' => array(
				'title' => 'Manage Invoices',
				'group' => 'billing',
				'type' => 'subadmin'
			),
			'manage_products' => array(
				'title' => 'Manage Products',
				'group' => 'billing',
				'type' => 'subadmin'
			),
			'manage_promotions' => array(
				'title' => 'Manage Promotions',
				'group' => 'billing',
				'type' => 'subadmin'
			),
			'manage_tax_rules' => array(
				'title' => 'Manage Tax Rules',
				'group' => 'billing',
				'type' => 'subadmin'
			),
			'manage_payment_gateways' => array(
				'title' => 'Manage Payment Gateways',
				'group' => 'billing',
				'type' => 'subadmin'
			),
			'transaction_history' => array(
				'title' => 'View Transaction History',
				'group' => 'billing',
				'type' => 'subadmin'
			),
			'payment_log' => array(
				'title' => 'View Payment Log',
				'group' => 'billing',
				'type' => 'subadmin'
			),

			//  	REPORTS
			'general_statistics' => array(
				'title' => 'View General Statistics',
				'group' => 'reports',
				'type' => 'subadmin'
			),
			'listings_reports' => array(
				'title' => 'View Listings Reports',
				'group' => 'reports',
				'type' => 'subadmin'
			),
			'applications_and_views_reports' => array(
				'title' => 'View Applications and Views Reports',
				'group' => 'reports',
				'type' => 'subadmin'
			),
			'sales_reports' => array(
				'title' => 'View Sales Reports',
				'group' => 'reports',
				'type' => 'subadmin'
			),
			'guest_alerts_reports' => array(
				'title' => 'View Guest Alerts Reports',
				'group' => 'reports',
				'type' => 'subadmin'
			),

			// LANGUAGES
			'manage_languages' => array(
				'title' => 'Manage Languages',
				'group' => 'languages',
				'type' => 'subadmin'
			),
			'translate_phrases' => array(
				'title' => 'Translate Phrases',
				'group' => 'languages',
				'type' => 'subadmin'
			),
			'import_languages' => array(
				'title' => 'Import Languages',
				'group' => 'languages',
				'type' => 'subadmin'
			),
			'export_languages' => array(
				'title' => 'Export Languages',
				'group' => 'languages',
				'type' => 'subadmin'
			),
		);
		$resources = array_merge($listingsTypesResources, $resources);
		$userGroups = SJB_UserGroupManager::getAllUserGroupsInfo();
		foreach ($userGroups as $userGroup) {
			$userGroupId = mb_strtolower($userGroup['id'], 'UTF-8');
			$userGroupName = $userGroup['name'];
			if ($userGroup['name'] == 'Job Seeker' || $userGroup['name'] == 'Employer') {
				$titleEdition = $userGroup['name'] . 's';
			} else {
				$titleEdition = '\'$permission.groupName\' users'; 
			}
			$resources['manage_' . $userGroupId] = array(
				'title'        => 'Manage ' . $titleEdition,
				'groupName'    => $userGroupName,
				'group'        => 'users',
				'type'         => 'subadmin'
			);
			$resources['get_notifications_on_' . $userGroupId . '_registration'] = array(
				'title'        => 'Get Notifications on Registration of ' . $titleEdition,
				'groupName'    => $userGroupName,
				'group'        => 'users',
				'type'         => 'subadmin',
				'parent'       => 'manage_' . $userGroupId,
				'notification' => true,
			);
			$resources['get_notifications_on_' . $userGroupId . '_subscription_expiration'] = array(
				'title'        => 'Get Notifications on Products Expiration of ' . $titleEdition,
				'groupName'    => $userGroupName,
				'group'        => 'users',
				'type'         => 'subadmin',
				'parent'       => 'manage_' . $userGroupId,
				'notification' => true,
			);
			$resources['get_notifications_on_deleting_' . $userGroupId . '_profile'] = array(
				'title'        => 'Get Notifications on Profile Deletion of ' . $titleEdition,
				'groupName'    => $userGroupName,
				'group'        => 'users',
				'type'         => 'subadmin',
				'parent'       => 'manage_' . $userGroupId,
				'notification' => true,
			);
		}

		return $resources;
	}

	public static function getPermissionGroups()
	{
		return array(
			'listings',
			'users',
			'layout and content',
			'billing',
			'reports',
			'system configuration',
			'languages',
		);
	}

	public static function mergePermissionsWithResources(&$aResources, $aPermissions = array())
	{
		foreach ($aResources as $key => $resource) {
			$aResources[$key]['value'] = 'deny';
			$aResources[$key]['name'] = $key;

			foreach ($aPermissions as $perm) {
				if ($key == $perm['name']) {
					$aResources[$key]['value'] = $perm['value'];
					$aResources[$key]['params'] = $perm['params'];
					break;
				}
			}
		}
	}

	/**
	 * get notifications from permissions list
	 * 
	 * @param array $aResources
	 * @param array $aPermissions
	 * @return array
	 */
	public static function getSubAdminNotifications(array $aResources, array $aPermissions)
	{
		$aNotifications = array();

		foreach ($aPermissions as $permission) {
			$permissionKey = $permission['name'];

			if (array_key_exists($permissionKey, $aResources) && isset($aResources[$permissionKey]['notification']) && 'allow' == $aResources[$permissionKey]['value']) {
				$aNotifications[$permissionKey] = $aResources[$permissionKey];
			}
		}

		return $aNotifications;
		
	}

	/**
	 * move subpermissions to parents
	 * @param array $aResources
	 */
	public static function prepareSubPermissions(&$aResources)
	{
		foreach ($aResources as $key => $resource) {
			if (isset($resource['parent']) && !empty($resource['parent'])) {
				$parentKey = $resource['parent'];
				$aResources[$parentKey]['subpermissions'][$key] = $resource;
				unset($aResources[$key]);
			}
		}
	}

	public static function getAllPermissions($type, $role)
	{
		return SJB_DB::query('SELECT * FROM `permissions` WHERE `type` = ?s AND `role` = ?s', $type, $role);
	}

	/**
	 *
	 * @param string $name
	 * @return string
	 */
	public static function definePermission( $name )
	{
		$value = SJB_Request::getVar($name, '');
		return empty($value) ? 'deny' : 'allow';
	}

	/**
	 * @param $resource
	 * @param null $roleId
	 * @param bool $returnByParams
	 * @param string $type
	 * @param bool $returnMessage
	 * @return bool|string
	 */
	public function isAllowed($resource, $roleId = null, $returnByParams = false, $type = 'user', $returnMessage = false)
    {
        $resource = mb_strtolower($resource, 'UTF-8');
		
		if (!isset($this->permissions[$roleId])) {
			$this->permissions = $this->getPermissions('subadmin', $roleId);
		}

        if (!$returnByParams) {
			return isset($this->permissions[$resource]['value']) && $this->permissions[$resource]['value'] == 'allow';
		}

		return isset($this->permissions[$resource]['value']) && $this->permissions[$resource]['value'] == 'allow' && $this->permissions[$resource]['params'] != 'deny';
    }

	public static function mergePermissionsWithRequest(array &$aResources)
	{
		foreach ($aResources as $name => &$resource) {
			$resource['value'] = self::definePermission($name);
		}
		// Nwy: если захочется менять массив ещё, то переменную resource нужно unset-нуть.. см manual
	}

	public static function setSubAdminNotificationByPermName($role, $permName, $value)
	{
		return SJB_DB::query('UPDATE `permissions` SET `params` = ?s WHERE `name` = ?s AND `type` = \'subadmin\' AND `role` = ?n',
				$value, $permName, $role);
	}

}
