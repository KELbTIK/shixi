<?php

class SJB_Classifieds_SavedSearches extends SJB_Function
{
	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		if (SJB_UserManager::isUserLoggedIn()) {

			$current_user = SJB_UserManager::getCurrentUser();
			if ($current_user->isSubuser()) // У саб-юзера должны быть свои алерты
				$current_user = $current_user->getSubuserInfo();
			else
				$current_user = SJB_UserManager::getCurrentUserInfo();

			$listing_type_id = '';
			/************************************************************/

			$tp = SJB_System::getTemplateProcessor();
			$tp->assign('action', 'list');
			$errors = array();
			$redirectUri = '/saved-searches/';

			if (isset($_REQUEST['is_alert'])) {
				if (isset($_REQUEST['listing_type_id'])) {
					$listing_type_id = $_REQUEST['listing_type_id'];
					SJB_Session::setValue('listing_type_id', $listing_type_id);
				}
				elseif (isset($_REQUEST['restore']))
					$listing_type_id = SJB_Session::getValue('listing_type_id');
				else
					SJB_Session::setValue('listing_type_id', null);

				if (!SJB_Acl::getInstance()->isAllowed("use_{$listing_type_id}_alerts")) {
					$errors = array('NOT_SUBSCRIBE' => true);
					$tp->assign('ERRORS', $errors);
					$tp->display('error.tpl');
					return;
				} else {
					$redirectUri = '/' . strtolower($listing_type_id) . '-alerts/';
				}
			}
			else {
				if (isset($_REQUEST['listing_type_id']))
					$listing_type_id = $_REQUEST['listing_type_id'];

				if (!SJB_Acl::getInstance()->isAllowed('save_searches')) {
					$errors = array('NOT_SUBSCRIBE' => true);
					$tp->assign('ERRORS', $errors);
					$tp->display('error.tpl');
					return;
				}
			}
			$isSubmittedForm = SJB_Request::getVar('submit', false);

			$listing_type_sid = !empty($listing_type_id) ? SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id) : 0;

			if (!isset($_REQUEST['listing_type']['equal']) && isset($listing_type_id))
				$_REQUEST['listing_type']['equal'] = $listing_type_id;

			$action = SJB_Request::getVar('action', 'list');

			switch ($action) {

				case 'save':
					if ($isSubmittedForm) {
						$search_name = SJB_Request::getVar('name');
						$emailFrequency = SJB_Request::getVar('email_frequency');
						if (empty($search_name['equal'])) {
							$errors['EMPTY_VALUE'] = 1;
							$tp->assign('action', 'save');
						} else {
							unset($_REQUEST['name']);
							unset($_REQUEST['email_frequency']);
							if ($emailFrequency) {
								$emailFrequency = array_pop($emailFrequency);
								$emailFrequency = '&email_frequency=' . array_pop($emailFrequency);
							} else {
								$emailFrequency = '';
							}
							$search_name = $search_name['equal'];
							$searchResultsTP = new SJB_SearchResultsTP($_REQUEST, $listing_type_id);
							$tp = $searchResultsTP->getChargedTemplateProcessor();
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . '/save-search/?alert=true&url=' . $redirectUri . '&action=save&search_name=' . $search_name . '&searchId=' . $searchResultsTP->searchId . $emailFrequency);
						}
					} else {
						$tp->assign('action', 'save');
					}
					break;

				case 'edit':
					if (isset($_REQUEST['id_saved'])) {
						if ($isSubmittedForm) {
							$id_saved = $_REQUEST['id_saved'];
							$name = $_REQUEST['name'];
							$search_name = SJB_Request::getVar('name');
							$emailFrequency = SJB_Request::getVar('email_frequency');
							if (empty($search_name['equal'])) {
								$errors['EMPTY_VALUE'] = 1;
							} else {
								unset($_REQUEST['name']);
								unset($_REQUEST['email_frequency']);
								if ($emailFrequency) {
									$emailFrequency = array_pop($emailFrequency);
									$emailFrequency = array_pop($emailFrequency);
								} else {
									$emailFrequency = 'daily';
								}
								$searchResultsTP = new SJB_SearchResultsTP($_REQUEST, $listing_type_id);
								$tp = $searchResultsTP->getChargedTemplateProcessor();
								$criteria_saver = new SJB_ListingCriteriaSaver($searchResultsTP->searchId);
								$requested_data = $criteria_saver->getCriteria();
								SJB_SavedSearches::updateSearchOnDB($requested_data, $id_saved, $current_user['sid'], $name['equal'], $emailFrequency);
							}
							if (!empty($errors)) {
								$tp->assign('action', 'edit');
								$tp->assign('id_saved', $_REQUEST['id_saved']);
							} else {
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . $redirectUri . '?alert=is_update');
							}
						} else {
							$tp->assign('action', 'edit');
							$tp->assign('id_saved', $_REQUEST['id_saved']);
						}
					}
					break;

				case 'edit_alert':
					$tp->assign('action', 'edit');
					$tp->assign('id_saved', $_REQUEST['id_saved']);
					break;

				case 'edit_search':
					$tp->assign('action', 'edit');
					$tp->assign('id_saved', $_REQUEST['id_saved']);
					$_REQUEST['form_template'] = SJB_Request::getVar('formTemplateNem');
					break;

				case 'new':
					$tp->assign('action', 'save');
					break;

				case 'delete';
					if (isset($_REQUEST['search_id'])) {
						$search_id = $_REQUEST['search_id'];
						SJB_SavedSearches::deleteSearchFromDBBySID($search_id);
					}
					break;

				case 'disable_notify':
					if (isset($_REQUEST['search_id']))
						SJB_SavedSearches::disableSearchAutoNotify($current_user['sid'], $_REQUEST['search_id']);
					break;

				case 'enable_notify':
					if (isset($_REQUEST['search_id']))
						SJB_SavedSearches::enableSearchAutoNotify($current_user['sid'], $_REQUEST['search_id']);
					break;
			}
			if ($action != 'new' && $action != 'edit_alert') {
				$saved_searches = SJB_SavedSearches::getSavedSearchesFromDB($current_user['sid']);
				if (isset($_REQUEST['is_alert']))
					$saved_searches = SJB_SavedSearches::getSavedJobAlertFromDB($current_user['sid']);
				foreach ($saved_searches as $key => $saved_search)
				{
					$saved_searches[$key]['data'] = SJB_SavedSearches::buildCriteriaFields($saved_search['data']);
					if (isset($saved_search['data']['listing_type']['equal']))
						$saved_searches[$key]['listing_type'] = $saved_search['data']['listing_type']['equal'];
				}
				$tp->assign('saved_searches', $saved_searches);
			}

			$listing = new SJB_Listing(array(), $listing_type_sid);
			$listing->addIDProperty();
			$listing->addActivationDateProperty();
			$listing->addUsernameProperty();
			$listing->addKeywordsProperty();
			$listing->addPicturesProperty();
			$listing->addEmailFrequencyProperty();
			$listing->addListingTypeIDProperty();
			$listing->addPostedWithinProperty();

			$search_form_builder = new SJB_SearchFormBuilder($listing);

			$criteria = SJB_SearchFormBuilder::extractCriteriaFromRequestData($_REQUEST);

			$search_form_builder->setCriteria($criteria);
			$search_form_builder->registerTags($tp);

			$form_fields = $search_form_builder->getFormFieldsInfo();

			$tp->assign('form_fields', $form_fields);
			if (!empty($_REQUEST['name']))
				$tp->assign('search_name', $_REQUEST['name']);
			if (!empty($_REQUEST['email_frequency']))
				$tp->assign('email_frequency', $_REQUEST['email_frequency']);
			$metaDataProvider = SJB_ObjectMother::getMetaDataProvider();
			$tp->assign('METADATA', array(
				'form_fields' => $metaDataProvider->getFormFieldsMetadata($form_fields)));

			$form_template = SJB_Request::getVar('form_template', 'search_form.tpl');
			switch (SJB_Request::getVar('alert')) {
				case 'added':
					$tp->assign('alert_added', 'added');
					break;
				case 'is_update':
					$tp->assign('alert_update', 'update');
					break;
			}

			if (!$listing_type_id && isset($saved_search['data']['listing_type']['equal']))
				$listing_type_id = $saved_search['data']['listing_type']['equal'];

			$tp->assign('errors', $errors);
			$tp->assign('user_logged_in', true);
			$tp->assign('listing_type_id', $listing_type_id);

			$formBuilder = SJB_FormBuilderManager::getFormBuilder(SJB_FormBuilderManager::FORM_BUILDER_TYPE_SEARCH, $listing_type_id );
			$formBuilder->setChargedTemplateProcessor($tp);

			$tp->display($form_template);
		}
		else {
			$tp->assign("ERROR", "NOT_LOGIN");
			$tp->display("../miscellaneous/error.tpl");
			return;
		}

	}
}
