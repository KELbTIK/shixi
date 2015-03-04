<?php

class SJB_FormBuilder
{
	/**
	 * @var string
	 */
	protected $formFieldSetTemplate;

	/**
	 * @var array
	 */
	protected static $aFieldsHolderIDs = array();

	/**
	 * Field types that must not be searchable
	 * @var array
	 */
	protected $_unsearchableFieldTypes = array('text', 'youtube');

	/**
	 * Field IDs that must not be searchable
	 * @var array
	 */
	protected $_unsearchableFieldIDs = array('access_type', 'ApplicationSettings', 'screening_questionnaire', 'expiration_date', 'ListingLogo');

	/**
	 *
	 * @var array $_customFields
	 */
	protected $_customFields = array();

	/**
	 * @var array
	 */
	protected $fieldsHolders = array();

	/**
	 * @var SJB_FieldsHolder
	 */
	protected $_currentFieldsHolder;

	/**
	 * @var string
	 */
	protected $_currentFieldsHolderID = null;

	/**
	 * @var string
	 */
	protected $listingTypeID;

	/**
	 * @var string
	 */
	protected $builderType;
	/**
	 * @var SJB_TemplateProcessor
	 */
	protected $_chargedTemplateProcessor;

	protected function __construct($type, $listingTypeID)
	{
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($listingTypeID);
		if ($listingTypeSID) {
			$this->setListingTypeID($listingTypeID);
			SJB_FieldsHolder::setListingTypeSID($listingTypeSID);
		}
	}

	public function prepareFieldsHolder($fieldsHolderID)
	{
		$fieldsHolderID = (string) $fieldsHolderID;
		if (!$fieldsHolderID) {
			return false;
		}

		if (!isset($this->fieldsHolders[$fieldsHolderID])) {

			$fieldsHolder = new SJB_FieldsHolder($fieldsHolderID);

			$this->fieldsHolders[$fieldsHolderID] = $fieldsHolder;
			$this->setCurrentFieldsHolder($fieldsHolderID);

			foreach ($fieldsHolder->getAFields() as $fieldOrder => $fieldInfo_) {

				$fieldSID	= $fieldInfo_['b_field_sid'];
				$fieldInfo	= SJB_ListingFieldManager::getFieldInfoBySID($fieldSID);

				if ($this->fieldCanBeShown($fieldInfo)) {
					if (!empty($fieldInfo_['complex']))
						$fieldsHolder->saveFieldAsActive($fieldInfo_['complex'], $fieldSID);
					else
						$fieldsHolder->saveFieldAsActive($fieldSID);
					$fieldsHolder->setFieldValueByKey($this->getBuilderType(), $fieldOrder, array_merge($fieldInfo_, $fieldInfo), $fieldInfo_['complex']);
				}
				else {
					if (!empty($fieldInfo_['html'])) {
						$fieldsHolder->setFieldValueByKey($this->getBuilderType(), $fieldOrder, $fieldInfo_);
					}
					/* check if this field is custom field */
					if (in_array($fieldSID, $this->_customFields) || substr($fieldSID, 0, 9) == 'htmlBlock') {
						$fieldsHolder->saveFieldAsActive($fieldSID);
					}
				}
			}
		}

		$this->setCurrentFieldsHolder($fieldsHolderID);
	}

	/**
	 * @param $fieldInfo
	 * @return bool
	 */
	protected function fieldCanBeShown($fieldInfo)
	{
		return !empty($fieldInfo) && !in_array($fieldInfo['type'], $this->_unsearchableFieldTypes) && !in_array($fieldInfo['id'], $this->_unsearchableFieldIDs);
	}

	/**
	 * @param string $listingTypeID
	 */
	public function setListingTypeID($listingTypeID)
	{
		$this->listingTypeID = $listingTypeID;
	}


	/**
	 *
	 * @return string
	 */
	public function getListingTypeID()
	{
		return $this->listingTypeID;
	}


	/**
	 *
	 * @return int
	 */
	public function getCurrentFieldsHolderID()
	{
		return $this->_currentFieldsHolderID;
	}


	/**
	 * get fieldsholder instance
	 *
	 * @return SJB_FieldsHolder
	 */
	public function getCurrentFieldsHolder()
	{
		return $this->_currentFieldsHolder;
	}


	/**
	 * @param string $fieldsHolderID
	 */
	public function setCurrentFieldsHolder($fieldsHolderID)
	{
		$this->_currentFieldsHolder = $this->fieldsHolders[$fieldsHolderID];
		$this->_currentFieldsHolderID = $fieldsHolderID;
	}


	/**
	 * @return SJB_FieldsHolder::$_active_fields_sids array
	 */
	public static function get_active_fields_sids()
	{
		return SJB_FieldsHolder::getActiveFieldsSIDs();
	}

	/**
	 * @return array
	 */
	public function getActiveFields()
	{
		$fieldsHolderFields = $this->_currentFieldsHolder->getAFields();
		$activeFields = $this->_currentFieldsHolder->getActiveFieldsSIDs();
		$activeComplexFields = $this->_currentFieldsHolder->getActiveComplexFields();
		foreach ($fieldsHolderFields as $key => $field) {
			$complexSID = SJB_Array::get($field, 'complex');
			if ($field['type'] == 'complex' && $complexSID) {
				$isNotInActiveFieldsList = !in_array(SJB_Array::get($field, 'complex'), $activeComplexFields);
			} else {
				$isNotInActiveFieldsList = !in_array(SJB_Array::get($field, 'b_field_sid'), $activeFields);
			}
			if ($isNotInActiveFieldsList) {
				unset($fieldsHolderFields[$key]);
			}
		}
		return $fieldsHolderFields;
	}


	/**
	 * @param int $listingTypeSID
	 * @return array
	 */
	public function getInactiveFields()
	{
		$listingTypeSID = SJB_ListingTypeManager::getListingTypeSIDByID($this->getListingTypeID());
		$aFields = array();

		if ($listingTypeSID) {
			$aFields = SJB_ListingFieldManager::getListingFieldsInfoByListingType($listingTypeSID);
			$aFields = array_merge(SJB_ListingFieldManager::getCommonListingFieldsInfo(), $aFields);
			$this->addCustomFields($aFields);
			self::deleteActiveAndUnsearchableFields($aFields);
		}
		return $aFields;
	}

	/**
	 * <p>push custom fields into fields array</p>
	 *
	 * @param array $aFields
	 */
	public function addCustomFields(&$aFields)
	{
		foreach	($this->_customFields as $fieldID) {
			array_push ($aFields, array('id' => $fieldID, 'sid' => $fieldID, 'type' => $fieldID));
		}
	}



	/**
	 * <p>unset fields that must not be in search form</p>
	 * <p>unset fields that are active</p>
	 *
	 * @param array $aFields
	 */
	public function deleteActiveAndUnsearchableFields(&$aFields)
	{
		$activeFieldsSIDs = SJB_FieldsHolder::getActiveFieldsSIDs();

		switch ($this->getBuilderType()) {
			case SJB_FormBuilderManager::FORM_BUILDER_TYPE_SEARCH:
				$this->deleteActiveAndUnsearchableComplexFields($aFields, $activeFieldsSIDs);
				break;

			case SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY:
				foreach ($aFields as $key => $aField) {
					if ($aField['type'] == 'location') {
						$fields = !empty($aField['fields'])?$aField['fields']:array();
						foreach ($fields as $field) {
							if ($field['id'] == 'ZipCode') 
								$aFields[] = $field;
						}
					}
				}
			default:
				foreach ($aFields as $key => $aField) {
					if (in_array($aField['type'], $this->_unsearchableFieldTypes) || in_array($aField['id'], $this->_unsearchableFieldIDs)) {
						unset($aFields[$key]);
					}
					elseif (!empty($activeFieldsSIDs)) {
						if (in_array($aField['sid'], $activeFieldsSIDs))
							unset($aFields[$key]);
					}
				}
				break;
		}

	}

	public function deleteActiveAndUnsearchableComplexFields(&$aFields, $activeFieldsSIDs)
	{
		$activeComplexFields = SJB_FieldsHolder::getActiveComplexFields();

		foreach ($aFields as $key => $aField) {
			if (in_array($aField['type'], $this->_unsearchableFieldTypes) || in_array($aField['id'], $this->_unsearchableFieldIDs)) {
				unset($aFields[$key]);
			} elseif (!empty($activeFieldsSIDs)) {
				if (in_array($aField['sid'], $activeFieldsSIDs)) {
					if ($aField['type'] == 'location')	{
						$aFields[$key]['used'] = 1;
					} else {
						unset($aFields[$key]);
					}
				}
			}
			if ($aField['type'] == 'complex') {
				foreach($aField['fields'] as $fieldKey => $complexFieldInfo) {
					if (in_array($complexFieldInfo['sid'], $activeComplexFields))
						unset($aFields[$key]['fields'][$fieldKey]);
				}
			} elseif ($aField['type'] == 'location') {
				foreach($aField['fields'] as $fieldKey => $locationFieldInfo) {
					if (in_array($locationFieldInfo['sid'], $activeFieldsSIDs) || $locationFieldInfo['hidden'] == 1 || ($aField['id'] != 'Location' && $locationFieldInfo['id'] == 'ZipCode')) {
						unset($aFields[$key]['fields'][$fieldKey]);
					}
				}
			}
		}
	}

	/**
	 *
	 * @param SJB_TemplateProcessor $tp
	 */
	public function setChargedTemplateProcessor($tp)
	{
		$this->_chargedTemplateProcessor = $tp;
	}



	/**
	 * get charged template processor
	 *
	 * @return SJB_TemplateProcessor
	 */
	public function getChargedTemplateProcessor()
	{
		if (is_null($this->_chargedTemplateProcessor))
			$this->createChargedTemplateProcessor();

		return $this->_chargedTemplateProcessor;
	}


	/**
	 * @return SJB_TemplateProcessor
	 */
	public function createChargedTemplateProcessor()
	{
		return SJB_System::getTemplateProcessor();
	}

	/**
	 * @param string $builderType
	 */
	public function setBuilderType($builderType)
	{
		$this->builderType = $builderType;
	}

	/**
	 * @return string
	 */
	public function getBuilderType()
	{
		return $this->builderType;
	}

	/**
	 * @return string
	 */
	public function getFormFieldSetTemplate()
	{
		return $this->formFieldSetTemplate;
	}
}



