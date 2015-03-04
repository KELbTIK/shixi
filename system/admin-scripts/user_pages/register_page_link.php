<?php

class SJB_Admin_UserPages_RegisterPageLink extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel(array('manage_site_pages', 'register_page_link'));
		return parent::isAccessible();
	}

	public function execute()
	{
		if (isset($_REQUEST['pageInfo'])) {
			$_REQUEST['pageInfo']['parameters'] = $this->array2String($_REQUEST['pageInfo']['parameters']);
			$template_processor = SJB_System::getTemplateProcessor();
			$template_processor->assign('pageInfo', $_REQUEST['pageInfo']);
			$template_processor->assign('caption', $_REQUEST['caption']);
			$template_processor->display('register_page_link.tpl');
		}
	}

	private function array2String($params) {
		if (empty($params) || !is_array($params))
			return false;
		$result = '';
		foreach ($params as $key => $value)
			$result .= $key . '=' . $value . "&";
		return rtrim($result, '&');
	}
}

