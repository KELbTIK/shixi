<?php

class SJB_DisplayForm extends SJB_ClassifiedsForm
{
	function SJB_DisplayForm($fields_info = null)
	{
		$this->display_type = 'display';
		parent::SJB_ClassifiedsForm($fields_info);
	}
}

