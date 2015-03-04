<?php

class MetaText extends MetaType
{
	function display($display_type)
	{
		if ($display_type == 'input') {
			return "<textarea name=\"{$this->meta_info['name']}\">{$this->meta_info['value']}</textarea>";
		}
		return $this->meta_info['value'];
	}
}

