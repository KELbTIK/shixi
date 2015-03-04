<?php

class SJB_YouTubeType extends SJB_Type
{
	function SJB_YouTubeType($property_info)
	{
		parent::SJB_Type($property_info);
		
		if (isset($this->property_info['value']))
			$this->property_info['value'] = strip_tags($this->property_info['value']);
			
		$this->default_template = 'youtube.tpl';
	}

	function getPropertyVariablesToAssign()
	{
		return array(	'id' 	=> $this->property_info['id'],
						'value'	=> $this->property_info['value'],
					);
	}

	function isValid()
	{
		$preg = preg_match('|^https?://www\.youtube\.com/watch\?v=|u', $this->property_info['value']);
		if ($preg) {
			return true;
		}
		return 'NOT_CORRECT_YOUTUBE_LINK';
	}

	public static function getFieldExtraDetails()
	{
		return array();
	}

	function getSQLValue()
	{
		return $this->property_info['value'];
	}

    function getKeywordValue()
    {
		return "";
	}

}
