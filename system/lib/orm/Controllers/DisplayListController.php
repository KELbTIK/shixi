<?php

class SJB_DisplayListController extends SJB_ListController
{
	var $list_items			= null;
	var $templateProcessor = null;

	function SJB_DisplayListController($input_data, $FieldManager, $ListItemManager)
	{
		parent::SJB_ListController($input_data, $FieldManager, $ListItemManager);
		$this->list_items = $this->ListItemManager->getHashedListItemsByFieldSID($this->field_sid);
	}

	/**
	 * 
	 * @param $templateProcessor SJB_TemplateProcessor
	 */
	function setTemplateProcessor($templateProcessor)
	{
		$this->templateProcessor = $templateProcessor;
	}
	
	
	function display($template)
	{
		if ($this->templateProcessor === null)
			$template_processor = SJB_System::getTemplateprocessor();
		else
			$template_processor = $this->templateProcessor;

		$template_processor->assign("field_sid", $this->field_sid);
		$template_processor->assign("list_items", $this->list_items);
		$template_processor->assign("field_info", $this->field_info);
		$template_processor->assign("type_sid", $this->_getTypeSID());
		$template_processor->assign("type_info", $this->_getTypeInfo());

		$template_processor->display($template);
	}

	function _getTypeInfo() {}

	function _getTypeSID() {}

}
