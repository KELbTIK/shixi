<?php

class SJB_Admin_Banners_AddBannerGroup extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_banners');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$bannersObj = new SJB_Banners();
		$action = SJB_Request::getVar('action');
		
		if (isset($action)) {
			$groupID = SJB_Request::getVar('groupID');
			switch ($action) {
				case 'add':
					if ($groupID == '') {
						SJB_FlashMessages::getInstance()->addWarning('EMPTY_VALUE', array('fieldCaption' => 'Group ID'));
						break;
					}
					
					$result = $bannersObj->addBannerGroup($groupID);
					if ($result === false) {
						SJB_FlashMessages::getInstance()->addWarning('ERROR_ADD_BANNER_GROUP');
						break;
					}
					
					$site_url = SJB_System::getSystemsettings('SITE_URL') . "/manage-banner-groups/";
					header("Location: {$site_url}");
					break;
			}
		}
		
		$tp->display("add_banner_group.tpl");
	}
}
