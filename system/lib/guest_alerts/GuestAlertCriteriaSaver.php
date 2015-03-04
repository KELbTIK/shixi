<?php

class SJB_GuestAlertCriteriaSaver extends SJB_CriteriaSaver
{
	function __construct($searchId = 'GuestAlertSearcher')
	{
		$searchId = 'GuestAlertSearcher_'.$searchId;
		parent::SJB_CriteriaSaver($searchId, new SJB_GuestAlertManager());
	}
}
