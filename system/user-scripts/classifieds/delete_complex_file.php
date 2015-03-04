<?php

class SJB_Classifieds_DeleteComplexFile extends SJB_Function
{
	public function execute()
	{
		$listing_id = SJB_Request::getVar('listing_id', null);
		$listing_info = SJB_ListingManager::getListingInfoBySID($listing_id);
		$field_id = SJB_Request::getVar('field_id', null);
		$current_user_sid = SJB_UserManager::getCurrentUserSID();
		$owner_sid = SJB_ListingManager::getUserSIDByListingSID($listing_id);
		$errors = array();

		$complexEmun = SJB_Request::getVar('complexEnum', null, 'GET');
		$complexFieldID = SJB_Request::getVar('complexParent', null, 'GET');

		if (!is_null($complexEmun) && !is_null($complexFieldID)) {
			$fieldArr = array();
			array_push($fieldArr, $complexFieldID);
			array_push($fieldArr, $field_id);
			array_push($fieldArr, $complexEmun);
			$field_id = $complexFieldID . ':' . $field_id . $complexEmun . '_' . $listing_id;
		}
		else {
			$fieldArr = explode(':', $field_id);
			$complexEmun = isset($fieldArr[2]) ? explode('_', $fieldArr[2]) : false;
			$complexEmun = $complexEmun ? $complexEmun[0] : false;
		}

		if (is_null($listing_id) || is_null($field_id)) {
			$errors['PARAMETERS_MISSED'] = 1;
		}
		else {
			if (is_null($listing_info) || ($complexEmun === false) || !isset($listing_info[$fieldArr[0]][$fieldArr[1]][$complexEmun])) {
				$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
			}
			else {
				if ($owner_sid != $current_user_sid) {
					$errors['NOT_OWNER'] = 1;
				}
				else {

					$uploaded_file_id = $listing_info[$fieldArr[0]][$fieldArr[1]][$complexEmun];
					SJB_UploadFileManager::deleteUploadedFileByID($uploaded_file_id);
					$listing_info[$field_id] = '';
					$listing = new SJB_Listing($listing_info, $listing_info['listing_type_sid']);
					$props = $listing->getProperties();
					foreach ($props as $prop) {
						if ($prop->getID() !== $field_id)
							$listing->deleteProperty($prop->getID());
					}
					$listing->setSID($listing_id);
					SJB_ListingManager::saveListing($listing);
				}
			}
		}

		$tp = SJB_System::getTemplateProcessor();
		$tp->assign('errors', $errors);
		$tp->assign('listing_id', $listing_id);
		$tp->display('delete_uploaded_file.tpl');
	}
}
