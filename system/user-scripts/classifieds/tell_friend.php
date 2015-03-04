<?php

class SJB_Classifieds_TellFriend extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$fatal_errors = array();
		$isDataSubmitted = false;

		try {
			$controller = new SJB_SendListingInfoController($_REQUEST);
		} catch (Exception $e) {
			$controller = false;
		}

		if(empty($controller)) {
			$fatal_errors['LISTING_ID_IS_NOT_NUMERIC'] = $e->getMessage();
		} elseif ($controller->isListingSpecified()) {
			if ($controller->isDataSubmitted()) {
				SJB_Captcha::getInstance($tp, $_REQUEST)->isValid($errors);

				if (!preg_match('^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.[a-zA-Z]{2,}$^', $_REQUEST['friend_email'])) {
					$errors['NOT_VALID_EMAIL_FORMAT'] = true;
				}

				if (empty($errors)) {
					$data_to_send = $controller->getData();
					if (!SJB_Notifications::sendTellFriendLetter($data_to_send))
						$errors['SEND_ERROR'] = true;
					$isDataSubmitted = true;
				}
			}
			$tp->assign('listing_info', SJB_ListingManager::createTemplateStructureForListing(SJB_ListingManager::getObjectBySID($controller->getListingID())));
		}
		else {
			$fatal_errors['UNDEFINED_LISTING_ID'] = true;
		}

		$tp->assign('fatal_errors', $fatal_errors);
		$tp->assign('errors', $errors);
		$tp->assign('info', SJB_Request::get());
		$tp->assign('is_data_submitted', $isDataSubmitted);
		$tp->display('tell_friend.tpl');
	}
}
