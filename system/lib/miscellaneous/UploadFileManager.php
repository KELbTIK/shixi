<?php

class SJB_UploadFileManager
{
	var $file_group;
	var $max_file_size;
	var $uploaded_file_id;
	var $error;
	public $fileId;

	function setFileGroup($file_group)
	{
		$this->file_group = $file_group;
	}

	function setMaxFileSize($max_file_size)
	{
		$this->max_file_size = $max_file_size;
	}

	public static function getIniUploadMaxFilesize($returnSize = 'mb')
	{
		$value = ini_get('upload_max_filesize');
		
		if(empty($value)) {
			return 0;
		}
		
		$value = trim($value);
		
		preg_match('#([0-9]+)[\s]*([a-z]+)#i', $value, $matches);
		
		$value = isset($matches[1])? (int) $matches[1]: 0;
		$size  = isset($matches[2])? $matches[2]: '';
		
		switch (strtolower($size))
		{
			case 'g':
			case 'gb':
				$value *= 1024;
			case 'm':
			case 'mb':
				if ($returnSize == 'mb') {
					break;
				}
				
				$value *= 1024;
			case 'k':
			case 'kb':
				if ($returnSize == 'mb') {
					$value /= 1024;
				} else {
					$value *= 1024;
				}
		}
		
		return floor($value*100)/100;
	}

	function isValidUploadedFile($fileId, $subField = false, $acceptableFormats = array())
	{
		$fileFormats = array_merge($acceptableFormats, explode(',', SJB_System::getSettingByName('file_valid_types')));

		if (!empty($this->max_file_size) && floatval($this->max_file_size) > 0) {
			if (!$subField) {
				$fileInfo = pathinfo($_FILES[$fileId]['name']);
				if (!isset($fileInfo['extension']) || !in_array(strtolower($fileInfo['extension']), $fileFormats)) {
					//$this->error = strtolower($fileInfo['extension']) . ' ' . SJB_I18N::getInstance()->gettext(null, 'is not in an acceptable file format');
					$this->error = 'NOT_ACCEPTABLE_FILE_FORMAT';
					return false;
				}
				if ($_FILES[$fileId]['size'] > ($this->max_file_size * 1024 * 1024)) {
					$this->error = 'MAX_FILE_SIZE_EXCEEDED';
					return false;
				}
			}
			else {
				foreach ($_FILES[$fileId]['name'][$subField] as $name) {
					$fileInfo = pathinfo($name);
					if (!isset($fileInfo['extension']) || !in_array(strtolower($fileInfo['extension']), $fileFormats)) {
						//$this->error = strtolower($fileInfo['extension']) . ' ' . SJB_I18N::getInstance()->gettext(null, 'is not in an acceptable file format');
						$this->error = 'NOT_ACCEPTABLE_FILE_FORMAT';
						return false;
					}
				}

				foreach ($_FILES[$fileId]['size'][$subField] as $size) {
					if ($size > ($this->max_file_size * 1024 * 1024)) {
						$this->error = 'MAX_FILE_SIZE_EXCEEDED';
						return false;
					}
				}
			}
		}
		return true;
	}

	function isValidUploadedVideoFile($file_id)
	{
		$videoFormats  = array('avi', 'xvid', 'asf', 'dv', 'mkv', 'flv', 'gif', 'mov', 'mp4', 'm4a', '3gp', '3gpp', '3g2', 'mpeg', 'mpg', 'wav', 'wmv', 'rm');
		$fileInfo      = pathinfo($_FILES[$file_id]['name']);
		$fileExtension = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';

		if (!in_array(strtolower($fileExtension), $videoFormats)) {
			$this->error = 'NOT_SUPPORTED_VIDEO_FORMAT';
			return false;
		} else {
			if ($this->isValidUploadedFile($file_id)) {
				return true;
			}
		}
		return false;
	}

	function setUploadedFileID($uploaded_file_id)
	{
		$this->uploaded_file_id = $uploaded_file_id;
	}

	public static function isFileReadyForUpload($file_id, $complexParentFieldId = false)
	{
		$formToken         = SJB_Request::getVar('form_token');
		$tmpUploadsStorage = SJB_Session::getValue('tmp_uploads_storage');
		if ($complexParentFieldId) {
			$file = $complexParentFieldId . ':' . $file_id . ':1';
			if (!empty($tmpUploadsStorage[$formToken][$file]['file_name'])) {
				return $tmpUploadsStorage[$formToken][$file]['file_name'];
			}
			if (!empty($tmpUploadsStorage[$formToken][$file]['file_id'])) {
				return $tmpUploadsStorage[$formToken][$file]['file_id'];
			}

			return !empty($_FILES[$complexParentFieldId]['name'][$file_id][1]);
		} else {
			if (!empty($tmpUploadsStorage[$formToken][$file_id]['file_name'])) {
				return $tmpUploadsStorage[$formToken][$file_id]['file_name'];
			}
			if (!empty($tmpUploadsStorage[$formToken][$file_id]['file_id'])) {
				return $tmpUploadsStorage[$formToken][$file_id]['file_id'];
			}

			return !empty($_FILES[$file_id]['name']);
		}
	}

	function uploadFile($file_id, $complexParentFieldId = false)
	{
		if (is_null($this->uploaded_file_id)) {
			return false;
		}
		elseif (!empty($_FILES[$file_id]['name'])) {
			if ($_FILES[$file_id]['error']) {
				$this->error = $this->getErrorId($_FILES[$file_id]['error']);
				return false;
			}

			list($file_name, $saved_file_name) = self::getArrayOfFileNames($_FILES[$file_id]['name']);

			if (!empty($_FILES[$file_id]['import'])) {
				$saved_file = @copy($_FILES[$file_id]['tmp_name'], $file_name);
			} else {
				$saved_file = move_uploaded_file($_FILES[$file_id]['tmp_name'], $file_name);
			}

			if ($saved_file) {
				SJB_UploadFileManager::deleteUploadedFileByID($this->uploaded_file_id);
				$this->fileId = SJB_DB::query("INSERT INTO uploaded_files(id, file_name, file_group, saved_file_name, mime_type, creation_time)"
						. " VALUES(?s, ?s, ?s, ?s, ?s, ?s)", $this->uploaded_file_id, $_FILES[$file_id]['name'], $this->file_group, $saved_file_name, $_FILES[$file_id]['type'], time());
				return $saved_file_name;
			}
		}
		else if ($complexParentFieldId && !empty($_FILES[$complexParentFieldId]['name'][$file_id])) {
			if ($_FILES[$complexParentFieldId]['error'][$file_id][1]) {
				$this->error = $this->getErrorId($_FILES[$complexParentFieldId]['error'][$file_id]);
				return false;
			}

			list($file_name, $saved_file_name) = self::getArrayOfFileNames($_FILES[$complexParentFieldId]['name'][$file_id][1]);

			if (!empty($_FILES[$complexParentFieldId]['import'][$file_id])) {
				$saved_file = @copy($_FILES[$complexParentFieldId]['tmp_name'][$file_id], $file_name);
			} else {
				$saved_file = move_uploaded_file($_FILES[$complexParentFieldId]['tmp_name'][$file_id][1], $file_name);
			}

			if ($saved_file) {
				SJB_UploadFileManager::deleteUploadedFileByID($this->uploaded_file_id);
				$this->fileId = SJB_DB::query("INSERT INTO uploaded_files(id, file_name, file_group, saved_file_name, mime_type, creation_time)"
					. " VALUES(?s, ?s, ?s, ?s, ?s, ?s)", $this->uploaded_file_id, $_FILES[$complexParentFieldId]['name'][$file_id][1], $this->file_group, $saved_file_name, $_FILES[$complexParentFieldId]['type'][$file_id][1], time());
				return $saved_file_name;
			}
		}
	}

	function uploadFiles($file_id, $subField)
	{
		if (is_null($this->uploaded_file_id)) {
			return false;
		}
		else {
			if (!empty($_FILES[$file_id]['name'])) {
				$results = array();

				foreach ($_FILES[$file_id]['name'][$subField] as $key => $subFile) {

					if ($_FILES[$file_id]['error'][$subField][$key]) {
						$this->error = $this->getErrorId($_FILES[$file_id]['error'][$subField][$key]);
						$results[$key] = '';
						continue;
					}

					$file_basename = $subFile;

					$ext = substr($file_basename, 1 + strrpos($file_basename, "."));
					$file_valid_types = explode(',', SJB_System::getSettingByName('file_valid_types'));
					if (!in_array(strtolower($ext), $file_valid_types)) {
						$this->error = 'INVALID_FILE_TYPE';
						return false;
					}

					list($file_name, $saved_file_name) = self::getArrayOfFileNames($file_basename);

					if (move_uploaded_file($_FILES[$file_id]['tmp_name'][$subField][$key], $file_name)) {
						SJB_UploadFileManager::deleteUploadedFileByID($this->uploaded_file_id . '_' . $key);
						SJB_DB::query("INSERT INTO uploaded_files(id, file_name, file_group, saved_file_name, mime_type, creation_time)"
								. " VALUES(?s, ?s, ?s, ?s, ?s, ?s)", $this->uploaded_file_id . '_' . $key, $subFile, $this->file_group, $saved_file_name, $_FILES[$file_id]['type'][$subField][$key], time());
						$results[$key] = $this->uploaded_file_id . '_' . $key;
					}
				}
			}
			return $results;
		}

	}
	
	public static function getErrorId($errorSid)
	{
		switch ($errorSid) {
			case 1:
				return 'UPLOAD_ERR_INI_SIZE';
			case 2:
				return 'UPLOAD_ERR_FORM_SIZE';
			case 3:
				return 'UPLOAD_ERR_PARTIAL';
			case 4:
				return 'UPLOAD_ERR_NO_FILE';
			default:
				return 'FILE_NOT_UPLOADED';
		}
	}

	function getArrayOfFileNames($fileBasename)	{
		$uploadFileDirectory = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");
		$fileExtension = strrchr($fileBasename, ".");
		if (!empty($fileExtension)) {
			$fileNameWithoutExt = substr($fileBasename, 0, -strlen($fileExtension));
		} else {
			$fileNameWithoutExt = $fileBasename;
		}

		$filter = array (" ", "\\", "/");
		$savedFileName = str_replace($filter, '_', $fileBasename);
		$savedFileName = str_replace('"', '', str_replace("'", '', $savedFileName));
		$fileName = $uploadFileDirectory . "/" . $this->file_group . "/" . $savedFileName;
		$i = 0;
		$tmpAvi = str_replace($fileExtension, '.flv', $fileName);

		while (file_exists($tmpAvi) || file_exists($fileName)) {
			$savedFileName = $fileNameWithoutExt . "_" . ++$i . $fileExtension;
			$savedFileName = str_replace($filter, '_', $savedFileName);
			$savedFileName = str_replace('"', '', str_replace("'", '', $savedFileName));
			$fileName = $uploadFileDirectory . "/" . $this->file_group . "/" . $savedFileName;
			$tmpAvi = str_replace($fileExtension, '.flv', $fileName);
		}
		return array($fileName, $savedFileName);
	}

	function fileExists($file_name, $out_error = false)
	{
		$upload_file_directory = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");
		$file_name = $upload_file_directory . "/" . $this->file_group . "/" . $file_name;

		if (file_exists($file_name) && filesize($file_name))
			return true;

		if ($out_error)
			$this->error = 'NOT_CONVERT_VIDEO';

		return false;
	}

	function registNewFile($file_id, $name)
	{
		SJB_DB::query("	INSERT INTO uploaded_files(id, file_name, file_group, saved_file_name, creation_time)
					VALUES(?s, ?s, ?s, ?s, ?s)", $file_id, $name, $this->file_group, $name, time());
	}

	public static function deleteUploadedFileByID($file_id)
	{
		$file_info = SJB_DB::query('SELECT * FROM uploaded_files WHERE id = ?s', $file_id);
		if (!empty($file_info)) {
			$file_info = array_pop($file_info);
			self::deleteUploadedFile($file_info, $file_id);
			if ('pictures' == SJB_Array::get($file_info, 'file_group')) {
				$file_id = $file_id.'_thumb';
				$file_info = SJB_DB::query('SELECT * FROM `uploaded_files` WHERE `id` = ?s', $file_id);
				if (!empty($file_info)) {
					$file_info = array_pop($file_info);
					self::deleteUploadedFile($file_info, $file_id);
				}
			}
		}
	}

	public static function deleteUploadedFile($file_info, $file_id)
	{
		$upload_file_directory = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY');
		$file_name = SJB_Path::combine($upload_file_directory, $file_info['file_group'], $file_info['saved_file_name']);

		if (SJB_WrappedFunctions::file_exists($file_name)) {
			SJB_WrappedFunctions::unlink($file_name);
		}

		$ext = substr($file_name, 1 + strrpos($file_name, '.'));
		if ($ext == 'flv') {
			$base_name = substr($file_name, 0, strrpos($file_name, '.'));
			$file_name_img = $base_name . '.png';
			if (SJB_WrappedFunctions::file_exists($file_name_img)) {
				SJB_WrappedFunctions::unlink($file_name_img);
			}
		}
		SJB_DB::query("DELETE FROM uploaded_files WHERE id = ?s", $file_id);
	}

	function getError()
	{
		return $this->error;
	}

	public static function getUploadedFileLink($uploaded_file_id, $file_info = false, $noHost = false)
	{
		if ($file_info === false)
			$file_info = SJB_UploadFileManager::getUploadedFileInfo($uploaded_file_id);
		if (!empty($file_info)) {
			$upload_files_directory = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY');
			$file_group = $file_info['file_group'];
			$saved_file_name = $file_info['saved_file_name'];
			$file_name = $upload_files_directory . "/" . $file_group . "/" . $saved_file_name;
			$site_url = SJB_System::getSystemSettings("SITE_URL");
			if ($noHost)
				$link = $file_name;
			else
				$link = $site_url . "/" . $file_name;
			if (!file_exists($file_name))
				$link = null;
			return $link;
		}
		return null;
	}

	public static function getUploadedFileSize($uploaded_file_id)
	{
		$fileInfo = self::getUploadedFileInfo($uploaded_file_id);
		if (empty($fileInfo)) {
			return false;
		}

		$uploadFileDirectory = SJB_System::getSystemSettings("UPLOAD_FILES_DIRECTORY");

		$file_name = $uploadFileDirectory . "/" . $fileInfo['file_group'] . "/" . $fileInfo['saved_file_name'];
		if (!file_exists($file_name)) {
			return false;
		}
		return filesize($file_name);
	}


	public static function getUploadedFileInfo($uploaded_file_id)
	{
		if (!is_string($uploaded_file_id) || empty($uploaded_file_id)) {
			return null;
		}
		if (SJB_MemoryCache::has('UploadedFileInfo' . $uploaded_file_id)) {
			return SJB_MemoryCache::get('UploadedFileInfo' . $uploaded_file_id);
		} else {
			$file_info = SJB_DB::query("SELECT * FROM `uploaded_files` WHERE `id` = ?s", $uploaded_file_id);
			if (!empty($file_info)) {
				$result = array_pop($file_info);
				SJB_MemoryCache::set('UploadedFileInfo' . $uploaded_file_id, $result);
				return $result;
			}
		}
		return null;
	}

	public static function getUploadedFileName($uploaded_file_id)
	{
		$file_info = SJB_UploadFileManager::getUploadedFileInfo($uploaded_file_id);
		if (!empty($file_info))
			return $file_info['file_name'];
		return null;
	}

	public static function getUploadedSavedFileName($uploaded_file_id)
	{
		$file_info = SJB_UploadFileManager::getUploadedFileInfo($uploaded_file_id);
		if (!empty($file_info))
			return $file_info['saved_file_name'];
		return null;
	}

	public static function getUploadedFileGroup($uploaded_file_id)
	{
		$file_info = SJB_UploadFileManager::getUploadedFileInfo($uploaded_file_id);
		if (!empty($file_info))
			return $file_info['file_group'];
		return null;
	}

	public static function doesFileExistByID($uploaded_file_id)
	{
		if (empty($uploaded_file_id))
			return false;
		$file_info = SJB_DB::query("SELECT * FROM `uploaded_files` WHERE `id` = ?s", $uploaded_file_id);
		return !empty($file_info);
	}

	public static function getMimeTypeByFilename($filename, $id)
	{
		$mime_type = false;
		$listing = SJB_ListingManager::getObjectBySID($id);
		$complexPropertyName = SJB_Request::getVar('complex_field', '');

		// check for new listing (add-listing action during we do not have real listing ID)
		if (strlen($id) == strlen(time())) {
			// look for listing ID in temporary storage

			if (empty($complexPropertyName)) {
				// simple field file
				$fieldName = SJB_Request::getVar('field_id', '');
				if (empty($fieldName)) {
					return false;
				}
			} else {
				// complex field file
				$fieldName = $complexPropertyName;
			}

			$tmpFileStorage = SJB_Session::getValue('tmp_uploads_storage');
			$tmpFileId      = SJB_Array::getPath($tmpFileStorage, "listings/{$id}/{$fieldName}/file_id");

			if (!empty($tmpFileId)) {
				$mime_type = SJB_DB::query("SELECT `up`.`mime_type`, `up`.`sid`, `up`.`id` FROM `uploaded_files` `up`
					WHERE `up`.`id` IN (?s) AND `up`.`saved_file_name` = ?s", $tmpFileId, $filename);
			}

		} else {
			// ORDINARY WORK WITH REAL LISTINGS
			foreach ($listing->getProperties() as $property) {
				if ($property->isComplex()) {
					foreach ($property->type->complex->getProperties() as $complexProperty) {
						if ($complexProperty->getType() == 'file' || $complexProperty->getType() == 'complexfile') {
							$fileIds = $complexProperty->type->property_info['value'];
							if (is_array($fileIds)) {
								foreach ($fileIds as $key => $fileID) {
									if ($fileID != $complexPropertyName) {
										unset($fileIds[$key]);
									}
								}

								if (!empty($fileIds)) {
									return SJB_DB::query('SELECT `up`.`mime_type`, `up`.`sid`, `up`.`id` FROM `uploaded_files` `up`
										WHERE `up`.`id` IN (?l) AND `up`.`saved_file_name` = ?s', $fileIds, $filename);
								} else {
									// check temporary session storage by filename
									$tmpFileStorage      = SJB_Session::getValue('tmp_uploads_storage');
									$complexPropertyName = SJB_Request::getVar('complex_field', '');

									$tmpFileId = SJB_Array::getPath($tmpFileStorage, "listings/{$id}/{$complexPropertyName}/file_id");

									if (!empty($tmpFileId)) {
										$mime_type = SJB_DB::query("SELECT `up`.`mime_type`, `up`.`sid`, `up`.`id` FROM `uploaded_files` `up`
											WHERE `up`.`id` IN (?s) AND `up`.`saved_file_name` = ?s", $tmpFileId, $filename);
									}
								}
							}
						}
					}
				}
				elseif ($property->getType() == 'file') {
					$fileIds = $property->getValue();
					if (is_array($fileIds) && !empty($fileIds['saved_file_name'])) {
						if (!empty($fileIds['file_id'])) {
							$query = 'SELECT `up`.`mime_type`, `up`.`sid`, `up`.`id` FROM `uploaded_files` `up`
										WHERE `up`.`id` IN (?l) AND `up`.`saved_file_name` = ?s';
							$mime_type = SJB_DB::query($query, $fileIds['file_id'], $filename);
							if ($mime_type) {
								break;
							}
						} else {
							// check temporary session storage by filename
							$tmpFileStorage = SJB_Session::getValue('tmp_uploads_storage');
							$tmpFileId = SJB_Array::getPath($tmpFileStorage, "listings/{$id}/{$property->id}/file_id");

							if (!empty($tmpFileId)) {
								$mime_type = SJB_DB::query("SELECT `up`.`mime_type`, `up`.`sid`, `up`.`id` FROM `uploaded_files` `up`
									WHERE `up`.`id` IN (?s) AND `up`.`saved_file_name` = ?s", $tmpFileId, $filename);
							}
						}
					}
				}
			}
		}

		return $mime_type ? array_pop($mime_type) : false;
	}

	public static function getMimeTypeAppsByFilename($filename, $appsID)
	{
		$mime_type = SJB_DB::query("SELECT `up`.`mime_type`, `up`.`sid`, `up`.`id` FROM `uploaded_files` `up`
								INNER JOIN `applications` `apps` ON `up`.`id` = `apps`.`file_id` WHERE `apps`.`id` = ?s AND `up`.`saved_file_name` = ?s", $appsID, $filename);
		return $mime_type ? array_pop($mime_type) : false;
	}

	public static function openFile($filename, $id)
	{
		$file_info = SJB_UploadFileManager::getMimeTypeByFilename($filename, $id);
		if ($file_info) {
			$file_link = SJB_UploadFileManager::getUploadedFileLink($file_info['id'], false, true);
			for ($i = 0; $i < ob_get_level(); $i++) {
				ob_end_clean();
			}
			header("Content-Length:" . filesize($file_link));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header("Content-type: " . $file_info['mime_type']);
			readfile($file_link);
			exit();
		}
		return false;
	}

	public static function openApplicationFile($filename, $appsID)
	{
		$file_info = SJB_UploadFileManager::getMimeTypeAppsByFilename($filename, $appsID);
		if ($file_info) {
			$file_link = SJB_UploadFileManager::getUploadedFileLink($file_info['id'], false, true);
			for ($i = 0; $i < ob_get_level(); $i++) {
				ob_end_clean();
			}
			header("Content-Length:" . filesize($file_link));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header("Content-type: " . $file_info['mime_type']);
			readfile($file_link);
			exit();
		}
		return false;
	}

	/**
	 * @static
	 * @param $fileName
	 * @param int $etSID - email template SID
	 * @return bool
	 */
	public static function openEmailTemplateFile($fileName, $etSID)
	{
		$emailTpl = SJB_EmailTemplateEditor::getEmailTemplateBySID($etSID);
		$file_info = SJB_UploadFileManager::getEmailTemplateFileMimeTypeByFilename($emailTpl->getPropertyValue('file'));
		if ($file_info) {
			$file_link = SJB_UploadFileManager::getUploadedFileLink($file_info['id'], false, true);
			for ($i = 0; $i < ob_get_level(); $i++) {
				ob_end_clean();
			}
			header('Content-Length:' . filesize($file_link));
			header('Content-Disposition: attachment; filename="' . $fileName . '"');
			header('Content-type: ' . $file_info['mime_type']);
			readfile($file_link);
			exit();
		}
		return false;
	}

	public static function getEmailTemplateFileMimeTypeByFilename($fileInfo)
	{
		$mime_type = SJB_DB::query('SELECT `mime_type`, `sid`, `id` FROM `uploaded_files`
			WHERE `uploaded_files`.`id` = ?s AND `uploaded_files`.`saved_file_name` = ?s',
			SJB_Array::get($fileInfo, 'file_id'), SJB_Array::get($fileInfo, 'saved_file_name'));
		return $mime_type ? array_pop($mime_type) : false;
	}

	/**
	 * The import of files
	 *
	 * @param  $import_info	array
	 * @param  $field_info	 array
	 * @return bool|array
	 */
	public static function fileImport($import_info, $field_info, $complexParent = false)
	{
		if ($complexParent && !empty($field_info) && $field_info['type'] == 'complexfile') {
			if (empty($import_info[$complexParent][$field_info['id']]) || !is_array($import_info[$complexParent][$field_info['id']]))
				return false;
			else {
				$temp = null;
				foreach ($import_info[$complexParent][$field_info['id']] as $key => $file_name) {
					$data = SJB_UploadFileManager::getDataForImportListing($file_name);
					$_FILES[$complexParent]['name'][$field_info['id']][$key] = $data['name'];
					$_FILES[$complexParent]['type'][$field_info['id']][$key] = $data['type'];
					$_FILES[$complexParent]['tmp_name'][$field_info['id']][$key] = $data['tmp_name'];
					$_FILES[$complexParent]['error'][$field_info['id']][$key] = $data['error'];
					$_FILES[$complexParent]['size'][$field_info['id']][$key] = $data['size'];
					$_FILES[$complexParent]['import'][$field_info['id']][$key] = 1;
					if (strpos($data['tmp_name'], '/temp/import/') !== false) {
						$temp = true;
					}
				}
				return $temp;
			}
		}
		elseif (empty($import_info[$field_info['id']])) {
			return false;
		}
		elseif (in_array($field_info['type'], array('file', 'logo', 'video'))) {
			$data = SJB_UploadFileManager::getDataForImportListing($import_info[$field_info['id']]);

			if (empty($data)) {
				return false;
			}

			$_FILES[$field_info['id']] = array(
				'name' => $data['name'],
				'type' => $data['type'],
				'tmp_name' => $data['tmp_name'],
				'error' => $data['error'],
				'size' => $data['size'],
				'import' => 1
			);

			if (strpos($data['tmp_name'], '/temp/import/') !== false) {
				return true;
			}
		}
		return false;
	}

	public static function getDataForImportListing($fileName)
	{
		$fileName = str_replace('\\', '/', $fileName);
		$ext = strrchr($fileName, '.');
		$pos = strpos($fileName, $ext);
		$saved_file_name = substr($fileName, 0, $pos);
		$saved_file_name = strrchr($saved_file_name, '/');
		$saved_file_name = trim($saved_file_name, '/');
		$file_name_with_ext = $saved_file_name . $ext;
		if (!file_exists($fileName)) {
			if (file_exists(SJB_System::getSystemSettings('IMPORT_FILES_DIRECTORY') . '/' . $fileName)) {
				$fileName = SJB_System::getSystemSettings('IMPORT_FILES_DIRECTORY') . '/' . $fileName;
			}
		}

		@$image_info = getimagesize($fileName);
		if ($image_info) {
			$mime_type = $image_info['mime'];
		} else {
			if (!file_exists($fileName)) {
				return array();
			}
			$mime_type = mime_content_type($fileName);
		}

		if (strpos($fileName, 'http://') !== false) {
			$import_files_dir = SJB_System::getSystemSettings("IMPORT_FILES_DIRECTORY") . '/';
			if (!is_dir($import_files_dir)) {
				mkdir($import_files_dir, 0777);
			}

			$file_name_tmp = $import_files_dir . $file_name_with_ext;
			if (file_exists($file_name_tmp) && filesize($file_name_tmp)) {
				$i = 0;
				do {
					$file_name_with_ext = $saved_file_name . '_' . ++$i . $ext;
					$file_name_tmp = $import_files_dir . $file_name_with_ext;
				} while (file_exists($file_name_tmp));
			}

			@copy($fileName, $file_name_tmp);
			$fileName = $file_name_tmp;
		}

		if (!file_exists($fileName)) {
			return array();
		}

		return array(
			'name' 	   => $file_name_with_ext,
			'type' 	   => $mime_type,
			'tmp_name' => $fileName,
			'error'    => 0,
			'size' 	   => filesize($fileName),
		);
	}

	public function copyFile($uploadedFileInfo, $fileID)
	{
		if (!SJB_UploadFileManager::doesFileExistByID($fileID)) {
			list($fileName, $savedFileName) = SJB_UploadFileManager::getArrayOfFileNames($uploadedFileInfo['file_name']);
			$uploadFileDirectory = SJB_System::getSystemSettings('UPLOAD_FILES_DIRECTORY');
			if (copy($uploadFileDirectory . '/' .$uploadedFileInfo['file_group'] . '/' . $uploadedFileInfo['saved_file_name'], $fileName)) {
				if ($uploadedFileInfo['file_group'] == 'video') {
					$baseName = substr($savedFileName,0, strrpos($savedFileName, '.'));
					$img = $uploadFileDirectory . '/' .$uploadedFileInfo['file_group'] . '/'  . $baseName . '.png';
					$uploadManager = new SJB_UploadVideoFileType($uploadedFileInfo);
					$uploadManager->grab_image($fileName, $img, '00:00:03', 'png', 320, 240);
				}

				if (!empty($uploadedFileInfo['sid'])) {
					unset($uploadedFileInfo['sid']);
				}
				$uploadedFileInfo['id'] = $fileID;
				$uploadedFileInfo['saved_file_name'] = $savedFileName;
				$uploadedFileFields = array_filter(array_keys($uploadedFileInfo), 'is_string');
				$keys = "`" . implode("`,`", $uploadedFileFields) . "`";
				$info = "'" . implode("','", array_values(array_intersect_key($uploadedFileInfo, array_flip($uploadedFileFields)))) . "'";
				SJB_DB::query("INSERT INTO `uploaded_files` (".$keys.") VALUES (".$info.")");
			}
		}
	}

	public static function deleteUploadedFilesByListingSID($listingSid)
	{
		$listing = SJB_ListingManager::getObjectBySID($listingSid);
		foreach ($listing->getProperties() as $property) {
			if (in_array($property->getType(), array('file', 'video', 'complex'))) {
				$uploadedFileId = null;
				if ($property->getType() == 'complex') {
					$complexFields = $property->type->complex->getProperties();
					foreach ($complexFields as $complexField) {
						if ($complexField->getType() == 'complexfile') {
							$complexFieldValues = $complexField->getValue();
							foreach ($complexFieldValues as $value) {
								$uploadedFileId = SJB_Array::get($value, 'file_id');
								SJB_UploadFileManager::deleteUploadedFileByID($uploadedFileId);
							}
						}
					}
				} else {
					$value = $property->getValue();
					$uploadedFileId = SJB_Array::get($value, 'file_id');
					SJB_UploadFileManager::deleteUploadedFileByID($uploadedFileId);
				}
			}
		}
	}
}
