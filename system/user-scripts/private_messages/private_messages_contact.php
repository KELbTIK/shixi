<?php

class SJB_PrivateMessages_Contact extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$action = null;

		if (SJB_UserManager::isUserLoggedIn()) {
			$userSID = SJB_UserManager::getCurrentUserSID();
			$contactSID = 0;
			$errors = array();

			if (isset($_REQUEST['passed_parameters_via_uri'])) {
				$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
				$contactSID = SJB_Array::get($passed_parameters_via_uri, 0);
			}

			if (!$contactSID) {
				$errors['UNDEFINED_CONTACT_ID'] = 1;
			}
			else {
				$contactInfo = SJB_PrivateMessage::getContactInfo($userSID, $contactSID);
				if (!$contactInfo) {
					$errors['WRONG_CONTACT_ID_SPECIFIED'] = 1;
				}
				else {
					$action = SJB_Request::getVar('action');
					switch ($action) {
						case 'save':
							$note = SJB_Request::getVar('note');
							$result = SJB_PrivateMessage::saveContactNote($userSID, $contactSID, $note);
							if ($result) {
								$tp->assign('noteSaved', true);
							}
							break;
						default:
							break;
					}
				}
				$tp->assign('contactInfo', $contactInfo);
			}

			$tp->assign('errors', $errors);
		}
		if ($action) {
			$tp->assign('action', $action);
			$tp->display('notes.tpl');
		}
		else {
			$tp->display('contact.tpl');
		}
	}
}
