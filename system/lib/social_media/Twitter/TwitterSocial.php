<?php

class SJB_TwitterSocial extends SJB_SocialMedia
{
	function SJB_TwitterSocial($info = array())
	{
		$this->db_table_name = 'twitter_feeds';
		$this->details = new SJB_TwitterSocialDetails($info);
		$this->common_fields = SJB_SocialMediaDetails::getCommonFields();
	}

	public static function getConnectSettings()
	{
		return array();
	}

	public function saveFeed($feed, $action = '')
	{
		parent::saveFeed($feed);
	}
}
