<?php

class SJB_Admin_Classifieds_AddListingType extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_listing_types_and_specific_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$listing_type = new SJB_ListingType($_REQUEST);
		$add_listing_type_form = new SJB_Form($listing_type);
		$form_is_submitted = SJB_Request::getVar('action') == 'add';
		$errors = null;
		if ($form_is_submitted && $add_listing_type_form->isDataValid($errors)) {
			SJB_ListingTypeManager::saveListingType($listing_type);
			$this->createListingTypePage($listing_type);
			$listingTypeID = $listing_type->getID();
			if (in_array($listingTypeID, array('Job', 'Resume'))) {
				$title = $listing_type->getPropertyValue('name');
			} else {
				$title = $listing_type->getPropertyValue('name') . ' Listing';
			}
			$pageInfo = array('page_id' => 'Post' . $listingTypeID, 'page_name' => 'Post ' . $title);
			$page = new SJB_PostingPages($pageInfo, $listing_type->getSID());
			SJB_PostingPagesManager::savePage($page);
			SJB_PageManager::addPage(
				array (
					'uri'                       => '/' . strtolower($listingTypeID) . '-preview/',
					'module'                    => 'classifieds',
					'function'                  => 'display_my_listing',
					'template'                  => '',
					'title'                     => $title . ' Preview',
					'access_type'               => 'user',
					'parameters'                => array('display_template' => 'display_listing.tpl'),
					'keywords'                  => '',
					'description'               => '',
					'pass_parameters_via_uri'   => 1,
				)
			);
			SJB_PageManager::addPage(
				array (
					'uri'                       => '/my-' . strtolower($listingTypeID) . '-details/',
					'module'                    => 'classifieds',
					'function'                  => 'display_my_listing',
					'template'                  => '',
					'title'                     => 'My ' . $title . ' Details',
					'access_type'               => 'user',
					'parameters'                =>  array('display_template' => 'display_listing.tpl'),
					'keywords'                  => '',
					'description'               => '',
					'pass_parameters_via_uri'   => 1,
				)
			);
			SJB_PageManager::addPage(
				array (
					'uri'                       => '/print-my-' . strtolower($listingTypeID) . '/',
					'module'                    => 'classifieds',
					'function'                  => 'display_my_listing',
					'template'                  => 'blank.tpl',
					'title'                     => 'Print ' . $title,
					'access_type'               => 'user',
					'parameters'                =>  array('display_template' => 'print_listing.tpl', 'listing_type_id' => $listingTypeID),
					'keywords'                  => '',
					'description'               => '',
					'pass_parameters_via_uri'   => 0,
				)
			);
			SJB_PageManager::addPage(
				array (
					'uri'                       => '/print-' . strtolower($listingTypeID) . '/',
					'module'                    => 'classifieds',
					'function'                  => 'display_listing',
					'template'                  => 'blank.tpl',
					'title'                     => 'Print ' . $title,
					'access_type'               => 'user',
					'parameters'                => array('display_template' => 'print_listing.tpl', 'listing_type_id' => $listingTypeID),
					'keywords'                  => '',
					'description'               => '',
					'pass_parameters_via_uri'   => 0,
				)
			);
			SJB_PageManager::addPage(
				array (
					'uri'                       => '/manage-' . strtolower($listingTypeID) . '/',
					'module'                    => 'classifieds',
					'function'                  => 'manage_listing',
					'template'                  => '',
					'title'                     => 'Manage ' . $title,
					'access_type'               => 'user',
					'parameters'                => array(),
					'keywords'                  => '',
					'description'               => '',
					'pass_parameters_via_uri'   => 1,
				)
			);
			SJB_PageManager::addPage(
				array (
					'uri'                       => '/edit-' . strtolower($listingTypeID) . '/',
					'module'                    => 'classifieds',
					'function'                  => 'edit_listing',
					'template'                  => '',
					'title'                     => 'Edit ' . $title,
					'access_type'               => 'user',
					'parameters'                => '',
					'keywords'                  => '',
					'description'               => '',
					'pass_parameters_via_uri'   => 0,
				)
			);
			$breadCrumbs = new SJB_Breadcrumbs();
			$parentId = $breadCrumbs->getElementByUri('/my-listings/');
			$uri = '/my-listings/' . $listingTypeID . '/';
			$breadCrumbs->addElement('My ' . $title . 's', $uri, $parentId);
			$newBreadcrumbId = $breadCrumbs->getElementByUri($uri);
			$breadCrumbs->addElement('My ' . $title . ' Preview', '/my-' . strtolower($listingTypeID) . '-details/', $newBreadcrumbId);
			$breadCrumbs->addElement('Edit ' . $title, '/edit-' . strtolower($listingTypeID) . '/', $newBreadcrumbId);
			$breadCrumbs->addElement('Manage ' . $title, '/manage-' . strtolower($listingTypeID) . '/', $newBreadcrumbId);
			SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/listing-types/");
		} else {
			$template_processor = SJB_System::getTemplateProcessor();
			$template_processor->assign("errors", $errors);
			$add_listing_type_form->registerTags($template_processor);
			$template_processor->assign("form_fields", $add_listing_type_form->getFormFieldsInfo());
			$template_processor->display("add_listing_type.tpl");
		}
	}

	protected function createListingTypePage(SJB_ListingType $listingType)
	{
		$listingsPage = array(
			'uri'			=> '/manage-' . strtolower($listingType->getID()) . '-listings/',
			'module'		=> 'classifieds',
			'function'		=> 'manage_listings',
			'access_type'	=> 'admin',
			'parameters'	=> 'listing_type_sid=' . $listingType->getSID(),
		);
		$userPage = new SJB_UserPage();
		$pageData = SJB_UserPage::extractPageData($listingsPage);
		$userPage->setPageData($pageData);
		$userPage->save();
	}
}
