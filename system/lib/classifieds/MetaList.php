<?php

class MetaList extends MetaType
{
	function display($display_type)
	{
		if ($display_type == 'input') {
			$html = "<select name=\"{$this->meta_info['name']}\">";
			foreach ($this->meta_info['available_values'] as $value) {
				$value = htmlspecialchars($value);
				if ($value == $this->meta_info['value']) {
					$html .= "<option value=\"{$value}\" selected=\"selected\">{$value}</option>";
				} else {
					$html .= "<option value=\"{$value}\">{$value}</option>";
				}
			}
			return $html . '</select>';
		}
		return $this->meta_info['value'];
	}
}
