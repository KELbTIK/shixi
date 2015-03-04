<?php
class SJB_Miscellaneous_BlogPage extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$tp->display('blog_page.tpl');
	}
}