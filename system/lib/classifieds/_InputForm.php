<?php

class SJB_InputForm extends SJB_ClassifiedsForm
{
	function SJB_InputForm($fields_info = null)
	{
		$this->display_type = 'input';
		parent::SJB_ClassifiedsForm($fields_info);
	}
}