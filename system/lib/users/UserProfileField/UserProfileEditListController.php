<?php

class SJB_UserProfileEditListController extends SJB_EditListController
{
	function SJB_UserProfileEditListController($input_data)
	{
		parent::SJB_EditListController($input_data, new SJB_UserProfileFieldManager, new SJB_UserProfileFieldListItemManager);
	}
}
