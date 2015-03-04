<?php

class SJB_FormBuilderManager
{
	/**
	 * @var SJB_FormBuilder
	 */
	private static $oFormBuilder;

	const FORM_BUILDER_TYPE_SEARCH = 'search';
	const FORM_BUILDER_TYPE_DISPLAY = 'display';
	const FORM_BUILDER_TYPE_PDF = 'pdf';

	/**
	 * @param string $mode
	 * @param string $listingTypeID
	 * @return SJB_FormBuilder|SJB_SearchFormFieldsBuilder|SJB_DisplayFormFieldsBuilder|SJB_DisplayFormFieldsBuilderForPDF|void
	 */
	public static function getFormBuilder($mode = '', $listingTypeID = '')
	{
		if (is_null(self::$oFormBuilder)) {
			switch ($mode) {
				case self::FORM_BUILDER_TYPE_SEARCH:
					$formBuilder = new SJB_SearchFormFieldsBuilder($listingTypeID);
					self::_setFormBuilder($formBuilder);
					break;

				case self::FORM_BUILDER_TYPE_PDF:
					$formBuilder = new SJB_DisplayFormFieldsBuilderForPDF($listingTypeID);
					self::_setFormBuilder($formBuilder);
					break;

				case self::FORM_BUILDER_TYPE_DISPLAY:
					$formBuilder = new SJB_DisplayFormFieldsBuilder($listingTypeID);
					self::_setFormBuilder($formBuilder);
					break;
			}
		}
		return self::$oFormBuilder;
	}

	/**
	 * @param $formBuilder SJB_FormBuilder
	 */
	private static function _setFormBuilder($formBuilder)
	{
		self::$oFormBuilder = $formBuilder;
	}

	/**
	 * <p> returns true if $_REQUEST['builder_mode] is set AND admin is authed </p>
	 * 
	 * @return boolean
	 */
	public static function getIfBuilderModeIsSet()
	{
		return isset($_REQUEST['builder_mode']) && (SJB_Admin::admin_authed() || SJB_SubAdmin::admin_authed());
	}

	public static function getIfAdminIsLoggedIn()
	{
		return (SJB_Admin::admin_authed() || SJB_SubAdmin::admin_authed());
	}

	/**
	 * @static
	 * @param int $fieldSID
	 * @return array|bool|int
	 */
	public static function deleteListingFieldBySidFromFieldsHolder($fieldSID)
	{
		return SJB_DB::query('DELETE FROM `formbuilders_fieldsholders` WHERE `field_sid` = ?n', $fieldSID);
	}

	public static function deleteFieldsByTheme($themeName)
	{
		return SJB_DB::query("DELETE FROM `formbuilders_fieldsholders` WHERE `theme` = ?s", $themeName);
	}

	public static function getFieldsByTheme($themeName)
	{
		return SJB_DB::query("SELECT * FROM `formbuilders_fieldsholders` WHERE `theme` = ?s", $themeName);
	}

	public static function insertField($themeName, $fieldsHolder)
	{
		return SJB_DB::query("INSERT INTO `formbuilders_fieldsholders` VALUES (?s, ?s, ?n, ?s, ?n, ?n, ?s)", $fieldsHolder['field_sid'], $fieldsHolder['fields_holder_id'],
			$fieldsHolder['order'], $fieldsHolder['html'], $fieldsHolder['complex'], $fieldsHolder['listing_type_sid'], $themeName);
	}

	/**
	 * @param SJB_FormBuilderData $builderData
	 * @return bool
	 * @throws Exception
	 */
	public static function save(SJB_FormBuilderData $builderData)
	{
		$listingTypeID = $builderData->getListingTypeID();
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
		if (!$listingTypeSID) {
			throw new Exception('Wrong listing type ID is specified');
		}

		$fieldsHolders = $builderData->getFieldsHoldersData();

		SJB_FieldsHolder::saveProcess($listingTypeSID, $fieldsHolders);

		if ($builderData->getType() == SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY) {
			self::saveLayout($listingTypeID, $builderData);
		}
		return true;
	}

	/**
	 * @param string $listingTypeID
	 * @param SJB_FormBuilderData $builderData
	 * @throws Exception
	 */
	private static function saveLayout($listingTypeID, SJB_FormBuilderData $builderData)
	{
		$result = SJB_DisplayFormFieldsBuilder::setDisplayLayout($listingTypeID, $builderData->getLayout());
		if (!$result) {
			throw new Exception('Error occured while saving builder layout');
		}
	}
}



