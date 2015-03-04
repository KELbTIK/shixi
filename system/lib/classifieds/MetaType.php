<?php

class MetaType
{
	public $meta_info;

	function __construct($meta_info)
    {
		$this->meta_info = $meta_info;
	}
	
	function display($display_type)
    {
		return $this->meta_info['value'];
	}
}

