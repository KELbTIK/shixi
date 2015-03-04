<?php

class SJB_FieldsHolder
{

	/**
	 * @var string
	 */
	protected $fieldsHolderID;

	/**
	 * list of fields
	 * @var array
	 */
	protected $aFields = array();

	/**
	 * @var array
	 */
	protected static $_active_fields_sids = array();

	/**
	 * @var array
	 */
	protected static $_active_complex_fields = array();

	/**
	 * @var int
	 */
	protected static $listingTypeSID;

	/**
	 * @param string $fieldsHolderID
	 * @return SJB_FieldsHolder
	 */
	function __construct($fieldsHolderID)
	{
		$this->fieldsHolderID = $fieldsHolderID;
		$this->getFields();
	}

	/**
	 *
	 * @param mixed $fieldSID
	 * @param boolean $complex
	 */
	public function saveFieldAsActive($fieldSID, $complex = false)
	{
		if ($complex)
			array_push(self::$_active_complex_fields, $fieldSID);
		else
			array_push(self::$_active_fields_sids, $fieldSID);
	}

	public static function getActiveFieldsSIDs()
	{
		return self::$_active_fields_sids;
	}

	public static function getActiveComplexFields()
	{
		return self::$_active_complex_fields;
	}

	/**
	 *
	 * @return SJB_FieldsHolder::$aFields array
	 */
	public function getAFields()
	{
		return $this->aFields;
	}

	/**
	 * <p>sets values to array by it's key</p>
	 * <p>also removes complex field info from array
	 * if its not current complex field or complexfield is inactive</p>
	 *
	 * @param int $fieldOrder
	 * @param mixed $value
	 */
	public function setFieldValueByKey($builderType, $fieldOrder, $value, $complexSID = null)
	{
		// unset complex fields for 'search' so we can output this fields separatedly
		if ($builderType === SJB_FormBuilderManager::FORM_BUILDER_TYPE_SEARCH && 'complex' == $value['type']) {
			foreach ($value['fields'] as $key => $complexFieldInfo) {
				if (($complexSID && $complexSID != $complexFieldInfo['sid']) || !in_array($complexFieldInfo['sid'], self::$_active_complex_fields))
					unset($value['fields'][$key]);
			}
		}
		$this->aFields[$fieldOrder] = $value;
	}

	/**
	 * retrieve fields from database
	 */
	protected function getFields()
	{
		$result = SJB_DB::query('SELECT `field_sid` as `b_field_sid`, `order` as `b_order`,`html`,`complex`,`fields_holder_id`
				FROM `formbuilders_fieldsholders`
				WHERE `fields_holder_id` = ?s AND `listing_type_sid` = ?n AND `theme` = ?s ORDER BY `b_order` ASC',
				$this->fieldsHolderID, self::getListingTypeSID(), SJB_TemplateSupplier::getUserCurrentTheme());
		if ($result) {
			foreach ($result as $aFieldInfo) {
				$fieldInfo = SJB_ListingFieldManager::getFieldInfoBySID($aFieldInfo['b_field_sid']);
				if (!$fieldInfo['hidden'] && !SJB_Users_CookiePreferences::isFieldDisabled($fieldInfo['type'])) {
					$this->aFields[$aFieldInfo['b_order']] = $aFieldInfo;
				}
			}
		}
	}

	public function getFieldsHolderID()
	{
		return $this->fieldsHolderID;
	}

	public function setFieldsHolderID($fieldsHolderID)
	{
		$this->fieldsHolderID = $fieldsHolderID;
	}

	public static function saveOrder($listingTypeSID, $order, $field_sid, $fieldsHolderID, $html = null, $parentSID = null)
	{
		return SJB_DB::query('INSERT INTO `formbuilders_fieldsholders`
					SET `order` = ?n, `field_sid` = ?s, `fields_holder_id` = ?s, `html` = ?s, `complex` = ?n,
						`listing_type_sid` = ?n, `theme` = ?s
					 ON DUPLICATE KEY UPDATE `order` = ?n, `html` = ?s',
			$order, $field_sid, $fieldsHolderID, $html, $parentSID, $listingTypeSID, SJB_TemplateSupplier::getUserCurrentTheme(), $order, $html);
	}

	/**
	 * delete records in database table with such parameters (fieldsHolderID, ListingTypeID)
	 *
	 * @static
	 * @param string $fieldsHolderID
	 * @param int $listingTypeSID
	 * @return array|bool|int
	 */
	public static function cleanOrder($fieldsHolderID, $listingTypeSID)
	{
		return SJB_DB::query('DELETE FROM `formbuilders_fieldsholders` WHERE `fields_holder_id` = ?s
			AND `theme` = ?s AND `listing_type_sid` = ?n',
			$fieldsHolderID, SJB_TemplateSupplier::getUserCurrentTheme(), $listingTypeSID);
	}

	/**
	 * retrieve fields sids from database formbuilder table
	 * @return array|bool|int
	 */
	public function getOrderedFieldsSIDs()
	{
		return SJB_DB::query('SELECT `field_sid` FROM `formbuilders_fieldsholders`
			WHERE `fields_holder_id` = ?s AND `listing_type_sid` = ?n AND `theme` = ?s ORDER BY `order` ASC',
			$this->fieldsHolderID, self::getListingTypeSID(), SJB_TemplateSupplier::getUserCurrentTheme());
	}

	/**
	 * @param int $listingTypeSID
	 * @param SJB_FieldsHolderData[] $fieldsHoldersData
	 * @throws Exception
	 */
	public static function saveProcess($listingTypeSID, $fieldsHoldersData)
	{
		$tp = SJB_System::getTemplateProcessor();

		/** @var $fieldsHoldersData SJB_FieldsHolderData[] */
		foreach ($fieldsHoldersData as $fieldsHolderID => $fieldsHolderData) {
			self::cleanOrder($fieldsHolderID, $listingTypeSID);
			foreach ($fieldsHolderData->getFields() as $order => $fieldSid) {
				if (!empty($fieldSid)) {
					$htmlBlockValue = $fieldsHolderData->getFieldHtmlValue($fieldSid);
					$parentSID		= $fieldsHolderData->getComplexField($fieldSid);
					if (!empty($htmlBlockValue)) { // check html block syntax
						$code = preg_replace('/{([\w\d.]+)}/', '{\$${1}}', $htmlBlockValue);
						$html =  $tp->fetch('eval:' . $code);
						if (stripos($html, 'Fatal error') != false) {
							throw new Exception('Wrong html content for HtmlBlock');
						}
					}
					if ($parentSID) {
						$result = self::saveOrder($listingTypeSID, $order, $parentSID, $fieldsHolderID, $htmlBlockValue, $fieldSid);
					} else {
						$result = self::saveOrder($listingTypeSID, $order, $fieldSid, $fieldsHolderID, $htmlBlockValue);
					}
					if (!$result) {
						throw new Exception('Error occured while saving field');
					}
				}
			}
		}
	}

	/**
	 * @param int $listingTypeSID
	 */
	public static function setListingTypeSID($listingTypeSID)
	{
		self::$listingTypeSID = $listingTypeSID;
	}

	/**
	 * @return int
	 */
	public static function getListingTypeSID()
	{
		return self::$listingTypeSID;
	}
}


