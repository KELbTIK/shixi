<?php

class SJB_Classifieds_SaveSearch extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$isAlert = $enableNotify = isset($_REQUEST["alert"]);
		$tp->assign('is_alert', $isAlert);

		if (SJB_UserManager::isUserLoggedIn()) {

			$cu = SJB_UserManager::getCurrentUser();
			if ($cu->isSubuser())
				$current_user_info = $cu->getSubuserInfo();
			else
				$current_user_info = SJB_UserManager::getCurrentUserInfo();

			$criteria_saver = new SJB_ListingCriteriaSaver(SJB_Request::getVar("searchId", ""));

			$requested_data = $criteria_saver->getCriteria();

			if (isset($requested_data['listing_type'])) {
				$current_listing_type = array_pop($requested_data['listing_type']);
			} else {
				$current_listing_type = '';
				if (isset($requested_data['listing_type_sid'])) {
					$listing_type_sid = array_pop($requested_data['listing_type_sid']);
					$current_listing_type = SJB_ListingTypeManager::getListingTypeIDBySID($listing_type_sid);
				}
			}

			$errors = array();
			if (!$isAlert && !SJB_Acl::getInstance()->isAllowed('save_searches')) {
				$errors[] = "DENIED_SAVE_JOB_SEARCH";
			}
			elseif ($isAlert && !SJB_Acl::getInstance()->isAllowed('use_' . trim($current_listing_type) . '_alerts')) {
				$errors[] = "DENIED_SAVE_JOB_SEARCH";
			}

			switch (SJB_Request::getVar("action")) {
				case 'edit':
					unset($_GET['action']);
					if (isset($_GET['id_saved'])) {
						$id_saved = $_GET['id_saved'];
						unset($_GET['id_saved']);
						$errors = array();

						SJB_SavedSearches::updateSearchOnDB($_GET, $id_saved, $current_user_info['sid'], 0);

						if (!empty($errors)) {
							$tp->assign("errors", $errors);
							$tp->display("save_search_failed.tpl");
						}
						else {
							$url = SJB_System::getSystemSettings('SITE_URL') . "/saved-searches/";
							if ($isAlert)
								$url = SJB_System::getSystemSettings('SITE_URL') . "/job-alerts/";

							$tp->assign("url", $url);
							$tp->display("save_search_success.tpl");
						}
					}
					break;

				case 'save':
					$search_name = SJB_Request::getVar("search_name");
					$errors = array();
					$criteria_saver = new SJB_ListingCriteriaSaver(SJB_Request::getVar("searchId", ""));
					$requested_data = $criteria_saver->getCriteria();
					if (is_array($criteria_saver->order_info))
						$requested_data = array_merge($requested_data, $criteria_saver->order_info);
					$requested_data['listings_per_page'] = $criteria_saver->listings_per_page;
					$emailFrequency = SJB_Request::getVar("email_frequency", 'daily');
					SJB_SavedSearches::saveSearchOnDB($requested_data, $search_name, $current_user_info['sid'], $enableNotify, $isAlert, $emailFrequency);

					if (!empty($errors)) {
						$tp->assign("errors", $errors);
						$tp->display("save_search_failed.tpl");
					}
					else {
						if (isset($_REQUEST['url']))
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . $_REQUEST['url'] . "?alert=added");
						$tp->display("save_search_success.tpl");
					}
					break;

				default:
					if (!empty($errors)) {
						$tp->assign("errors", $errors);
						$tp->display("save_search_failed.tpl");
					} else {
						$tp->assign("searchId", SJB_Request::getVar("searchId", ""));
						$tp->assign("listing_type_id", SJB_Session::getValue('listing_type_id'));
						$tp->display("save_search_form.tpl");
					}
					break;
			}
		}
		else {
			$tp->assign("return_url", base64_encode(SJB_Navigator::getURIThis()));
			$tp->assign("ajaxRelocate", true);
			$tp->display("../users/login.tpl");
		}
	}
}
