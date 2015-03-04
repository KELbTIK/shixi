<?php

class SJB_Classifieds_JobImport extends SJB_Function
{
	public function isAccessible()
	{
		if ($this->getAclRoleID()) {
			$this->setPermissionLabel('subuser_add_listings');
		}
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$listing_type_id = SJB_Request::getVar('listing_type_id', false);
		$action = SJB_Request::getVar('action', false);
		$type = SJB_Request::getVar('type', false);
		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$supportedFormats = array('xlsx','xls','csv');
		$warning = false;
		$error = '';

		if ($action == 'example' && $type) {
			$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
			$listing_field_manager = new SJB_ListingFieldManager();
			$common_details = $listing_field_manager->getCommonListingFieldsInfo();
			$extra_details = $listing_field_manager->getListingFieldsInfoByListingType($listing_type_sid);
			$listing_fields = array_merge($common_details, $extra_details);
			$directory_to_export = SJB_System::getSystemSettings('EXPORT_FILES_DIRECTORY');
			$export_properties = array();
			$export_data = array();
			foreach ($listing_fields as $listing_field) {
				$export_properties[$listing_field['id']] = $listing_field['id'];
				$export_data[0][$listing_field['id']] = '';
			}
			SJB_ExportController::createExportDirectoriesForExample();
			switch ($type) {
				case 'exl':
					SJB_ExportController::makeExportFile($export_data, 'example.xls');
					$export_files_dir = SJB_Path::combine($directory_to_export, 'example.xls');
					for ($i = 0; $i < ob_get_level(); $i++) {
						ob_end_clean();
					}
					header('Content-type: application/vnd.ms-excel');
					header('Content-disposition: attachment; filename=example.xls');
					header('Content-Length: ' . filesize($export_files_dir));
					readfile($export_files_dir);
					break;
				case 'csv':
					$export_files_dir = SJB_Path::combine($directory_to_export, 'example.csv');
					$fp = fopen($export_files_dir, 'w');
					fputcsv($fp, explode(',', implode(',', $export_properties)));
					fclose($fp);
					for ($i = 0; $i < ob_get_level(); $i++) {
						ob_end_clean();
					}
					header('Content-type: application/vnd.ms-excel');
					header('Content-disposition: attachment; filename=example.csv');
					header('Content-Length: ' . filesize($export_files_dir));
					readfile($export_files_dir);
					break;
			}
			SJB_Filesystem::delete($directory_to_export);
			exit();
		}

		if ($productsInfo = $this->canCurrentUserAddListing($error)) {
			$acl = SJB_Acl::getInstance();
			if ($acl->isAllowed('bulk_job_import') == true) {
				$fileInfo = null;
				if (isset($_FILES['import_file'])) {
					$extension = strtolower(substr(strrchr($_FILES['import_file']['name'], '.'), 1));

					if (empty($_FILES['import_file']['name']) || !in_array($extension, $supportedFormats)) {
						$warning = 'Please choose Excel or csv file';
					} else {
						$fileInfo = $_FILES['import_file'];
					}
				}
				$contractID = SJB_Request::getVar('contract_id', false);
				$current_user = SJB_UserManager::getCurrentUser();

				if ($contractID) {
					$contract = new SJB_Contract(array('contract_id' => $contractID));
				} elseif (count($productsInfo) == 1) {
					$productInfo = array_pop($productsInfo);
					$contractID = $productInfo['contract_id'];
					$contract = new SJB_Contract(array('contract_id' => $contractID));
				}
				else {
					$tp->assign("products_info", $productsInfo);
					$tp->assign("listing_type_id", $listing_type_id);
					$tp->display("listing_product_choice.tpl");
				}

				if ($contractID && $listing_type_id) {

					$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
					if ($fileInfo) {
						switch ($extension) {
							case 'xls':
							case 'xlsx':
								$import_file = new SJB_ImportFileXLS($fileInfo);
								break;
							case 'csv':
								$import_file = new SJB_ImportFileCSV($fileInfo, ',');
								break;
						}
						$import_file->parse($encodingFromCharset);
						$bulkPermissionParam = $this->acl->getPermissionParams('post_' . $listing_type_id, $contract->getID(), 'contract');
						$imported_data = $import_file->getData();
						$countData = 0;
						foreach ($imported_data as $val) {
							if ($val)
								$countData++;
						}
						if (empty($bulkPermissionParam) || ($bulkPermissionParam - $contract->getPostingsNumber() - ($countData - 1)) >= 0) {

							$listing = new SJB_Listing(array(), $listing_type_sid);
							$count = 0;
							$listingSIDs = array();
							foreach ($imported_data as $key => $importedColumn) {
								if ($key == 1) {
									$imported_data_processor = new SJB_ImportedDataProcessor($importedColumn, $listing);
									continue;
								}
								if (!$importedColumn)
									continue;

								$count++;
								$listing_info = $imported_data_processor->getData('ignore', $importedColumn);
								$doc = new DOMDocument();
								foreach ($listing->getProperties() as $property) {
									if ($property->getType() == 'complex' && !empty($listing_info[$property->id])) {
										$childFields = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($property->sid);
										$doc->loadXML($listing_info[$property->id]);
										$results = $doc->getElementsByTagName($property->id . 's');
										$listing_info[$property->id] = array();
										foreach ($results as $complexparent) {
											$i = 0;
											foreach ($complexparent->getElementsByTagName($property->id) as $result) {
												$resultXML = simplexml_import_dom($result);
												foreach ($childFields as $childField) {
													if (isset($resultXML->$childField['id']))
														$listing_info[$property->id][$childField['id']][$i] = (string)$resultXML->$childField['id'];
												}
												$i++;
											}
										}
									} elseif ($property->getType() == 'location') {
										$locationFields = array($property->id.'.Country', $property->id.'.State', $property->id.'.City', $property->id.'.ZipCode');
										$locationFieldAdded = array();
										foreach ($locationFields as $locationField) {
											if (array_key_exists($locationField, $listing_info)) {
												switch ($locationField) {
													case $property->id.'.Country':
														$value = SJB_CountriesManager::getCountrySIDByCountryName($listing_info[$locationField]);
														if (!$value) {
															$value = SJB_CountriesManager::getCountrySIDByCountryCode($listing_info[$locationField]);
														}
														break;
													case $property->id.'.State':
														$value = SJB_StatesManager::getStateSIDByStateName($listing_info[$locationField]);
														if (!$value) {
															$value = SJB_StatesManager::getStateSIDByStateCode($listing_info[$locationField]);
														}
														break;
													default:
														$value = $listing_info[$locationField];
														break;
												}
												$listing_info[$property->id][str_replace($property->id.'.', '', $locationField)] = $value;
												$locationFieldAdded[] = str_replace($property->id.'.', '', $locationField);
											}
										}
										if ($property->id == 'Location') {
											$locationFields = array('Country', 'State', 'City', 'ZipCode');
											foreach ($locationFields as $locationField) {
												if (array_key_exists($locationField, $listing_info) && !in_array($locationField, $locationFieldAdded) && !$listing->getProperty($locationField)) {
													switch ($locationField) {
														case 'Country':
															$value = SJB_CountriesManager::getCountrySIDByCountryName($listing_info[$locationField]);
															if (!$value) {
																$value = SJB_CountriesManager::getCountrySIDByCountryCode($listing_info[$locationField]);
															}
															break;
														case 'State':
															$value = SJB_StatesManager::getStateSIDByStateName($listing_info[$locationField]);
															if (!$value) {
																$value = SJB_StatesManager::getStateSIDByStateCode($listing_info[$locationField]);
															}
															break;
														default:
															$value = $listing_info[$locationField];
															break;
													}
													$listing_info[$property->id][$locationField] = $value;
												}
											}
										}
									}
								}
								$field_info = null;
								$listing = new SJB_Listing($listing_info, $listing_type_sid);

								foreach ($listing->getProperties() as $property) {
									if ($property->getType() == 'tree' && $property->value !== '') {
										$treeValues = explode(',', $property->value);
										$treeSIDs = array();
										foreach ($treeValues as $treeValue) {
											$info = SJB_ListingFieldTreeManager::getItemInfoByCaption($property->sid, trim($treeValue));
											$treeSIDs[] = $info['sid'];
										}
										$listing->setPropertyValue($property->id, implode(',', $treeSIDs));
										$listing->details->properties[$property->id]->type->property_info['value'] = implode(',', $treeSIDs);
									}
									elseif ($property->getType() == 'monetary') {
										$currency = SJB_CurrencyManager::getDefaultCurrency();
										$listing->details->properties[$property->id]->type->property_info['value']['add_parameter'] = $currency['sid'];
									}
									elseif ($property->id == 'ApplicationSettings' && !empty($listing_info['ApplicationSettings'])) {
										if (preg_match("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,}\$^iu", $listing_info['ApplicationSettings']))
											$listing_info['ApplicationSettings'] = array('value' => $listing_info['ApplicationSettings'], 'add_parameter' => 1);
										elseif (preg_match("^(https?:\/\/)^iu", $listing_info['ApplicationSettings']))
											$listing_info['ApplicationSettings'] = array('value' => $listing_info['ApplicationSettings'], 'add_parameter' => 2);
										else
											$listing_info['ApplicationSettings'] = array('value' => '', 'add_parameter' => '');
										$listing->details->properties[$property->id]->type->property_info['value'] = $listing_info['ApplicationSettings'];
									}
									elseif ($property->getType() == 'complex' && is_array($property->value)) {
										$childFields = SJB_ListingComplexFieldManager::getListingFieldsInfoByParentSID($property->sid);
										$complexChildValues = $property->value;
										foreach ($childFields as $childField) {
											if (($childField['type'] == 'complexfile') && !empty($complexChildValues[$childField['id']])) {
												$field_info = SJB_ListingComplexFieldManager::getFieldInfoBySID($childField['sid']);
												if (isset($listing_info[$property->id][$field_info['id']]) && file_exists($listing_info[$property->id][$field_info['id']]))
													SJB_UploadFileManager::fileImport($listing_info, $field_info, $property->id);
											}
										}
									}
									// The import of files at import of listings
									if (in_array($property->getType(), array('file', 'logo', 'picture', 'video')) && $property->value !== '') {
										$field_info = SJB_ListingFieldDBManager::getListingFieldInfoByID($property->id);
										if (isset($listing_info[$field_info['id']]) && file_exists($listing_info[$field_info['id']]))
											SJB_UploadFileManager::fileImport($listing_info, $field_info);
									}
								}

								$listing->deleteProperty('featured');
								$listing->deleteProperty('priority');
								$listing->deleteProperty('status');
								$listing->deleteProperty('reject_reason');
								$listing->addProperty(
									array('id' => 'contract_id',
										'type' => 'id',
										'value' => $contract->getID(),
										'is_system' => true));
								$extraInfo = $contract->extra_info;
								$listing->setProductInfo($extraInfo);
								$listing->setPropertyValue('access_type', 'everyone');
								$listing->setUserSID($current_user->sid);
								if ($current_user->isSubuser()) {
									$subuserInfo = $current_user->getSubuserInfo();
									$listing->addSubuserProperty($subuserInfo['sid']);
								}
								SJB_ListingManager::saveListing($listing);
								SJB_Statistics::addStatistics('addListing', $listing->getListingTypeSID(), $listing->getSID(), false, $extraInfo['featured'], $extraInfo['priority']);
								$contract->incrementPostingsNumber();
								SJB_ProductsManager::incrementPostingsNumber($contract->product_sid);
								if (!empty($extraInfo['featured']))
									SJB_ListingManager::makeFeaturedBySID($listing->getSID());
								if (!empty($extraInfo['priority']))
									SJB_ListingManager::makePriorityBySID($listing->getSID());

								$this->FillGallery($listing, $listing_info);

								$listingSIDs[] = $listing->getSID();
							}
							SJB_ListingManager::activateListingBySID($listingSIDs);
							$tp->assign('listingsNum', count($listingSIDs));
							$tp->display('job_import_complete.tpl');
						}
						else {
							$tp->assign('charSets', SJB_HelperFunctions::getCharSets());
							$error = 'LISTINGS_NUMBER_LIMIT_EXCEEDED';
							$tp->assign('listing_type_id', $listing_type_id);
							$tp->assign('error', $error);
							$tp->display('job_import.tpl');
						}
					}
					else {
						$tp->assign('charSets', SJB_HelperFunctions::getCharSets());
						$tp->assign('warning', $warning);
						$tp->assign('contract_id', $contractID);
						$tp->assign('listing_type_id', $listing_type_id);
						$tp->display('job_import.tpl');
					}
				}
			} else {
				$error = $acl->getPermissionMessage('bulk_job_import');
				if (empty($error)) {
					$error = 'This action is not allowed within your current product';
				}
				$tp->assign('error', $error);
				$tp->assign('charSets', SJB_HelperFunctions::getCharSets());
				$tp->assign('listing_type_id', $listing_type_id);
				$tp->display('job_import.tpl');
			}
		}
		else {
			if ($error == 'NO_CONTRACT') {
				if ($_GET) {
					$getParam = '?';
					foreach ($_GET as $key => $val)
						$getParam .= $key . '=' . $val . '&';
					$getParam = substr($getParam, 0, -1);
				}
				$page = base64_encode(SJB_System::getURI() . $getParam);
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/my-products/?page=' . $page);
			}

			$tp->assign('charSets', SJB_HelperFunctions::getCharSets());
			$tp->assign('listing_type_id', $listing_type_id);
			$tp->assign('error', $error);
			$tp->display('job_import.tpl');
		}
	}

	private function canCurrentUserAddListing(& $error)
	{
		if (SJB_UserManager::isUserLoggedIn()) {
			$current_user = SJB_UserManager::getCurrentUser();
			if ($current_user->hasContract()) {
				$listing_type_id = SJB_Request::getVar('listing_type_id', false);
				$contracts_id = $current_user->getContractID();
				$contractsSIDs = $contracts_id ? implode(',', $contracts_id) : 0;
				$resultContractInfo = SJB_DB::query("SELECT `id`, `product_sid`, `expired_date`, `number_of_postings` FROM `contracts` WHERE `id` in ({$contractsSIDs}) ORDER BY `expired_date` DESC");
				$PlanAcces = count($resultContractInfo) > 0 ? true : false;
				if ($PlanAcces && $this->acl->isAllowed('post_' . $listing_type_id)) {
					$productsInfo = array();
					$is_contract = false;
					foreach ($resultContractInfo as $contractInfo) {
						if ($this->acl->isAllowed('post_' . $listing_type_id, $contractInfo['id'], 'contract')) {
							$permissionParam = $this->acl->getPermissionParams('post_' . $listing_type_id, $contractInfo['id'], 'contract');
							if (empty($permissionParam) || $this->acl->getPermissionParams('post_' . $listing_type_id, $contractInfo['id'], 'contract') > $contractInfo['number_of_postings']) {
								$product = SJB_ProductsManager::getProductInfoBySID($contractInfo['product_sid']);
								$productsInfo[$contractInfo['id']]['product_name'] = $product['name'];
								$productsInfo[$contractInfo['id']]['expired_date'] = $contractInfo['expired_date'];
								$productsInfo[$contractInfo['id']]['contract_id'] = $contractInfo['id'];
							}
						}
						$is_contract = true;
					}

					if ($is_contract && count($productsInfo) > 0)
						return $productsInfo;
					else
						$error = 'LISTINGS_NUMBER_LIMIT_EXCEEDED';
				}
				else
					$error = 'DO_NOT_MATCH_POST_THIS_TYPE_LISTING';
			}
			else
				$error = 'NO_CONTRACT';
		}
		else
			$error = 'NOT_LOGGED_IN';
		return false;
	}

	private function FillGallery($listing, $listing_info)
	{
		$gallery = new SJB_ListingGallery();
		$gallery->setListingSID($listing->getSID());

		if (!empty($listing_info['pictures'])) {
			foreach ($listing_info['pictures'] as $picture)
				$gallery->uploadImage($picture, '');
		}
	}
}