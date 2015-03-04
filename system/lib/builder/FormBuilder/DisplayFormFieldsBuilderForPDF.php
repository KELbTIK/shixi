<?php

class SJB_DisplayFormFieldsBuilderForPDF extends SJB_DisplayFormFieldsBuilder
{
	/**
	 * @var string
	 */
	protected $formFieldSetTemplate = 'bf_displaylisting_fieldset_for_pdf.tpl';

	/**
	 * @var string
	 */
	protected $builderType = SJB_FormBuilderManager::FORM_BUILDER_TYPE_PDF;

}
