<?php

class SJB_Classifieds_ManagePictures extends SJB_Function
{
	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$listing_id = SJB_Request::getVar('listing_sid', null);
		$listingInfo = SJB_ListingManager::getListingInfoBySID($listing_id);
		$contractID = SJB_Request::getVar('contract_id', null);
		$errors = '';
		$field_errors = '';

		if (empty($listing_id)) {
			$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
		}
		elseif (!empty($listing_id) && strlen($listing_id) == strlen(time())) {
			if ($contractID) {
				SJB_Session::setValue('contract_id', $contractID);
			} else {
				$contractID = SJB_Session::getValue('contract_id');
			}

			if (empty($_SESSION['tmp_file_storage'])) {
				SJB_Session::setValue('tmp_file_storage', array());
			}

			$contract = new SJB_Contract(array('contract_id' => $contractID));

			$gallery = new SJB_ListingGallery();
			$gallery->setListingSID($listing_id);
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
			$extraInfo = array();
			if ($listingInfo) {
				$extraInfo = !empty($listingInfo['product_info']) ? unserialize($listingInfo['product_info']) : array();
			} elseif ($contract) {
				$extraInfo = $contract->extra_info;
			}
			if (empty($extraInfo)) {
				if ($productSID = SJB_Request::getVar('product_sid', null)) {
					SJB_Session::setValue('product_sid', $productSID);
				} else {
					$productSID = SJB_Session::getValue('product_sid');
				}
				$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
			}

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
			if (is_null($listing))
				$errors['WRONG_PARAMETERS_SPECIFIED'] = 1;
			elseif ($listing->getUserSID() != SJB_UserManager::getCurrentUserSID())
				$errors['NOT_OWNER'] = 1;
			else {
				$gallery = new SJB_ListingGallery();
				$gallery->setListingSID($listing_id);
				$contract = new SJB_Contract(array('contract_id' => $contractID));
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

				$extraInfo = array();
				if ($listingInfo) {
					$extraInfo = !empty($listingInfo['product_info']) ? unserialize($listingInfo['product_info']) : array();
				} elseif ($contract) {
					$extraInfo = $contract->extra_info;
				}
				if (empty($extraInfo)) {
					if ($productSID = SJB_Request::getVar('product_sid', null)) {
						SJB_Session::setValue('product_sid', $productSID);
					} else {
						$productSID = SJB_Session::getValue('product_sid');
					}
					$extraInfo = SJB_ProductsManager::getProductExtraInfoBySID($productSID);
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
		$template_processor->assign("errors", $errors);
		$template_processor->assign("field_errors", $field_errors);
		$template_processor->display("manage_pictures.tpl");
	}
}