<?php

class SJB_ImportLanguageAction
{
	/**
	 * @var SJB_I18N
	 */
	protected $i18n;
	function SJB_ImportLanguageAction($i18n, $lang_file_data)
	{
		$this->i18n = $i18n;
		$this->lang_file_data = $lang_file_data;
		
		$this->file_name = isset($lang_file_data['name'])
		                       ? $lang_file_data['name']
		                       : null;
		$this->temp_file_path = isset($lang_file_data['tmp_name'])
		                            ? $lang_file_data['tmp_name']
		                            : null;
		
		$temp_dest = SJB_System::getSystemSettings('TEMP_FILES_DIRECTORY');
		$this->file_path = SJB_Path::combine($temp_dest, $this->file_name);
	}
	
	function canPerform()
	{
		$this->errors = $this->_validate();
		return empty($this->errors);
	}
	
	function perform()
	{
		return $this->i18n->importLangFile($this->file_name, $this->file_path);
	}

	function getErrors()
	{
		return $this->errors;
	}

	function _validate()
	{
		$errors = array();

		if (!empty($this->lang_file_data) && $this->lang_file_data['error'] == UPLOAD_ERR_NO_FILE) {
			$errors[] = 'Please choose language file';
			return $errors;
		}

		if (!empty($this->lang_file_data) && $this->lang_file_data['error'] == UPLOAD_ERR_INI_SIZE) {
			$errors[] = 'File size exceeds system limit. Please check the file size limits on your hosting or upload another file';
			return $errors;
		}
		
		if (!SJB_WrappedFunctions::is_uploaded_file($this->temp_file_path)) {
			$errors[] = 'LANG_FILE_UPLOAD_FAILED';
			SJB_Logger::error('LANG_FILE_UPLOAD_FAILED');
		}
		
		if (!SJB_WrappedFunctions::move_uploaded_file($this->temp_file_path, $this->file_path)) {
			$errors[] = 'UPLOADED_LANG_FILE_CANNOT_BE_MOVED';
			SJB_Logger::error('UPLOADED_LANG_FILE_CANNOT_BE_MOVED');
		}
		
		$fileHelper = $this->i18n->getFileHelper();
		$languageID = $fileHelper->getLanguageIDForImportFile($this->file_name);
		
		if ($languageID === false) {
			$errors[] = 'The file format is invalid. Please try another file.';
			SJB_Logger::error('The file format is invalid. Please try another file.');
		}
		
		$lang_file_data = array(
			'languageId' => $languageID,
			'lang_file_path' => $this->file_path,
		);
				
		$validator = $this->i18n->createImportLanguageValidator($lang_file_data);
		
		if (!$validator->isValid()) {
			$errors = array_merge($errors, $validator->getErrors());
		}	

		$fileSystem = new SJB_FileSystem();
		
		if (!empty($errors))
			$fileSystem->deleteFile($this->file_path);
		
		return $errors;
	}
}

