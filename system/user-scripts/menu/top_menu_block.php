<?php

class SJB_Menu_TopMenu extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('listingTypesInfo', SJB_ListingTypeManager::getAllListingTypesInfo());
		$tp->display('top.tpl');
	}
}

