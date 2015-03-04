<?php
class SJB_Admin_Menu_AdminMenu extends SJB_Function
{
	public function isAccessible()
	{
		return true;
	}


	public function execute()
	{
		$GLOBALS['LEFT_ADMIN_MENU']['Listing Configuration'] = array
		(
			array
			(
				'title' => 'Common Fields',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/listing-fields/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-listing-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/delete-listing-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-tree/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-list/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-list-item/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-location-fields/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-fields/',
				),
				'perm_label' => 'manage_common_listing_fields',
			),
			array
			(
				'title' => 'Listing Types',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/listing-types/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-listing-type/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-type/',
					SJB_System::getSystemsettings('SITE_URL') . '/delete-listing-type/',
					SJB_System::getSystemsettings('SITE_URL') . '/add-listing-type-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-type-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/delete-listing-type-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/posting-pages/',
					SJB_System::getSystemsettings('SITE_URL') . '/attention-listing-type-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-tree/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-list/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-list-item/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-location-fields/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing-field/edit-fields/',
				),
				'perm_label' => array('manage_listing_types_and_specific_listing_fields', 'set_posting_pages')

			),
		);

		$listingTypes = SJB_ListingTypeManager::getAllListingTypesInfo();

		usort($listingTypes, function($listingType) {
			return ($listingType['id'] == 'Job' || $listingType['id'] == 'Resume') ? true : false;
		});
		$listingTypes = array_reverse($listingTypes);

		foreach ($listingTypes as $listingType) {
			if (!in_array($listingType['id'], array('Resume', 'Job'))) {
				$title	= "'{$listingType['name']}' Listings";
				$link	= strtolower($listingType['id']) . '-listings/';
				$permLabel = strtolower($listingType['id']) . '_listings';
			} else {
				$title	= "{$listingType['name']}s";
				$link	= strtolower($listingType['id']) . 's/';
				$permLabel = strtolower($listingType['id']) . 's';
			}
			$manageListings[] = array(
				'title'		=> 'Manage ' . $title,
				'reference'	=> SJB_System::getSystemsettings('SITE_URL') . '/manage-' . $link,
				'highlight'	=> array (
					SJB_System::getSystemsettings('SITE_URL') . '/add-listing/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-listing/',
					SJB_System::getSystemsettings('SITE_URL') . '/display-listing/',
					SJB_System::getSystemsettings('SITE_URL') . '/manage-pictures/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-picture/',
				),
				'perm_label' => 'manage_' . $permLabel,
			);
		}

		$listingsManagement = array(
			array
			(
				'title' => 'Import Listings',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/import-listings/',
				'highlight' => array(),
				'perm_label' => 'import_listings',
			),
			array
			(
				'title' => 'Export Listings',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/export-listings/',
				'highlight' => array(),
				'perm_label' => 'export_listings',
			),
			array
			(
				'title' => 'XML Feeds',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/listing-feeds/',
				'highlight' => array(),
				'perm_label' => 'set_xml_feeds',
			),
			array
			(
				'title' => 'XML Import',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/show-import/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-import/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-import/',
					SJB_System::getSystemsettings('SITE_URL') . '/run-import/',
				),
				'perm_label' => 'set_xml_import',
			),
			array
			(
				'title' => 'Flagged Listings',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/flagged-listings/',
				'highlight' => array(),
				'perm_label' => 'manage_flagged_listings',
			)
		);
		$GLOBALS['LEFT_ADMIN_MENU']['Listing Management'] = array_merge($manageListings, $listingsManagement);

		$userGroup = array
		(
			array
			(
				'title' => 'User Groups',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/user-groups/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-user-group/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-group/',
					SJB_System::getSystemsettings('SITE_URL') . '/delete-user-group/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-profile/',
					SJB_System::getSystemsettings('SITE_URL') . '/add-user-profile-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-profile-field/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-profile-field/edit-location-fields/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-profile-field/edit-tree/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-profile-field/edit-list/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user-profile-field/edit-list-item/',
					SJB_System::getSystemsettings('SITE_URL') . '/system/users/acl/',
				),
				'perm_label'	=>	array('manage_user_groups','manage_user_groups_permissions')
			),
		);
		$userGroups = SJB_UserGroupManager::getAllUserGroupsInfo();
		$manageUsers = array();
		foreach ($userGroups as $userGroups) {
			$userGroupId = mb_strtolower($userGroups['id'], 'utf8');
			if (in_array($userGroups['id'], array('JobSeeker', 'Employer'))) {
				$name = "{$userGroups['name']}s";
			}
			else {
				$name = "'{$userGroups['name']}' Users";
			}
			$link = 'manage-users/' . $userGroupId . '/';
			$manageUsers[] = array
			(
				'title' => "Manage {$name}",
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/'.$link,
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/edit-user/',
					SJB_System::getSystemsettings('SITE_URL') . '/add-user/',
					SJB_System::getSystemsettings('SITE_URL') . '/manage-users/',
					SJB_System::getSystemsettings('SITE_URL') . '/email-log/',
					SJB_System::getSystemsettings('SITE_URL') . '/user-products/',
					SJB_System::getSystemsettings('SITE_URL') . '/private-messages/pm-main/',
					SJB_System::getSystemsettings('SITE_URL') . '/private-messages/pm-inbox/',
					SJB_System::getSystemsettings('SITE_URL') . '/private-messages/pm-outbox/',
					SJB_System::getSystemsettings('SITE_URL') . '/system/applications/view/',
					SJB_System::getSystemsettings('SITE_URL') . '/system/users/acl/',
				),
				'perm_label'	=>	'manage_' . $userGroupId,
			);
		}
		$GLOBALS['LEFT_ADMIN_MENU']['Users'] = array_merge($userGroup, $manageUsers);
		$users = array
		(
			array
			(
				'title' => 'Import Users',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/import-users/',
				'highlight' => array(),
				'perm_label'	=>	'import_users',
			),
			array
			(
				'title' => 'Export Users',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/export-users/',
				'highlight' => array(),
				'perm_label'	=>	'export_users',
			),
			array
			(
				'title' => 'Mass Mailing',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/mailing/',
				'highlight' => array(),
				'perm_label'	=>	'create_and_send_mass_mailings',
			),
			array
			(
				'title' => 'Guest Email Alerts',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/guest-alerts/',
				'highlight' => array(),
				'perm_label'	=>	'manage_guest_email_alerts',
			),
			array
			(
				'title' => 'Banned IPs',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/banned-ips/',
				'perm_label'	=>	'manage_banned_ips',
			),
		);
		$GLOBALS['LEFT_ADMIN_MENU']['Users'] = array_merge($GLOBALS['LEFT_ADMIN_MENU']['Users'], $users);

		$GLOBALS['LEFT_ADMIN_MENU']['Layout and Content'] = array
		(
			array
			(
				'title' => 'Form Builder',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/form-builders/',
				'perm_label'	=>	'edit_form_builder',
			),
			array
			(
				'title' => 'System Templates',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/edit-templates/',
				'perm_label'	=>	'edit_templates_and_themes',
			),
			array
			(
				'title' => 'Email Templates',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/edit-email-templates/',
				'perm_label'	=>	'edit_templates_and_themes',
			),
			array
			(
				'title' => 'Themes',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/edit-themes/',
				'perm_label'	=>	'edit_templates_and_themes',
			),
			array
			(
				'title' => 'Site Pages',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/user-pages/',
				'perm_label'	=>	'manage_site_pages',
			),
			array
			(
				'title' => 'Static Content',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/stat-pages/',
				'perm_label'	=>	'manage_static_content',
			),
			array
			(
				'title' => 'Banners',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/manage-banner-groups/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL').'/add-banner-group/',
					SJB_System::getSystemsettings('SITE_URL').'/edit-banner-group/',
					SJB_System::getSystemsettings('SITE_URL').'/edit-banner/',
					SJB_System::getSystemsettings('SITE_URL').'/add-banner/',
				),
				'perm_label'	=>	'manage_banners',
			),
			array
			(
				'title' => 'News',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/news-categories/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL').'/manage-news/',
				),
				'perm_label'	=>	'manage_news',
			),
			array
			(
				'title' => 'Polls',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/manage-polls/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL').'/poll-answers/',
					SJB_System::getSystemsettings('SITE_URL').'/poll-results/',
				),
				'perm_label'	=>	'manage_polls',
			),
		);

		$GLOBALS['LEFT_ADMIN_MENU']['Billing'] = array
		(
			array
			(
				'title' => 'Invoices',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/manage-invoices/',
				'perm_label'	=>	'manage_invoices',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-invoice/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-invoice/',
				)
			),
			array
			(
				'title' => 'Products',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/products/',
				'highlight' => array(
					SJB_System::getSystemsettings('SITE_URL') . '/edit-product/',
					SJB_System::getSystemsettings('SITE_URL') . '/add-product/',
				),
				'perm_label'	=>	'manage_products',
			),
			array
			(
				'title' => 'Promotions',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/promotions/',
				'highlight' => array(
					SJB_System::getSystemsettings('SITE_URL') . '/add-promotion-code/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-promotion-code/',
					SJB_System::getSystemsettings('SITE_URL') . '/promotions/log/',
				),
				'perm_label'	=>	'manage_promotions',
			),
			array
			(
				'title' => 'Tax Rules',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/manage-taxes/',
				'highlight' => array(
					SJB_System::getSystemsettings('SITE_URL') . '/add-tax/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-tax/',
				),
				'perm_label' => 'manage_tax_rules',
			),
			array
			(
				'title' => 'Payment Gateways',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/system/payment/gateways/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL').'/configure-gateway/',
				),
				'perm_label'	=>	'manage_payment_gateways',
			),
			array
			(
				'title' => 'Transaction History',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/payments/',
				'perm_label'	=>	'transaction_history',
			),
			array
			(
				'title' => 'Payment Log',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/payment-log/',
				'perm_label'	=>	'payment_log',
			),
		);

		$GLOBALS['LEFT_ADMIN_MENU']['Reports'] = array
		(
			array
			(
				'title' => 'General Statistics',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/general-statistics/',
				'perm_label'	=>	'general_statistics',
			),
			array
			(
				'title' => 'Listings',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/listings-statistics/',
				'perm_label'	=>	'listings_reports',
			),
			array
			(
				'title' => 'Applications and Views',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/applications-and-views/',
				'perm_label'	=>	'applications_and_views_reports',
			),
			array
			(
				'title' => 'Sales',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/sales/',
				'perm_label'	=>	'sales_reports',
			),
			array
			(
				'title' => 'Guest Alerts',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/statistics/guest-alerts/',
				'perm_label'	=>	'guest_alerts_reports',
			),
			array
			(
				'title' => 'Promotions Usage',
				'reference' => SJB_System::getSystemsettings('SITE_URL').'/statistics/promotions/',
				'perm_label' =>	'promotions_statistics',
			),

		);

		$GLOBALS['LEFT_ADMIN_MENU']['System Configuration'] = array
		(
			array
			(
				'title' => 'System Settings',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/settings/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/alphabet-letters/',
					SJB_System::getSystemsettings('SITE_URL') . '/view-error-log/',
				),
				'perm_label' => 'configure_system_settings',
			),
			array
			(
				'title' => 'Social Media',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/social-media/',
				'perm_label' => 'social_media',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/social-media/bitly/',
					SJB_System::getSystemsettings('SITE_URL') . '/social-media/facebook',
					SJB_System::getSystemsettings('SITE_URL') . '/social-media/linkedin',
					SJB_System::getSystemsettings('SITE_URL') . '/social-media/twitter',
					SJB_System::getSystemsettings('SITE_URL') . '/social-media/googleplus',
				),
			),
			array
			(
				'title' => 'Admin Password',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/adminpswd/',
			),
			array
			(
				'title' => 'Admin Sub Accounts',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/manage-subadmins/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-subadmin/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-subadmin/',
				),
			),
			array
			(
				'title' => 'ZipCode Database',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/geographic-data/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/geographic-data/import-data/',
					SJB_System::getSystemsettings('SITE_URL') . '/geographic-data/edit-location/',
				),
				'perm_label' => 'edit_zipcode_database',
			),
			array
			(
				'title' => 'Countries',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/countries/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-country/',
					SJB_System::getSystemsettings('SITE_URL') . '/import-countries/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-country/',
				),
				'perm_label' => 'manage_countries',
			),
			array
			(
				'title' => 'States/Regions',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/states/',
				'highlight' => array(
					SJB_System::getSystemsettings('SITE_URL') . '/add-state/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-state/',
					SJB_System::getSystemsettings('SITE_URL') . '/import-states/',
				),
				'perm_label' => 'manage_states_or_regions',
			),
			array
			(
				'title' => 'Manage Currencies',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/currency-list/',
				'perm_label' => 'manage_currencies',
			),
			array
			(
				'title' => 'Refine Search Settings',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/refine-search-settings/',
				'perm_label' => 'set_refine_search_parameters',
			),
			array
			(
				'title' => 'Flag Listing Settings',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/flag-listing-settings/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/flag/',
					SJB_System::getSystemsettings('SITE_URL') . '/flag/',
				),
				'perm_label' => 'edit_flag_listing_settings',
			),
			array
			(
				'title' => 'Breadcrumbs Config',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/manage-breadcrumbs/',
				'perm_label' => 'configure_breadcrumbs',
			),
			array
			(
				'title' => 'HTML filters',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/filters/',
				'perm_label' => 'set_html_filters',
			),
			array
			(
				'title' => 'Task Scheduler',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/task-scheduler-settings/',
				'perm_label' => 'set_task_scheduler',
			),
			array
			(
				'title' => 'Plugins',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/system/miscellaneous/plugins/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/system/miscellaneous/fb_app_settings/',
				),
				'perm_label' => array('manage_plug-ins', 'set_phpbb_plug-in', 'set_facebook_plug-in',
									'set_linkedin_plug-in', 'set_twitter_plug-in', 'set_wordpress_plug-in',
									'set_sharethisplugin', 'set_captchaplugin', 'set_indeedplugin', 'set_jujuplugin',
									'set_simplyhiredplugin', 'set_googleplugin', 'set_googleplusplugin', 'set_googleanalyticsplugin',
									'set_beyondplugin'),
			),
			array
			(
				'title' => 'Backup/Restore',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/backup/',
				'perm_label' => 'create_and_restore_backups',
			),
			array
			(
				'title' => 'Email Log',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/email-log/',
				'perm_label' => 'email_log',
			),
		);

		$GLOBALS['LEFT_ADMIN_MENU']['Language Management'] = array
		(
			array
			(
				'title' => 'Manage Languages',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/manage-languages/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-language/',
					SJB_System::getSystemsettings('SITE_URL') . '/edit-language/',
				),
				'perm_label' => 'manage_languages',
			),
			array
			(
				'title' => 'Translate Phrases',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/manage-phrases/',
				'highlight' => array
				(
					SJB_System::getSystemsettings('SITE_URL') . '/add-phrase/',
				),
				'perm_label' => 'translate_phrases',
			),
			array
			(
				'title' => 'Import Language',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/import-language/',
				'perm_label' => 'import_languages',
			),
			array
			(
				'title' => 'Export Language',
				'reference' => SJB_System::getSystemsettings('SITE_URL') . '/export-language/',
				'perm_label' => 'export_languages',
			),
		);


		// set subadmin mode
		if (SJB_SubAdmin::getSubAdminSID()) {
			$GLOBALS['subadmin_id'] = SJB_SubAdmin::getSubAdminSID();
		}

	}
}
