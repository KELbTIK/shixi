<?php

class SJB_Admin_Banners_ManageBannerGroups extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_banners');
		return parent::isAccessible();
	}

	public function execute()
	{
		$bannersObj = new SJB_Banners();
		$errors = array();
		$tp = SJB_System::getTemplateProcessor();

		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$params = $_REQUEST;

			switch ($action_name) {
				case 'delete_banner_group':

					$result = $bannersObj->deleteBannerGroup($params['groupSID']);

					if ($result === false) {
						$errors[] = 'ERROR_DELETING_BANNER_GROUP';
					}
					break;
			}

			$site_url = SJB_System::getSystemsettings('SITE_URL') . '/manage-banner-groups/';
			header("Location: {$site_url}");
		}

		$bannerGroups = $bannersObj->getAllBannerGroups();

		$tp->assign('errors', $errors);
		$tp->assign('bannerGroups', $bannerGroups);

		$tp->display('manage_banner_groups.tpl');

	}
}
