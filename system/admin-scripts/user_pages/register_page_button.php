<?php

class SJB_Admin_UserPages_RegisterPageButton extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_site_pages');
		return parent::isAccessible();
	}

	public function execute()
	{
		if (isset($_REQUEST['pageInfo'])) {
			$_REQUEST['pageInfo']['parameters'] = $this->array2String($_REQUEST['pageInfo']['parameters']);
			$template_processor = SJB_System::getTemplateProcessor();
			$template_processor->assign('pageInfo', $_REQUEST['pageInfo']);
			$template_processor->assign('caption', $_REQUEST['caption']);
			$template_processor->display('register_page_button.tpl');
		}
	}

	private function array2String($params)
	{
		if (empty ($params))
			return false;
		$result = false;
		foreach ($params as $key => $value)
			$result .= $key.'='.$value."\r\n";
		return substr ($result, 0, strlen($result) - 2);
	}
}

