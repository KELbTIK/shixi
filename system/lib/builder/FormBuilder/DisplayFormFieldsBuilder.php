<?php

class SJB_DisplayFormFieldsBuilder extends SJB_FormBuilder
{
	/**
	 * @var string
	 */
	protected $formFieldSetTemplate = 'bf_displaylisting_fieldset.tpl';

	/**
	 * @var string
	 */
	protected $builderType = SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY;

	function __construct($listingTypeID)
	{
		$this->_unsearchableFieldTypes = array();
		$this->_customFields = array('id', 'views', 'posted');
		switch ($listingTypeID) {
			case 'Resume':
				array_push($this->_customFields, 'desiredSalary');
				break;
			case 'Job':
				array_push($this->_customFields, 'customSalary');
				break;
			default:
				break;
		}
		array_push($this->_unsearchableFieldIDs, 'anonymous');
		parent::__construct($this->builderType, $listingTypeID);
	}

	/**
	 * get display layout propterty name as it is saved in system
	 * @static
	 * @param $listingTypeID
	 * @return string
	 */
	public static function getDisplayLayoutNamePart($listingTypeID)
	{
		return 'display_layout_' . $listingTypeID . '_' . SJB_TemplateSupplier::getUserCurrentTheme();
	}

	/**
	 * save display layout property value in system
	 * @static
	 * @param $listingTypeID
	 * @param $layoutID
	 * @return array|bool|int
	 */
	public static function setDisplayLayout($listingTypeID, $layoutID)
	{
		$name = SJB_DisplayFormFieldsBuilder::getDisplayLayoutNamePart($listingTypeID);
		return SJB_Settings::saveSetting($name, $layoutID);
	}

	/**
	 * retrieve display layout property for builder
	 * returns from $_GET array if value exists
	 * else returns saved value from system
	 *
	 * @return bool|mixed|null
	 */
	public function getDisplayLayout()
	{
		$layoutID = SJB_Request::getVar('builder-layout', null, 'GET');
		if ($layoutID)
			return $layoutID;
		return SJB_Settings::getSettingByName(SJB_DisplayFormFieldsBuilder::getDisplayLayoutNamePart($this->listingTypeID));
	}

	/**
	 * @param SJB_TemplateProcessor $tp
	 */
	public function setChargedTemplateProcessor($tp)
	{
		$tp->assign('display_layout', $this->getDisplayLayout($this->listingTypeID));
		parent::setChargedTemplateProcessor($tp);
	}

}



