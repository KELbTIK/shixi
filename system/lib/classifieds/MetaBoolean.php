<?php

class MetaBoolean extends MetaType
{
	function display($display_type)
    {
		if ($display_type == 'input') {
			$checked = $this->meta_info['value'];
			$disabled = empty($this->meta_info['disabled']) ? '' : 'disabled="disabled"';
			if ($disabled) {
				$value = empty($this->meta_info['value']) ? 0 : $this->meta_info['value'];
			}
			else {
				$value = 0;
			}
			return "<input type=\"hidden\" name=\"{$this->meta_info['name']}\" value=\"{$value}\" />"
					. "<input type=\"checkbox\" name=\"{$this->meta_info['name']}\" value=\"1\" {$disabled}" . ($checked ? ' checked="checked"' : '') . ' />';
		}
		return $this->meta_info['value'] ? 'yes' : 'no';
	}
}