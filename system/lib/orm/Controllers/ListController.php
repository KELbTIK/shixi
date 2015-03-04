<?php

class SJB_ListController
{
	var $field_sid = null;
	var $field = null;
	var $field_info = null;

	var $FieldManager = null;
	var $ListItemManager = null;

	function SJB_ListController($input_data, $FieldManager, $ListItemManager)
	{
		$this->FieldManager = $FieldManager;
		$this->ListItemManager = $ListItemManager;

		if (isset($input_data['field_sid']))
			$this->field_sid = $input_data['field_sid'];

		if (!is_null($this->field_sid))
			$this->field = $this->FieldManager->getFieldBySID($this->field_sid);

		if (!is_null($this->field)) {
			$this->field_info = $this->FieldManager->getFieldInfoBySID($this->field_sid);
		}

	}

	function isvalidFieldSID()
	{
		return !is_null($this->field_sid) && !is_null($this->field);
	}

}
