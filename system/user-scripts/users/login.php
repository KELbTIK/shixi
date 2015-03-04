<?php

class SJB_Users_Login extends SJB_Function
{
	public function execute()
	{
		$logged_in = false;
		$tp = SJB_System::getTemplateProcessor();
		$shoppingCart = SJB_Request::getVar('shopping_cart', false);
		$proceedToPosting = SJB_Request::getVar('proceed_to_posting', false);
		$productSID = SJB_Request::getVar('productSID', false);
		$listingTypeID = SJB_Request::getVar('listing_type_id', false);
		$errors = array();

		if (SJB_Authorization::isUserLoggedIn() && !isset($_REQUEST['as_user'])) {
			$tp->display('already_logged_in.tpl');
		} else {
			$template = SJB_Request::getVar('template', 'login.tpl');
			$page_config = SJB_System::getPageConfig(SJB_System::getURI());

			if (SJB_Request::getVar('action', false) == 'login') {
				$username = SJB_Request::getVar('username');
				$password = SJB_Request::getVar('password');
				$keep_signed = SJB_Request::getVar('keep', false);

				$login_as_user = false;
				if (isset($_REQUEST['as_user'])) {
					$login_as_user = true;
					if (SJB_UserManager::getCurrentUserSID()) {
						SJB_Authorization::logout();
					}
				}
				// redirect user to the home page if it's login page or to the same page otherwise

				if (SJB_Request::getVar('return_url', false) != false) {
					$redirect_url = base64_decode(SJB_Request::getVar('return_url'));
					if (!empty($proceedToPosting)) {
						$redirect_url .= '&proceed_to_posting=1&productSID=' . $productSID;
					}
				}
				else {
					if ($page_config->module == 'users' && $page_config->function == 'login') {
						$redirect_url = SJB_System::getSystemSettings("SITE_URL") . "/my-account/";
					} else {
						$redirect_url = SJB_System::getSystemSettings("SITE_URL") . SJB_System::getURI();
					}
				}
				if (SJB_UserManager::getCurrentUserSID()) {
					$logged_in = true;
				} else {
					SJB_UserManager::login($username, $password, $errors, false, $login_as_user);
					if ($errors) {
						if (is_null(SJB_Session::getValue('userLoginCounter'))) {
							SJB_Session::setValue('userLoginCounter', 1);
						} else {
							SJB_Session::setValue('userLoginCounter', SJB_Session::getValue('userLoginCounter') + 1);
						}
					}
					if (SJB_Captcha::getInstance($tp, $_REQUEST)->isValid($errors) && empty($errors)) {
						$logged_in = SJB_Authorization::login($username, $password, $keep_signed, $errors, $login_as_user);
					}
				}

				if ($logged_in && !$shoppingCart) {
					SJB_HelperFunctions::redirect($redirect_url);
				}
				$tp->assign('logged_in', $logged_in);
			}

			$return_url = SJB_Request::getVar('return_url', ($page_config->function != 'login' && $page_config->function != 'search_form') ? base64_encode(SJB_Navigator::getURIThis()) : false);
			if (!filter_var(SJB_System::getSystemSettings("SITE_URL") . base64_decode($return_url), FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
				$return_url = '';
			}

			$tp->assign('shopping_cart', $shoppingCart);
			$tp->assign('proceedToPosting', $proceedToPosting);
			$tp->assign('productSID', $productSID);
			$tp->assign('listingTypeID', $listingTypeID);
			$tp->assign('return_url', $return_url);
			$tp->assign('ajaxRelocate', SJB_Request::getVar('ajaxRelocate', false));
			$tp->assign('errors', $errors);
			$tp->assign('adminEmail', SJB_System::getSettingByName('system_email'));
			$tp->display($template);
		}
	}
}
