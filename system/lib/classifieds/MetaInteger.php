<?php

class MetaInteger extends MetaType
{
	function display($display_type)
	{
		if ($display_type == 'input') {
			return "<input type=\"text\" name=\"{$this->meta_info['name']}\" value=\"{$this->meta_info['value']}\">";
		}
		return $this->meta_info['value'];
	}
}
