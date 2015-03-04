<?php

class SJB_Admin_Classifieds_ImportListings extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('import_listings');
		return parent::isAccessible();
	}

	public function execute()
	{
		ini_set('max_execution_time', 0);
		$tp = SJB_System::getTemplateProcessor();
		$file_info = isset($_FILES['import_file']) ? $_FILES['import_file'] : null;
		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$listingTypeID = SJB_Request::getVar('listing_type_id', null);
		$productSID = SJB_Request::getVar('product_sid', 0);
		$errors = array();

		if ($listingTypeID && $productSID) {
			$acl = SJB_Acl::getInstance();
			$resource = 'post_' . strtolower($listingTypeID);
			if (!$acl->isAllowed($resource, $productSID, 'product'))
				$errors[] = 'You cannot import listings of this type under the selected product';
		}

		if (!empty($file_info)) {
			$extension = SJB_Request::getVar('file_type');
			if (!SJB_ImportFile::isValidFileExtensionByFormat($extension, $file_info)) {
				$errors['DO_NOT_MATCH_SELECTED_FILE_FORMAT'] = true;
			}
		}

		if (empty($file_info) || $file_info['error'] || $errors) {
			if (isset($file_info['error']) && $file_info['error'] > 0) {
				$errors[SJB_UploadFileManager::getErrorId($file_info['error'])] = 1;
			}
			
			$listing_types = SJB_ListingTypeManager::getAllListingTypesInfo();
			$products = SJB_ProductsManager::getProductsByProductType('post_listings');
			$tp->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
			$tp->assign('listing_types', $listing_types);
			$tp->assign('products', $products);
			$tp->assign('errors', $errors);
			$tp->assign('charSets', SJB_HelperFunctions::getCharSets());
			$tp->display('import_listings.tpl');
		}
		else {
			$i18n = SJB_I18N::getInstance();
			$csv_delimiter = SJB_Request::getVar('csv_delimiter', null);
			$activeStatus = SJB_Request::getVar('active', 0);
			$activationDate = SJB_Request::getVar('activation_date', date('Y-m-d'));
			$activationDate = $i18n->getInput('date', $activationDate);
			$non_existed_values_flag = SJB_Request::getVar('non_existed_values', null);
			$productInfo = SJB_ProductsManager::getProductInfoBySID($productSID);
			if (empty($productInfo['listing_duration'])) {
				$expirationDate = '';
			} else {
				$timestamp = strtotime($activationDate . ' + ' . $productInfo['listing_duration'] . ' days');
				$expirationDate = $i18n->getDate(date('Y-m-d', $timestamp));
			}

			$extension = $_REQUEST['file_type'];

			if ($extension == 'xls') {
				$import_file = new SJB_ImportFileXLS($file_info);
			} elseif ($extension == 'csv') {
				$import_file = new SJB_ImportFileCSV($file_info, $csv_delimiter);
			}

			$import_file->parse($encodingFromCharset);

			$listing = $this->CreateListing(array(), $listingTypeID);
			$imported_data = $import_file->getData();
			$isFileImported = true;

			$count = 0;
			$addedListingsSids = array();
			$nonExistentUsers = array();
			foreach ($imported_data as $key => $importedColumn) {
				if ($key == 1) {
					$imported_data_processor = new SJB_ImportedDataProcessor($importedColumn, $listing);
					continue;
				}
				if (!$importedColumn)
					continue;
				$count++;
				$listingInfo = $imported_data_processor->getData($non_existed_values_flag, $importedColumn);
				$doc = new DOMDocument();
				foreach ($listing->getProperties() as $property) {
					if ($property->getType() == 'complex' && !empty($listingInfo[$property->id])) {
						$childFields = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($property->sid);
						$doc->loadXML($listingInfo[$property->id]);
						$results = $doc->getElementsByTagName($property->id . 's');
						$listingInfo[$property->id] = array();
						foreach ($results as $complexparent) {
							$i = 1;
							foreach ($complexparent->getElementsByTagName($property->id) as $result) {
								$resultXML = simplexml_import_dom($result);
								foreach ($childFields as $childField) {
									if (isset($resultXML->$childField['id']))
										$listingInfo[$property->id][$childField['id']][$i] = XML_Util::reverseEntities((string)$resultXML->$childField['id']);
								}
								$i++;
							}
						}
					} elseif ($property->getType() == 'monetary' && !empty($listingInfo[$property->id])) {
						$value = $listingInfo[$property->id];
						$listingInfo[$property->id] = array();
						$listingInfo[$property->id]['value'] = $value;
						$defaultCurrency = SJB_CurrencyManager::getDefaultCurrency();
						$currencyCode = !empty($listingInfo[$property->id . "Currency"]) ? $listingInfo[$property->id . "Currency"] : $defaultCurrency['currency_code'];
						$currency = SJB_CurrencyManager::getCurrencyByCurrCode($currencyCode);
						$listingInfo[$property->id]['add_parameter'] = !empty($currency['sid']) ? $currency['sid'] : '';
						if (isset($listingInfo[$property->id . "Currency"])) {
							unset($listingInfo[$property->id . "Currency"]);
						}
					} elseif ($property->getType() == 'location') {
						$locationFields = array($property->id.'.Country', $property->id.'.State', $property->id.'.City', $property->id.'.ZipCode');
						$locationFieldAdded = array();
						foreach ($locationFields as $locationField) {
							if (array_key_exists($locationField, $listingInfo)) {
								switch ($locationField) {
									case $property->id.'.Country':
										$value = SJB_CountriesManager::getCountrySIDByCountryName($listingInfo[$locationField]);
										if (!$value) {
											$value = SJB_CountriesManager::getCountrySIDByCountryCode($listingInfo[$locationField]);
										}
										break;
									case $property->id.'.State':
										$value = SJB_StatesManager::getStateSIDByStateName($listingInfo[$locationField]);
										if (!$value) {
											$value = SJB_StatesManager::getStateSIDByStateCode($listingInfo[$locationField]);
										}
										break;
									default:
										$value = $listingInfo[$locationField];
										break;
								}
								$listingInfo[$property->id][str_replace($property->id.'.', '', $locationField)] = $value;
								$locationFieldAdded[] = str_replace($property->id.'.', '', $locationField);
							}
						}
						if ($property->id == 'Location') {
							$locationFields = array('Country', 'State', 'City', 'ZipCode');
							foreach ($locationFields as $locationField) {
								if (array_key_exists($locationField, $listingInfo) && !in_array($locationField, $locationFieldAdded) && !$listing->getProperty($locationField)) {
									switch ($locationField) {
										case 'Country':
											$value = SJB_CountriesManager::getCountrySIDByCountryName($listingInfo[$locationField]);
											if (!$value) {
												$value = SJB_CountriesManager::getCountrySIDByCountryCode($listingInfo[$locationField]);
											}
											break;
										case 'State':
											$value = SJB_StatesManager::getStateSIDByStateName($listingInfo[$locationField]);
											if (!$value) {
												$value = SJB_StatesManager::getStateSIDByStateCode($listingInfo[$locationField]);
											}
											break;
										default:
											$value = $listingInfo[$locationField];
											break;
									}
									$listingInfo[$property->id][$locationField] = $value;
								}
							}
						}
					}
				}

				$listing = $this->CreateListing($listingInfo, $listingTypeID);
				$pictures = array();
				if (isset($listingInfo['pictures'])) {
					$listing->addPicturesProperty();
					$explodedPictures = explode(';', $listingInfo['pictures']);
					foreach ($explodedPictures as $picture) {
						if (!empty($picture)) {
							$pictures[] = $picture;
						}
					}
					$listing->setPropertyValue('pictures', count($pictures));
				}
				$listing->addActiveProperty($activeStatus);
				$listing->addActivationDateProperty($activationDate);
				$listing->addExpirationDateProperty($expirationDate);
				SJB_ListingDBManager::setListingExpirationDateBySid($listing->sid);
				$listing->setProductInfo(SJB_ProductsManager::getProductExtraInfoBySID($productSID));
				$listing->setPropertyValue('access_type', 'everyone');
				$listing->setPropertyValue('status', 'approved');

				foreach ($listing->getProperties() as $property) {
					if ($property->getType() == 'tree' && $property->value !== '') {
						try {
							$treeImportHelper = new SJB_FieldTreeImportHelper($property->value);
							$treeValues = $treeImportHelper->parseAndGetValues();
							$listing->setPropertyValue($property->id, $treeValues);
							$listing->details->properties[$property->id]->type->property_info['value'] = $treeValues;
						}
						catch (Exception $e) {
							$listing->setPropertyValue($property->id, '');
							$listing->details->properties[$property->id]->type->property_info['value'] = '';
							SJB_Error::writeToLog('Listing Import. Tree Field Value Error: ' . $e->getMessage());
						}
					}

					// fix for new format of ApplicationSettings
					elseif ($property->id == 'ApplicationSettings' && !empty($listingInfo['ApplicationSettings'])) {
						if (preg_match("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,}\$^iu", $listingInfo['ApplicationSettings']))
							$listingInfo['ApplicationSettings'] = array('value' => $listingInfo['ApplicationSettings'], 'add_parameter' => 1);
						elseif (preg_match("^(https?:\/\/)^", $listingInfo['ApplicationSettings']))
							$listingInfo['ApplicationSettings'] = array('value' => $listingInfo['ApplicationSettings'], 'add_parameter' => 2);
						else
							$listingInfo['ApplicationSettings'] = array('value' => '', 'add_parameter' => ''); //put empty if not valid email or url
						$listing->details->properties[$property->id]->type->property_info['value'] = $listingInfo['ApplicationSettings'];
					}
					elseif ($property->getType() == 'complex') {
						$childFields = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($property->sid);
						$complexChildValues = $property->value;
						foreach ($childFields as $childField) {
							if (($childField['type'] == 'complexfile') && !empty($complexChildValues[$childField['id']])) {
								$fieldInfo = SJB_ListingComplexFieldManager::getFieldInfoBySID($childField['sid']);
								if (!SJB_UploadFileManager::fileImport($listingInfo, $fieldInfo, $property->id)) {
									$isFileImported = false;
								}
							}
							if ($property->type->complex->details->properties[$childField['id']]->value == null) {
								$property->type->complex->details->properties[$childField['id']]->value = array(1 => '');
								$property->type->complex->details->properties[$childField['id']]->type->property_info['value'] = array(1 => '');
							}
						}
					}
					// The import of files at import of listings
					if (in_array($property->getType(), array('file', 'logo', 'video')) && $property->value !== '') {
						$fieldInfo = SJB_ListingFieldDBManager::getListingFieldInfoByID($property->id);
						if (!SJB_UploadFileManager::fileImport($listingInfo, $fieldInfo)) {
							$isFileImported = false;
						}
					}
				}

				if ($non_existed_values_flag == 'add') {
					$this->UpdateListValues($listing);
				}
				if ($listing->getUserSID()) {
					SJB_ListingManager::saveListing($listing);
					$listingSid = $listing->getSID();
					SJB_Statistics::addStatistics('addListing', $listing->getListingTypeSID(), $listingSid);
					SJB_ListingManager::activateListingBySID($listingSid, false);
					if (!$this->fillGallery($listingSid, $pictures)) {
						$isFileImported = false;
					}
					$addedListingsSids[] = $listingSid;
				} else {
					$nonExistentUsers[] = $listingInfo['username'];
					$count--;
				}
			}
			
			SJB_BrowseDBManager::addListings($addedListingsSids);
			SJB_ProductsManager::incrementPostingsNumber($productSID, count($addedListingsSids));

			if ($isFileImported && file_exists(SJB_System::getSystemSettings('IMPORT_FILES_DIRECTORY'))) {
				SJB_Filesystem::delete(SJB_System::getSystemSettings('IMPORT_FILES_DIRECTORY'));
			}

			$tp->assign('imported_listings_count', $count);
			$tp->assign('nonExistentUsers', $nonExistentUsers);
			$tp->display('import_listings_result.tpl');
		}
	}

	private function fillGallery($listingSid, $pictures)
	{
		$gallery = new SJB_ListingGallery();
		$gallery->setListingSID($listingSid);

		$isImportSuccessful = true;
		if (!empty($pictures)) {
			foreach ($pictures as $picture) {
				$picturePath = SJB_System::getSystemSettings("IMPORT_FILES_DIRECTORY") . '/' . $picture;
				if (file_exists($picturePath)) {
					if(!$gallery->uploadImage($picturePath, '')) {
						$isImportSuccessful = false;
					}
				}
			}
		}
		return $isImportSuccessful;
	}

	private function CreateListing($listing_info, $listing_type_id)
	{
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$listing = new SJB_Listing($listing_info, $listing_type_sid);
		$userInfo = array();
		if (!empty($listing_info['extUserID'])) {
			$userInfo = SJB_UserManager::getUserInfoByExtUserID($listing_info['extUserID'], $listing_type_id);
			if ($userInfo) {
				$listing->setUserSID($userInfo['sid']);
			}
		}
		if (!$userInfo && !empty($listing_info['username'])) {
			$userInfo = SJB_UserManager::getUserInfoByUserName($listing_info['username']);
			$listing->setUserSID($userInfo['sid']);
		}
		return $listing;
	}

	private function UpdateListValues($listing)
	{
		$list_properties = array();

		$details = $listing->getDetails();
		$properties = $details->getProperties();

		foreach ($properties as $property) {
			if ($property->getType() == 'list') {
				$list_properties[$property->getID()] = $property;
			}
		}

		$listingFieldListItemManager = new SJB_ListingFieldListItemManager();

		foreach ($list_properties as $property) {
			$property_sid = $property->getSID();
			$property_value = $property->getValue();

			if (!empty($property_value)) {
				$list_item = $listingFieldListItemManager->getListItemByValue($property->getSID(), $property->getValue());

				if (empty($list_item)) {
					$list_item = new SJB_ListItem();
					$list_item->setFieldSID($property_sid);
					$list_item->setValue($property_value);

					$listingFieldListItemManager->saveListItem($list_item);
				}
			}
		}
	}
}
