<?php
class SJB_Admin_Classifieds_PostingPages extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('set_posting_pages');
		return parent::isAccessible();
	}

	public function execute()
	{
		$tp = SJB_System::getTemplateProcessor();

		$passed_parameters_via_uri = SJB_Request::getVar('passed_parameters_via_uri', false);
		$listing_type_id = '';
		$action = SJB_Request::getVar('action', 'list');
		$pageSID = SJB_Request::getVar('page_sid', 0);

		if ($passed_parameters_via_uri) {
			$passed_parameters_via_uri = SJB_UrlParamProvider::getParams();
			$listing_type_id = isset($passed_parameters_via_uri[0]) ? $passed_parameters_via_uri[0] : null;
			$action = isset($passed_parameters_via_uri[1]) ? $passed_parameters_via_uri[1] : $action;
			$pageSID = isset($passed_parameters_via_uri[2]) ? $passed_parameters_via_uri[2] : $pageSID;
		}
		$listing_type_sid = SJB_ListingTypeManager::getListingTypeSIDByID($listing_type_id);
		$submit = SJB_Request::getVar('submit', false);
		$errors = array();
		$template = 'posting_pages.tpl';

		if ($listing_type_sid) {
			$listingTypeInfo = SJB_ListingTypeManager::getListingTypeInfoBySID($listing_type_sid);
			switch ($action) {
				case 'new':
					$page = new SJB_PostingPages($_REQUEST, $listing_type_sid);
					$form = new SJB_Form($page);
					$form->registerTags($tp);
					$form_fields = $form->getFormFieldsInfo();
					if ($submit) {
						$addValidParam = array('field' => 'listing_type_sid', 'value' => $listing_type_sid);
						if ($form->isDataValid($errors, $addValidParam)) {
							SJB_PostingPagesManager::savePage($page);
							SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/posting-pages/" . strtolower($listingTypeInfo['id']) . "/added");
						}
					}
					$tp->assign("form_fields", $form_fields);
					$template = 'input_page_form.tpl';
					break;
				case 'edit':
					$template = 'input_page_form.tpl';
					$field_action = SJB_Request::getVar('field_action');
					$pageInfo = SJB_PostingPagesManager::getPageInfoBySID($pageSID);
					$pageInfo = array_merge($pageInfo, $_REQUEST);
					$page = new SJB_PostingPages($pageInfo, $listing_type_sid);
					$page->setSID($pageSID);
					$form = new SJB_Form($page);
					$form->registerTags($tp);
					$form_fields = $form->getFormFieldsInfo();
					if ($submit) {
						$addValidParam = array('field' => 'listing_type_sid', 'value' => $listing_type_sid);
						if ($form->isDataValid($errors, $addValidParam)) {
							SJB_PostingPagesManager::savePage($page);
							$pageInfo = SJB_PostingPagesManager::getPageInfoBySID($pageSID);
							if ($submit == 'save')
								SJB_HelperFunctions::redirect(SJB_System::getSystemSettings('SITE_URL') . "/posting-pages/" . $listing_type_id . "/");
						}
					}
					//echo "field_action: $field_action<br>"; exit;
					switch ($field_action) {
						case 'add_fields':
							$listing_fields = SJB_Request::getVar('listing_fields', array());
							foreach ($listing_fields as $listing_field) {
								SJB_PostingPagesManager::addListingFieldOnPage($listing_field, $pageSID, $listing_type_sid);
							}
							break;
						case 'move_down':
							$field_sid = SJB_Request::getVar('field_sid', null);
							SJB_PostingPagesManager::moveDownFieldBySID($field_sid, $pageSID);
							break;
						case 'move_up':
							$field_sid = SJB_Request::getVar('field_sid', null);
							SJB_PostingPagesManager::moveUpFieldBySID($field_sid, $pageSID);
							break;
						case 'remove':
							SJB_PostingPagesManager::removeFieldFromPageById(SJB_Request::getVar('relationId', null), $listing_type_sid);
							break;
						case 'move':
							$field_sid = SJB_Request::getVar('field_sid', null);
							$movePageID = SJB_Request::getVar('movePageID', false);
							if ($movePageID !== false)
								SJB_PostingPagesManager::moveFieldToPade($field_sid, $movePageID, $listing_type_sid);
							$template = 'move_field.tpl';
							break;
						case 'save_order':
							$item_order = SJB_Request::getVar('item_order', null);
							SJB_PostingPagesManager::saveNewJobFieldsOrder($item_order, $pageSID);
							break;
					}

					$listing_fields = SJB_PostingPagesManager::getListingFieldsInfo($listing_type_sid);
					$fieldsOnPage = SJB_PostingPagesManager::getAllFieldsByPageSID($pageSID);
					$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing_type_sid);
					$tp->assign('pageInfo', $pageInfo);
					$tp->assign('pages', $pages);
					$tp->assign('countPages', count($pages));
					$tp->assign("pageSID", $pageSID);
					$tp->assign("fieldsOnPage", $fieldsOnPage);
					$tp->assign("form_fields", $form_fields);
					$tp->assign("listing_fields", $listing_fields);
					break;
				case 'added':
					$action = 'list';
					break;
				case 'modified':
					$action = 'list';
					break;
				case 'move_up':
					SJB_PostingPagesManager::moveUpPageBySID($pageSID);
					$action = 'list';
					break;
				case 'move_down':
					SJB_PostingPagesManager::moveDownPageBySID($pageSID);
					$action = 'list';
					break;
				case 'delete':
					$countPages = SJB_PostingPagesManager::getNumPagesByListingTypeSID($listing_type_sid);
					if ($countPages > 1)
						SJB_PostingPagesManager::deletePageBySID($pageSID);
					$action = 'list';
					break;
			}

			if ($action == 'list') {
				$pages = SJB_PostingPagesManager::getPagesByListingTypeSID($listing_type_sid);
				$tp->assign('pages', $pages);
				$tp->assign('countPages', count($pages));
				$template = 'posting_pages.tpl';
			}
			$tp->assign('listingTypeInfo', $listingTypeInfo);
		}
		else {
			$errors['UNDEFINED_LISTING_TYPE_ID'] = 1;
		}
		$tp->assign('action', $action);
		$tp->assign('errors', $errors);
		$tp->display($template);
	}
}