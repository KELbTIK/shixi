<?php

class SJB_UserCriteriaSaver extends SJB_CriteriaSaver
{
	function SJB_UserCriteriaSaver($searchId = 'UserSearcher')
	{
		$searchId = 'UserSearcher_'.$searchId;
		parent::SJB_CriteriaSaver($searchId, new SJB_UserManager);
	}
}
