<?php


class SJB_Admin_Classifieds_ManagePictures extends SJB_Function
{
	public function isAccessible()
	{
		$listingId = SJB_Request::getVar('listing_sid', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listingId);
		$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($listingInfo['listing_type_sid']);
		if ($productInfo = SJB_ProductsManager::getProductInfoBySID(SJB_Request::getVar('product_sid', null))) {
			$listingTypeId = SJB_ListingTypeManager::getListingTypeIDBySID($productInfo['listing_type_sid']);
		}
		$listingType = !in_array($listingTypeId, array('Resume', 'Job')) ? "{$listingTypeId}_listings" : $listingTypeId . 's';
		$this->setPermissionLabel('manage_' . strtolower($listingType));
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$listing_id = SJB_Request::getVar('listing_sid', SJB_Request::getVar('listing_id', null));
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
		$productSID = SJB_Request::getVar('product_sid' . null);
		$errors = null;
		$field_errors = null;
		$extraInfo = array();

		if (empty($listing_id)) {
			$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
		}
		elseif (!empty($listing_id) && strlen($listing_id) == strlen(time())) {
			if ($productSID) {
				SJB_Session::setValue('product_sid', $productSID);
			} else {
				$productSID = SJB_Session::getValue('product_sid');
			}

			if (empty($_SESSION['tmp_file_storage']))
				SJB_Session::setValue('tmp_file_storage', array());

			$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
			$contract = new SJB_Contract(array('contract_id' => $productSID));

			$gallery = new SJB_ListingGallery();
			$gallery->setListingSID($listing_id);
			$template_processor->assign("contract", $contract);
			if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add') {
				if (!isset($_FILES['picture']))
					$field_errors['Picture'] = 'FILE_NOT_SPECIFIED';
				elseif ($_FILES['picture']['error']) {
					switch ($_FILES['picture']['error']) {
						case '1':
							$field_errors['Picture'] = 'UPLOAD_ERR_INI_SIZE';
							break;
						case '2':
							$field_errors['Picture'] = 'UPLOAD_ERR_FORM_SIZE';
							break;
						case '3':
							$field_errors['Picture'] = 'UPLOAD_ERR_PARTIAL';
							break;
						case '4':
							$field_errors['Picture'] = 'UPLOAD_ERR_NO_FILE';
							break;
						default:
							break;
					}
				}
				else {
					$image_caption = isset($_REQUEST['caption']) ? $_REQUEST['caption'] : '';
					$_FILES['picture']['caption'] = $image_caption;
					if (!$gallery->uploadImage($_FILES['picture']['tmp_name'], $image_caption))
						$field_errors['Picture'] = $gallery->getError();
				}
			}
			elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
				if (isset($_REQUEST['picture_id'])) {
					$picture_id = $_REQUEST['picture_id'];
					$gallery->deleteImageBySID($picture_id);
				}
			}
			if ($listingInfo)
				$extraInfo = !empty($listingInfo['product_info']) ? unserialize($listingInfo['product_info']) : array();
			elseif ($contract && $contract->extra_info)
				$extraInfo = $contract->extra_info;
			elseif ($productInfo)
				$extraInfo = !empty($productInfo['serialized_extra_info']) ? unserialize($productInfo['serialized_extra_info']) : array();
				
			$number_of_picture_allowed = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
			$number_of_picture = $gallery->getPicturesAmount();
			$pictures_info = $gallery->getPicturesInfo();
			$_SESSION['tmp_file_storage'] = $pictures_info;

			$template_processor->assign("listing", array('id' => "$listing_id"));
			$template_processor->assign("number_of_picture_allowed", $number_of_picture_allowed);
			$template_processor->assign("number_of_picture", $number_of_picture);
			$template_processor->assign('pictures', $_SESSION['tmp_file_storage']);

		} else {

			$listing = SJB_ListingManager::getObjectBySID($listing_id);
			if (is_null($listing)) {
				$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
			} else {
				$gallery = new SJB_ListingGallery();
				$gallery->setListingSID($listing_id);
				$contract = new SJB_Contract(array('contract_id' => $productSID));
				if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'add') {
					if (!isset($_FILES['picture']))
						$field_errors['Picture'] = 'FILE_NOT_SPECIFIED';
					elseif ($_FILES['picture']['error']) {
						switch ($_FILES['picture']['error']) {
							case '1':
								$field_errors['Picture'] = 'UPLOAD_ERR_INI_SIZE';
								break;
							case '2':
								$field_errors['Picture'] = 'UPLOAD_ERR_FORM_SIZE';
								break;
							case '3':
								$field_errors['Picture'] = 'UPLOAD_ERR_PARTIAL';
								break;
							case '4':
								$field_errors['Picture'] = 'UPLOAD_ERR_NO_FILE';
								break;
							default:
								break;
						}

					}
					else {
						$image_caption = isset($_REQUEST['caption']) ? $_REQUEST['caption'] : '';
						if (!$gallery->uploadImage($_FILES['picture']['tmp_name'], $image_caption))
							$field_errors['Picture'] = $gallery->getError();
					}
				}
				elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete') {
					if (isset($_REQUEST['picture_id']))
						$gallery->deleteImageBySID($_REQUEST['picture_id']);
				}
				elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'move_up') {
					if (isset($_REQUEST['picture_id']))
						$gallery->moveUpImageBySID($_REQUEST['picture_id']);
				}
				elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'move_down') {
					if (isset($_REQUEST['picture_id']))
						$gallery->moveDownImageBySID($_REQUEST['picture_id']);
				}

				if ($listingInfo) {
					$extraInfo = !empty($listingInfo['product_info']) ? unserialize($listingInfo['product_info']) : array();
				} elseif ($contract && $contract->extra_info) {
					$extraInfo = $contract->extra_info;
				}

				$number_of_picture_allowed = isset($extraInfo['number_of_pictures']) ? $extraInfo['number_of_pictures'] : 0;
				$number_of_picture = $gallery->getPicturesAmount();
				$listing_info['id'] = $listing_id;
				$template_processor->assign("listing", $listing_info);
				$pictures_info = $gallery->getPicturesInfo();
				$template_processor->assign("pictures", $pictures_info);
				$template_processor->assign("number_of_picture", $number_of_picture);
				$template_processor->assign("number_of_picture_allowed", $number_of_picture_allowed);
			}
		}
		$template_processor->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
		$template_processor->assign("errors", $errors);
		$template_processor->assign("field_errors", $field_errors);
		$template_processor->display("manage_pictures.tpl");
	}
}
