<?php

class SJB_Classifieds_DisplayListing extends SJB_Function
{
	public function isAccessible()
	{
		$listingTypeID = SJB_Array::get($this->params, 'listing_type_id');

		if ($listingTypeID) {
			$permissionLabel = 'view_' . strtolower($listingTypeID) . '_details';
			$this->setPermissionLabel($permissionLabel);
			$allow = parent::isAccessible() && SJB_System::isUserAccessThisPage();
			$listingID = SJB_Request::getVar('listing_id', false);
			$passedParametersViaUri = SJB_Request::getVar('passed_parameters_via_uri', false);
			if (!$listingID && $passedParametersViaUri) {
				$passedParametersViaUri = SJB_UrlParamProvider::getParams();
				if (isset($passedParametersViaUri[0])) {
					$listingID = $passedParametersViaUri[0];
				}
			}
			if (SJB_UserManager::isUserLoggedIn()) {
				$currentUser = SJB_UserManager::getCurrentUser();
				if (!$allow && $listingID) {
					$pageID = SJB_PageManager::getPageParentURI(SJB_Navigator::getURI(), SJB_System::getSystemSettings('SYSTEM_ACCESS_TYPE'), false);
					$pageHasBeenVisited = SJB_ContractManager::isPageViewed($currentUser->getSID(), $pageID, $listingID);
					if ($pageHasBeenVisited || strpos($pageID, 'print') !== false) {
						$allow = true;
					}
				}
				if (!$allow && 'Resume' == $listingTypeID && $listingID) {
					// if view resume not allowed by ACL, check applications table
					// for current resume ID, applied for one of current user jobs
					// if present in applications - allow current user to view resume
					// check for all jobs of current user
					$cuJobs = SJB_ListingManager::getListingsByUserSID($currentUser->getSID());
					$listingSids = array();
					foreach ($cuJobs as $job) {
						$listingSids[] = $job->getSID();
					}
					if (!empty($listingSids)) {
						$result = SJB_DB::query('SELECT * FROM `applications` WHERE `resume` = ?n AND `listing_id` IN (?l) LIMIT 1', $listingID, $listingSids);
						if (!empty($result)) {
							$allow = true;
						}
					}
				}
			}
			return $allow;
		}

		return parent::isAccessible() && SJB_System::isUserAccessThisPage();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();
		$display_form = new SJB_Form();
		$display_form->registerTags($tp);
		$current_user = SJB_UserManager::getCurrentUser();
		$errors = array();
		$template = SJB_Request::getVar('display_template', 'display_listing.tpl');
		$tcpdfError = SJB_Request::getVar('error', false);
		$action = substr($template, 0, -4);
		$listing_id = SJB_Request::getVar("listing_id");
		if (isset($_REQUEST['passed_parameters_via_uri'])) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listing_id = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
		}

		if (is_null($listing_id) && SJB_FormBuilderManager::getIfBuilderModeIsSet()) {
			$listing_type_id = SJB_Request::getVar('listing_type_id');
			$listing_id = SJB_ListingManager::getListingIDByListingTypeID($listing_type_id);
		}

		if (is_null($listing_id)) {
			$errors['UNDEFINED_LISTING_ID'] = true;
		}
		elseif (is_null($listing = SJB_ListingManager::getObjectBySID($listing_id)) || !SJB_ListingManager::isListingAccessableByUser($listing_id, SJB_UserManager::getCurrentUserSID())) {
			$errors['WRONG_LISTING_ID_SPECIFIED'] = true;
		}
		elseif (!$listing->isActive() && $listing->getUserSID() != SJB_UserManager::getCurrentUserSID()) {
			$errors['LISTING_IS_NOT_ACTIVE'] = true;
		}
		elseif (($listingStatus = SJB_ListingManager::getListingApprovalStatusBySID($listing_id)) != 'approved' && SJB_ListingTypeManager::getWaitApproveSettingByListingType($listing->listing_type_sid) == 1 && $listing->getUserSID() != SJB_UserManager::getCurrentUserSID()) {
			$errors['LISTING_IS_NOT_APPROVED'] = true;
		}
		elseif ((SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid) == 'Resume' && ($template == 'display_job.tpl' OR SJB_System::getURI() == '/print-job/')) ||
				(SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid) == 'Job' && ($template == 'display_resume.tpl' OR SJB_System::getURI() == '/print-resume/'))
		) {
			$errors['WRONG_DISPLAY_TEMPLATE'] = true;
		} else {

			$listing_type_id = SJB_ListingTypeManager::getListingTypeIDBySID($listing->listing_type_sid);
			if (SJB_System::getURI() == '/print-listing/') {
				SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/print-' . strtolower($listing_type_id) . '/?listing_id=' . $listing_id);
				exit;
			}

			$listing->addPicturesProperty();

			$display_form = new SJB_Form($listing);

			$display_form->registerTags($tp);

			$form_fields = $display_form->getFormFieldsInfo();

			$listingOwner = SJB_UserManager::getObjectBySID($listing->user_sid);

			if ($action !== 'print_listing')
				SJB_ListingManager::incrementViewsCounterForListing($listing_id, $listing);
			$listing_structure = SJB_ListingManager::createTemplateStructureForListing($listing, array('comments', 'ratings'));
			$filename = SJB_Request::getVar('filename', false);
			if ($filename) {
				$file = SJB_UploadFileManager::openFile($filename, $listing_id);
				$errors['NO_SUCH_FILE'] = true;
			}

			$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
			$tp->assign(
				"METADATA", array(
				"listing" => $metaDataProvider->getMetaData($listing_structure['METADATA']),
				"form_fields" => $metaDataProvider->getFormFieldsMetadata($form_fields)));

			$comments = array();
			$comments_total = '';

			if (SJB_Settings::getSettingByName('show_comments') == '1') {
				$comments = SJB_CommentManager::getEnabledCommentsToListing($listing_id);
				$comments_total = count($comments);
			}

			$searchId = SJB_Request::getVar("searchId", "");
			$page = SJB_Request::getVar("page", "");
			$criteria_saver = new SJB_ListingCriteriaSaver($searchId);
			$searchCriteria = $criteria_saver->getCriteria();
			$keywordsHighlight = '';
			if (isset($searchCriteria['keywords']) && SJB_System::getSettingByName('use_highlight_for_keywords')) {
				foreach ($searchCriteria['keywords'] as $type => $keywords) {
					switch ($type) {
						case 'like':
						case 'exact_phrase':
							$keywordsHighlight = json_encode($keywords);
							break;
						case 'all_words':
						case 'any_words':
							$keywordsHighlight = json_encode(explode(' ', $keywords));
							break;
						case 'boolean':
							$keywordsHighlight = json_encode(SJB_BooleanEvaluator::parse($keywords, true));
							break;
					}
				}
			}
			$prevNextIds = $criteria_saver->getPreviousAndNextObjectID($listing_id);
			$search_criteria_structure = $criteria_saver->createTemplateStructureForCriteria();

			//permissions contact info
			$acl = SJB_Acl::getInstance();

			$permission = 'view_' . $listing_type_id . '_contact_info';
			$allowViewContactInfo = false;
			if (SJB_UserManager::isUserLoggedIn()) {
				if (SJB_ContractManager::isPageViewed($current_user->getSID(), $permission, $listing_id) || ($acl->isAllowed($permission) && in_array($acl->getPermissionParams($permission), array('', '0')))) {
					$allowViewContactInfo = true;
				}
				elseif ($acl->isAllowed($permission)) {
					$viewContactInfo['count_views'] = 0;
					$contractIDs = $current_user->getContractID();
					$numberOfContactViewed = SJB_ContractManager::getNumbeOfPagesViewed($current_user->getSID(), $contractIDs, $permission);
					foreach ($contractIDs as $contractID) {
						if ($acl->getPermissionParams($permission, $contractID, 'contract')) {
							$params = $acl->getPermissionParams($permission, $contractID, 'contract');
							$viewsLeft = SJB_ContractManager::getNumbeOfPagesViewed($current_user->getSID(), array($contractID), $permission);
							if (isset($viewContactInfo['count_views']) && is_numeric($params)) {
								$viewContactInfo['count_views'] += $params;
								if ($params > $viewsLeft) {
									$viewContactInfo['contract_id'] = $contractID;
								}
							}
						}
					}
					if ($viewContactInfo && $viewContactInfo['count_views'] > $numberOfContactViewed) {
						$allowViewContactInfo = true;
						SJB_ContractManager::addViewPage($current_user->getSID(), $permission, $listing_id, $viewContactInfo['contract_id'], $listing->getListingTypeSID());
					}
				}

				$user_group_id = SJB_UserGroupManager::getUserGroupIDBySID($current_user->getUserGroupSID());
				if ($allowViewContactInfo && $user_group_id == 'JobSeeker' && $listing_type_id == 'Job') {
					SJB_UserManager::saveRecentlyViewedListings($current_user->getSID(), $listing_id);
				}
			}
			elseif ($acl->isAllowed($permission)) {
				$allowViewContactInfo = true;
			}

			$tp->assign("keywordsHighlight", $keywordsHighlight);
			$tp->assign('allowViewContactInfo', $allowViewContactInfo);
			$tp->assign('show_rates', SJB_Settings::getSettingByName('show_rates'));
			$tp->assign("isApplied", SJB_Applications::isApplied($listing_id, SJB_UserManager::getCurrentUserSID()));
			$tp->assign('show_rates', SJB_Settings::getSettingByName('show_rates'));
			$tp->assign('show_comments', SJB_Settings::getSettingByName('show_comments'));
			$tp->assign('comments', $comments);
			$tp->assign('comments_total', $comments_total);
			$tp->assign('listing_id', $listing_id);
			$tp->assign("form_fields", $form_fields);
			$tp->assign('video_fields', SJB_HelperFunctions::takeMediaFields($form_fields));
			$tp->assign('uri', base64_encode(SJB_Navigator::getURIThis()));
			$tp->assign('listingOwner', $listingOwner);
			$listing_structure = SJB_ListingManager::newValueFromSearchCriteria($listing_structure, $criteria_saver->criteria);

			// SJB-1197: ajax autoupload.
			// Fix to view video from temporary uploaded storage.
			$sessionFilesStorage = SJB_Session::getValue('tmp_uploads_storage');

			// NEED TO CHECK FOR COMPLEX PARENT AND COMPLEX STEP PARAMETERS!
			$complexParent = SJB_Request::getVar('complexParent');
			$complexStep   = SJB_Request::getVar('complexEnum');
			$fieldId       = SJB_Request::getVar('field_id');
			$isComplex     = false;
			if ($complexParent && $complexStep) {
				$fieldId   = $complexParent . ":" . $fieldId . ":" . $complexStep;
				$isComplex = true;
			}
			$tempFileValue = SJB_Array::getPath($sessionFilesStorage, "listings/{$listing_id}/{$fieldId}");

			if ($isComplex) {
				$uploadFileManager = new SJB_UploadFileManager();
				$fileLink = $uploadFileManager->getUploadedFileLink($tempFileValue['file_id']);

				$tp->assign('videoFileLink', $fileLink);
			} else {
				if (!empty($tempFileValue)) {
					$fileUniqueId = isset($tempFileValue['file_id']) ? $tempFileValue['file_id'] : '';
					if (!empty($fileUniqueId)) {
						$upload_manager = new SJB_UploadFileManager();

						// file structure for videoplayer
						$fileInfo = array(
								'file_url'        => $upload_manager->getUploadedFileLink($fileUniqueId),
								'file_name'       => $upload_manager->getUploadedFileName($fileUniqueId),
								'saved_file_name' => $upload_manager->getUploadedSavedFileName($fileUniqueId),
								'file_id'         => $fileUniqueId,
						);
						$listing_structure[$fieldId] = $fileInfo;
					}
				}
			}
			// SJB-1197

			// GOOGLE MAP SEARCH RESULTS CUSTOMIZATION
			$zipCode = '';
			if (!empty($listing_structure['Location']['ZipCode']))
				$zipCode = $listing_structure['Location']['ZipCode'];
			// get 'latitude' and 'longitude' from zipCode field, if it not set
			$latitude  = isset($listing_structure['latitude']) ? $listing_structure['latitude'] : '';
			$longitude = isset($listing_structure['longitude']) ? $listing_structure['longitude'] : '';
			if (!empty($zipCode) && empty($latitude) && empty($longitude)) {
				$result = SJB_DB::query("SELECT * FROM `locations` WHERE `name` = ?s LIMIT 1", $zipCode);
				if ($result) {
					$listing_structure['latitude']  = $result[0]['latitude'];
					$listing_structure['longitude'] = $result[0]['longitude'];
				}
			} elseif (!empty($listing_structure['Location']['City']) && !empty($listing_structure['Location']['State']) && !empty($listing_structure['Location']['Country'])) {
				$address = $listing_structure['Location']['City'].', '.$listing_structure['Location']['State'].', '.$listing_structure['Location']['Country'];
				$address = urlencode($address);
				$cache = SJB_Cache::getInstance();
				$parameters = array(
					'City'    => $listing_structure['Location']['City'],
					'State'   => $listing_structure['Location']['State'],
					'Country' => $listing_structure['Location']['Country']
				);
				$hash = md5('google_map'.serialize($parameters));
				$data = $cache->load($hash);
				$geoCod = '';
				if (!$data) {
					try {
						$geoCod = SJB_HelperFunctions::getUrlContentByCurl("http://maps.googleapis.com/maps/api/geocode/json?address={$address}&sensor=false");
						$geoCod = json_decode($geoCod);
						if ($geoCod->status == 'OK') {
							$cache->save($geoCod, $hash);
						}
					}
					catch (Exception $e) {
						$backtrace = SJB_Logger::getBackTrace();
						SJB_Error::writeToLog(array(( array('level' => 'E_USER_WARNING', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'backtrace' => sprintf("BACKTRACE:\n [%s]", join("<br/>\n", $backtrace)) ))));
					}
				} else {
					$geoCod = $data;
				}
				try {
					if (!is_object($geoCod)) {
						throw new Exception("Map object nave not been Created");
					}
					if ($geoCod->status !== 'OK') {
						throw new Exception("Status is not OK");
					}
					$location = $geoCod->results[0]->geometry->location;
					$listing_structure['latitude'] = $location->lat;
					$listing_structure['longitude'] = $location->lng;
				} catch (Exception $e) {
					$backtrace = SJB_Logger::getBackTrace();
					SJB_Error::writeToLog(array(( array('level' => 'E_USER_WARNING', 'message' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine(), 'backtrace' => sprintf("BACKTRACE:\n [%s]", join("<br/>\n", $backtrace)) ))));
				}
			}
			if (SJB_Request::getVar('view')) {
				$tp->assign('listings', array($listing_structure));
			}

			$tp->filterThenAssign("listing", $listing_structure);
			$tp->assign("prev_next_ids", $prevNextIds);
			$tp->assign("searchId", $searchId);
			$tp->assign("page", $page);
			$tp->filterThenAssign("search_criteria", $search_criteria_structure);
			$tp->filterThenAssign("search_uri", $criteria_saver->getUri());

			if ($field_id = SJB_Request::getVar('field_id')) {
				// SJB-825
				$complexEnum = SJB_Request::getVar('complexEnum', null, 'GET');
				$complexFieldID = SJB_Request::getVar('complexParent', null, 'GET');

				if (!is_null($complexEnum) && !is_null($complexFieldID)) {
					$videoFileID = $complexFieldID . ':' . $field_id . ':' . $complexEnum . '_' . $listing_id;
					$videoFileLink = SJB_UploadFileManager::getUploadedFileLink($videoFileID);
					if ($videoFileLink)
						$tp->assign('videoFileLink', $videoFileLink);
				}
				// SJB-825
				$tp->assign('field_id', $field_id);
			}
			else {
				if (SJB_Request::getVar('action', false) == 'download_pdf_version') {
					$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_PDF, $listing_type_id);
					$formBuilder->setChargedTemplateProcessor($tp);
					$tpl = 'resume_to_pdf.tpl';
					if ($listing_structure['anonymous'] == '1') {
						$filename = 'Anonymous User_' . $listing_structure['Title'] . '.pdf';
					} else {
						$filename = $listing_structure['user']['FirstName'] . ' ' . $listing_structure['user']['LastName'] . '_' . $listing_structure['Title'] . '.pdf';
					}
					try {
						$html = $tp->fetch($tpl);
						$html = preg_replace('/<div[^>]*>/', '', $html);
						$html = str_replace('</div>', '', $html);
						SJB_HelperFunctions::html2pdf($html, $filename, str_replace('http://', '' , SJB_HelperFunctions::getSiteUrl()));
						exit();
					} catch (Exception $e) {
						SJB_Error::writeToLog($e->getMessage());
						SJB_HelperFunctions::redirect(SJB_System::getSystemSettings("SITE_URL") . '/display-resume/' . $listing_id . '/?error=TCPDF_ERROR');
					}
				} else {
					$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_DISPLAY, $listing_type_id);
					$formBuilder->setChargedTemplateProcessor($tp);
				}
			}
		}

		if ($errors) {
			foreach ($errors as $k => $v) {
				switch ($k) {
					case 'TCPDF_ERROR':
					case 'UNDEFINED_LISTING_ID':
					case 'WRONG_LISTING_ID_SPECIFIED':
					case 'LISTING_IS_NOT_ACTIVE':
					case 'LISTING_IS_NOT_APPROVED':
						$header = $_SERVER['SERVER_PROTOCOL'] . ' 404  Not Found';
						$header_status = "Status: 404  Not Found";
						header($header_status);
						header($header);
						SJB_System::setGlobalTemplateVariable('page_not_found', true);
						break;
				}
			}
		}
		$tp->assign('errors', $errors);
		$tp->assign('tcpdfError', $tcpdfError);
		$tp->display($template);
	}
}
