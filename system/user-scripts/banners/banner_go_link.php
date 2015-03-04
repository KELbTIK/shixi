<?php

class SJB_Banners_GoLink extends SJB_Function
{
	public function execute()
	{
		$bannersObj = new SJB_Banners();

		$params = $_REQUEST;

		$bannerId = SJB_Request::getVar('bannerId', 0, null, 'int');

		$banner = $bannersObj->getBannerProperties($bannerId);


		// get link of banner
		$link = $banner['link'];


		// increment CLICK counter
		$bannersObj->incrementClickCounter($bannerId);

		header("Location: {$link}");
		exit;

	}
}
