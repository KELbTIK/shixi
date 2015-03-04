<?php


class SJB_Classifieds_Notes extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$errors = array();
		$actionPage = SJB_Request::getVar('action');
		$page = SJB_Request::getVar('page', false);
		$action = SJB_Request::getVar('actionNew');
		$close = SJB_Request::getVar('close', false);
		$closeWindow = SJB_Request::getVar('closeWindow', false);
		$action = $action ? $action : $actionPage;
		$action = (isset($close) && $close != '') ? 'close' : $action;
		if (!$page) {
			$sid = SJB_Request::getVar('listing_sid');
			$tp->assign("listing_sid", $sid);
		}
		elseif ($page == 'apps') {
			$sid = SJB_Request::getVar('apps_id');
			$action = ($action != 'add') ? $action . '_apps' : $action;
			$tp->assign("page", $page);
			$tp->assign("apps_id", $sid);
			$tp->assign("listing_sid", $sid);
		}

		if ($sid) {
			switch ($action) {
				case 'add':
					$action = 'edit';
					$tp->assign("saved_listing", false);
					break;
				case 'save':
					$noteSaved = false;
					$note = SJB_Request::getVar('note');
					if (SJB_SavedListings::saveNoteOnDB(SJB_UserManager::getCurrentUserSID(), $sid, $note))
						$noteSaved = true;
					$tp->assign("noteText", $note);
					$tp->assign("noteSaved", $noteSaved);
					break;
				case 'edit':
					$saved_listing = SJB_SavedListings::getSavedListingsByUserAndListingSid(SJB_UserManager::getCurrentUserSID(), $sid);
					$tp->assign("saved_listing", $saved_listing);
					break;
				case 'save_apps':
					$noteSaved = false;
					$note = SJB_Request::getVar('note');
					if (SJB_Applications::saveNoteOnDB($note, $sid))
						$noteSaved = true;
					$action = 'save';
					$tp->assign("noteSaved", $noteSaved);
					break;
				case 'edit_apps':
					$apps = SJB_Applications::getById($sid);
					$action = 'edit';
					$tp->assign("saved_listing", $apps);
					break;
				case 'close_apps':
					$apps = SJB_Applications::getById($sid);
					$action = 'close';
					$tp->assign("saved_listing", $apps);
					break;
				case 'close':
					$saved_listing = SJB_SavedListings::getSavedListingsByUserAndListingSid(SJB_UserManager::getCurrentUserSID(), $sid);
					$tp->assign("saved_listing", $saved_listing);
					break;
			}
		}
		else {
			if (!$page)
				$errors['UNDEFINED_LISTING_ID'] = true;
			else
				$errors['UNDEFINED_APPS_ID'] = true;
		}
		$tp->assign('view', SJB_Request::getVar('view'));
		$tp->assign("closeWindow", $closeWindow);
		$tp->assign("action", $action);
		$tp->assign("errors", $errors);
		$tp->display('notes.tpl');
	}
}