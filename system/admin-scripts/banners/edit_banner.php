<?php

class SJB_Admin_Banners_EditBanner extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_banners');
		return parent::isAccessible();
	}

	public function execute()
	{
		$bannersObj = new SJB_Banners();
		$params = $_REQUEST;
		$bannerId = $params['bannerId'];
		
		if (SJB_Request::isAjax()) {
			$response = array(
				'success' => $bannersObj->deleteBannerImage($bannerId),
				'error'	  => SJB_I18N::getInstance()->gettext('Backend', $bannersObj->bannersError)
			);
			die(json_encode($response));
		}
		
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$banner = array_merge($bannersObj->getBannerProperties($bannerId), $params);
		$form_submitted = SJB_Request::getVar('submit');
		$filesDir = SJB_System::getSystemSettings('FILES_DIR');

		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			switch ($action_name) {
				case 'edit':
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
					if ($params['bannerType'] == 'file' && $_FILES['image']['name'] == '' && empty($params['imagePath'])) {
						$errors[] = 'No image attached!';
					}
					if ($_FILES['image']['name'] && $_FILES['image']['error']) {
						$errors[SJB_UploadFileManager::getErrorId($_FILES['image']['error'])] = 1;
					}
					if ($errors) {
						break;
					}

					// if image changed - save it
					if ($_FILES['image']['name'] != '' && $_FILES['image']['tmp_name'] != '') {

						$hashName = md5((time() * $_FILES['image']['size']) . "_" . $_FILES['image']['name']);
						$ext = preg_match("|\.(\w{3})\b|", $_FILES['image']['name'], $arr);

						$bannerFilePath = $filesDir . "banners/" . $hashName . "." . $arr[1];

						// move file from temporary folder, and fill banner info to DB
						$copy = copy($_FILES['image']['tmp_name'], $bannerFilePath);

						if (!$copy) {
							$errors = 'Cannot copy file from TMP dir to Banners Dir';
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

						$bannerFilePath = "/" . str_replace("../", "/", str_replace(SJB_BASE_DIR, '', $bannerFilePath));
						// now delete old banner image
						$bannersObj->deleteBannerImage($bannerId);

					} else {
						// if image not changed - leave it as is
						$bannerOldInfo = $bannersObj->getBannerProperties($params['bannerId']);

						$sx = $bannerOldInfo['width'];
						$sy = $bannerOldInfo['height'];
						if ($params['width'] != '' && $params['height'] != '') {
							if ($params['width'] != $sx || $params['height'] != $sy) {
								$sx = $params['width'];
								$sy = $params['height'];
							}
						}

						$type = $bannerOldInfo['type'];
						$bannerFilePath = $bannerOldInfo['image_path'];
					}

					$title = $params['title'];
					$link = $params['link'];
					$active = $params['active'];
					$group = $params['groupSID'];

					// check 'link' for correct. If it hasn't 'http://' or 'https://' - add them
					$expr = preg_match("/^(https?:\/\/)/", $link);
					if ($expr != true && $params['bannerType'] != 'code') {
						$link = "http://" . $link;
					}
					if ($params['bannerType'] == 'code') {
						$bannersObj->deleteBannerImage($bannerId);
					}
					$result = $bannersObj->updateBanner($params['bannerId'], $title, $link, $bannerFilePath, $sx, $sy, $type, $active, $group, $params);

					if ($form_submitted == 'save_banner') {
						$site_url = SJB_System::getSystemsettings('SITE_URL') . "/edit-banner-group/?groupSID=$group";
					} else {
						$site_url = SJB_System::getSystemsettings('SITE_URL') . "/edit-banner/?bannerId=" . $bannerId;
					}
					SJB_HelperFunctions::redirect($site_url);
					break;
			}
		}

		$banner_fields = $bannersObj->getBannersMeta();

		$tp->assign("banner_fields", $banner_fields);
		$tp->assign("banner", $banner);
		$tp->assign('errors', $errors);
		$tp->assign('bannersPath', SJB_Banners::getSiteUrl());
		$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		$tp->display("edit_banner.tpl");
	}
}
