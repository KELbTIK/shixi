<?php

class SJB_Classifieds_ApplyNowJobg8 extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();

		$listingSID = SJB_Request::getVar('listing_id', false);

		if ($listingSID == false) {
			$errors['UNDEFINED_LISTING_ID'] = 1;
		} else {

			$queryParams = '';

			$userInfo = SJB_UserManager::getCurrentUserInfo();

			// if logged user
			if (!empty($userInfo)) {
				$firstName = !empty($userInfo['FirstName']) ? $userInfo['FirstName'] : false;
				$lastName = !empty($userInfo['LastName']) ? $userInfo['LastName'] : false;
				$town = !empty($userInfo['City']) ? $userInfo['City'] : false;
				$postCode = !empty($userInfo['ZipCode']) ? $userInfo['ZipCode'] : false;
				$email = !empty($userInfo['email']) ? $userInfo['email'] : false;
				$phone = !empty($userInfo['PhoneNumber']) ? $userInfo['PhoneNumber'] : false;

				// Optional prefilled params for apply for JogG8
				//	  * Title
				//    * FirstName
				//    * LastName
				//    * Town
				//    * PostCode
				//    * HomeTelephone
				//    * WorkTelephone
				//    * Mobile
				//    * Email
				//    * ContactedPreviously

				if ($firstName) {
					$queryParams .= '&FirstName=' . urlencode($firstName);
				}
				if ($lastName) {
					$queryParams .= '&LastName=' . urlencode($lastName);
				}
				if ($town) {
					$queryParams .= '&Town=' . urlencode($town);
				}
				if ($postCode) {
					$queryParams .= '&PostCode=' . urlencode($postCode);
				}
				if ($phone) {
					$queryParams .= '&Mobile=' . urlencode($phone);
				}
				if ($email) {
					$queryParams .= '&Email=' . urlencode($email);
				}
			}

			$listing = SJB_ListingManager::getObjectBySID($listingSID);
			if (!$listing)
				$errors['WRONG_LISTING_ID_SPECIFIED'] = 1;
			else {
				$applicationSettings = $listing->getPropertyValue('ApplicationSettings');
				$tp->assign('applicationURL', $applicationSettings['value'] . $queryParams);
			}
		}

		$tp->assign('errors', $errors);

		$tp->display("apply_now_jobg8.tpl");
	}
}