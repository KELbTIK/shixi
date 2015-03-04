<?php

class SJB_ObjectMother
{
	public static function createListingSearcher()
	{
		return new SJB_ListingSearcher();
	}
	
	/**
	 * @return SJB_ListingCriteriaSaver
	 */
	public static function createListingCriteriaSaver()
	{
		return new SJB_ListingCriteriaSaver();
	}
	
	/**
	 * 
	 * @param $user_info
	 * @param $user_group_sid
	 * @return SJB_User
	 */
	public static function createUser($user_info = array(), $user_group_sid = 0)
	{
		return new SJB_User($user_info, $user_group_sid);
	}

	/**
	 * create object of SJB_SubAdmin class
	 * 
	 * @param array $adminInfo
	 * @return SJB_SubAdminProp
	 */
	public static function createSubAdmin( $adminInfo )
	{
		return new SJB_SubAdminProp($adminInfo);
	}
	
	/**
	 * 
	 * @param unknown_type $object
	 * @return SJB_Form
	 */
	public static function createForm($object = null)
	{
		return new SJB_Form($object);
	}
	
	/**
	 * Create listing object
	 *
	 * @param array $listing_info
	 * @param int $listing_type_sid
	 * @return SJB_Listing
	 */
	public static function createListing($listing_info = array(), $listing_type_sid = 0)
	{
		return new SJB_Listing($listing_info, $listing_type_sid);
	}
	
	public static function createListingGallery()
	{
		return new SJB_ListingGallery();
	}
	
	public static function createParamProvider($schema)
	{
		return new SJB_UrlParamProvider($schema);
	}
	
	public static function createCategorySearcherFactory()
	{
		return new SJB_CategorySearcherFactory();
	}

	public static function create_CategorySearcher_Tree($field)
	{
		return new SJB_CategorySearcher_Tree($field);
	}
	
	public static function create_CategorySearcher_Value($field)
	{
		return new SJB_CategorySearcher_Value($field);
	}
	
	public static function create_CategorySearcher_List($field)
	{
		return new SJB_CategorySearcher_List($field);
	}
	
	public static function create_CategorySearcher_Multilist($field)
	{
		return new SJB_CategorySearcher_MultiList($field);
	}

	/**
	 * @static
	 * @param string $listingTypeId
	 * @param array  $parameters
	 * @return SJB_BrowseManager
	 */
	public static function createBrowseManager($listingTypeId, array $parameters)
	{
		return new SJB_BrowseManager($listingTypeId, $parameters);
	}
	
	public static function createListingFieldListItemManager()
	{
		return new SJB_ListingFieldListItemManager();
	}
	
	public static function createContactForm()
	{
		return new SJB_ContactForm();
	}
	
	public static function createTemplateEditor()
	{
		return new SJB_TemplateEditor();
	}
	
	/**
	 * @return SJB_I18N
	 */
	public static function createI18N()
	{
		return SJB_I18N::getInstance();
	}	
	
	public static function getMetaDataProvider()
	{
		return new SJB_MetaDataProvider();
	}	
	
	public static function createFileSystem()
	{
		return new SJB_Filesystem();
	}

	public static function createHTMLTagConverterInArray()
	{
		if (empty($GLOBALS['ObjectMother_instances_HtmlTagConverterInArray'])) {
			$converter = null;
			if (SJB_Settings::getSettingByName('escape_html_tags') === 'htmlentities' && SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE') != 'admin')
				$converter = new SJB_HTMLTagConverter();
			else
				$converter = new SJB_NullConverter();
			$GLOBALS['ObjectMother_instances_HtmlTagConverterInArray'] = new SJB_StructureExplorer(array($converter, 'getConverted'));
		}
		return $GLOBALS['ObjectMother_instances_HtmlTagConverterInArray'];
	}

}
