<?php

class SJB_Banners_ShowBanners extends SJB_Function
{
	public function execute()
	{
		$groupID = SJB_Request::getVar("group", false);
		$tp = SJB_System::getTemplateProcessor();

		$bannersObj = new SJB_Banners();
		$bannersIDs	= $bannersObj->getActiveBannerIdByGroupID($groupID);

		if ($bannersIDs !== false && $groupID !== false) {
			$banners = array();
			$ids = array();
			foreach ($bannersIDs as $key => $bannerId) {
				$bannerId = $bannerId['id'];
				$banner = $bannersObj->getBannerProperties($bannerId);
				// must get all banner params, assign it to template, and - show it!
				$banner['image_path'] = preg_replace("|\.\./|u", "/", $banner['image_path']);
				$banners[$key] = $banner;
				$ids[] = $bannerId;
			}

			$tp->assign('current_banners', $banners);
			$tp->display('banners_template.tpl');

			$bannersObj->incrementShowCounter($ids);
		}
	}
}
