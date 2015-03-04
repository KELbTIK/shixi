<?php

class SJB_Admin_Banners_AddBanner extends SJB_Function
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

		$filesDir = SJB_System::getSystemSettings('FILES_DIR');

		// set null values, to initialize
		$errors = array();

		$groupSID = SJB_Request::getVar('groupSID', false);

		$params = $_REQUEST;
		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];

			switch ($action_name) {
				case 'add':
					// ERRORS
					if ($params['title'] == '') {
						$errors[] = 'Banner Title is empty.';
					}
					if ($params['link'] == '' && $params['bannerType'] != 'code') {
						$errors[] = 'Banner link mismatched!';
					}
					if ($params['bannerType'] == 'code' && $params['code'] == '') {
						$errors[] = 'Banner code is empty.';
					}
					if ($_FILES['image']['name'] == '' && $params['bannerType'] == 'file') {
						$errors[] = 'No image attached!';
					}
					if ($_FILES['image']['name'] && $_FILES['image']['error']) {
						$errors[SJB_UploadFileManager::getErrorId($_FILES['image']['error'])] = 1;
					}
					if ($errors) {
						break;
					}

					// ok. All input fields presented
					$title = $params['title'];
					$link = $params['link'];

					// check 'link' for correct. If it hasn't 'http://' or 'https://' and bannerType != code - add them
					$expr = preg_match("/^(https?:\/\/)/", $link);
					if ($expr != true && $params['bannerType'] != 'code') {
						$link = 'http://' . $link;
					}

					if ($params['bannerType'] == 'file') {
						// make filename
						preg_match("|\.(\w{3})\b|u", $_FILES['image']['name'], $arr);
						$fileName = preg_replace("|\.(\w{3})\b|u", '', $_FILES['image']['name']);

						$hashName = md5((time() * $_FILES['image']['size'])) . '_' . $fileName;
						$bannerFilePath = $filesDir . 'banners/' . $hashName . '.' . $arr[1];

						// move file from temporary folder, and fill banner info to DB
						$copy = copy($_FILES['image']['tmp_name'], $bannerFilePath);

						if (!$copy) {
							$errors[] = 'Cannot copy file from TMP dir to Banners Dir';
							break;
						}

						if ($_FILES['image']['type'] != 'application/x-shockwave-flash') {
							// array of bannerInfo
							// [0] - width
							// [1] - height
							// [2] - ??
							// [3] - width & height in next view: width="104" height="150"
							// [bits] - bit size of image
							// [channels]
							// [mime] - type, (image/jpeg, image/gif, image/png )
							$bannerInfo = getimagesize($bannerFilePath);
							if ($params['width'] != '' && $params['height'] != '') {
								$sx = $params['width'];
								$sy = $params['height'];
							} else {
								$sx = $bannerInfo[0];
								$sy = $bannerInfo[1];
							}
							$type = $bannerInfo['mime'];

						} else {
							if ($params['width'] == '' || $params['height'] == '') {
								$errors[] = 'SIZE_PARAMETER_MISMATCHED';
								break;
							}
							$sx = $params['width'];
							$sy = $params['height'];
							$type = $_FILES['image']['type'];
						}

						$active = $params['active'];
						$group = $params['groupSID'];

						$bannerFilePath = '/' . str_replace('../', '/', str_replace(SJB_BASE_DIR, '', $bannerFilePath));
					}
					else {
						$sx = $params['width'];
						$sy = $params['height'];
						$type = '';
						$active = $params['active'];
						$group = $params['groupSID'];
						$bannerFilePath = '';
					}

					$bannersObj->addBanner($title, $link, $bannerFilePath, $sx, $sy, $type, $active, $group, $params);
					$site_url = SJB_System::getSystemsettings('SITE_URL') . "/edit-banner-group/?groupSID={$groupSID}";
					header("Location: {$site_url}");
					break;
			}
		}

		$banner_fields = $bannersObj->getBannersMeta();
		$bannerGroup = $bannersObj->getBannerGroupBySID($groupSID);

		$tp->assign('params', $params);
		$tp->assign('errors', $errors);
		$tp->assign('banner_fields', $banner_fields);
		$tp->assign('bannerGroup', $bannerGroup);
		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		$tp->display('add_banner.tpl');
	}
}
