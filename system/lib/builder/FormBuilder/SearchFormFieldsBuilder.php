<?php

class SJB_SearchFormFieldsBuilder extends SJB_FormBuilder
{
	protected $formFieldSetTemplate = 'bf_searchform_fieldset.tpl';

	/**
	 * @var string
	 */
	protected $builderType = SJB_FormBuilderManager::FORM_BUILDER_TYPE_SEARCH;

	function __construct($listingTypeID)
	{
		$this->_customFields = array('keywords', 'PostedWithin');
		parent::__construct(SJB_FormBuilderManager::FORM_BUILDER_TYPE_SEARCH, $listingTypeID);
	}
}
