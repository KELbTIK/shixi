<?php

class SJB_Classifieds_GetTree extends SJB_Function
{
	public function execute()
	{
		$treeType   = SJB_Request::getVar('userTree', '') ? 'user' : 'listing';
		$treeHelper = new SJB_TreeHelper($treeType);
		$treeHelper->init();
		
		if ($treeHelper->get_displayAsSelectBoxes()) {
			$treeHelper->displayAsSelectBoxes();
		} else {
			$treeHelper->displayAsTree();
		}
	}
}