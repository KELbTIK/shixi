<?php


class SJB_Admin_Classifieds_ImportTreeData extends SJB_Function
{
	public function isAccessible()
	{
		$this->setPermissionLabel('manage_common_listing_fields');
		return parent::isAccessible();
	}

	public function execute()
	{
		$template_processor = SJB_System::getTemplateProcessor();
		$encodingFromCharset = SJB_Request::getVar('encodingFromCharset', 'UTF-8');
		$file_info = SJB_Array::get($_FILES, 'imported_tree_file');
		$field_sid = isset($_REQUEST['field_sid']) ? $_REQUEST['field_sid'] : null;
		$field_info = SJB_ListingFieldManager::getFieldInfoBySID($field_sid);

		$template_processor->assign("field", $field_info);
		$template_processor->assign("field_sid", $field_sid);

		$listing_type_info = SJB_ListingTypeManager::getListingTypeInfoBySID($field_info['listing_type_sid']);

		$template_processor->assign("type_info", $listing_type_info);
		$template_processor->assign('charSets', SJB_HelperFunctions::getCharSets());
		if (!strcasecmp("tree", $field_info['type'])) {

			if (empty($_FILES['imported_tree_file']['name']))
				$errors['File'] = 'EMPTY_VALUE';

			if (isset($_FILES['imported_tree_file']['error']) && $_FILES['imported_tree_file']['error']) {
				$errors[] = SJB_UploadFileManager::getErrorId($_FILES['imported_tree_file']['error']);
			}

			$start_line = SJB_Request::getVar('start_line', null);

			if (empty($start_line))
				$errors['Start Line'] = 'EMPTY_VALUE';
			elseif (!is_numeric($start_line) || !is_int($start_line + 0))
				$errors['Start Line'] = 'NOT_INT_VALUE';

			$form_submitted = ($_SERVER['REQUEST_METHOD'] == 'POST');
			if ($form_submitted) {
				if (!SJB_ImportFile::isValidFileExtensionByFormat($_REQUEST['file_format'], $_FILES['imported_tree_file'])) {
					$errors['File'] = 'DO_NOT_MATCH_SELECTED_FILE_FORMAT';
				}
			}
			$is_data_valid = empty($errors);

			if ($form_submitted && $is_data_valid) {
				if (!strcasecmp($_REQUEST['file_format'], 'excel')) {
					$import_file = new SJB_ImportFileXLS($file_info);
				} else {
					$import_file = new SJB_ImportFileCSV($file_info, ',');
				}
				$import_file->parse($encodingFromCharset);
				$imported_data = $import_file->getData();

				$count = 0;
				foreach($imported_data as $key => $importedColumn) {
					if (!$importedColumn || ($start_line > $key))
						continue;
					
					if (SJB_ListingFieldTreeManager::importTreeItem($field_sid, $importedColumn)) 
						$count++;
				}

				$template_processor->assign("count", $count);
				$template_processor->display("import_tree_data_statistics.tpl");
			} else {
				if (!$form_submitted) {
					$errors = null;
				}
				$template_processor->assign("errors", isset($errors) ? $errors : null);
				$template_processor->assign("uploadMaxFilesize", SJB_UploadFileManager::getIniUploadMaxFilesize());
				$template_processor->display("import_tree_data.tpl");
			}
		} else {
			echo 'invalid Tree SID is specified';
		}
	}
}
