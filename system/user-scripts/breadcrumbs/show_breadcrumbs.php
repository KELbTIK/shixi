<?php

class SJB_Breadcrumbs_ShowBreadcrumbs extends SJB_Function
{
	public function execute()
	{
		$breadCrumbs = new SJB_Breadcrumbs();

		$navArray = $breadCrumbs->getBreadcrumbs();

		$tp = SJB_System::getTemplateProcessor();

		$tp->assign ('navArray', $navArray);
		$tp->assign ('navCount', count($navArray));
		$tp->display ('show_breadcrumbs.tpl');
	}
}

