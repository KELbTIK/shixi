<?php

class SJB_UploadFileType extends SJB_Type
{
	function SJB_UploadFileType($property_info)
	{
		parent::SJB_Type($property_info);
		$this->default_template = 'file.tpl';
	}

	function isEmpty() 
	{
		if ($this->getComplexParent()) {
			return SJB_UploadFileManager::isFileReadyForUpload($this->getComplexParent());
		}
		return parent::isEmpty() && !SJB_UploadFileManager::isFileReadyForUpload($this->property_info['id']);
	}
	
	function getPropertyVariablesToAssign()
	{
		$upload_manager = new SJB_UploadFileManager();
		$upload_manager->setFileGroup("files");

		if (is_array($this->property_info['value'])) {
			$value = array();
			foreach ($this->property_info['value'] as $key => $fileId) {
				$value[$key] = array(
							'file_url' => $upload_manager->getUploadedFileLink($fileId),
							'file_name' => $upload_manager->getUploadedFileName($fileId),
							'saved_file_name' => $upload_manager->getUploadedSavedFileName($fileId),
							'file_id' => $fileId
						);
			}
			return array(
				'id' 	=> $this->property_info['id'],
				'filesInfo' => $value,
				'value' => $value
			);
		}

		return array(
						'id' 	=> $this->property_info['id'],
						'value'	=> array(
							'file_url' => $upload_manager->getUploadedFileLink($this->property_info['value']),
							'file_name' => $upload_manager->getUploadedFileName($this->property_info['value']),
							'saved_file_name' => $upload_manager->getUploadedSavedFileName($this->property_info['value']),
							'file_id' => $this->property_info['value'],
						),
					);
	}

	function getValue()
	{
        $upload_manager = new SJB_UploadFileManager();
		if (is_array($this->property_info['value'])) {
			$value = array();
			foreach ($this->property_info['value'] as $key => $fileId) {
				$file_info = SJB_UploadFileManager::getUploadedFileInfo($fileId);
				$value[$key] = array(
							'file_url' => $upload_manager->getUploadedFileLink($fileId, $file_info),
							'file_name' => $file_info['file_name'],
							'saved_file_name' => $file_info['saved_file_name'],
							'file_id' => $fileId,
						);
			}
			return $value;
		}
		$file_info = SJB_UploadFileManager::getUploadedFileInfo($this->property_info['value']);
		return array(
			'file_url' 	=> $upload_manager->getUploadedFileLink($this->property_info['value'], $file_info),
			'file_name' => empty($file_info['file_name']) ? null : $file_info['file_name'],
			'saved_file_name' => empty($file_info['saved_file_name']) ? null : $file_info['saved_file_name'],
			'file_id' => $this->property_info['value'],
		);
	}

	function isValid()
	{
		$this->fieldID = $this->property_info['id'];
		if (!isset($_FILES[$this->fieldID]['name']) || $_FILES[$this->fieldID]['name'] == '')
			return true;
			
		$upload_manager = new SJB_UploadFileManager();
		if (!empty($this->property_info['max_file_size'])) {
			$upload_manager->setMaxFileSize($this->property_info['max_file_size']);
		} else {
			$upload_manager->setMaxFileSize(SJB_UploadFileManager::getIniUploadMaxFilesize());
		}

		if ($this->getComplexParent()) {
			$upload_manager->isValidUploadedFile($this->getComplexParent(), $this->property_info['id']);
		} else if ($upload_manager->isValidUploadedFile($this->property_info['id'])) {
			return true;
		}
		
		return $upload_manager->getError();
	}
	
	function getSQLValue()
	{
		if (is_array($this->property_info['value']) && ! empty($this->property_info['value']['import'])) {
			return $this->property_info['value']['import'];
		} else {
			$fileId = $this->property_info['id'] . "_" .$this->object_sid;
			$this->property_info['value'] = $fileId;
			$uploadManager = new SJB_UploadFileManager();
			$uploadManager->setFileGroup('files');
			$uploadManager->setUploadedFileID($fileId);
			$uploadManager->uploadFile($this->property_info['id']);
			if (SJB_UploadFileManager::doesFileExistByID($fileId)) {
				return $fileId;
			}
			return '';
        }
	}

	public static function getFieldExtraDetails()
	{
		return array(
			array(
				'id'		=> 'max_file_size',
				'caption'	=> 'Maximum File Size', 
				'comment'   => 'Server configuration upload max filesize '.ini_get('upload_max_filesize'),
				'type'		=> 'float',
				'length'	=> '20',
				'minimum'	=> '0',
				'signs_num' => '2',
				),
		);
	}
	
    function getKeywordValue()
	{
		$keywords = '';
		if (!parent::isEmpty() && SJB_Settings::getSettingByName('get_keyword_from_file')) {
			$fileId = $this->property_info['id'] . "_" .$this->object_sid;
			$fileInfo = SJB_UploadFileManager::getUploadedFileInfo($fileId);
			if ($fileInfo) {
				$uploadManager = new SJB_UploadFileManager();
				$uploadManager->setFileGroup("files");
				$fileUrl = $uploadManager->getUploadedFileLink($fileId, $fileInfo, true);
				$fileExtension = substr(strrchr($fileInfo['saved_file_name'], "."), 1);
				if (file_exists($fileUrl)) {
					switch ($fileExtension) {
						case 'doc':
							$doc = new doc();
							$doc->read($fileUrl);
							$keywords = preg_replace('/[^[:punct:]\w]/', '', strip_tags($doc->parse()));
							break;
						case 'docx':
							$keywords = SJB_HelperFunctions::docx2text($fileUrl);
							$keywords = preg_replace('/[^[:punct:]\w]/', '', strip_tags(html_entity_decode($keywords)));
							break;
						case 'xls':
						case 'xlsx':
							$fileInfo['tmp_name'] = $fileUrl;
							$fileObj = new SJB_ImportFileXLS($fileInfo);
							$fileObj->parse();
							$data = $fileObj->getData();
							$keywords = '';
							foreach ($data as $val) {
								$val = array_unique($val);
								$val = array_diff($val, array(''));
								$keywords .= implode(' ', $val);
							}
							$keywords = preg_replace("/[[:punct:]^\s]/ui", " ", $keywords);
							break;
						case 'pdf':
							$outFilename = str_replace(".".$fileExtension, '.txt', $fileUrl);
							exec("pdftotext {$fileUrl} {$outFilename}");
							if (file_exists($outFilename)) {
								$keywords = file_get_contents($outFilename);
								$keywords = preg_replace('/[^[:punct:]\w]/', '', strip_tags(html_entity_decode($keywords)));
								unlink($outFilename);
							}
							break;
						case 'txt':
							$keywords = file_get_contents($fileUrl);
							$keywords = preg_replace('/[^[:punct:]\w]/', '', strip_tags(html_entity_decode($keywords)));
							break;
					}
				}
			}
		}
		return $keywords;
	}
}
