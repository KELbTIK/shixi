<?php
class SJB_Classifieds_SelectPostingType extends SJB_Function
{
	public function execute()
	{
		$listingTypeID = SJB_Request::getVar('listing_type', 'Job');

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('listing_type', $listingTypeID);
		$tp->display('select_posting_type.tpl');
	}
}