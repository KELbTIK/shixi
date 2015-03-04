<?php

class MetaString extends MetaType
{
	function display($display_type)
	{
		if ($display_type == 'input') {
			$disabled = empty($this->meta_info['disabled']) ? '' : 'disabled="disabled"';
			return "<input {$disabled} type=\"text\" name=\"{$this->meta_info['name']}\" value=\"{$this->meta_info['value']}\" />";
		}
		return $this->meta_info['value'];
	}
}
