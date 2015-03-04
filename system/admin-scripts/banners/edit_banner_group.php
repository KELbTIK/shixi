<?php

class SJB_Admin_Banners_EditBannerGroup extends SJB_Function
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
		$errors = array();
		$groupSID = SJB_Request::getVar('groupSID', false);
		$form_submitted = SJB_Request::getVar('submit');

		if (isset($_REQUEST['action'])) {
			$action_name = $_REQUEST['action'];
			$params = $_REQUEST;

			switch ($action_name) {

				case 'edit':
					if ($params['groupID'] == '')
						$errors[] = 'GROUP_ID_MISMATCHED';
					if ($groupSID === false)
						$errors[] = 'BANNER_GROUP_SID_NOT_DEFINED';

					if ($errors)
						break;

					$result = $bannersObj->updateBannerGroup($params['groupSID'], $params['groupID'], $params['number_banners_display_at_once']);
					if ($result === false) {
						$errors[] = 'ERROR_UPDATE_BANNER_GROUP';
						break;
					}

					if ($form_submitted == 'save_banner') {
						$site_url = SJB_System::getSystemsettings('SITE_URL') . '/manage-banner-groups/';
						SJB_HelperFunctions::redirect($site_url);
					}

					break;

				case 'delete_banner':
					if (!isset($params['bannerId'])) {
						$banners_sids = SJB_Request::getVar('banners', false);
						if (count($banners_sids) > 0) {
							$keys = array_keys($banners_sids);
							$groupSID = $bannersObj->getBannerGroupSIDByBannerSID($keys[0]);

							foreach ($banners_sids as $b_sid => $keys) {
								$deleteBanner = $bannersObj->deleteBanner($b_sid);
								if ($deleteBanner === false) {
									$errors[] = $bannersObj->bannersError;
								}
							}
						}
					}
					else {
						$groupSID = $bannersObj->getBannerGroupSIDByBannerSID($params['bannerId']);
						$deleteBanner = $bannersObj->deleteBanner($params['bannerId']);
						if ($deleteBanner === false) {
							$errors[] = $bannersObj->bannersError;
							break;
						}
					}

					$site_url = SJB_System::getSystemsettings('SITE_URL') . '/edit-banner-group/?groupSID=' . $groupSID;
					SJB_HelperFunctions::redirect($site_url);
					break;

				case 'activate':
					$banners_sids = SJB_Request::getVar('banners', false);
					if ($banners_sids) {
						$keys = array_keys($banners_sids);
						$groupSID = $bannersObj->getBannerGroupSIDByBannerSID($keys[0]);

						foreach ($banners_sids as $b_sid => $keys) {
							$deleteBanner = $bannersObj->updateActiveStatus($b_sid, true);
							if ($deleteBanner === false) {
								$errors[] = 'Can\'t activate banner. ID: ' . $b_sid;
							}
						}
					}

					break;

				case 'deactivate':
					$banners_sids = SJB_Request::getVar('banners', false);
					if ($banners_sids) {
						$keys = array_keys($banners_sids);
						$groupSID = $bannersObj->getBannerGroupSIDByBannerSID($keys[0]);

						foreach ($banners_sids as $b_sid => $keys) {
							$deleteBanner = $bannersObj->updateActiveStatus($b_sid, false);
							if ($deleteBanner === false)
								$errors[] = 'Can\'t deactivate banner. ID: ' . $b_sid;
						}
					}
					break;
					
				case 'approve':
					$banners_sids = SJB_Request::getVar('banners', false);
					if ($banners_sids) {
						$keys = array_keys($banners_sids);
						$groupSID = $bannersObj->getBannerGroupSIDByBannerSID($keys[0]);

						foreach ($banners_sids as $b_sid => $keys) {
							$bannerInfo = $bannersObj->getBannerProperties($b_sid);
							$contractSID = !empty($bannerInfo['contract_sid'])?$bannerInfo['contract_sid']:false;
							$approveBanner = $bannersObj->updateStatus($b_sid, 'approved');
							if ($approveBanner === false)
								$errors[] = 'Can\'t approved banner. ID: ' . $b_sid;
							else {
								$bannersObj->updateActiveStatus($b_sid, true);
								if ($contractSID) 
									SJB_ContractManager::updateExpirationPeriod($contractSID);
							}
						}
					}
					break;
					
			case 'reject':
					$banners_sids = SJB_Request::getVar('banners', false);
					if ($banners_sids) {
						$keys = array_keys($banners_sids);
						$groupSID = $bannersObj->getBannerGroupSIDByBannerSID($keys[0]);
						$reject = SJB_Request::getVar('rejection_reason', '');
						foreach ($banners_sids as $b_sid => $keys) {
							$approveBanner = $bannersObj->updateStatus($b_sid, 'rejected');
							if ($approveBanner === false)
								$errors[] = 'Can\'t rejected banner. ID: ' . $b_sid;
							else {
								$bannersObj->updateActiveStatus($b_sid, false);
								if ($bannersObj->getBannerUserSID($b_sid)) {
									$bannerInfo = $bannersObj->getBannerProperties($b_sid);
									SJB_Notifications::sendBannerRejectedLetter($bannerInfo, $bannersObj->getBannerUserSID($b_sid), $reject);
								}
							}
						}
					}
					break;
			}
		}

		$bannerGroup = $bannersObj->getBannerGroupBySID($groupSID);
		$banners = $bannersObj->getBannersByGroupSID($groupSID);

		$tp->assign('form_submitted', $form_submitted);
		$tp->assign('bannerGroup', $bannerGroup);
		$tp->assign('errors', $errors);

		$tp->display('edit_banner_group.tpl');

		$tp->assign('banners', $banners);
		$tp->assign('bannersPath', SJB_Banners::getSiteUrl());

		$tp->display('manage_banners.tpl');
	}
}
